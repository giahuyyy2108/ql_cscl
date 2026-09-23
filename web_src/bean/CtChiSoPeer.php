<?php

require_once('web_src/bean/CtChiSo.php');

class CtChiSoPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    public function getSoChuKyDuocNhap($maChiSo, $idKhoaPhong)
    {
        $cauHinh = $this->getCauHinhNhap($maChiSo, $idKhoaPhong);
        return $cauHinh ? (int) $cauHinh['chuky'] : 0;
    }

    public function getCauHinhNhap($maChiSo, $idKhoaPhong)
    {
        $maChiSo = (int) $maChiSo;
        $idKhoaPhong = (int) $idKhoaPhong;
        $sql = "SELECT ck.chuky, cs.created_at, cs.bieumau
                FROM chi_so_chat_luong cs
                INNER JOIN chuky ck ON ck.id = cs.id_chuky
                WHERE cs.ma_chi_so = $maChiSo
                  AND cs.trang_thai = 2
                  AND CASE
                        WHEN JSON_VALID(cs.phong) = 1
                        THEN JSON_CONTAINS(cs.phong, '$idKhoaPhong', '$')
                        ELSE cs.id_khoaphong = $idKhoaPhong
                      END = 1
                LIMIT 1";
        $result = $this->dbsql->query($sql);
        if ($this->dbsql->num_rows($result) === 0) return false;
        return $this->dbsql->fetch_array($result);
    }

    public function getTheoNam($maChiSo, $idKhoaPhong, $nam)
    {
        $sql = "SELECT * FROM ct_chiso
                WHERE ma_chi_so = " . (int) $maChiSo . "
                  AND id_khoaphong = " . (int) $idKhoaPhong . "
                  AND nam = " . (int) $nam . "
                ORDER BY ky ASC, created_at ASC, id ASC";
        $result = $this->dbsql->query($sql);
        $items = array();

        while ($row = $this->dbsql->fetch_array($result)) {
            $duLieu = json_decode($row['du_lieu'], true);
            if (!is_array($duLieu)) $duLieu = array();

            $items[] = array(
                'id' => $row['id'],
                'nam' => (int) $row['nam'],
                'ky' => (int) $row['ky'],
                'du_lieu' => $duLieu,
                'created_at' => $row['created_at']
            );
        }
        return $items;
    }

    public function daNhapKy($maChiSo, $idKhoaPhong, $nam, $ky)
    {
        $sql = "SELECT 1 FROM ct_chiso
                WHERE ma_chi_so = " . (int) $maChiSo . "
                  AND id_khoaphong = " . (int) $idKhoaPhong . "
                  AND nam = " . (int) $nam . "
                  AND ky = " . (int) $ky . "
                LIMIT 1";
        $result = $this->dbsql->query($sql);
        return $this->dbsql->num_rows($result) > 0;
    }

    public function getTrungBinhTheoKy($maChiSo)
    {
        $maChiSo = (int) $maChiSo;
        // Không lọc Khoa/Phòng: biểu đồ của chỉ số sử dụng toàn bộ phiếu đã lưu.
        $result = $this->dbsql->query("SELECT nam, ky, du_lieu
            FROM ct_chiso
            WHERE ma_chi_so = $maChiSo
            ORDER BY nam ASC, ky ASC, id ASC");
        $tongTheoKy = array();

        while ($row = $this->dbsql->fetch_array($result)) {
            $giaTri = json_decode($row['du_lieu'], true);
            if (!is_array($giaTri) || !isset($giaTri['ty_le_phan_tram']) || !is_numeric($giaTri['ty_le_phan_tram'])) continue;

            $nam = (int) $row['nam'];
            $ky = (int) $row['ky'];
            $khoa = $nam . '-' . $ky;
            if (!isset($tongTheoKy[$khoa])) {
                $tongTheoKy[$khoa] = array('nam' => $nam, 'ky' => $ky, 'tong' => 0, 'so_phieu' => 0);
            }
            $tongTheoKy[$khoa]['tong'] += (float) $giaTri['ty_le_phan_tram'];
            $tongTheoKy[$khoa]['so_phieu']++;
        }

        $ketQua = array_values($tongTheoKy);
        usort($ketQua, function ($a, $b) {
            return $a['nam'] === $b['nam'] ? $a['ky'] - $b['ky'] : $a['nam'] - $b['nam'];
        });
        foreach ($ketQua as &$item) {
            $item['trung_binh'] = round($item['tong'] / $item['so_phieu'], 2);
            unset($item['tong']);
        }
        unset($item);
        return $ketQua;
    }

    public function getDanhSachPhieu($maChiSo)
    {
        $maChiSo = (int) $maChiSo;
        $sql = "SELECT ct.id, ct.ma_chi_so, ct.id_khoaphong, ct.nam, ct.ky,
                       ct.du_lieu, ct.created_at, ct.updated_at,
                       k.ten AS ten_khoaphong,
                       u.hoTen AS nguoi_nhap
                FROM ct_chiso ct
                LEFT JOIN khoa k ON k.id = ct.id_khoaphong
                LEFT JOIN user u ON u.id = ct.id_user
                WHERE ct.ma_chi_so = $maChiSo
                ORDER BY ct.nam DESC, ct.ky DESC, ct.created_at DESC, ct.id DESC";
        $result = $this->dbsql->query($sql);
        $items = array();

        while ($row = $this->dbsql->fetch_array($result)) {
            $duLieu = json_decode($row['du_lieu'], true);
            if (!is_array($duLieu)) $duLieu = array();

            $items[] = array(
                'id' => (int) $row['id'],
                'ma_chi_so' => (int) $row['ma_chi_so'],
                'id_khoaphong' => (int) $row['id_khoaphong'],
                'ten_khoaphong' => $row['ten_khoaphong'] !== null ? $row['ten_khoaphong'] : '',
                'nam' => (int) $row['nam'],
                'ky' => (int) $row['ky'],
                'tong_diem' => isset($duLieu['tong_diem']) ? (float) $duLieu['tong_diem'] : 0,
                'diem_toi_da' => isset($duLieu['diem_toi_da']) ? (float) $duLieu['diem_toi_da'] : 0,
                'ty_le_phan_tram' => isset($duLieu['ty_le_phan_tram']) ? (float) $duLieu['ty_le_phan_tram'] : 0,
                'nguoi_nhap' => $row['nguoi_nhap'] !== null ? $row['nguoi_nhap'] : '',
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'cau_tra_loi' => isset($duLieu['cau_tra_loi']) && is_array($duLieu['cau_tra_loi'])
                    ? $duLieu['cau_tra_loi']
                    : array()
            );
        }

        return $items;
    }

    public function save($item)
    {
        $maChiSo = (int) $item->get('ma_chi_so');
        $idUser = (int) $item->get('id_user');
        $idKhoaPhong = (int) $item->get('id_khoaphong');
        $duLieu = $item->get('du_lieu');
        if (!is_array($duLieu)) $duLieu = array();
        $duLieuJson = addslashes(json_encode($duLieu, JSON_UNESCAPED_UNICODE));

        $sql = "INSERT INTO ct_chiso
                    (ma_chi_so, id_user, id_khoaphong, nam, ky, du_lieu)
                VALUES (
                    $maChiSo,
                    $idUser,
                    $idKhoaPhong,
                    " . (int) $item->get('nam') . ",
                    " . (int) $item->get('ky') . ",
                    '$duLieuJson'
                )";
        $this->dbsql->query($sql);
        return true;
    }
}
?>
