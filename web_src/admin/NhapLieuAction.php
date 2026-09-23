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

        if (empty($cauHoi)) {
            $this->lastErrorMessage = 'Chỉ tiêu chưa được thiết kế biểu mẫu nhập liệu';
            return false;
        }

        foreach ($cauHoi as $index => $item) {
            $id = isset($item['id']) && $item['id'] !== '' ? (string) $item['id'] : 'q' . ($index + 1);
            $noiDung = trim(isset($item['noi_dung']) ? (string) $item['noi_dung'] : '');
            $loai = isset($item['loai']) ? (string) $item['loai'] : 'short_text';
            $batBuoc = !empty($item['bat_buoc']);
            $giaTri = isset($giaTriGui[$id]) ? $giaTriGui[$id] : null;
            $luaChonGoc = isset($item['lua_chon']) && is_array($item['lua_chon']) ? $item['lua_chon'] : array();
            $luaChon = array();
            $diemLuaChon = array();
            foreach ($luaChonGoc as $phuongAn) {
                $tenPhuongAn = is_array($phuongAn)
                    ? trim(isset($phuongAn['noi_dung']) ? (string) $phuongAn['noi_dung'] : '')
                    : trim((string) $phuongAn);
                if ($tenPhuongAn === '') continue;
                $luaChon[] = $tenPhuongAn;
                $diemLuaChon[$tenPhuongAn] = is_array($phuongAn) && isset($phuongAn['diem'])
                    ? (float) $phuongAn['diem']
                    : 0;
            }

            if ($loai === 'checkbox') {
                $giaTri = is_array($giaTri) ? array_values(array_filter(array_map('strval', $giaTri), 'strlen')) : array();
                $rong = empty($giaTri);
            } else {
                $giaTri = is_scalar($giaTri) ? trim((string) $giaTri) : '';
                $rong = $giaTri === '';
            }

            if ($batBuoc && $rong) {
                $this->lastErrorMessage = 'Vui lòng trả lời câu hỏi: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'number' && !is_numeric($giaTri)) {
                $this->lastErrorMessage = 'Câu trả lời phải là số: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'score' && (!is_numeric($giaTri) || (int) $giaTri < 1 || (int) $giaTri > 10)) {
                $this->lastErrorMessage = 'Điểm phải nằm trong khoảng từ 1 đến 10: ' . $noiDung;
                return false;
            }
            if (!$rong && in_array($loai, array('radio', 'select'), true) && !in_array($giaTri, $luaChon, true)) {
                $this->lastErrorMessage = 'Phương án trả lời không hợp lệ: ' . $noiDung;
                return false;
            }
            if (!$rong && $loai === 'checkbox' && array_diff($giaTri, $luaChon)) {
                $this->lastErrorMessage = 'Phương án trả lời không hợp lệ: ' . $noiDung;
                return false;
            }

            if (!$rong && in_array($loai, array('radio', 'select'), true)) {
                $tongDiem += isset($diemLuaChon[$giaTri]) ? $diemLuaChon[$giaTri] : 0;
            } elseif (!$rong && $loai === 'checkbox') {
                foreach ($giaTri as $phuongAnDaChon) {
                    $tongDiem += isset($diemLuaChon[$phuongAnDaChon]) ? $diemLuaChon[$phuongAnDaChon] : 0;
                }
            } elseif (!$rong && $loai === 'score') {
                $tongDiem += (float) $giaTri;
            }

            if (in_array($loai, array('radio', 'select'), true) && !empty($diemLuaChon)) {
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
