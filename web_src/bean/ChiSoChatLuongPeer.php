<?PHP

require_once ("web_src/bean/ChiSoChatLuong.php");
require_once ("web_src/bean/TinhTrang.php");
require_once ("web_src/bean/User.php");
require_once ("web_src/bean/UserPeer.php");

class ChiSoChatLuongPeer
{
	var $dbsql;

	function __construct()
	{
		$this->dbsql = new db_mysql;
		$this->dbsql->connect();
		// $this->dbsql->selectdb();
	}

	function Set_chiso($result)
	{
		$chiso = new ChiSoChatLuong;
		$tinhtrang = new TinhTrang;
		$nguoigui = new User;
		$nguoiduyet = new User;
        $userPeer = new UserPeer();

		$tinhtrang->set("maTrangThai", $result["trang_thai"]);
		$tinhtrang->set("tenTrangThai", isset($result["tenTrangThai"]) ? $result["tenTrangThai"] : "");
		$tinhtrang->set("tag", isset($result["tag"]) ? $result["tag"] : "");

        $nguoigui =  $userPeer->getUserID($result['nguoi_gui']);
        $nguoiduyet =  $userPeer->getUserID($result['nguoi_duyet']);

        $chiso->set("ma_chi_so", $result["ma_chi_so"]);
        $chiso->set("ten_chi_so", $result["ten_chi_so"]);
        $chiso->set("ma_khia_canh", $result["ma_khia_canh"]);
        $chiso->set("ma_thanh_to", $result["ma_thanh_to"]);
        $chiso->set("nhom_chi_so", $result["nhom_chi_so"]);
        $chiso->set("pham_vi", $result["pham_vi"]);
        $chiso->set("muc_tieu", $result["muc_tieu"]);
        $chiso->set("nguong_canh_bao", $result["nguong_canh_bao"]);
        $chiso->set("id_donvitinh", $result["id_donvitinh"]);
        $chiso->set("id_chuky", $result["id_chuky"]);
        // $chiso->set("du_lieu_chu_ky", $result["du_lieu_chu_ky"]);
        $chiso->set("loai_cong_thuc", $result["loai_cong_thuc"]);
        $chiso->set("cong_thuc", $result["cong_thuc"]);
        $chiso->set("trang_thai", $tinhtrang);
        $chiso->set("nguoi_gui", $nguoigui);
        $chiso->set("thoi_gian_gui", $result["thoi_gian_gui"]);
        $chiso->set("nguoi_duyet", $nguoiduyet);
        $chiso->set("thoi_gian_duyet", $result["thoi_gian_duyet"]);
        $chiso->set("ly_do_tu_choi", $result["ly_do_tu_choi"]);
        $chiso->set("dinh_nghia", $result["dinh_nghia"]);
        $chiso->set("thu_thap", $result["thu_thap"]);
        $chiso->set("ten_tu_so", $result["ten_tu_so"]);
        $chiso->set("ten_mau_so", $result["ten_mau_so"]);
        $chiso->set("created_at", $result["created_at"]);
        $chiso->set("updated_at", $result["updated_at"]);

		return $chiso;
	}


    function GetLisT(){
        // Nguoi co quyen chisochatluong.all duoc xem tat ca chi tieu.
        // Nguoi khac chi xem chi tieu cua minh va chi tieu pham vi 3.
        $userId = isset($_SESSION["sUserID"]) ? (int) $_SESSION["sUserID"] : 0;
        $roles = isset($_SESSION["quyen"]) ? $_SESSION["quyen"] : array();
        if (!is_array($roles)) {
            $roles = $roles === "" ? array() : explode(",", $roles);
        }

        $hasAllPermission = in_array("chisochatluong.all", $roles, true);

        $where = $hasAllPermission
            ? " WHERE (cs.trang_thai != 0 OR cs.nguoi_gui = " . $userId . ")"
            : " WHERE (cs.nguoi_gui = " . $userId . " OR cs.pham_vi = 3)";

		$sql_select = "SELECT cs.*, tt.tenTrangThai, tt.tag
                       FROM chi_so_chat_luong cs
                       LEFT JOIN trangthai tt ON tt.maTrangThai = cs.trang_thai
                       " . $where . "
                       ORDER BY cs.ma_chi_so DESC";

        // echo($sql_select);
		$result= $this->dbsql->query($sql_select);

		
        $arrList = [];
		$i = 0;
		while ($row = $this->dbsql->fetch_Array($result)) {
			$arrList[$i] = $this->Set_chiso($row);
			$i++;
		}
		return $arrList;
    }

