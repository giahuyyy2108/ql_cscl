<?PHP
require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");
require_once ("web_src/bean/TinhTrangPeer.php");
require_once ("web_src/bean/CtChiSoPeer.php");
require_once ("web_src/common/SimpleXLSXGen/SimpleXLSXGen.php");

class chisokhoaAction
{
	var $request;
	var $ChiSoPeer;
	var $CtChiSoPeer;
	public static $listRole = "chisokhoa";

	public function __construct()
	{
		$this->request = new Request;
		$this->ChiSoPeer = new ChiSoChatLuongPeer();
		$this->CtChiSoPeer = new CtChiSoPeer();
		$this->request->setTitle("Chi so theo khoa/phong");
	}

	function index()
	{
		$KCTTpeer = new DanhMucKCTTPeer();
		$PVpeer = new PhamViPeer();
		$ChuKyPeer = new ChuKyPeer();
		$donvitinhPeer = new DonViTinhPeer();
		$tinhtrangPeer = new TinhTrangPeer();

		$this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/chisokhoa/chisokhoa.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
		$this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');

		$this->request->setAttribute("listKCCT", $KCTTpeer->Get_danhmucKCTT());
		$this->request->setAttribute("listPV", $PVpeer->getPhamVi());
		$this->request->setAttribute("listCKy", $ChuKyPeer->getChuKy());
		$this->request->setAttribute("listDvt", $donvitinhPeer->getDonViTinh());
		$this->request->setAttribute("listTT", $tinhtrangPeer->getTinhTrang());
		$this->request->setAttribute("listKhoi", $this->ChiSoPeer->getListKhoi());
		$this->request->setAttribute("listKhoaPhong", $this->ChiSoPeer->getListKhoaPhongByKhoi());

		$this->request->setModel("www/chisokhoa/index.php");
		return true;
	}

	function getData()
	{
		$idKhoaPhong = $this->request->getParameter("id_khoaphong") != "" ? $this->request->getParameter("id_khoaphong") : 0;

		$data['data'] = $this->ChiSoPeer->getListByKhoa($idKhoaPhong);
		return $this->request->json_response(json_encode($data));
	}

