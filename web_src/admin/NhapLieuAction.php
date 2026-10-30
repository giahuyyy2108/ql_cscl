<?php

require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");
require_once ("web_src/bean/TinhTrangPeer.php");
require_once ("web_src/bean/CtChiSoPeer.php");
class NhapLieuAction
{
	var $request;
	var $ChiSoPeer;
	var $CtChiSoPeer;
	var $lastErrorMessage;
	public static $listRole = "chisokhoa";

	public function __construct()
	{
		$this->request = new Request;
		$this->ChiSoPeer = new ChiSoChatLuongPeer();
		$this->CtChiSoPeer = new CtChiSoPeer();
		$this->request->setTitle("Nhập liệu");
	}

    public function index()
    {
        $KCTTpeer = new DanhMucKCTTPeer();
        $PVpeer = new PhamViPeer();
        $ChuKyPeer = new ChuKyPeer();

        $this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/nhaplieu/nhaplieu.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setAttribute("listKCCT", $KCTTpeer->Get_danhmucKCTT());
        $this->request->setAttribute("listPV", $PVpeer->getPhamVi());
        $this->request->setAttribute("listCKy", $ChuKyPeer->getChuKy());
        $this->request->setAttribute("listKhoi", $this->ChiSoPeer->getListKhoi());
        $this->request->setAttribute("listKhoaPhong", $this->ChiSoPeer->getListKhoaPhongByKhoi());
		$this->request->setModel("www/nhaplieu/index.php");
        return true;
    }

    public function getData()
    {
        $userId = isset($_SESSION["sUserID"]) ? (int) $_SESSION["sUserID"] : 0;
        $idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($userId);
        $items = $this->ChiSoPeer->getListDaDuyetByKhoa($idKhoaPhong);
        $daNhap = 0;

        foreach ($items as $item) {
            $cauHinh = $this->CtChiSoPeer->getCauHinhNhap(
                $item->get('ma_chi_so'),
                $idKhoaPhong
            );
            $kyHienTai = $this->tinhKyHienTai($cauHinh);
            $daNhapKy = $kyHienTai && $this->CtChiSoPeer->daNhapKy(
                $item->get('ma_chi_so'),
                $idKhoaPhong,
                $kyHienTai['nam'],
                $kyHienTai['ky']
            );
            $item->set('da_nhap_ky_hien_tai', $daNhapKy);
            if ($daNhapKy) {
                $daNhap++;
            }
        }

        return $this->request->json_response(json_encode(array(
            "data" => $items,
            "thong_ke" => array(
                "tong" => count($items),
                "da_nhap" => $daNhap,
                "chua_nhap" => count($items) - $daNhap
            )
        )));
    }

    public function getTrangThaiNhap()
    {
        $maChiSo = (int) $this->request->getParameter('ma_chi_so');
        $userId = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
        $idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($userId);
        $cauHinh = $this->CtChiSoPeer->getCauHinhNhap($maChiSo, $idKhoaPhong);
        $kyHienTai = $this->tinhKyHienTai($cauHinh);

        if (!$kyHienTai || $userId <= 0) {
            return $this->request->json_response(json_encode(array(
                'success' => false,
                'message' => 'Chỉ tiêu, năm hoặc Khoa/Phòng không hợp lệ'
            )));
        }

        return $this->request->json_response(json_encode(array(
            'success' => true,
            'so_chu_ky' => $kyHienTai['so_chu_ky'],
            'nam' => $kyHienTai['nam'],
            'ky' => $kyHienTai['ky'],
            'thang_bat_dau' => $kyHienTai['thang_bat_dau'],
            'nam_bat_dau' => $kyHienTai['nam_bat_dau'],
            'bieumau' => $this->docBieuMau($cauHinh['bieumau']),
            'da_nhap' => $this->CtChiSoPeer->getTheoNam($maChiSo, $idKhoaPhong, $kyHienTai['nam'])
        )));
    }