    public function getListChiSoUser($idUser)
    {
        $idUser = (int) $idUser;
        $sql = "SELECT cs.*, tt.tenTrangThai, tt.tag,
                       ck.ten AS ten_chuky, dvt.ten AS ten_don_vi_tinh,
                       u_tao.hoTen AS ten_khoa_phong
                FROM chi_so_chat_luong cs
                INNER JOIN user u_tao ON u_tao.id = cs.nguoi_gui
                LEFT JOIN trangthai tt ON tt.maTrangThai = cs.trang_thai
                LEFT JOIN chuky ck ON ck.id = cs.id_chuky
                LEFT JOIN donvitinh dvt ON dvt.id = cs.id_donvitinh
                WHERE cs.pham_vi = 1
                  AND cs.nguoi_gui = " . $idUser . "
                  AND cs.trang_thai = 2
                ORDER BY cs.ma_chi_so DESC";
        $result = $this->dbsql->query($sql);
        $items = array();
        while ($row = $this->dbsql->fetch_array($result)) {
            $items[] = $row;
        }
        return $items;
    }

    public function isChiSoPhamViUser($maChiSo, $idUser)
    {
        $result = $this->dbsql->query(
            "SELECT ma_chi_so FROM chi_so_chat_luong WHERE ma_chi_so=" . (int) $maChiSo .
            " AND nguoi_gui=" . (int) $idUser . " AND pham_vi=1 LIMIT 1"
        );
        return $this->dbsql->num_rows($result) > 0;
    }

    function Save($_chisochatluong){
        $value = function ($key) use ($_chisochatluong) {
            return "'" . addslashes((string) $_chisochatluong->get($key)) . "'";
        };
        $number = function ($key) use ($_chisochatluong) {
            return (int) $_chisochatluong->get($key);
        };

        $sql = "INSERT INTO `chi_so_chat_luong`
            (`ten_chi_so`, `ma_khia_canh`, `ma_thanh_to`, `nhom_chi_so`,
             `pham_vi`, `muc_tieu`, `nguong_canh_bao`, `id_donvitinh`,
             `id_chuky`, `loai_cong_thuc`, `cong_thuc`, `trang_thai`,
             `nguoi_gui`, `thoi_gian_gui`, `nguoi_duyet`, `thoi_gian_duyet`,
             `ly_do_tu_choi`, `dinh_nghia`, `thu_thap`, `ten_tu_so`, `ten_mau_so`)
            VALUES (" . $value('ten_chi_so') . ",
                    " . $number('ma_khia_canh') . ",
                    " . $number('ma_thanh_to') . ",
                    " . $value('nhom_chi_so') . ",
                    " . $number('pham_vi') . ",
                    " . $value('muc_tieu') . ",
                    " . $value('nguong_canh_bao') . ",
                    " . $number('id_donvitinh') . ",
                    " . $number('id_chuky') . ",
                    " . $value('loai_cong_thuc') . ",
                    " . $value('cong_thuc') . ",
                    0, " . $value('nguoi_gui') . ", NULL, NULL, NULL, '',
                    " . $value('dinh_nghia') . ", " . $value('thu_thap') . ",
                    " . $value('ten_tu_so') . ", " . $value('ten_mau_so') . ")";

        $this->dbsql->query($sql);
        return $this->dbsql->insert_id();
    }

