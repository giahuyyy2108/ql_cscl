<?PHP
class ChiSoChatLuong
{
	var $ma_chi_so;
	var $ten_chi_so;
	var $ma_khia_canh;
	var $ma_thanh_to;
	var $nhom_chi_so;
	var $pham_vi;
	var $muc_tieu;
	var $nguong_canh_bao;
	var $don_vi_tinh;
	var $id_chuky;
	var $du_lieu_chu_ky;
	var $loai_cong_thuc;
	var $cong_thuc;
	var $trang_thai;
	var $nguoi_gui;
	var $thoi_gian_gui;
	var $nguoi_duyet;
	var $thoi_gian_duyet;
	var $ly_do_tu_choi;
	var $dinh_nghia;
	var $thu_thap;
	var $ten_tu_so;
	var $ten_mau_so;
	var $created_at;
	var $updated_at;

	function __construct()
	{
		$this->ma_chi_so = 0;
		$this->ten_chi_so = "";
		$this->ma_khia_canh = 0;
		$this->ma_thanh_to = 0;
		$this->nhom_chi_so = "";
		$this->pham_vi = "";
		$this->muc_tieu = "";
		$this->nguong_canh_bao = "";
		$this->don_vi_tinh = "";
		$this->id_chuky = 0;
		$this->du_lieu_chu_ky = "";
		$this->loai_cong_thuc = "";
		$this->cong_thuc = "";
		$this->trang_thai = "";
		$this->nguoi_gui = "";
		$this->thoi_gian_gui = "";
		$this->nguoi_duyet = "";
		$this->thoi_gian_duyet = "";
		$this->ly_do_tu_choi = "";
		$this->dinh_nghia = "";
		$this->thu_thap = "";
		$this->ten_tu_so = "";
		$this->ten_mau_so = "";
		$this->created_at = "";
		$this->updated_at = "";
	}

	function set($key, $value)
	{
		$this->$key = $value;
	}

	function get($key)
	{
		return $this->$key;
	}

}
?>