    public function xuatThongKe()
    {
        $maChiSo = (int) $this->request->getParameter('ma_chi_so');
        $userId = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
        $idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($userId);
        $cauHinh = $this->CtChiSoPeer->getCauHinhNhap($maChiSo, $idKhoaPhong);

        if ($userId <= 0 || $idKhoaPhong <= 0 || $maChiSo <= 0 || !$cauHinh) {
            header('HTTP/1.1 403 Forbidden');
            echo 'Bạn không có quyền xuất thống kê chỉ số này.';
            exit;
        }

        $tenChiSo = 'Chỉ số ' . $maChiSo;
        foreach ($this->ChiSoPeer->getListDaDuyetByKhoa($idKhoaPhong) as $chiSo) {
            if ((int) $chiSo->get('ma_chi_so') === $maChiSo) {
                $tenChiSo = trim((string) $chiSo->get('ten_chi_so'));
                break;
            }
        }

        $tenKhoaPhong = 'Khoa/Phòng ' . $idKhoaPhong;
        foreach ($this->ChiSoPeer->getListKhoaPhongByKhoi() as $khoaPhong) {
            if ((int) $khoaPhong['id'] === $idKhoaPhong) {
                $tenKhoaPhong = trim((string) $khoaPhong['ten']);
                break;
            }
        }

        $bieuMau = $this->docBieuMau($cauHinh['bieumau']);
        $cauHoi = array();
        foreach ($bieuMau['cau_hoi'] as $index => $item) {
            $loai = isset($item['loai']) ? (string) $item['loai'] : 'short_text';
            if (in_array($loai, array('category', 'subcategory'), true)) continue;
            $id = isset($item['id']) && $item['id'] !== '' ? (string) $item['id'] : 'q' . ($index + 1);
            $cauHoi[$id] = array(
                'id' => $id,
                'noi_dung' => trim(isset($item['noi_dung']) ? (string) $item['noi_dung'] : 'Câu hỏi ' . ($index + 1)),
                'loai' => $loai
            );
        }

        $danhSachPhieu = $this->CtChiSoPeer->getDanhSachPhieu($maChiSo, $idKhoaPhong);

        $escape = function ($value) {
            return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        };
        $hienThiTraLoi = function ($value) {
            if (!is_array($value)) return (string) $value;
            if (isset($value['lua_chon']) && isset($value['noi_dung_khac'])) {
                return (string) $value['lua_chon'] . ': ' . (string) $value['noi_dung_khac'];
            }
            $items = array();
            foreach ($value as $item) {
                if (is_scalar($item) && trim((string) $item) !== '') $items[] = trim((string) $item);
            }
            return implode('; ', $items);
        };
        $soPhieu = count($danhSachPhieu);
        $tenKhoaTrongFile = preg_replace('/[\\\/:*?"<>|\x00-\x1F]+/u', '_', $tenKhoaPhong);
        $tenKhoaTrongFile = trim($tenKhoaTrongFile, " ._\t\n\r\0\x0B");
        if ($tenKhoaTrongFile === '') $tenKhoaTrongFile = 'Khoa phong ' . $idKhoaPhong;
        $tenFileDayDu = 'Thong ke chi so ' . $maChiSo . ' - ' . $tenKhoaTrongFile . '.xlsx';
        $tenKhoaKhongDau = function_exists('iconv')
            ? iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $tenKhoaTrongFile)
            : $tenKhoaTrongFile;
        $tenKhoaKhongDau = strtolower(preg_replace('/[^A-Za-z0-9]+/', '-', (string) $tenKhoaKhongDau));
        $tenKhoaKhongDau = trim($tenKhoaKhongDau, '-');
        if ($tenKhoaKhongDau === '') $tenKhoaKhongDau = 'khoa-phong-' . $idKhoaPhong;
        $tenFileDuPhong = 'thong-ke-chi-so-' . $maChiSo . '-' . $tenKhoaKhongDau . '.xlsx';
        $tenSheet = preg_replace('/[\\\\\/:*?\[\]]+/u', ' ', $tenKhoaPhong);
        $tenSheet = trim(preg_replace('/\s+/u', ' ', $tenSheet), " '\t\n\r\0\x0B");
        if ($tenSheet === '') $tenSheet = 'Khoa phong ' . $idKhoaPhong;
        $tenSheet = function_exists('mb_substr')
            ? mb_substr($tenSheet, 0, 31, 'UTF-8')
            : substr($tenSheet, 0, 31);

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