    function Update($_chisochatluong){
        $value = function ($key) use ($_chisochatluong) {
            return "'" . addslashes((string) $_chisochatluong->get($key)) . "'";
        };
        $number = function ($key) use ($_chisochatluong) {
            return (int) $_chisochatluong->get($key);
        };

        $roles = isset($_SESSION["quyen"]) && is_array($_SESSION["quyen"])
            ? $_SESSION["quyen"] : array();
        $canChoosePhamVi = (isset($_SESSION["AdminType"]) && (int) $_SESSION["AdminType"] === 1)
            || in_array("chisochatluong.all", $roles, true);
        $phamViUpdate = $canChoosePhamVi
            ? "                    `pham_vi` = " . $number('pham_vi') . ",\n"
            : "";

        $sql = "UPDATE `chi_so_chat_luong` SET
                    `ten_chi_so` = " . $value('ten_chi_so') . ",
                    `ma_khia_canh` = " . $number('ma_khia_canh') . ",
                    `ma_thanh_to` = " . $number('ma_thanh_to') . ",
                    `nhom_chi_so` = " . $value('nhom_chi_so') . ",
" . $phamViUpdate . "
                    `muc_tieu` = " . $value('muc_tieu') . ",
                    `nguong_canh_bao` = " . $value('nguong_canh_bao') . ",
                    `id_donvitinh` = " . $number('id_donvitinh') . ",
                    `id_chuky` = " . $number('id_chuky') . ",
                    `dinh_nghia` = " . $value('dinh_nghia') . ",
                    `thu_thap` = " . $value('thu_thap') . ",
                    `ten_tu_so` = " . $value('ten_tu_so') . ",
                    `ten_mau_so` = " . $value('ten_mau_so') . ",
                    `updated_at` = NOW()
                WHERE `ma_chi_so` = " . $number('ma_chi_so');

        $this->dbsql->query($sql);
        return $_chisochatluong->get("ma_chi_so");
    }

    //Trang thai
    // 0 nháp
    // 1 chờ duyệt (gửi) 
    // 2 đã duyệt 
    // 3 từ chối 

    public function Duyet($_chisochatluong){
        $value = function ($key) use ($_chisochatluong) {
            return "'" . addslashes((string) $_chisochatluong->get($key)) . "'";
        };
        $number = function ($key) use ($_chisochatluong) {
            return (int) $_chisochatluong->get($key);
        };

        $sql = "UPDATE `chi_so_chat_luong` SET
                        `trang_thai` = 2,
                        `nguoi_duyet` = " . $value('nguoi_duyet') . ",
                        `thoi_gian_duyet` = NOW(),
                        `updated_at` = NOW()
                WHERE `ma_chi_so` = " . $number('ma_chi_so');

        $this->dbsql->query($sql);
        return $_chisochatluong->get("ma_chi_so");
    }

    public function Gui($_chisochatluong){
        $value = function ($key) use ($_chisochatluong) {
            return "'" . addslashes((string) $_chisochatluong->get($key)) . "'";
        };
        $number = function ($key) use ($_chisochatluong) {
            return (int) $_chisochatluong->get($key);
        };

        $sql = "UPDATE `chi_so_chat_luong` SET
                        `trang_thai` = 1,
                        `updated_at` = NOW()
                WHERE `ma_chi_so` = " . $number('ma_chi_so');

        $this->dbsql->query($sql);
        return $_chisochatluong->get("ma_chi_so");
    }

    public function Xoa($_chisochatluong){
        $value = function ($key) use ($_chisochatluong) {
            return "'" . addslashes((string) $_chisochatluong->get($key)) . "'";
        };
        $number = function ($key) use ($_chisochatluong) {
            return (int) $_chisochatluong->get($key);
        };

        $sql = "DELETE FROM `chi_so_chat_luong` 
                WHERE `chi_so_chat_luong`.`ma_chi_so` = ". $value('ma_chi_so').";";
;

        $this->dbsql->query($sql);
        return $_chisochatluong->get("ma_chi_so");
    }

    public function TuChoi($_chisochatluong){
        $value = function ($key) use ($_chisochatluong) {
            return "'" . addslashes((string) $_chisochatluong->get($key)) . "'";
        };
        $number = function ($key) use ($_chisochatluong) {
            return (int) $_chisochatluong->get($key);
        };

        $sql = "UPDATE `chi_so_chat_luong` SET
                        `trang_thai` = 3,
                        `updated_at` = NOW()
                WHERE `ma_chi_so` = " . $number('ma_chi_so');

        $this->dbsql->query($sql);
        return $_chisochatluong->get("ma_chi_so");
    }

