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
            'da_nhap' => $this->CtChiSoPeer->getTheoNam($maChiSo, $idKhoaPhong, $kyHienTai['nam'])
        )));
    }

    public function save()
    {
        $maChiSo = (int) $this->request->getParameter('ma_chi_so');
        $nam = (int) $this->request->getParameter('nam');
        $ky = (int) $this->request->getParameter('ky');
        $tuSo = (float) $this->request->getParameter('tu_so', false);
        $mauSo = (float) $this->request->getParameter('mau_so', false);
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
        if ($tuSo == 0) {
            return $this->jsonError('Tử số phải khác 0');
        }
        if ($tuSo < 0 || $mauSo < 0) {
            return $this->jsonError('Tử số và mẫu số không được âm');
        }

        // Công thức theo yêu cầu nghiệp vụ: mẫu số / tử số * 100.
        $value = round(($mauSo / $tuSo) * 100, 2);
        $item = new CtChiSo();
        $item->set('ma_chi_so', $maChiSo);
        $item->set('id_user', $userId);
        $item->set('id_khoaphong', $idKhoaPhong);
        $item->set('nam', $nam);
        $item->set('ky', $ky);
        $item->set('du_lieu', array(
            (string) $ky => array(
                'tu_so' => $tuSo,
                'mau_so' => $mauSo,
                'value' => $value
            )
        ));
        $this->CtChiSoPeer->save($item);

        return $this->request->json_response(json_encode(array(
            'success' => true,
            'value' => $value,
            'message' => 'Lưu nhập liệu thành công'
        )));
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
