<?PHP
require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/ChucNangPeer.php");
require_once ("web_src/bean/NhomQuyenPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");
require_once ("web_src/bean/TinhTrangPeer.php");

class chisochatluongAction
{
	var $request;
	var $ChiSoPeer;
	var $lastErrorMessage;
	public static $listRole ="chisochatluong,save,update,gui,duyet,xoa,tuchoi";
	public function __construct()
	{
		$this->request = new Request;
		$this->ChiSoPeer = new ChiSoChatLuongPeer();
		$this->request->setTitle("Danh sach chi so");
	}

	function index()
	{
		$this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/chisochatluong/chisochatluong.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
		$this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');

		$nhomquyenPeer = new NhomQuyenPeer();
		$KCTTpeer = new DanhMucKCTTPeer();
		$PVpeer = new PhamViPeer();
		$ChuKyPeer = new ChuKyPeer();
		$donvitinhPeer = new DonViTinhPeer();
		$tinhtrangPeer = new TinhTrangPeer();

		$this->request->setAttribute("listNQ", $nhomquyenPeer->getListNQ());
		$this->request->setAttribute("listKCCT", $KCTTpeer->Get_danhmucKCTT());
		$this->request->setAttribute("listPV", $PVpeer->getPhamVi());
		$this->request->setAttribute("listCKy", $ChuKyPeer->getChuKy());
		$this->request->setAttribute("listDvt", $donvitinhPeer->getDonViTinh());
		$this->request->setAttribute("listTT", $tinhtrangPeer->getTinhTrang());
		$this->request->setAttribute("listKhoi", $this->ChiSoPeer->getListKhoi());
		$this->request->setAttribute("listKhoaPhong", $this->ChiSoPeer->getListKhoaPhongByKhoi());
		$this->request->setAttribute(
			"currentKhoaPhongId",
			$this->ChiSoPeer->getKhoaPhongIdByUserId(isset($_SESSION["sUserID"]) ? $_SESSION["sUserID"] : 0)
		);
		$this->request->setModel("www/chisochatluong/index.php");
		return true;
	}

	public function getData()
	{
		$userId = isset($_SESSION["sUserID"])
			? (int) $_SESSION["sUserID"]
			: 0;

		$coQuyenDuyet = $this->request->checkRole(
			"chisochatluong.duyet"
		);

		$idKhoaPhong = $this->ChiSoPeer
			->getKhoaPhongIdByUserId($userId);

		$data['data'] = $this->ChiSoPeer->getList(
			$coQuyenDuyet,
			$idKhoaPhong
		);

		return $this->request->json_response(
			json_encode($data)
		);
	}

	function save()
	{
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		$nguoigui = !empty($_SESSION["sUserID"]) ? $_SESSION["sUserID"]
			: (isset($_SESSION["sUserID"]) ? $_SESSION["sUserID"] : "");
		// Khoa/phong chinh luon la khoa/phong mac dinh cua nguoi tao.
		// Danh sach cac phong duoc chon duoc luu rieng trong cot `phong` dang JSON.
		$idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($nguoigui);
		if ($idKhoaPhong <= 0) {
			$this->lastErrorMessage = "Nguoi dung chua duoc gan khoa/phong mac dinh";
			return $this->request->json_response(json_encode(array(
				"success" => false,
				"message" => $this->getErrorMessage()
			)));
		}
		$chiso->set("nguoi_gui", $nguoigui);
		$chiso->set("id_khoaphong", $idKhoaPhong);
		// print_r($chiso);
		$id = $this->ChiSoPeer->Save($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Them chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	public function update()
	{
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$id = $this->ChiSoPeer->Update($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Cap nhat chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	private function getChiSoFromRequest()
	{
		$data = $this->request->getParameter('data', true);
		$arrayData = json_decode($data, true);

		if (!is_array($arrayData) || empty($arrayData[0])) {
			$this->lastErrorMessage = "Chua co du lieu";
			return false;
		}

		$chiso = new ChiSoChatLuong;
		foreach ($arrayData[0] as $key => $value) {
			if (property_exists($chiso, $key)) {
				$chiso->set($key, $value);
			}
		}

		$maPhong = $chiso->get("phong");
		if (is_string($maPhong)) {
			$phongDaGiaiMa = json_decode($maPhong, true);
			$maPhong = is_array($phongDaGiaiMa) ? $phongDaGiaiMa : array();
		}

		if (!is_array($maPhong) || empty($maPhong)) {
			$maPhong = array($chiso->get("id_khoaphong"));
		}

		if ((int) $chiso->get("pham_vi") === 3) {
			$maPhong = array();
			foreach ($this->ChiSoPeer->getListKhoaPhong() as $khoaPhong) {
				$maPhong[] = (int) $khoaPhong['id'];
			}
		}

		$maPhong = array_values(array_unique(array_filter(array_map('intval', $maPhong))));
		$chiso->set("phong", json_encode($maPhong));
		$chiso->set("id_khoaphong", !empty($maPhong) ? $maPhong[0] : 0);

		if (trim($chiso->get("ten_chi_so")) === "") {
			$this->lastErrorMessage = "Ten chi so khong duoc de trong";
			return false;
		}

		return $chiso;
	}

	private function getErrorMessage()
	{
		$message = new Message();
		$message->set("flag", false);
		$message->set("errorMessage", $this->lastErrorMessage);
		return $message;
	}

	public function duyet(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$nguoiDuyet = !empty($_SESSION["sUserID"]) ? $_SESSION["sUserID"]
			: (isset($_SESSION["sUserID"]) ? $_SESSION["sUserID"] : "");
		$chiso->set("nguoi_duyet", $nguoiDuyet);

		$id = $this->ChiSoPeer->Duyet($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Duyet chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	public function gui(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$id = $this->ChiSoPeer->Gui($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Gui chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}


	public function xoa(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$id = $this->ChiSoPeer->Xoa($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Xoa chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}

	public function tuchoi(){
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		if ((int) $chiso->get("ma_chi_so") <= 0) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Thieu ma chi so can cap nhat");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		if (trim((string) $chiso->get("ly_do_tu_choi")) === "") {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Vui long nhap ly do tu choi");
			return $this->request->json_response(json_encode(array(
				"success" => false,
				"message" => $message
			)));
		}

		$id = $this->ChiSoPeer->TuChoi($chiso);
		$message = new Message();
		$message->set("flag", true);
		$message->set("successMessage", "Tu choi chi tieu thanh cong");

		return $this->request->json_response(json_encode(array(
			"success" => true,
			"id" => $id,
			"message" => $message
		)));
	}
}
?>
