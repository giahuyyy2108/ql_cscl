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
			$cauHoi[$id] = array(
				'id' => $id,
				'noi_dung' => trim(isset($item['noi_dung']) ? (string) $item['noi_dung'] : 'Câu hỏi ' . ($index + 1))
			);
		}

		$danhSachPhieu = $this->CtChiSoPeer->getDanhSachPhieu($maChiSo);
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
		$tieuDeBang = '<b><center><bottom><wraptext><style height="72" color="#FFFFFF" bgcolor="#1F4E78" border="thin">';
		$duLieuBang = '<top><wraptext><style border="thin">';
		$oTrongThongTin = $giaTriExcel('', $giaTriThongTin);

		$soCot = 6 + count($cauHoi);
		$cotCuoi = $tenCot($soCot);
		$tenChiSo = trim((string) $chiSo->get('ten_chi_so'));
		$xlsx = new \Shuchkin\SimpleXLSXGen();
		$xlsx->setDefaultFont('Arial')->setDefaultFontSize(10)->setTitle('Thống kê chỉ số theo khoa');
		$tenSheetDaDung = array();

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

			$tieuDeExcel = array('STT', 'Mã phiếu', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập');
			foreach ($cauHoi as $item) $tieuDeExcel[] = $item['noi_dung'];
			foreach ($tieuDeExcel as &$tieuDeCot) {
				$tieuDeCot = $giaTriExcel($tieuDeCot, $tieuDeBang);
			}
			unset($tieuDeCot);
			$duLieuSheet[] = $tieuDeExcel;

			foreach ($cacPhieu as $index => $phieu) {
				$dongExcel = array(
					$giaTriExcel($index + 1, $duLieuBang),
					$giaTriExcel($phieu['id'], $duLieuBang),
					$giaTriExcel($phieu['nam'], $duLieuBang),
					$giaTriExcel($phieu['ky'], $duLieuBang),
					$giaTriExcel($phieu['nguoi_nhap'], $duLieuBang),
					$giaTriExcel($phieu['created_at'], $duLieuBang)
				);
				foreach ($cauHoi as $id => $item) {
					$giaTri = isset($phieu['cau_tra_loi'][$id]) ? $phieu['cau_tra_loi'][$id] : '';
					$dongExcel[] = $giaTriExcel($hienThiTraLoi($giaTri), $duLieuBang);
				}
				$duLieuSheet[] = $dongExcel;
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

		$tenFileDayDu = 'Thong ke theo khoa - ' . $tenFile . '.xlsx';
		try {
			$noiDungExcel = (string) $xlsx;
			if ($noiDungExcel === '') throw new Exception('Không tạo được nội dung XLSX');
			if (ob_get_length()) ob_end_clean();
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="thong-ke-chi-so-' . $maChiSo . '-theo-khoa.xlsx"; filename*=UTF-8\'\'' . rawurlencode($tenFileDayDu));
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