    public function saveNhapLieu($maChiSo, $idUser, $duLieu)
    {
        global $connect;
        $maChiSo = (int) $maChiSo;
        $idUser = (int) $idUser;
        $json = mysqli_real_escape_string($connect,
            json_encode($duLieu, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $result = $this->dbsql->query(
            "SELECT id FROM ct_chiso WHERE ma_chi_so = " . $maChiSo .
            " AND id_user = " . $idUser . " ORDER BY id DESC LIMIT 1"
        );
        $row = $this->dbsql->fetch_array($result);
        if ($row) {
            $this->dbsql->query("UPDATE ct_chiso SET dulieu='" . $json . "' WHERE id=" . (int) $row['id']);
            return (int) $row['id'];
        }
        $this->dbsql->query(
            "INSERT INTO ct_chiso (ma_chi_so,id_user,dulieu) VALUES (" .
            $maChiSo . "," . $idUser . ",'" . $json . "')"
        );
        return $this->dbsql->insert_id();
    }

    public function getNhapLieu($maChiSo, $idUser, $chiLayDaDuyet = true)
    {
        $maChiSo = (int) $maChiSo;
        $idUser = (int) $idUser;
        $roles = isset($_SESSION['quyen']) && is_array($_SESSION['quyen'])
            ? $_SESSION['quyen'] : array();
        $hasAllPermission = (isset($_SESSION['AdminType']) && (int) $_SESSION['AdminType'] === 1)
            || in_array('chisochatluong.all', $roles, true);
        $accessSql = $hasAllPermission
            ? ''
            : " AND (
                    cs.nguoi_gui = " . $idUser . "
                    OR cs.pham_vi = 3
                    OR cs.nguoi_gui IN (
                        SELECT u_all.id
                        FROM user u_all
                        LEFT JOIN nhomquyen nq_all ON nq_all.maNQ = u_all.maNQ
                        WHERE FIND_IN_SET('chisochatluong.all', REPLACE(COALESCE(u_all.quyen, ''), ' ', '')) > 0
                           OR FIND_IN_SET('chisochatluong.all', REPLACE(COALESCE(nq_all.quyen, ''), ' ', '')) > 0
                    )
                )";
        $approvedSql = $chiLayDaDuyet ? " AND cs.trang_thai = 2" : "";

        $sql = "
            SELECT
                cs.*,
                ck.ten AS ten_chuky,
                kc.ten AS ten_khia_canh,
                tt.ten AS ten_thanh_to,
                pv.ten AS ten_pham_vi,
                dvt.ten AS ten_don_vi_tinh,
                ct.dulieu
            FROM chi_so_chat_luong cs
            INNER JOIN chuky ck
                ON ck.id = cs.id_chuky
            LEFT JOIN danhmuc_kctt kc
                ON kc.id = cs.ma_khia_canh
            LEFT JOIN danhmuc_kctt tt
                ON tt.id = cs.ma_thanh_to
            LEFT JOIN phamvi pv
                ON pv.id = cs.pham_vi
            LEFT JOIN donvitinh dvt
                ON dvt.id = cs.id_donvitinh
            LEFT JOIN ct_chiso ct
                ON ct.ma_chi_so = cs.ma_chi_so
                AND ct.id_user = " . $idUser . "
            WHERE cs.ma_chi_so = " . $maChiSo . $approvedSql . $accessSql . "
            ORDER BY ct.id DESC
            LIMIT 1
        ";

        $result = $this->dbsql->query($sql);
        $row = $this->dbsql->fetch_array($result);

        if (!$row) {
            return false;
        }

        $row['dulieu'] = !empty($row['dulieu'])
            ? json_decode($row['dulieu'], true)
            : array();

        return $row;
    }

    /** Du lieu bieu do cua tat ca khoa/phong theo ma chi so. */
    public function getDuLieuChartChiSo($maChiSo)
    {
        $maChiSo = (int) $maChiSo;
        if ($maChiSo <= 0) return array();

        $sql = "SELECT ct.id_user, ct.dulieu, u.hoTen, u.username
                FROM ct_chiso ct
                LEFT JOIN user u ON u.id = ct.id_user
                WHERE ct.ma_chi_so = " . $maChiSo . "
                  AND ct.id = (
                      SELECT MAX(ct_moi.id)
                      FROM ct_chiso ct_moi
                      WHERE ct_moi.ma_chi_so = ct.ma_chi_so
                        AND ct_moi.id_user = ct.id_user
                  )
                ORDER BY u.hoTen ASC, ct.id ASC";
        $result = $this->dbsql->query($sql);
        $items = array();
        while ($row = $this->dbsql->fetch_array($result)) {
            $dulieu = !empty($row['dulieu']) ? json_decode($row['dulieu'], true) : array();
            $items[] = array(
                'id_user' => (int) $row['id_user'],
                'ten_user' => !empty($row['hoTen']) ? $row['hoTen'] : $row['username'],
                'dulieu' => is_array($dulieu) ? $dulieu : array()
            );
        }
        return $items;
    }

    /** Danh sach chi so theo thang va trang thai nhap cua user trong thang hien tai. */
    public function getChiTieuThang($idUser, $nam, $thang)
    {
        $idUser = (int) $idUser;
        $nam = (int) $nam;
        $thang = (int) $thang;
        $dauThangSau = date('Y-m-01', strtotime(sprintf('%04d-%02d-01 +1 month', $nam, $thang)));
        $quy = (int) ceil($thang / 3);
        $thangDauQuySau = $quy * 3 + 1;
        $namDauQuySau = $nam;
        if ($thangDauQuySau > 12) {
            $thangDauQuySau = 1;
            $namDauQuySau++;
        }
        $dauQuySau = sprintf('%04d-%02d-01', $namDauQuySau, $thangDauQuySau);
        $roles = isset($_SESSION['quyen']) && is_array($_SESSION['quyen'])
            ? $_SESSION['quyen'] : array();
        $accessSql = in_array('chisochatluong.all', $roles, true)
            ? ''
            : " AND (
                    cs.nguoi_gui = " . $idUser . "
                    OR cs.pham_vi = 3
                    OR cs.nguoi_gui IN (
                        SELECT u_all.id
                        FROM user u_all
                        LEFT JOIN nhomquyen nq_all ON nq_all.maNQ = u_all.maNQ
                        WHERE FIND_IN_SET('chisochatluong.all', REPLACE(COALESCE(u_all.quyen, ''), ' ', '')) > 0
                           OR FIND_IN_SET('chisochatluong.all', REPLACE(COALESCE(nq_all.quyen, ''), ' ', '')) > 0
                    )
                )";

        $sql = "SELECT cs.ma_chi_so, cs.ten_chi_so, cs.muc_tieu,
                       cs.nguong_canh_bao, cs.ten_tu_so, cs.ten_mau_so,
                       cs.pham_vi, cs.id_chuky, ck.ten AS ten_chuky,
                       pv.ten AS ten_pham_vi, dvt.ten AS ten_don_vi_tinh,
                       (SELECT ct.dulieu FROM ct_chiso ct
                        WHERE ct.ma_chi_so = cs.ma_chi_so AND ct.id_user = " . $idUser . "
                        ORDER BY ct.id DESC LIMIT 1) AS dulieu
                FROM chi_so_chat_luong cs
                LEFT JOIN chuky ck ON ck.id = cs.id_chuky
                LEFT JOIN donvitinh dvt ON dvt.id = cs.id_donvitinh
                LEFT JOIN phamvi pv ON pv.id = cs.pham_vi
                WHERE cs.trang_thai = 2
                  AND cs.id_chuky IN (1, 2)
                  AND (
                        (cs.id_chuky = 1 AND COALESCE(cs.thoi_gian_duyet, cs.created_at) < '" . $dauThangSau . "')
                        OR
                        (cs.id_chuky = 2 AND COALESCE(cs.thoi_gian_duyet, cs.created_at) < '" . $dauQuySau . "')
                  )" . $accessSql . "
                ORDER BY cs.ten_chi_so ASC";
        $result = $this->dbsql->query($sql);
        $items = array();
        while ($row = $this->dbsql->fetch_array($result)) {
            $json = !empty($row['dulieu']) ? json_decode($row['dulieu'], true) : array();
            $ky = (int) $row['id_chuky'] === 2 ? (int) ceil($thang / 3) : $thang;
            $duLieuThang = null;
            if (is_array($json) && isset($json[(string) $nam]['du_lieu'][$ky - 1])) {
                $duLieuThang = $json[(string) $nam]['du_lieu'][$ky - 1];
            }
            $row['ten_ky_hien_tai'] = (int) $row['id_chuky'] === 2
                ? 'Quý ' . $ky : 'Tháng ' . $ky;
            $row['du_lieu_thang'] = $duLieuThang;
            $row['da_nhap'] = is_array($duLieuThang)
                && array_key_exists('tu_so', $duLieuThang)
                && array_key_exists('mau_so', $duLieuThang)
                && $duLieuThang['tu_so'] !== null && $duLieuThang['tu_so'] !== ''
                && $duLieuThang['mau_so'] !== null && $duLieuThang['mau_so'] !== '';
            unset($row['dulieu']);
            $items[] = $row;
        }
        usort($items, function ($a, $b) {
            if ($a['da_nhap'] === $b['da_nhap']) return 0;
            return $a['da_nhap'] ? 1 : -1;
        });
        return $items;
    }

