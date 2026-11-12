<?PHP
require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/ChucNangPeer.php");
require_once ("web_src/bean/NhomQuyenPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");
require_once ("web_src/bean/TinhTrangPeer.php");
require_once ("web_src/bean/CtChiSoPeer.php");
require_once ("web_src/common/SimpleXLSXGen/SimpleXLSXGen.php");

class chisochatluongAction
{
	var $request;
	var $ChiSoPeer;
	var $CtChiSoPeer;
	var $lastErrorMessage;
  	public static $listRole ="chisochatluong,save,update,gui,duyet,xoa,tuchoi,khoaA,khoaB";
	public function __construct()
	{
		$this->request = new Request;
		$this->ChiSoPeer = new ChiSoChatLuongPeer();
		$this->CtChiSoPeer = new CtChiSoPeer();
		$this->request->setTitle("Danh sach chi so");
	}

	function index()
	{
		$this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/chisochatluong/chisochatluong.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
		$this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');

		$nhomquyenPeer = new NhomQuyenPeer();
		$KCTTpeer = new DanhMucKCTTPeer();
		$PVpeer = new PhamViPeer();
		$ChuKyPeer = new ChuKyPeer();
		$donvitinhPeer = new DonViTinhPeer();
		$tinhtrangPeer = new TinhTrangPeer();

		$this->request->setAttribute("listNQ", $nhomquyenPeer->getListNQ());
		$this->request->setAttribute("listKCCT", $KCTTpeer->Get_danhmucKCTT());
		$this->request->setAttribute("listPV", $PVpeer->getPhamVi());
		$this->request->setAttribute("listCKy", $ChuKyPeer->getChuKy());
		$this->request->setAttribute("listDvt", $donvitinhPeer->getDonViTinh());
		$this->request->setAttribute("listTT", $tinhtrangPeer->getTinhTrang());
		$this->request->setAttribute("listKhoi", $this->ChiSoPeer->getListKhoi());
		$this->request->setAttribute("listKhoaPhong", $this->ChiSoPeer->getListKhoaPhongByKhoi());
		$this->request->setAttribute(
			"currentKhoaPhongId",
			$this->ChiSoPeer->getKhoaPhongIdByUserId(isset($_SESSION["sUserID"]) ? $_SESSION["sUserID"] : 0)
		);
		$this->request->setModel("www/chisochatluong/index.php");
		return true;
	}

	public function getData()
	{
		$userId = isset($_SESSION["sUserID"])
			? (int) $_SESSION["sUserID"]
			: 0;

		$coQuyenDuyet = $this->request->checkRole(
			"chisochatluong.duyet"
		);

		$idKhoaPhong = $this->ChiSoPeer
			->getKhoaPhongIdByUserId($userId);

		$data['data'] = $this->ChiSoPeer->getList(
			$coQuyenDuyet,
			$idKhoaPhong,
			$userId
		);

		return $this->request->json_response(
			json_encode($data)
		);
	}

