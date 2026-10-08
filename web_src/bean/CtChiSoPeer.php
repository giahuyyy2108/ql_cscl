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

    public function getTrungBinhTheoCauHoi($maChiSo)
    {
        $maChiSo = (int) $maChiSo;
        $resultChiSo = $this->dbsql->query("SELECT bieumau FROM chi_so_chat_luong WHERE ma_chi_so = $maChiSo LIMIT 1");
        if ($this->dbsql->num_rows($resultChiSo) === 0) return array();

        $rowChiSo = $this->dbsql->fetch_array($resultChiSo);
        $bieuMau = json_decode($rowChiSo['bieumau'], true);
        $cauHoi = isset($bieuMau['cau_hoi']) && is_array($bieuMau['cau_hoi']) ? $bieuMau['cau_hoi'] : array();
        $cauHoiTinhDiem = array();

        foreach ($cauHoi as $index => $item) {
            $loai = isset($item['loai']) ? $item['loai'] : '';
            $id = isset($item['id']) && $item['id'] !== '' ? (string) $item['id'] : 'q' . ($index + 1);
            $bangDiem = array();
            $diemToiDa = 0;

            if (isset($item['lua_chon']) && is_array($item['lua_chon'])) {
                foreach ($item['lua_chon'] as $luaChon) {
                    $noiDung = is_array($luaChon) && isset($luaChon['noi_dung']) ? (string) $luaChon['noi_dung'] : (string) $luaChon;
                    $diem = is_array($luaChon) && isset($luaChon['diem']) && is_numeric($luaChon['diem']) ? (float) $luaChon['diem'] : 0;
                    $bangDiem[$noiDung] = $diem;
                }
            }

            if ($loai === 'checkbox') {
                foreach ($bangDiem as $diem) if ($diem > 0) $diemToiDa += $diem;
            } elseif (in_array($loai, array('radio', 'select', 'satisfaction'), true) && !empty($bangDiem)) {
                $diemToiDa = max(0, max($bangDiem));
            } elseif ($loai === 'score') {
                $diemToiDa = 10;
            }

            if ($diemToiDa <= 0) continue;
            $cauHoiTinhDiem[$id] = array(
                'ky_hieu' => isset($item['ky_hieu']) && trim($item['ky_hieu']) !== '' ? trim($item['ky_hieu']) : $id,
                'noi_dung' => isset($item['noi_dung']) ? trim((string) $item['noi_dung']) : '',
                'loai' => $loai,
                'bang_diem' => $bangDiem,
                'diem_toi_da' => $diemToiDa,
                'tong' => 0,
                'so_phieu' => 0,
                'theo_khoa_phong' => array()
            );
        }

        if (empty($cauHoiTinhDiem)) return array();
        $resultPhieu = $this->dbsql->query("SELECT ct.du_lieu, ct.id_khoaphong, k.ten AS ten_khoaphong
            FROM ct_chiso ct
            LEFT JOIN khoa k ON k.id = ct.id_khoaphong
            WHERE ct.ma_chi_so = $maChiSo
            ORDER BY ct.id ASC");
        while ($row = $this->dbsql->fetch_array($resultPhieu)) {
            $duLieu = json_decode($row['du_lieu'], true);
            $traLoi = isset($duLieu['cau_tra_loi']) && is_array($duLieu['cau_tra_loi']) ? $duLieu['cau_tra_loi'] : array();

            foreach ($cauHoiTinhDiem as $id => &$thongKe) {
                if (!array_key_exists($id, $traLoi) || $traLoi[$id] === '' || $traLoi[$id] === null) continue;
                $giaTri = $traLoi[$id];
                $diem = 0;
                if ($thongKe['loai'] === 'score' && is_numeric($giaTri)) {
                    $diem = (float) $giaTri;
                } elseif ($thongKe['loai'] === 'checkbox') {
                    $cacLuaChon = is_array($giaTri) ? $giaTri : array($giaTri);
                    foreach ($cacLuaChon as $luaChon) $diem += isset($thongKe['bang_diem'][$luaChon]) ? $thongKe['bang_diem'][$luaChon] : 0;
                } else {
                    $diem = isset($thongKe['bang_diem'][$giaTri]) ? $thongKe['bang_diem'][$giaTri] : 0;
                }
                $tyLe = ($diem / $thongKe['diem_toi_da']) * 100;
                $thongKe['tong'] += $tyLe;
                $thongKe['so_phieu']++;

                $idKhoaPhong = (int) $row['id_khoaphong'];
                if (!isset($thongKe['theo_khoa_phong'][$idKhoaPhong])) {
                    $thongKe['theo_khoa_phong'][$idKhoaPhong] = array(
                        'id_khoaphong' => $idKhoaPhong,
                        'ten_khoaphong' => $row['ten_khoaphong'] !== null ? $row['ten_khoaphong'] : 'Chưa xác định',
                        'tong' => 0,
                        'so_phieu' => 0
                    );
                }
                $thongKe['theo_khoa_phong'][$idKhoaPhong]['tong'] += $tyLe;
                $thongKe['theo_khoa_phong'][$idKhoaPhong]['so_phieu']++;
            }
            unset($thongKe);
        }

        $ketQua = array();
        foreach ($cauHoiTinhDiem as $item) {
            if ($item['so_phieu'] <= 0) continue;
            $theoKhoaPhong = array();
            foreach ($item['theo_khoa_phong'] as $khoaPhong) {
                $theoKhoaPhong[] = array(
                    'id_khoaphong' => $khoaPhong['id_khoaphong'],
                    'ten_khoaphong' => $khoaPhong['ten_khoaphong'],
                    'trung_binh' => round($khoaPhong['tong'] / $khoaPhong['so_phieu'], 2),
                    'so_phieu' => $khoaPhong['so_phieu']
                );
            }
            $ketQua[] = array(
                'ky_hieu' => $item['ky_hieu'],
                'noi_dung' => $item['noi_dung'],
                'trung_binh' => round($item['tong'] / $item['so_phieu'], 2),
                'diem_toi_da' => $item['diem_toi_da'],
                'so_phieu' => $item['so_phieu'],
                'theo_khoa_phong' => $theoKhoaPhong
            );
        }
        return $ketQua;
    }

    public function getDanhSachPhieu($maChiSo)
    {
        $maChiSo = (int) $maChiSo;
        $sql = "SELECT ct.id, ct.ma_chi_so, ct.id_khoaphong, ct.nam, ct.ky,
                       ct.du_lieu, ct.created_at, ct.updated_at, cs.bieumau,
                       k.ten AS ten_khoaphong,
                       u.hoTen AS nguoi_nhap
                FROM ct_chiso ct
                LEFT JOIN chi_so_chat_luong cs ON cs.ma_chi_so = ct.ma_chi_so
                LEFT JOIN khoa k ON k.id = ct.id_khoaphong
                LEFT JOIN user u ON u.id = ct.id_user
                WHERE ct.ma_chi_so = $maChiSo
                ORDER BY ct.nam DESC, ct.ky DESC, ct.created_at DESC, ct.id DESC";
        $result = $this->dbsql->query($sql);
        $items = array();

        while ($row = $this->dbsql->fetch_array($result)) {
            $duLieu = json_decode($row['du_lieu'], true);
            if (!is_array($duLieu)) $duLieu = array();
            $giaTriTraLoi = isset($duLieu['cau_tra_loi']) && is_array($duLieu['cau_tra_loi'])
                ? $duLieu['cau_tra_loi']
                : array();
            $bieuMau = json_decode($row['bieumau'], true);
            $cauHoi = isset($bieuMau['cau_hoi']) && is_array($bieuMau['cau_hoi'])
                ? $bieuMau['cau_hoi']
                : array();
            $chiTietCauTraLoi = array();
            $idDaDung = array();

            foreach ($cauHoi as $index => $noiDungCauHoi) {
                $loaiCauHoi = isset($noiDungCauHoi['loai']) ? $noiDungCauHoi['loai'] : 'short_text';
                if (in_array($loaiCauHoi, array('category', 'subcategory'), true)) {
                    $chiTietCauTraLoi[] = array(
                        'id' => isset($noiDungCauHoi['id']) ? (string) $noiDungCauHoi['id'] : '',
                        'loai' => $loaiCauHoi,
                        'ky_hieu' => isset($noiDungCauHoi['ky_hieu']) ? $noiDungCauHoi['ky_hieu'] : '',
                        'noi_dung' => isset($noiDungCauHoi['noi_dung']) ? $noiDungCauHoi['noi_dung'] : 'Danh mục chưa có tên',
                        'gia_tri' => null
                    );
                    continue;
                }
                $idCauHoi = isset($noiDungCauHoi['id']) && $noiDungCauHoi['id'] !== ''
                    ? (string) $noiDungCauHoi['id']
                    : 'q' . ($index + 1);
                $idDaDung[$idCauHoi] = true;
                $chiTietCauTraLoi[] = array(
                    'id' => $idCauHoi,
                    'loai' => $loaiCauHoi,
                    'ky_hieu' => isset($noiDungCauHoi['ky_hieu']) ? $noiDungCauHoi['ky_hieu'] : '',
                    'noi_dung' => isset($noiDungCauHoi['noi_dung']) ? $noiDungCauHoi['noi_dung'] : 'Câu hỏi ' . ($index + 1),
                    'gia_tri' => isset($giaTriTraLoi[$idCauHoi]) ? $giaTriTraLoi[$idCauHoi] : ''
                );
            }

            foreach ($giaTriTraLoi as $idCauHoi => $giaTri) {
                if (isset($idDaDung[$idCauHoi])) continue;
                $chiTietCauTraLoi[] = array(
                    'id' => (string) $idCauHoi,
                    'loai' => 'short_text',
                    'ky_hieu' => '',
                    'noi_dung' => 'Câu hỏi ' . (count($chiTietCauTraLoi) + 1),
                    'gia_tri' => $giaTri
                );
            }

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
                    : array(),
                'chi_tiet_cau_tra_loi' => $chiTietCauTraLoi
            );
        }

        return $items;
    }

    public function xoaPhieu($id, $maChiSo)
    {
        $id = (int) $id;
        $maChiSo = (int) $maChiSo;
        if ($id <= 0 || $maChiSo <= 0) return false;

        $result = $this->dbsql->query("SELECT 1 FROM ct_chiso
            WHERE id = $id AND ma_chi_so = $maChiSo
            LIMIT 1");
        if ($this->dbsql->num_rows($result) === 0) return false;

        $this->dbsql->query("DELETE FROM ct_chiso
            WHERE id = $id AND ma_chi_so = $maChiSo");
        return true;
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