    /** Cap nhat ky thang/quy duoc chon, giu nguyen du lieu cac ky khac. */
    public function saveChiTieuThang($maChiSo, $idUser, $nam, $thang, $tuSo, $mauSo)
    {
        $chiSo = $this->getNhapLieu($maChiSo, $idUser);
        if (!$chiSo || !in_array((int) $chiSo['id_chuky'], array(1, 2), true)) return false;
        $idChuKy = (int) $chiSo['id_chuky'];
        $soKy = $idChuKy === 2 ? 4 : 12;
        $ky = $idChuKy === 2 ? (int) ceil($thang / 3) : (int) $thang;
        $tenKy = $idChuKy === 2 ? 'Quý ' . $ky : 'Tháng ' . $ky;
        $ngayDuyet = !empty($chiSo['thoi_gian_duyet'])
            ? $chiSo['thoi_gian_duyet'] : $chiSo['created_at'];
        $namDuyet = (int) date('Y', strtotime($ngayDuyet));
        $thangDuyet = (int) date('n', strtotime($ngayDuyet));
        $kyDuyet = $idChuKy === 2 ? (int) ceil($thangDuyet / 3) : $thangDuyet;
        if ((int) $nam < $namDuyet || ((int) $nam === $namDuyet && $ky < $kyDuyet)) return false;

        $allData = is_array($chiSo['dulieu']) ? $chiSo['dulieu'] : array();
        if (!isset($allData[(string) $nam]) || !is_array($allData[(string) $nam])) {
            $allData[(string) $nam] = array(
                'nam' => (int) $nam,
                'id_chuky' => $idChuKy,
                'ten_chuky' => $chiSo['ten_chuky'],
                'du_lieu' => array()
            );
        }
        if (!isset($allData[(string) $nam]['du_lieu']) || !is_array($allData[(string) $nam]['du_lieu'])) {
            $allData[(string) $nam]['du_lieu'] = array();
        }
        $allData[(string) $nam]['id_chuky'] = $idChuKy;
        $allData[(string) $nam]['ten_chuky'] = $chiSo['ten_chuky'];
        for ($i = 0; $i < $soKy; $i++) {
            if (!isset($allData[(string) $nam]['du_lieu'][$i])) {
                $allData[(string) $nam]['du_lieu'][$i] = array(
                    'ky' => $i + 1,
                    'ten_ky' => $idChuKy === 2 ? 'Quý ' . ($i + 1) : 'Tháng ' . ($i + 1),
                    'tu_so' => null, 'mau_so' => null, 'value' => null
                );
            }
        }
        $allData[(string) $nam]['du_lieu'][$ky - 1] = array(
            'ky' => $ky,
            'ten_ky' => $tenKy,
            'tu_so' => (float) $tuSo,
            'mau_so' => (float) $mauSo,
            'value' => round(((float) $tuSo / (float) $mauSo) * 100, 2)
        );
        return $this->saveNhapLieu($maChiSo, $idUser, $allData);
    }
}
?>