        $soCot = 6 + count($cauHoi);
        $cotCuoi = $tenCot($soCot);
        $cacDong = array();
        $cacDong[] = '<row r="1" ht="24" customHeight="1">' . $oChu('A1', 'THỐNG KÊ PHIẾU THEO CHỈ SỐ CỦA KHOA/PHÒNG', 1) . '</row>';
        $cacDong[] = '<row r="2">' . $oChu('A2', 'Mã chỉ số', 3) . $oChu('B2', $maChiSo, 4) . '</row>';
        $cacDong[] = '<row r="3">' . $oChu('A3', 'Tên chỉ số', 3) . $oChu('B3', $tenChiSo, 4) . '</row>';
        $cacDong[] = '<row r="4">' . $oChu('A4', 'Khoa/Phòng', 3) . $oChu('B4', $tenKhoaPhong, 4) . '</row>';
        $cacDong[] = '<row r="5">' . $oChu('A5', 'Tổng số phiếu', 3) . $oChu('B5', $soPhieu, 4, true) . '</row>';
        $cacDong[] = '<row r="7" ht="22" customHeight="1">' . $oChu('A7', 'CHI TIẾT CÂU TRẢ LỜI CỦA TỪNG PHIẾU', 2) . '</row>';

        $tieuDe = array('STT', 'Mã phiếu', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập');
        foreach ($cauHoi as $item) $tieuDe[] = $item['noi_dung'];
        $dongTieuDe = '';
        foreach ($tieuDe as $index => $noiDung) {
            $dongTieuDe .= $oChu($tenCot($index + 1) . '8', $noiDung, 2);
        }
        $cacDong[] = '<row r="8" ht="36" customHeight="1">' . $dongTieuDe . '</row>';

        $soDong = 8;
        foreach ($danhSachPhieu as $index => $phieu) {
            $soDong++;
            $giaTriDong = array(
                $index + 1,
                $phieu['id'],
                $phieu['nam'],
                $phieu['ky'],
                $phieu['nguoi_nhap'],
                $phieu['created_at']
            );
            foreach ($cauHoi as $id => $item) {
                $giaTri = isset($phieu['cau_tra_loi'][$id]) ? $phieu['cau_tra_loi'][$id] : '';
                $giaTriDong[] = $hienThiTraLoi($giaTri);
            }
            $noiDungDong = '';
            foreach ($giaTriDong as $cot => $giaTri) {
                $laSo = in_array($cot, array(0, 2, 3), true);
                $noiDungDong .= $oChu($tenCot($cot + 1) . $soDong, $giaTri, 4, $laSo);
            }
            $cacDong[] = '<row r="' . $soDong . '">' . $noiDungDong . '</row>';
        }
        if ($soPhieu === 0) {
            $soDong++;
            $cacDong[] = '<row r="' . $soDong . '">' . $oChu('A' . $soDong, 'Chưa có phiếu nào của Khoa/Phòng cho chỉ số này.', 4) . '</row>';
        }

        $merge = array('A1:' . $cotCuoi . '1', 'B2:' . $cotCuoi . '2', 'B3:' . $cotCuoi . '3', 'B4:' . $cotCuoi . '4', 'B5:' . $cotCuoi . '5', 'A7:' . $cotCuoi . '7');
        if ($soPhieu === 0) $merge[] = 'A' . $soDong . ':' . $cotCuoi . $soDong;
        $mergeXml = '';
        foreach ($merge as $vung) $mergeXml .= '<mergeCell ref="' . $vung . '"/>';

        $cotXml = '<col min="1" max="1" width="7" customWidth="1"/>'
            . '<col min="2" max="2" width="12" customWidth="1"/>'
            . '<col min="3" max="4" width="10" customWidth="1"/>'
            . '<col min="5" max="5" width="22" customWidth="1"/>'
            . '<col min="6" max="6" width="20" customWidth="1"/>';
        if ($soCot > 6) $cotXml .= '<col min="7" max="' . $soCot . '" width="32" customWidth="1"/>';

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetViews><sheetView workbookViewId="0"><pane ySplit="8" topLeftCell="A9" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews>'
            . '<sheetFormatPr defaultRowHeight="18"/><cols>' . $cotXml . '</cols><sheetData>' . implode('', $cacDong) . '</sheetData>'
            . '<mergeCells count="' . count($merge) . '">' . $mergeXml . '</mergeCells>'
            . '<autoFilter ref="A8:' . $cotCuoi . max(8, $soDong) . '"/>'
            . '<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';

        $stylesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="3"><font><sz val="10"/><name val="Arial"/></font><font><b/><sz val="14"/><name val="Arial"/><color rgb="FF1F2937"/></font><font><b/><sz val="10"/><name val="Arial"/><color rgb="FFFFFFFF"/></font></fonts>'
            . '<fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1F4E78"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFD9EAF7"/><bgColor indexed="64"/></patternFill></fill></fills>'
            . '<borders count="2"><border/><border><left style="thin"><color rgb="FFB8C2CC"/></left><right style="thin"><color rgb="FFB8C2CC"/></right><top style="thin"><color rgb="FFB8C2CC"/></top><bottom style="thin"><color rgb="FFB8C2CC"/></bottom><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="5"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/><xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyAlignment="1"><alignment vertical="center" wrapText="1"/></xf><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment vertical="top" wrapText="1"/></xf></cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>';
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
        $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="' . $xml($tenSheet) . '" sheetId="1" r:id="rId1"/></sheets></workbook>';
        $workbookRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';

        $duongDanTam = tempnam(sys_get_temp_dir(), 'qlcscl_');
        if ($duongDanTam === false) {
            header('HTTP/1.1 500 Internal Server Error');
            echo 'Không thể tạo file Excel tạm.';
            exit;
        }
        @unlink($duongDanTam);
        $duongDanTam .= '.zip';

        try {
            $goiExcel = new PharData($duongDanTam, 0, null, Phar::ZIP);
            $goiExcel->addFromString('[Content_Types].xml', $contentTypes);
            $goiExcel->addFromString('_rels/.rels', $rels);
            $goiExcel->addFromString('xl/workbook.xml', $workbookXml);
            $goiExcel->addFromString('xl/_rels/workbook.xml.rels', $workbookRels);
            $goiExcel->addFromString('xl/styles.xml', $stylesXml);
            $goiExcel->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
            unset($goiExcel);

            if (ob_get_length()) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $tenFileDuPhong . '"; filename*=UTF-8\'\'' . rawurlencode($tenFileDayDu));
            header('Content-Length: ' . filesize($duongDanTam));
            header('Cache-Control: max-age=0');
            readfile($duongDanTam);
        } catch (Throwable $exception) {
            if (!headers_sent()) header('HTTP/1.1 500 Internal Server Error');
            echo 'Không thể tạo file Excel.';
        }
        @unlink($duongDanTam);
        exit;
    }

    public function save()
    {
        $maChiSo = (int) $this->request->getParameter('ma_chi_so');
        $nam = (int) $this->request->getParameter('nam');
        $ky = (int) $this->request->getParameter('ky');
        $duLieuGui = $this->request->getParameter('du_lieu', true);
        $duLieuGui = is_string($duLieuGui) ? json_decode($duLieuGui, true) : $duLieuGui;
        $userId = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
        $idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($userId);
        $cauHinh = $this->CtChiSoPeer->getCauHinhNhap($maChiSo, $idKhoaPhong);
        $kyHienTai = $this->tinhKyHienTai($cauHinh);

        if ($userId <= 0 || $idKhoaPhong <= 0 || !$kyHienTai) {
            return $this->jsonError('Bạn không được nhập liệu cho chỉ tiêu này');
        }
        if ($nam !== $kyHienTai['nam'] || $ky < 1 || $ky > $kyHienTai['ky']) {
            return $this->jsonError('Không được nhập dữ liệu cho chu kỳ tương lai');
        }
        $bieuMau = $this->docBieuMau($cauHinh['bieumau']);
        $duLieuKy = $this->kiemTraCauTraLoi($bieuMau, $duLieuGui);
        if ($duLieuKy === false) {
            return $this->jsonError($this->lastErrorMessage);
        }

        $item = new CtChiSo();
        $item->set('ma_chi_so', $maChiSo);
        $item->set('id_user', $userId);
        $item->set('id_khoaphong', $idKhoaPhong);
        $item->set('nam', $nam);
        $item->set('ky', $ky);
        $item->set('du_lieu', $duLieuKy);
        $this->CtChiSoPeer->save($item);

        return $this->request->json_response(json_encode(array(
            'success' => true,
            'message' => 'Lưu phiếu mới thành công'
        )));
    }

    private function docBieuMau($json)
    {
        $bieuMau = is_array($json) ? $json : json_decode((string) $json, true);
        if (isset($bieuMau['cau_hoi']) && is_array($bieuMau['cau_hoi'])) {
            return $bieuMau;
        }
        if (is_array($bieuMau) && array_values($bieuMau) === $bieuMau) {
            return array('version' => 1, 'cau_hoi' => $bieuMau);
        }
        return array('version' => 1, 'cau_hoi' => array());
    }

    private function kiemTraCauTraLoi($bieuMau, $duLieuGui)
    {
        $cauHoi = isset($bieuMau['cau_hoi']) ? $bieuMau['cau_hoi'] : array();
        $giaTriGui = isset($duLieuGui['cau_tra_loi']) && is_array($duLieuGui['cau_tra_loi'])
            ? $duLieuGui['cau_tra_loi']
            : array();
        $ketQua = array();
        $tongDiem = 0;
        $diemToiDa = 0;
        $soCauHoiNhapLieu = 0;

        if (empty($cauHoi)) {
            $this->lastErrorMessage = 'Chỉ tiêu chưa được thiết kế biểu mẫu nhập liệu';
            return false;
        }

        foreach ($cauHoi as $index => $item) {
            $loai = isset($item['loai']) ? (string) $item['loai'] : 'short_text';
            if (in_array($loai, array('category', 'subcategory'), true)) continue;
            $soCauHoiNhapLieu++;

            $id = isset($item['id']) && $item['id'] !== '' ? (string) $item['id'] : 'q' . ($index + 1);
            $noiDung = trim(isset($item['noi_dung']) ? (string) $item['noi_dung'] : '');
            $doDaiSo = isset($item['do_dai_so']) ? max(0, min(50, (int) $item['do_dai_so'])) : 0;
            $giaTri = isset($giaTriGui[$id]) ? $giaTriGui[$id] : null;
            $luaChonGoc = isset($item['lua_chon']) && is_array($item['lua_chon']) ? $item['lua_chon'] : array();
            $luaChon = array();
            $diemLuaChon = array();
            $phuongAnNhapThem = array();
            foreach ($luaChonGoc as $phuongAn) {
                $tenPhuongAn = is_array($phuongAn)
                    ? trim(isset($phuongAn['noi_dung']) ? (string) $phuongAn['noi_dung'] : '')
                    : trim((string) $phuongAn);
                if ($tenPhuongAn === '') continue;
                $luaChon[] = $tenPhuongAn;
                $diemLuaChon[$tenPhuongAn] = is_array($phuongAn) && isset($phuongAn['diem'])
                    ? (float) $phuongAn['diem']
                    : 0;
                if ($loai === 'radio' && is_array($phuongAn) && !empty($phuongAn['cho_nhap_them'])) {
                    $phuongAnNhapThem[$tenPhuongAn] = true;
                }
            }

            if ($loai === 'checkbox') {
                $giaTri = is_array($giaTri) ? array_values(array_filter(array_map('strval', $giaTri), 'strlen')) : array();
                $rong = empty($giaTri);
            } elseif ($loai === 'radio') {
                $giaTriCoNhapThem = is_array($giaTri);
                $giaTriLuaChon = $giaTriCoNhapThem
                    ? (isset($giaTri['lua_chon']) && is_scalar($giaTri['lua_chon']) ? trim((string) $giaTri['lua_chon']) : '')
                    : (is_scalar($giaTri) ? trim((string) $giaTri) : '');
                $noiDungNhapThem = $giaTriCoNhapThem && isset($giaTri['noi_dung_khac']) && is_scalar($giaTri['noi_dung_khac'])
                    ? trim((string) $giaTri['noi_dung_khac']) : '';
                $rong = $giaTriLuaChon === '';

                if (!$rong && isset($phuongAnNhapThem[$giaTriLuaChon])) {
                    if (!$giaTriCoNhapThem || $noiDungNhapThem === '') {
                        $this->lastErrorMessage = 'Vui lòng ghi rõ nội dung cho lựa chọn khác: ' . $noiDung;
                        return false;
                    }
                    if ((function_exists('mb_strlen') ? mb_strlen($noiDungNhapThem, 'UTF-8') : strlen($noiDungNhapThem)) > 500) {
                        $this->lastErrorMessage = 'Nội dung nhập thêm không được quá 500 ký tự: ' . $noiDung;
                        return false;
                    }
                    $giaTri = array('lua_chon' => $giaTriLuaChon, 'noi_dung_khac' => $noiDungNhapThem);
                } else {
                    if ($giaTriCoNhapThem) {
                        $this->lastErrorMessage = 'Phương án nhập thêm không hợp lệ: ' . $noiDung;
                        return false;
                    }
                    $giaTri = $giaTriLuaChon;
                }
            } else {
                $giaTri = is_scalar($giaTri) ? trim((string) $giaTri) : '';
                $rong = $giaTri === '';
            }

            if ($rong) {
                $this->lastErrorMessage = 'Vui lòng trả lời câu hỏi: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'number' && !is_numeric($giaTri)) {
                $this->lastErrorMessage = 'Câu trả lời phải là số: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'percentage'
                && (!is_numeric($giaTri) || (float) $giaTri < 0 || (float) $giaTri > 100)) {
                $this->lastErrorMessage = 'Tỷ lệ phải nằm trong khoảng từ 0 đến 100%: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'number' && $doDaiSo > 0
                && strlen(preg_replace('/\D/', '', $giaTri)) > $doDaiSo) {
                $this->lastErrorMessage = 'Câu trả lời không được vượt quá ' . $doDaiSo . ' chữ số: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'score' && (!is_numeric($giaTri) || (int) $giaTri < 1 || (int) $giaTri > 10)) {
                $this->lastErrorMessage = 'Điểm phải nằm trong khoảng từ 1 đến 10: ' . $noiDung;
                return false;
            }
            if (!$rong && in_array($loai, array('radio', 'select', 'satisfaction'), true)
                && !in_array($loai === 'radio' ? $giaTriLuaChon : $giaTri, $luaChon, true)) {
                $this->lastErrorMessage = 'Phương án trả lời không hợp lệ: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'checkbox' && array_diff($giaTri, $luaChon)) {
                $this->lastErrorMessage = 'Phương án trả lời không hợp lệ: ' . $noiDung;
                return false;
            }

            if (!$rong && in_array($loai, array('radio', 'select', 'satisfaction'), true)) {
                $giaTriDiem = $loai === 'radio' ? $giaTriLuaChon : $giaTri;
                $tongDiem += isset($diemLuaChon[$giaTriDiem]) ? $diemLuaChon[$giaTriDiem] : 0;
            } elseif (!$rong && $loai === 'checkbox') {
                foreach ($giaTri as $phuongAnDaChon) {
                    $tongDiem += isset($diemLuaChon[$phuongAnDaChon]) ? $diemLuaChon[$phuongAnDaChon] : 0;
                }
            } elseif (!$rong && $loai === 'score') {
                $tongDiem += (float) $giaTri;
            }

            if (in_array($loai, array('radio', 'select', 'satisfaction'), true) && !empty($diemLuaChon)) {
                $diemToiDa += max(0, max($diemLuaChon));
            } elseif ($loai === 'checkbox') {
                foreach ($diemLuaChon as $diemPhuongAn) {
                    if ($diemPhuongAn > 0) $diemToiDa += $diemPhuongAn;
                }
            } elseif ($loai === 'score') {
                $diemToiDa += 10;
            }

            $ketQua[$id] = $giaTri;
        }

        if ($soCauHoiNhapLieu === 0) {
            $this->lastErrorMessage = 'Biểu mẫu chưa có câu hỏi nhập liệu';
            return false;
        }

        $tyLePhanTram = $diemToiDa > 0 ? round(($tongDiem / $diemToiDa) * 100, 2) : 0;

        return array(
            'cau_tra_loi' => $ketQua,
            'tong_diem' => round($tongDiem, 2),
            'diem_toi_da' => round($diemToiDa, 2),
            'ty_le_phan_tram' => $tyLePhanTram
        );
    }

    private function jsonError($message)
    {
        return $this->request->json_response(json_encode(array(
            'success' => false,
            'message' => $message
        )));
    }

    private function tinhKyHienTai($cauHinh)
    {
        if (!$cauHinh) return false;

        $soChuKy = (int) $cauHinh['chuky'];
        if ($soChuKy <= 0 || $soChuKy > 12 || 12 % $soChuKy !== 0) return false;

        $ngayTao = !empty($cauHinh['created_at'])
            ? new DateTime($cauHinh['created_at'])
            : new DateTime();
        $hienTai = new DateTime();
        $thangBatDau = (int) $ngayTao->format('n');
        $namBatDau = (int) $ngayTao->format('Y');
        $thangHienTai = (int) $hienTai->format('n');
        $namHienTai = (int) $hienTai->format('Y');
        $doLechThang = ($namHienTai - $namBatDau) * 12
            + ($thangHienTai - $thangBatDau);
        if ($doLechThang < 0) return false;
        $soThangMotKy = 12 / $soChuKy;
        $kyHienTai = (int) floor($doLechThang / $soThangMotKy) + 1;
        $kyHienTai = min($kyHienTai, $soChuKy);

        return array(
            'so_chu_ky' => $soChuKy,
            'nam' => $namBatDau,
            'ky' => $kyHienTai,
            'thang_bat_dau' => $thangBatDau,
            'nam_bat_dau' => $namBatDau
        );
    }
}
