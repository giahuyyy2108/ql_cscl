<?php

require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");
require_once ("web_src/bean/TinhTrangPeer.php");
require_once ("web_src/bean/CtChiSoPeer.php");
require_once ("web_src/common/SimpleXLSXGen/SimpleXLSXGen.php");
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
            $diemLuaChon = array();
            $coNhapKhac = false;
            if ($loai === 'radio' && isset($item['lua_chon']) && is_array($item['lua_chon'])) {
                foreach ($item['lua_chon'] as $luaChon) {
                    if (is_array($luaChon) && !empty($luaChon['cho_nhap_them'])) {
                        $coNhapKhac = true;
                        break;
                    }
                }
            }
            if ($loai === 'satisfaction' && isset($item['lua_chon']) && is_array($item['lua_chon'])) {
                foreach ($item['lua_chon'] as $luaChon) {
                    if (is_array($luaChon) && isset($luaChon['noi_dung'], $luaChon['diem'])
                        && is_numeric($luaChon['diem'])) {
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

        $danhSachPhieu = $this->CtChiSoPeer->getDanhSachPhieu($maChiSo, $idKhoaPhong);
        foreach ($danhSachPhieu as $phieu) {
            foreach ($cauHoi as $id => $item) {
                if ($item['loai'] === 'radio'
                    && isset($phieu['cau_tra_loi'][$id]) && is_array($phieu['cau_tra_loi'][$id])
                    && array_key_exists('noi_dung_khac', $phieu['cau_tra_loi'][$id])) {
                    $cauHoi[$id]['co_nhap_khac'] = true;
                }
            }
        }

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
        $giaTriExcel = function ($value, $dinhDang = '') {
            return $dinhDang . '<raw>'
                . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')
                . '</raw>';
        };

        $tenKhoaTrongFile = str_replace(
            array('\\', '/', ':', '*', '?', '"', '<', '>', '|'),
            '_',
            preg_replace('/[\x00-\x1F]+/u', '', $tenKhoaPhong)
        );
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

        $tenSheet = str_replace(array('\\', '/', ':', '*', '?', '[', ']'), ' ', $tenKhoaPhong);
        $tenSheet = trim(preg_replace('/\s+/u', ' ', $tenSheet), " '\t\n\r\0\x0B");
        if ($tenSheet === '') $tenSheet = 'Khoa phong ' . $idKhoaPhong;
        $tenSheet = function_exists('mb_substr')
            ? mb_substr($tenSheet, 0, 31, 'UTF-8')
            : substr($tenSheet, 0, 31);

        $tieuDeBang = '<b><center><bottom><wraptext><style height="72" border="thin">';
        $duLieuBang = '<top><wraptext><style border="thin">';
        $duLieuSheet = array();

        $tieuDe = array('STT', 'Mã phiếu', 'Năm', 'Kỳ', 'Người nhập', 'Ngày nhập');
        foreach ($cauHoi as $item) {
            $tieuDe[] = $item['noi_dung'];
            if ($item['co_nhap_khac']) $tieuDe[] = $item['noi_dung'] . ' - Nội dung khác';
        }
        foreach ($tieuDe as &$noiDungTieuDe) {
            $noiDungTieuDe = $giaTriExcel($noiDungTieuDe, $tieuDeBang);
        }
        unset($noiDungTieuDe);
        $duLieuSheet[] = $tieuDe;

        foreach ($danhSachPhieu as $index => $phieu) {
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
                if ($item['loai'] === 'satisfaction') {
                    $luaChon = is_scalar($giaTri) ? trim((string) $giaTri) : '';
                    $diem = $luaChon !== '' && array_key_exists($luaChon, $item['diem_lua_chon'])
                        ? $item['diem_lua_chon'][$luaChon] : null;
                    $dongExcel[] = $diem === null
                        ? $giaTriExcel('', $duLieuBang)
                        : $duLieuBang . (string) $diem;
                } elseif ($item['loai'] === 'radio' && $item['co_nhap_khac']) {
                    $laLuaChonKhac = is_array($giaTri) && array_key_exists('noi_dung_khac', $giaTri);
                    $dongExcel[] = $giaTriExcel($laLuaChonKhac ? 'Khác' : $hienThiTraLoi($giaTri), $duLieuBang);
                    $dongExcel[] = $giaTriExcel($laLuaChonKhac ? $giaTri['noi_dung_khac'] : '', $duLieuBang);
                } else {
                    $dongExcel[] = $giaTriExcel($hienThiTraLoi($giaTri), $duLieuBang);
                }
            }
            $duLieuSheet[] = $dongExcel;
        }

        $soCot = count($tieuDe);
        $tenCot = function ($soCot) {
            $ketQua = '';
            while ($soCot > 0) {
                $soCot--;
                $ketQua = chr(65 + ($soCot % 26)) . $ketQua;
                $soCot = (int) floor($soCot / 26);
            }
            return $ketQua;
        };
        $cotCuoi = $tenCot($soCot);

        $xlsx = new \Shuchkin\SimpleXLSXGen();
        $xlsx->setDefaultFont('Arial')
            ->setDefaultFontSize(10)
            ->setTitle('Thống kê phiếu theo chỉ số')
            ->addSheet($duLieuSheet, $tenSheet)
            ->freezePanes('A2')
            ->autoFilter('A1:' . $cotCuoi . max(1, count($duLieuSheet)));

        $xlsx->setColWidth(1, 14)
            ->setColWidth(2, 12)
            ->setColWidth(3, 10)
            ->setColWidth(4, 10)
            ->setColWidth(5, 22)
            ->setColWidth(6, 20);
        for ($cot = 7; $cot <= $soCot; $cot++) {
            $xlsx->setColWidth($cot, 32);
        }

        try {
            $noiDungExcel = (string) $xlsx;
            if ($noiDungExcel === '') throw new Exception('Không tạo được nội dung XLSX');
            while (ob_get_level()) ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $tenFileDuPhong . '"; filename*=UTF-8\'\'' . rawurlencode($tenFileDayDu));
            header('Content-Length: ' . strlen($noiDungExcel));
            header('Cache-Control: max-age=0');
            echo $noiDungExcel;
        } catch (Throwable $exception) {
            error_log('NhapLieuAction::xuatThongKe - ' . $exception->getMessage());
            if (!headers_sent()) header('HTTP/1.1 500 Internal Server Error');
            echo 'Không thể tạo file Excel.';
        }
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