	function getBieuDoChuKy()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0) {
			return $this->request->json_response(json_encode(array('success' => false, 'message' => 'Mã chỉ số không hợp lệ')));
		}
		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getTrungBinhTheoKy($maChiSo),
			'cau_hoi' => $this->CtChiSoPeer->getTrungBinhTheoCauHoi($maChiSo)
		)));
	}

	function getDanhSachPhieu()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0) {
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

	private function getChiSoDuocXem($maChiSo)
	{
		foreach ($this->ChiSoPeer->getListByKhoa(0) as $chiSo) {
			if ((int) $chiSo->get('ma_chi_so') === (int) $maChiSo) {
				return $chiSo;
			}
		}

		return false;
	}

	public function xuatThongKe()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		$chiSo = $this->getChiSoDuocXem($maChiSo);

		if ($maChiSo <= 0 || $chiSo === false) {
			header('HTTP/1.1 403 Forbidden');
			echo 'Bạn không có quyền xuất thống kê chỉ số này.';
			exit;
		}

		$bieuMau = json_decode((string) $chiSo->get('bieumau'), true);
		$cauHoiGoc = isset($bieuMau['cau_hoi']) && is_array($bieuMau['cau_hoi'])
			? $bieuMau['cau_hoi']
			: (is_array($bieuMau) && array_values($bieuMau) === $bieuMau ? $bieuMau : array());
		$cauHoi = array();
		foreach ($cauHoiGoc as $index => $item) {
			$loai = isset($item['loai']) ? (string) $item['loai'] : 'short_text';
			if (in_array($loai, array('category', 'subcategory'), true)) continue;
			$id = isset($item['id']) && $item['id'] !== '' ? (string) $item['id'] : 'q' . ($index + 1);
			$diemLuaChon = array();
			$coNhapKhac = false;
			if (isset($item['lua_chon']) && is_array($item['lua_chon'])) {
				foreach ($item['lua_chon'] as $luaChon) {
					if (in_array($loai, array('radio', 'select'), true)
						&& is_array($luaChon) && !empty($luaChon['cho_nhap_them'])) {
						$coNhapKhac = true;
					}
					if ($loai === 'satisfaction' && is_array($luaChon)
						&& isset($luaChon['noi_dung'], $luaChon['diem']) && is_numeric($luaChon['diem'])) {
						$diemLuaChon[trim((string) $luaChon['noi_dung'])] = (float) $luaChon['diem'];
					}
				}
			}
			$cauHoi[$id] = array(
				'id' => $id,
				'noi_dung' => trim(isset($item['noi_dung']) ? (string) $item['noi_dung'] : 'Câu hỏi ' . ($index + 1)),
				'loai' => $loai,
				'diem_lua_chon' => $diemLuaChon,
				'co_nhap_khac' => $coNhapKhac
			);
		}

		$danhSachPhieu = $this->CtChiSoPeer->getDanhSachPhieu($maChiSo);
		// Giữ cột nội dung khác cho cả những phiếu cũ khi biểu mẫu đã được chỉnh sửa.
		foreach ($danhSachPhieu as $phieu) {
			foreach ($cauHoi as $id => $item) {
				if (in_array($item['loai'], array('radio', 'select'), true)
					&& isset($phieu['cau_tra_loi'][$id]) && is_array($phieu['cau_tra_loi'][$id])
					&& array_key_exists('noi_dung_khac', $phieu['cau_tra_loi'][$id])) {
					$cauHoi[$id]['co_nhap_khac'] = true;
				}
			}
		}
		$tenKhoaTheoId = array();
		foreach ($this->ChiSoPeer->getListKhoaPhongByKhoi() as $khoaPhong) {
			$tenKhoaTheoId[(int) $khoaPhong['id']] = trim((string) $khoaPhong['ten']);
		}

		$khoaDuocApDung = $chiSo->get('phong');
		$khoaDuocApDung = is_array($khoaDuocApDung) ? $khoaDuocApDung : array();
		$phieuTheoKhoa = array();
		foreach ($khoaDuocApDung as $idKhoaPhong) {
			$idKhoaPhong = (int) $idKhoaPhong;
			if ($idKhoaPhong > 0 && isset($tenKhoaTheoId[$idKhoaPhong])) $phieuTheoKhoa[$idKhoaPhong] = array();
		}
		foreach ($danhSachPhieu as $phieu) {
			$idKhoaPhong = (int) $phieu['id_khoaphong'];
			if (!isset($phieuTheoKhoa[$idKhoaPhong])) $phieuTheoKhoa[$idKhoaPhong] = array();
			$phieuTheoKhoa[$idKhoaPhong][] = $phieu;
			if (!isset($tenKhoaTheoId[$idKhoaPhong]) || $tenKhoaTheoId[$idKhoaPhong] === '') {
				$tenKhoaTheoId[$idKhoaPhong] = trim((string) $phieu['ten_khoaphong']);
			}
		}
		if (empty($phieuTheoKhoa)) $phieuTheoKhoa[0] = array();
		uksort($phieuTheoKhoa, function ($a, $b) use ($tenKhoaTheoId) {
			$tenA = isset($tenKhoaTheoId[$a]) ? $tenKhoaTheoId[$a] : '';
			$tenB = isset($tenKhoaTheoId[$b]) ? $tenKhoaTheoId[$b] : '';
			return strcasecmp($tenA, $tenB);
		});

		$tenCot = function ($soCot) {
			$ketQua = '';
			while ($soCot > 0) {
				$soCot--;
				$ketQua = chr(65 + ($soCot % 26)) . $ketQua;
				$soCot = (int) floor($soCot / 26);
			}
			return $ketQua;
		};
		$hienThiTraLoi = function ($value) {
			if (!is_array($value)) return (string) $value;
			if (isset($value['lua_chon']) && isset($value['noi_dung_khac'])) {
				return (string) $value['lua_chon'] . ': ' . (string) $value['noi_dung_khac'];
			}
			$items = array();
			array_walk_recursive($value, function ($item) use (&$items) {
				if (is_scalar($item) && trim((string) $item) !== '') {
					$items[] = trim((string) $item);
				}
			});
			return implode('; ', $items);
		};
		$catTenSheet = function ($value, $length) {
			return function_exists('mb_substr') ? mb_substr($value, 0, $length, 'UTF-8') : substr($value, 0, $length);
		};
		$giaTriExcel = function ($value, $dinhDang = '') {
			return $dinhDang . '<raw>'
				. htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')
				. '</raw>';
		};

		$nhanThongTin = '<b><middle><style border="thin">';
		$giaTriThongTin = '<right><middle><style border="thin">';
		$tieuDeBang = '<b><center><bottom><wraptext><style height="72" border="thin">';
		$duLieuBang = '<top><wraptext><style border="thin">';
		$oTrongThongTin = $giaTriExcel('', $giaTriThongTin);
		$tieuDeCauHoi = function ($tieuDe) use ($cauHoi, $giaTriExcel, $tieuDeBang) {
			foreach ($cauHoi as $item) {
				$tieuDe[] = $item['noi_dung'];
				if ($item['co_nhap_khac']) $tieuDe[] = $item['noi_dung'] . ' - Nội dung khác';
			}
			foreach ($tieuDe as &$noiDung) $noiDung = $giaTriExcel($noiDung, $tieuDeBang);
			unset($noiDung);
			return $tieuDe;
		};
		$themCauTraLoi = function ($dongExcel, $phieu) use ($cauHoi, $giaTriExcel, $duLieuBang, $hienThiTraLoi) {
			foreach ($cauHoi as $id => $item) {
				$giaTri = isset($phieu['cau_tra_loi'][$id]) ? $phieu['cau_tra_loi'][$id] : '';
				if ($item['loai'] === 'satisfaction') {
					$luaChon = is_scalar($giaTri) ? trim((string) $giaTri) : '';
					$diem = $luaChon !== '' && array_key_exists($luaChon, $item['diem_lua_chon'])
						? $item['diem_lua_chon'][$luaChon] : null;
					$dongExcel[] = $diem === null ? $giaTriExcel('', $duLieuBang) : $duLieuBang . (string) $diem;
				} elseif ($item['co_nhap_khac']) {
					$laLuaChonKhac = is_array($giaTri) && array_key_exists('noi_dung_khac', $giaTri);
					$dongExcel[] = $giaTriExcel($laLuaChonKhac ? 'Khác' : $hienThiTraLoi($giaTri), $duLieuBang);
					$dongExcel[] = $giaTriExcel($laLuaChonKhac ? $giaTri['noi_dung_khac'] : '', $duLieuBang);
				} else {
					$dongExcel[] = $giaTriExcel($hienThiTraLoi($giaTri), $duLieuBang);
				}
			}
			return $dongExcel;
		};

		$soCotCauHoi = 0;
		foreach ($cauHoi as $item) $soCotCauHoi += $item['co_nhap_khac'] ? 2 : 1;
		$soCot = 6 + $soCotCauHoi;
		$cotCuoi = $tenCot($soCot);
		$tenChiSo = trim((string) $chiSo->get('ten_chi_so'));
		$xlsx = new \Shuchkin\SimpleXLSXGen();
		$xlsx->setDefaultFont('Arial')->setDefaultFontSize(10)->setTitle('Thống kê chỉ số theo khoa');

		// Sheet đầu tiên tổng hợp tất cả phiếu, chỉ có bảng dữ liệu (không có khối thông tin chỉ số).
		$tieuDeTongHop = $tieuDeCauHoi(array('STT', 'Mã phiếu', 'Khoa/Phòng', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập'));
		$duLieuTongHop = array($tieuDeTongHop);
		foreach ($danhSachPhieu as $index => $phieu) {
			$idKhoaPhong = (int) $phieu['id_khoaphong'];
			$tenKhoaPhong = isset($tenKhoaTheoId[$idKhoaPhong]) && $tenKhoaTheoId[$idKhoaPhong] !== ''
				? $tenKhoaTheoId[$idKhoaPhong]
				: (trim((string) $phieu['ten_khoaphong']) !== '' ? $phieu['ten_khoaphong'] : 'Chưa xác định');
			$dongExcel = array(
				$giaTriExcel($index + 1, $duLieuBang),
				$giaTriExcel($phieu['id'], $duLieuBang),
				$giaTriExcel($tenKhoaPhong, $duLieuBang),
				$giaTriExcel($phieu['nam'], $duLieuBang),
				$giaTriExcel($phieu['ky'], $duLieuBang),
				$giaTriExcel($phieu['nguoi_nhap'], $duLieuBang),
				$giaTriExcel($phieu['created_at'], $duLieuBang)
			);
			$duLieuTongHop[] = $themCauTraLoi($dongExcel, $phieu);
		}
		$soCotTongHop = 7 + $soCotCauHoi;
		$tenSheetTongHop = 'Tất cả phiếu';
		$xlsx->addSheet($duLieuTongHop, $tenSheetTongHop)
			->freezePanes('A2')
			->autoFilter('A1:' . $tenCot($soCotTongHop) . count($duLieuTongHop));
		$xlsx->setColWidth(1, 10)
			->setColWidth(2, 12)
			->setColWidth(3, 32)
			->setColWidth(4, 10)
			->setColWidth(5, 10)
			->setColWidth(6, 22)
			->setColWidth(7, 20);
		for ($cot = 8; $cot <= $soCotTongHop; $cot++) {
			$xlsx->setColWidth($cot, 32);
		}
		$tenSheetDaDung = array(strtolower($tenSheetTongHop) => true);

		foreach ($phieuTheoKhoa as $idKhoaPhong => $cacPhieu) {
			$tenKhoaPhong = isset($tenKhoaTheoId[$idKhoaPhong]) && $tenKhoaTheoId[$idKhoaPhong] !== ''
				? $tenKhoaTheoId[$idKhoaPhong]
				: 'Chưa xác định';
			$tenSheetGoc = str_replace(array('\\', '/', ':', '*', '?', '[', ']'), ' ', $tenKhoaPhong);
			$tenSheetGoc = trim(preg_replace('/\s+/u', ' ', $tenSheetGoc), " '\t\n\r\0\x0B");
			if ($tenSheetGoc === '') $tenSheetGoc = 'Khoa phong ' . $idKhoaPhong;
			$tenSheet = $catTenSheet($tenSheetGoc, 31);
			$thuTuTrung = 1;
			while (isset($tenSheetDaDung[strtolower($tenSheet)])) {
				$thuTuTrung++;
				$duoi = ' (' . $thuTuTrung . ')';
				$tenSheet = $catTenSheet($tenSheetGoc, 31 - strlen($duoi)) . $duoi;
			}
			$tenSheetDaDung[strtolower($tenSheet)] = true;

			$soPhieu = count($cacPhieu);
			$duLieuSheet = array(
				array($giaTriExcel('Mã chỉ số', $nhanThongTin), $giaTriExcel($maChiSo, $giaTriThongTin), $oTrongThongTin, $oTrongThongTin, $oTrongThongTin),
				array($giaTriExcel('Tên chỉ số', $nhanThongTin), $giaTriExcel($tenChiSo, $giaTriThongTin), $oTrongThongTin, $oTrongThongTin, $oTrongThongTin),
				array($giaTriExcel('Khoa/Phòng', $nhanThongTin), $giaTriExcel($tenKhoaPhong, $giaTriThongTin), $oTrongThongTin, $oTrongThongTin, $oTrongThongTin),
				array($giaTriExcel('Tổng số phiếu', $nhanThongTin), $giaTriExcel($soPhieu > 0 ? $soPhieu : '', $giaTriThongTin), $oTrongThongTin, $oTrongThongTin, $oTrongThongTin),
				array(),
				array()
			);

			$duLieuSheet[] = $tieuDeCauHoi(array('STT', 'Mã phiếu', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập'));

			foreach ($cacPhieu as $index => $phieu) {
				$dongExcel = array(
					$giaTriExcel($index + 1, $duLieuBang),
					$giaTriExcel($phieu['id'], $duLieuBang),
					$giaTriExcel($phieu['nam'], $duLieuBang),
					$giaTriExcel($phieu['ky'], $duLieuBang),
					$giaTriExcel($phieu['nguoi_nhap'], $duLieuBang),
					$giaTriExcel($phieu['created_at'], $duLieuBang)
				);
				$duLieuSheet[] = $themCauTraLoi($dongExcel, $phieu);
			}

			$xlsx->addSheet($duLieuSheet, $tenSheet)
				->mergeCells('B1:E1')
				->mergeCells('B2:E2')
				->mergeCells('B3:E3')
				->mergeCells('B4:E4')
				->freezePanes('A8')
				->autoFilter('A7:' . $cotCuoi . max(7, count($duLieuSheet)));
			$xlsx->setColWidth(1, 14)
				->setColWidth(2, 12)
				->setColWidth(3, 10)
				->setColWidth(4, 10)
				->setColWidth(5, 22)
				->setColWidth(6, 20);
			for ($cot = 7; $cot <= $soCot; $cot++) {
				$xlsx->setColWidth($cot, 32);
			}
		}

		$tenFile = str_replace(
			array('\\', '/', ':', '*', '?', '"', '<', '>', '|'),
			'_',
			preg_replace('/[\x00-\x1F]+/u', '', $tenChiSo)
		);
		$tenFile = trim($tenFile, " ._\t\n\r\0\x0B");
		if ($tenFile === '') $tenFile = 'chi-so-' . $maChiSo;

		$tenFileDayDu = 'Thong ke phieu chi so ' . $maChiSo . ' - ' . $tenFile . '.xlsx';
		try {
			$noiDungExcel = (string) $xlsx;
			if ($noiDungExcel === '') throw new Exception('Không tạo được nội dung XLSX');
			if (ob_get_length()) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Thong ke phieu chi so ' . $maChiSo . '.xlsx"; filename*=UTF-8\'\'' . rawurlencode($tenFileDayDu));
			header('Content-Length: ' . strlen($noiDungExcel));
			header('Cache-Control: max-age=0');
			echo $noiDungExcel;
		} catch (Throwable $exception) {
			if (!headers_sent()) header('HTTP/1.1 500 Internal Server Error');
			echo 'Không thể tạo file Excel.';
		}
		exit;
	}

	function xoaPhieu()
	{
		$id = (int) $this->request->getParameter('id');
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');

		if ($id <= 0 || $maChiSo <= 0) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'message' => 'Thông tin phiếu không hợp lệ'
			)));
		}

		if (!$this->CtChiSoPeer->xoaPhieu($id, $maChiSo)) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'message' => 'Phiếu không tồn tại hoặc đã được xóa'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'message' => 'Xóa phiếu thành công'
		)));
	}
}
?>