	public function getBieuDoChuKy()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0 || !$this->coQuyenXemChiSo($maChiSo)) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'message' => 'Mã chỉ số không hợp lệ'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getTrungBinhTheoKy($maChiSo),
			'cau_hoi' => $this->CtChiSoPeer->getTrungBinhTheoCauHoi($maChiSo)
		)));
	}

	public function getThongKeTheoThang()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0 || !$this->coQuyenXemChiSo($maChiSo)) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'data' => array(),
				'message' => 'Bạn không có quyền xem chỉ số này'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getThongKeTheoThang($maChiSo)
		)));
	}

	public function getThongKeNhapThem()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0 || !$this->coQuyenXemChiSo($maChiSo)) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'data' => array(),
				'message' => 'Bạn không có quyền xem chỉ số này'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getThongKeNhapThem($maChiSo)
		)));
	}

	public function getDanhSachPhieu()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0 || !$this->coQuyenXemChiSo($maChiSo)) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'data' => array(),
				'message' => 'Mã chỉ số không hợp lệ'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getDanhSachPhieu($maChiSo)
		)));
	}

	private function coQuyenXemChiSo($maChiSo)
	{
		return $this->getChiSoDuocXem($maChiSo) !== false;
	}

	private function getChiSoDuocXem($maChiSo)
	{
		$userId = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
		$coQuyenDuyet = $this->request->checkRole('chisochatluong.duyet');
		$idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($userId);
		$danhSach = $this->ChiSoPeer->getList($coQuyenDuyet, $idKhoaPhong, $userId);

		foreach ($danhSach as $chiSo) {
			if ((int) $chiSo->get('ma_chi_so') === (int) $maChiSo) {
				return $chiSo;
			}
		}
		return false;
	}

	public function xuatExcel()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		$chiSo = $this->getChiSoDuocXem($maChiSo);
		if ($maChiSo <= 0 || $chiSo === false) {
			header('HTTP/1.1 403 Forbidden');
			echo 'Bạn không có quyền xuất chỉ số này.';
			exit;
		}

		try {
			$thongKe = $this->CtChiSoPeer->getTrungBinhTheoCauHoi($maChiSo);
			$nhapThem = $this->CtChiSoPeer->getThongKeNhapThem($maChiSo);
			$danhSachPhieu = $this->CtChiSoPeer->getDanhSachPhieu($maChiSo);
			$noiDung = $this->taoNoiDungExcel($chiSo, $thongKe, $nhapThem, $danhSachPhieu);
			if ($noiDung === '') throw new Exception('Không tạo được XLSX');

			$tenFile = str_replace(array('\\', '/', ':', '*', '?', '"', '<', '>', '|'), '_', trim((string) $chiSo->get('ten_chi_so')));
			$tenFile = trim(preg_replace('/[\x00-\x1F]+/', '', $tenFile), " ._\t\n\r\0\x0B");
			if ($tenFile === '') $tenFile = 'chi-so-' . $maChiSo;
			while (ob_get_level() > 0) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="chi-so-' . $maChiSo . '.xlsx"; filename*=UTF-8\'\'' . rawurlencode($tenFile . '.xlsx'));
			header('Content-Length: ' . strlen($noiDung));
			header('Cache-Control: no-store, max-age=0');
			echo $noiDung;
		} catch (Throwable $e) {
			error_log('chisochatluong.xuatExcel: ' . $e->getMessage());
			while (ob_get_level() > 0) ob_end_clean();
			if (!headers_sent()) {
				header('HTTP/1.1 500 Internal Server Error');
				header('Content-Type: text/plain; charset=UTF-8');
			}
			echo 'Không thể tạo file Excel. Vui lòng thử lại.';
		}
		exit;
	}

	private function taoNoiDungExcel($chiSo, $thongKe, $nhapThem, $danhSachPhieu = array())
	{
		$cell = function ($value, $style = '') {
			return $style . '<raw>' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</raw>';
		};
		$number = function ($value, $style = '') use ($cell) {
			return is_numeric($value) ? $style . (string) (float) $value : $cell('', $style);
		};
		$title = '<b><center><middle><wraptext><style height="30" border="thin">';
		$header = '<b><center><middle><wraptext><style height="50" border="thin">';
		$label = '<b><middle><wraptext><style border="thin">';
		$data = '<top><wraptext><style border="thin">';
		$rows = array(array($cell('THÔNG TIN CHỈ SỐ CHẤT LƯỢNG', $title)));
		$merges = array('A1:E1');
		$info = array(
			'Mã chỉ số' => $chiSo->get('ma_chi_so'),
			'Tên chỉ số' => $chiSo->get('ten_chi_so'),
			'Mục tiêu' => $chiSo->get('muc_tieu'),
			'Ngưỡng cảnh báo' => $chiSo->get('nguong_canh_bao'),
			'Định nghĩa' => $chiSo->get('dinh_nghia'),
			'Phương pháp thu thập' => $chiSo->get('thu_thap'),
			'Tên tử số' => $chiSo->get('ten_tu_so'),
			'Tên mẫu số' => $chiSo->get('ten_mau_so')
		);
		foreach ($info as $key => $value) {
			$rows[] = array($cell($key, $label), $cell($value, $data));
		}
		$rows[] = array();
		$rows[] = array($cell('THỐNG KÊ CÂU HỎI', $title));
		$merges[] = 'A' . count($rows) . ':E' . count($rows);
		$rows[] = array_map(function ($text) use ($cell, $header) {
			return $cell($text, $header);
		}, array('Ký hiệu', 'Nội dung câu hỏi', 'Khoa/Phòng', 'Tỷ lệ trung bình (%)', 'Số phiếu'));
		foreach ($thongKe as $item) {
			$rows[] = array($cell($item['ky_hieu'], $data), $cell($item['noi_dung'], $data),
				$cell('Tổng số phiếu', $data), $number($item['trung_binh'], $data),
				$number($item['so_phieu'], $data));
			foreach ($item['theo_khoa_phong'] as $khoa) {
				$rows[] = array($cell($item['ky_hieu'], $data), $cell($item['noi_dung'], $data),
					$cell($khoa['ten_khoaphong'], $data), $number($khoa['trung_binh'], $data),
					$number($khoa['so_phieu'], $data));
			}
		}
		if (empty($thongKe)) {
			$rows[] = array($cell('Chưa có dữ liệu thống kê.', $data));
			$merges[] = 'A' . count($rows) . ':E' . count($rows);
		}

		if (!empty($nhapThem)) {
			$rows[] = array();
			$rows[] = array($cell('THỐNG KÊ PHƯƠNG ÁN CÓ Ô NHẬP (KHÁC)', $title));
			$merges[] = 'A' . count($rows) . ':E' . count($rows);
			$rows[] = array_map(function ($text) use ($cell, $header) {
				return $cell($text, $header);
			}, array('Câu hỏi', 'Phương án', 'Tổng lượt chọn', 'Nội dung nhập thêm', 'Số lượt cùng nội dung'));
			foreach ($nhapThem as $item) {
				$cauHoi = $item['ky_hieu'] . ' - ' . $item['cau_hoi'];
				if (empty($item['noi_dung_nhap'])) {
					$rows[] = array($cell($cauHoi, $data), $cell($item['phuong_an'], $data),
						$number(0, $data), $cell('-', $data), $number(0, $data));
					continue;
				}
				foreach ($item['noi_dung_nhap'] as $noiDung) {
					$rows[] = array($cell($cauHoi, $data), $cell($item['phuong_an'], $data),
						$number($item['so_luot'], $data), $cell($noiDung['noi_dung'], $data),
						$number($noiDung['so_luot'], $data));
				}
			}
		}

		$xlsx = new \Shuchkin\SimpleXLSXGen();
		$xlsx->setDefaultFont('Arial')->setDefaultFontSize(11)->setTitle('Thống kê chỉ số chất lượng');
		$xlsx->addSheet($rows, 'Thống kê chỉ số');
		foreach ($merges as $range) $xlsx->mergeCells($range);
		$xlsx->setColWidth(1, 22)->setColWidth(2, 56)->setColWidth(3, 32)
			->setColWidth(4, 33)->setColWidth(5, 24);
		$this->themSheetTatCaPhieu($xlsx, $chiSo, $danhSachPhieu);
		return (string) $xlsx;
	}

	/** Sheet dữ liệu từng phiếu, cùng bố cục với mẫu xuất "Tất cả phiếu". */
	private function themSheetTatCaPhieu($xlsx, $chiSo, $danhSachPhieu)
	{
		$bieuMau = json_decode((string) $chiSo->get('bieumau'), true);
		$cauHoiGoc = isset($bieuMau['cau_hoi']) && is_array($bieuMau['cau_hoi'])
			? $bieuMau['cau_hoi']
			: (is_array($bieuMau) && array_values($bieuMau) === $bieuMau ? $bieuMau : array());
		$cauHoi = array();
		foreach ($cauHoiGoc as $index => $item) {
			if (!is_array($item)) continue;
			$loai = isset($item['loai']) ? (string) $item['loai'] : 'short_text';
			if (in_array($loai, array('category', 'subcategory'), true)) continue;
			$id = isset($item['id']) && $item['id'] !== '' ? (string) $item['id'] : 'q' . ($index + 1);
			$diemLuaChon = array();
			$coNhapKhac = false;
			foreach (isset($item['lua_chon']) && is_array($item['lua_chon']) ? $item['lua_chon'] : array() as $luaChon) {
				if (in_array($loai, array('radio', 'select'), true)
					&& is_array($luaChon) && !empty($luaChon['cho_nhap_them'])) {
					$coNhapKhac = true;
				}
				if ($loai === 'satisfaction' && is_array($luaChon)
					&& isset($luaChon['noi_dung'], $luaChon['diem']) && is_numeric($luaChon['diem'])) {
					$diemLuaChon[trim((string) $luaChon['noi_dung'])] = (float) $luaChon['diem'];
				}
			}
			$cauHoi[$id] = array(
				'noi_dung' => isset($item['noi_dung']) ? trim((string) $item['noi_dung']) : 'Câu hỏi ' . ($index + 1),
				'loai' => $loai,
				'diem_lua_chon' => $diemLuaChon,
				'co_nhap_khac' => $coNhapKhac
			);
		}

		// Phiếu cũ có thể chứa nội dung Khác dù biểu mẫu hiện tại đã thay đổi.
		foreach ($danhSachPhieu as $phieu) {
			foreach ($cauHoi as $id => $item) {
				if (in_array($item['loai'], array('radio', 'select'), true)
					&& isset($phieu['cau_tra_loi'][$id]) && is_array($phieu['cau_tra_loi'][$id])
					&& array_key_exists('noi_dung_khac', $phieu['cau_tra_loi'][$id])) {
					$cauHoi[$id]['co_nhap_khac'] = true;
				}
			}
		}

		$cell = function ($value, $style) {
			return $style . '<raw>' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '</raw>';
		};
		$headerStyle = '<b><center><bottom><wraptext><style height="72" border="thin">';
		$dataStyle = '<top><wraptext><style border="thin">';
		$tieuDe = array('STT', 'Mã phiếu', 'Khoa/Phòng', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập');
		foreach ($cauHoi as $item) {
			$tieuDe[] = $item['noi_dung'];
			if ($item['co_nhap_khac']) $tieuDe[] = $item['noi_dung'] . ' - Nội dung khác';
		}
		$rows = array(array_map(function ($value) use ($cell, $headerStyle) {
			return $cell($value, $headerStyle);
		}, $tieuDe));
		$hienThiTraLoi = function ($value) {
			if (!is_array($value)) return (string) $value;
			$items = array();
			array_walk_recursive($value, function ($part) use (&$items) {
				if (is_scalar($part) && trim((string) $part) !== '') $items[] = trim((string) $part);
			});
			return implode('; ', $items);
		};

		foreach ($danhSachPhieu as $index => $phieu) {
			$tenKhoa = trim((string) $phieu['ten_khoaphong']);
			if ($tenKhoa === '') $tenKhoa = 'Chưa xác định';
			$row = array(
				$cell($index + 1, $dataStyle),
				$cell($phieu['id'], $dataStyle),
				$cell($tenKhoa, $dataStyle),
				$cell($phieu['nam'], $dataStyle),
				$cell($phieu['ky'], $dataStyle),
				$cell($phieu['nguoi_nhap'], $dataStyle),
				$cell($phieu['created_at'], $dataStyle)
			);
			$traLoi = isset($phieu['cau_tra_loi']) && is_array($phieu['cau_tra_loi']) ? $phieu['cau_tra_loi'] : array();
			foreach ($cauHoi as $id => $item) {
				$value = isset($traLoi[$id]) ? $traLoi[$id] : '';
				if ($item['loai'] === 'satisfaction') {
					$luaChon = is_scalar($value) ? trim((string) $value) : '';
					$diem = $luaChon !== '' && array_key_exists($luaChon, $item['diem_lua_chon'])
						? $item['diem_lua_chon'][$luaChon] : null;
					$row[] = $diem === null ? $cell('', $dataStyle) : $dataStyle . (string) $diem;
				} elseif ($item['co_nhap_khac']) {
					$laKhac = is_array($value) && array_key_exists('noi_dung_khac', $value);
					$row[] = $cell($laKhac ? 'Khác' : $hienThiTraLoi($value), $dataStyle);
					$row[] = $cell($laKhac ? $value['noi_dung_khac'] : '', $dataStyle);
				} else {
					$row[] = $cell($hienThiTraLoi($value), $dataStyle);
				}
			}
			$rows[] = $row;
		}

		$soCot = count($tieuDe);
		$so = $soCot;
		$cotCuoi = '';
		while ($so > 0) {
			$so--;
			$cotCuoi = chr(65 + ($so % 26)) . $cotCuoi;
			$so = (int) floor($so / 26);
		}
		$xlsx->addSheet($rows, 'Tất cả phiếu')
			->freezePanes('A2')
			->autoFilter('A1:' . $cotCuoi . count($rows));
		$xlsx->setColWidth(1, 10)->setColWidth(2, 12)->setColWidth(3, 32)
			->setColWidth(4, 10)->setColWidth(5, 10)->setColWidth(6, 22)->setColWidth(7, 20);
		for ($cot = 8; $cot <= $soCot; $cot++) $xlsx->setColWidth($cot, 32);
	}
	function save()
	{
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		$nguoigui = !empty($_SESSION["sUserID"]) ? $_SESSION["sUserID"]
			: (isset($_SESSION["sUserID"]) ? $_SESSION["sUserID"] : "");
		// Khoa/phong chinh luon la khoa/phong mac dinh cua nguoi tao.
		// Danh sach cac phong duoc chon duoc luu rieng trong cot `phong` dang JSON.
		$idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($nguoigui);
		if ($idKhoaPhong <= 0) {
			$this->lastErrorMessage = "Nguoi dung chua duoc gan khoa/phong mac dinh";
			return $this->request->json_response(json_encode(array(
				"success" => false,
				"message" => $this->getErrorMessage()
			)));
		}
		$chiso->set("nguoi_gui", $nguoigui);
		$chiso->set("id_khoaphong", $idKhoaPhong);
		// print_r($chiso);
		$id = $this->ChiSoPeer->Save($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Them chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	public function update()
	{
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$id = $this->ChiSoPeer->Update($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Cap nhat chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	private function getChiSoFromRequest()
	{
		$data = $this->request->getParameter('data', true);
		$arrayData = json_decode($data, true);

		if (!is_array($arrayData) || empty($arrayData[0])) {
			$this->lastErrorMessage = "Chua co du lieu";
			return false;
		}

		$chiso = new ChiSoChatLuong;
		foreach ($arrayData[0] as $key => $value) {
			if (property_exists($chiso, $key)) {
				$chiso->set($key, $value);
			}
		}

		$bieumau = $chiso->get("bieumau");
		if (is_string($bieumau)) {
			$bieumau = json_decode($bieumau, true);
		}
		if (!is_array($bieumau)) {
			$bieumau = array("version" => 1, "cau_hoi" => array());
		}
		$chiso->set("bieumau", json_encode($bieumau, JSON_UNESCAPED_UNICODE));

		$maPhong = $chiso->get("phong");
		if (is_string($maPhong)) {
			$phongDaGiaiMa = json_decode($maPhong, true);
			$maPhong = is_array($phongDaGiaiMa) ? $phongDaGiaiMa : array();
		}

		if (!is_array($maPhong) || empty($maPhong)) {
			$maPhong = array($chiso->get("id_khoaphong"));
		}

		if ((int) $chiso->get("pham_vi") === 3) {
			$maPhong = array();
			foreach ($this->ChiSoPeer->getListKhoaPhong() as $khoaPhong) {
				$maPhong[] = (int) $khoaPhong['id'];
			}
		}

		$maPhong = array_values(array_unique(array_filter(array_map('intval', $maPhong))));
		$chiso->set("phong", json_encode($maPhong));
		$chiso->set("id_khoaphong", !empty($maPhong) ? $maPhong[0] : 0);

		if (trim($chiso->get("ten_chi_so")) === "") {
			$this->lastErrorMessage = "Ten chi so khong duoc de trong";
			return false;
		}

		return $chiso;
	}

	private function getErrorMessage()
	{
		$message = new Message();
		$message->set("flag", false);
		$message->set("errorMessage", $this->lastErrorMessage);
		return $message;
	}

	public function duyet(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$nguoiDuyet = !empty($_SESSION["sUserID"]) ? $_SESSION["sUserID"]
			: (isset($_SESSION["sUserID"]) ? $_SESSION["sUserID"] : "");
		$chiso->set("nguoi_duyet", $nguoiDuyet);

		$id = $this->ChiSoPeer->Duyet($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("succesMessage", "Duyệt chỉ tiêu thành công");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	public function gui(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$id = $this->ChiSoPeer->Gui($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Gui chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}


	public function xoa(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$id = $this->ChiSoPeer->Xoa($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Xoa chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	public function tuchoi(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		if (trim((string) $chiso->get("ly_do_tu_choi")) === "") {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Vui long nhap ly do tu choi");
			return $this->request->json_response(json_encode(array(
				"success" => false,
				"message" => $message
			)));
		}

		$id = $this->ChiSoPeer->TuChoi($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Tu choi chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}
	public function taolai()
	{
		$maChiSoCu = (int) $this->request->getParameter('ma_chi_so');
		$userId = isset($_SESSION['sUserID'])
			? (int) $_SESSION['sUserID']
			: 0;

		$maChiSoMoi = $this->ChiSoPeer->taoLaiTuDonBiTuChoi(
			$maChiSoCu,
			$userId
		);

		if ($maChiSoMoi <= 0) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'message' => 'Không thể tạo lại chỉ số này'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'id' => $maChiSoMoi,
			'message' => 'Đã tạo một bản nháp mới'
		)));
	}
}
?>
