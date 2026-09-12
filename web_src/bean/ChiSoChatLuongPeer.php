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
        // tao cau truy van		

		$sql_select = "SELECT cs.*, tt.tenTrangThai, tt.tag
                       FROM chi_so_chat_luong cs
                       LEFT JOIN trangthai tt ON tt.maTrangThai = cs.trang_thai
                       ORDER BY cs.ma_chi_so DESC";

		$result= $this->dbsql->query($sql_select);

		
        $arrList = [];
		$i = 0;
		while ($row = $this->dbsql->fetch_Array($result)) {
			$arrList[$i] = $this->Set_chiso($row);
			$i++;
		}
		return $arrList;
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

        $sql = "UPDATE `chi_so_chat_luong` SET
                    `ten_chi_so` = " . $value('ten_chi_so') . ",
                    `ma_khia_canh` = " . $number('ma_khia_canh') . ",
                    `ma_thanh_to` = " . $number('ma_thanh_to') . ",
                    `nhom_chi_so` = " . $value('nhom_chi_so') . ",
                    `pham_vi` = " . $number('pham_vi') . ",
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
}
?>
