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
		$xml = function ($value) {
			$value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string) $value);
			return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
		};
		$oChu = function ($toaDo, $giaTri, $kieu = 4, $laSo = false) use ($xml) {
			if ($laSo && is_numeric($giaTri)) {
				return '<c r="' . $toaDo . '" s="' . $kieu . '"><v>' . (0 + $giaTri) . '</v></c>';
			}
			return '<c r="' . $toaDo . '" s="' . $kieu . '" t="inlineStr"><is><t xml:space="preserve">'
				. $xml($giaTri) . '</t></is></c>';
		};
		$hienThiTraLoi = function ($value) {
			if (!is_array($value)) return (string) $value;
			if (isset($value['lua_chon']) && isset($value['noi_dung_khac'])) {
				return (string) $value['lua_chon'] . ': ' . (string) $value['noi_dung_khac'];
			}
			$items = array();
			array_walk_recursive($value, function ($item) use (&$items) {
				if (is_scalar($item) && trim((string) $item) !== '') $items[] = trim((string) $item);
			});
			return implode('; ', $items);
		};
		$catTenSheet = function ($value, $length) {
			return function_exists('mb_substr') ? mb_substr($value, 0, $length, 'UTF-8') : substr($value, 0, $length);
		};
		$giaTriExcel = function ($value) {
			return "\0" . (string) $value;
		};

		$soCot = 6 + count($cauHoi);
		$cotCuoi = $tenCot($soCot);
		$tenChiSo = trim((string) $chiSo->get('ten_chi_so'));
		$xlsx = new \Shuchkin\SimpleXLSXGen();
		$xlsx->setDefaultFont('Arial')->setDefaultFontSize(10)->setTitle('Thống kê chỉ số theo khoa');
		$sheetXmls = array();
		$tenSheetDaDung = array();
		$workbookSheets = '';
		$workbookRels = '';
		$contentTypeSheets = '';
		$sheetIndex = 0;

		foreach ($phieuTheoKhoa as $idKhoaPhong => $cacPhieu) {
			$sheetIndex++;
			$tenKhoaPhong = isset($tenKhoaTheoId[$idKhoaPhong]) && $tenKhoaTheoId[$idKhoaPhong] !== ''
				? $tenKhoaTheoId[$idKhoaPhong]
				: 'Chưa xác định';
			$tenSheetGoc = preg_replace('/[\\\\\/:*?\[\]]+/u', ' ', $tenKhoaPhong);
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

			$duLieuSheet = array(
				array('<b><center>THỐNG KÊ PHIẾU THEO CHỈ SỐ CỦA KHOA/PHÒNG'),
				array('<b>Mã chỉ số', $maChiSo),
				array('<b>Tên chỉ số', $giaTriExcel($tenChiSo)),
				array('<b>Khoa/Phòng', $giaTriExcel($tenKhoaPhong)),
				array('<b>Tổng số phiếu', count($cacPhieu)),
				array(),
				array('<b><center>CHI TIẾT CÂU TRẢ LỜI CỦA TỪNG PHIẾU')
			);
			$tieuDeExcel = array('STT', 'Mã phiếu', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập');
			foreach ($cauHoi as $item) $tieuDeExcel[] = $item['noi_dung'];
			foreach ($tieuDeExcel as &$tieuDeCot) {
				$tieuDeCot = '<b><center><wraptext><style color="#FFFFFF" bgcolor="#1F4E78" border="thin">'
					. htmlspecialchars((string) $tieuDeCot, ENT_QUOTES, 'UTF-8');
			}
			unset($tieuDeCot);
			$duLieuSheet[] = $tieuDeExcel;
			foreach ($cacPhieu as $index => $phieu) {
				$dongExcel = array($index + 1, $phieu['id'], $phieu['nam'], $phieu['ky'], $giaTriExcel($phieu['nguoi_nhap']), $giaTriExcel($phieu['created_at']));
				foreach ($cauHoi as $id => $item) {
					$dongExcel[] = $giaTriExcel($hienThiTraLoi(isset($phieu['cau_tra_loi'][$id]) ? $phieu['cau_tra_loi'][$id] : ''));
				}
				$duLieuSheet[] = $dongExcel;
			}
			if (empty($cacPhieu)) $duLieuSheet[] = array($giaTriExcel('Chưa có phiếu nào của Khoa/Phòng cho chỉ số này.'));
			$xlsx->addSheet($duLieuSheet, $tenSheet);
			$xlsx->mergeCells('A1:' . $cotCuoi . '1')
				->mergeCells('B2:' . $cotCuoi . '2')
				->mergeCells('B3:' . $cotCuoi . '3')
				->mergeCells('B4:' . $cotCuoi . '4')
				->mergeCells('B5:' . $cotCuoi . '5')
				->mergeCells('A7:' . $cotCuoi . '7')
				->freezePanes('A9')
				->autoFilter('A8:' . $cotCuoi . max(8, count($duLieuSheet)));
			$xlsx->setColWidth(1, 7)->setColWidth(2, 12)->setColWidth(3, 10)->setColWidth(4, 10)->setColWidth(5, 22)->setColWidth(6, 20);
			for ($cot = 7; $cot <= $soCot; $cot++) $xlsx->setColWidth($cot, 32);

			$cacDong = array();
			$cacDong[] = '<row r="1" ht="24" customHeight="1">' . $oChu('A1', 'THỐNG KÊ PHIẾU THEO CHỈ SỐ CỦA KHOA/PHÒNG', 1) . '</row>';
			$cacDong[] = '<row r="2">' . $oChu('A2', 'Mã chỉ số', 3) . $oChu('B2', $maChiSo, 4) . '</row>';
			$cacDong[] = '<row r="3">' . $oChu('A3', 'Tên chỉ số', 3) . $oChu('B3', $tenChiSo, 4) . '</row>';
			$cacDong[] = '<row r="4">' . $oChu('A4', 'Khoa/Phòng', 3) . $oChu('B4', $tenKhoaPhong, 4) . '</row>';
			$cacDong[] = '<row r="5">' . $oChu('A5', 'Tổng số phiếu', 3) . $oChu('B5', count($cacPhieu), 4, true) . '</row>';
			$cacDong[] = '<row r="7" ht="22" customHeight="1">' . $oChu('A7', 'CHI TIẾT CÂU TRẢ LỜI CỦA TỪNG PHIẾU', 2) . '</row>';

			$tieuDe = array('STT', 'Mã phiếu', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập');
			foreach ($cauHoi as $item) $tieuDe[] = $item['noi_dung'];
			$dongTieuDe = '';
			foreach ($tieuDe as $index => $noiDung) $dongTieuDe .= $oChu($tenCot($index + 1) . '8', $noiDung, 2);
			$cacDong[] = '<row r="8" ht="36" customHeight="1">' . $dongTieuDe . '</row>';

			$soDong = 8;
			foreach ($cacPhieu as $index => $phieu) {
				$soDong++;
				$giaTriDong = array($index + 1, $phieu['id'], $phieu['nam'], $phieu['ky'], $phieu['nguoi_nhap'], $phieu['created_at']);
				foreach ($cauHoi as $id => $item) {
					$giaTriDong[] = $hienThiTraLoi(isset($phieu['cau_tra_loi'][$id]) ? $phieu['cau_tra_loi'][$id] : '');
				}
				$noiDungDong = '';
				foreach ($giaTriDong as $cot => $giaTri) {
					$noiDungDong .= $oChu($tenCot($cot + 1) . $soDong, $giaTri, 4, in_array($cot, array(0, 1, 2, 3), true));
				}
				$cacDong[] = '<row r="' . $soDong . '">' . $noiDungDong . '</row>';
			}
			if (empty($cacPhieu)) {
				$soDong++;
				$cacDong[] = '<row r="' . $soDong . '">' . $oChu('A' . $soDong, 'Chưa có phiếu nào của Khoa/Phòng cho chỉ số này.', 4) . '</row>';
			}

			$merge = array('A1:' . $cotCuoi . '1', 'B2:' . $cotCuoi . '2', 'B3:' . $cotCuoi . '3', 'B4:' . $cotCuoi . '4', 'B5:' . $cotCuoi . '5', 'A7:' . $cotCuoi . '7');
			if (empty($cacPhieu)) $merge[] = 'A' . $soDong . ':' . $cotCuoi . $soDong;
			$mergeXml = '';
			foreach ($merge as $vung) $mergeXml .= '<mergeCell ref="' . $vung . '"/>';
			$cotXml = '<col min="1" max="1" width="7" customWidth="1"/><col min="2" max="2" width="12" customWidth="1"/>'
				. '<col min="3" max="4" width="10" customWidth="1"/><col min="5" max="5" width="22" customWidth="1"/>'
				. '<col min="6" max="6" width="20" customWidth="1"/>';
			if ($soCot > 6) $cotXml .= '<col min="7" max="' . $soCot . '" width="32" customWidth="1"/>';

			$sheetXmls[$sheetIndex] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
				. '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
				. '<sheetViews><sheetView workbookViewId="0"><pane ySplit="8" topLeftCell="A9" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
				. '<sheetFormatPr defaultRowHeight="18"/><cols>' . $cotXml . '</cols><sheetData>' . implode('', $cacDong) . '</sheetData>'
				. '<mergeCells count="' . count($merge) . '">' . $mergeXml . '</mergeCells>'
				. '<autoFilter ref="A8:' . $cotCuoi . max(8, $soDong) . '"/><pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';
			$workbookSheets .= '<sheet name="' . $xml($tenSheet) . '" sheetId="' . $sheetIndex . '" r:id="rId' . $sheetIndex . '"/>';
			$workbookRels .= '<Relationship Id="rId' . $sheetIndex . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $sheetIndex . '.xml"/>';
			$contentTypeSheets .= '<Override PartName="/xl/worksheets/sheet' . $sheetIndex . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
		}

		$stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
			. '<fonts count="3"><font><sz val="10"/><name val="Arial"/></font><font><b/><sz val="14"/><name val="Arial"/><color rgb="FF1F2937"/></font><font><b/><sz val="10"/><name val="Arial"/><color rgb="FFFFFFFF"/></font></fonts>'
			. '<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1F4E78"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFD9EAF7"/><bgColor indexed="64"/></patternFill></fill></fills>'
			. '<borders count="2"><border/><border><left style="thin"><color rgb="FFB8C2CC"/></left><right style="thin"><color rgb="FFB8C2CC"/></right><top style="thin"><color rgb="FFB8C2CC"/></top><bottom style="thin"><color rgb="FFB8C2CC"/></bottom><diagonal/></border></borders>'
			. '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="5"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/><xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf></cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
		$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' . $contentTypeSheets . '</Types>';
		$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
		$workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>' . $workbookSheets . '</sheets></workbook>';
		$workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $workbookRels . '<Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';

		$tenFile = preg_replace('/[\\\\\/:*?"<>|\x00-\x1F]+/u', '_', $tenChiSo);
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
