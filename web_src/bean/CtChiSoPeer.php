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
                LIMIT 1";
        $result = $this->dbsql->query($sql);
        $items = array();
        if ($this->dbsql->num_rows($result) === 0) return $items;

        $row = $this->dbsql->fetch_array($result);
        $duLieu = json_decode($row['du_lieu'], true);
        $duLieuNam = isset($duLieu[(string) $nam]) && is_array($duLieu[(string) $nam])
            ? $duLieu[(string) $nam]
            : array();
        foreach ($duLieuNam as $ky => $giaTri) {
            $items[] = array(
                'id' => $row['id'],
                'nam' => (int) $nam,
                'ky' => (int) $ky,
                'du_lieu' => array((string) $ky => $giaTri)
            );
        }
        return $items;
    }

    public function daNhapKy($maChiSo, $idKhoaPhong, $nam, $ky)
    {
        $sql = "SELECT du_lieu FROM ct_chiso
                WHERE ma_chi_so = " . (int) $maChiSo . "
                  AND id_khoaphong = " . (int) $idKhoaPhong . "
                LIMIT 1";
        $result = $this->dbsql->query($sql);
        if ($this->dbsql->num_rows($result) === 0) return false;
        $row = $this->dbsql->fetch_array($result);
        $duLieu = json_decode($row['du_lieu'], true);
        return isset($duLieu[(string) $nam][(string) $ky]);
    }

    public function save($item)
    {
        $maChiSo = (int) $item->get('ma_chi_so');
        $idUser = (int) $item->get('id_user');
        $idKhoaPhong = (int) $item->get('id_khoaphong');
        $nam = (string) (int) $item->get('nam');
        $ky = (string) (int) $item->get('ky');
        $duLieuMoi = $item->get('du_lieu');
        $duLieuKy = isset($duLieuMoi[$ky]) ? $duLieuMoi[$ky] : array();
        $duLieu = array();

        $result = $this->dbsql->query("SELECT du_lieu FROM ct_chiso
            WHERE ma_chi_so = $maChiSo AND id_khoaphong = $idKhoaPhong LIMIT 1");
        if ($this->dbsql->num_rows($result) > 0) {
            $row = $this->dbsql->fetch_array($result);
            $duLieu = json_decode($row['du_lieu'], true);
            if (!is_array($duLieu)) $duLieu = array();
        }
        if (!isset($duLieu[$nam]) || !is_array($duLieu[$nam])) $duLieu[$nam] = array();
        $duLieu[$nam][$ky] = $duLieuKy;
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
                )
                ON DUPLICATE KEY UPDATE
                    id_user = VALUES(id_user),
                    id_khoaphong = VALUES(id_khoaphong),
                    nam = VALUES(nam),
                    ky = VALUES(ky),
                    du_lieu = VALUES(du_lieu),
                    updated_at = NOW()";
        $this->dbsql->query($sql);
        return true;
    }
}
?>
