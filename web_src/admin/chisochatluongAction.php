<?PHP
require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/ChucNangPeer.php");
require_once ("web_src/bean/NhomQuyenPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");

class chisochatluongAction
{
	var $request;
	var $ChiSoPeer;

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

		$this->request->setAttribute("listNQ", $nhomquyenPeer->getListNQ());
		$this->request->setAttribute("listKCCT", $KCTTpeer->Get_danhmucKCTT());
		$this->request->setAttribute("listPV", $PVpeer->getPhamVi());
		$this->request->setAttribute("listCKy", $ChuKyPeer->getChuKy());
		$this->request->setAttribute("listDvt", $donvitinhPeer->getDonViTinh());
		$this->request->setModel("www/chisochatluong/index.php");
		return true;
	}

	function getData()
	{
		$data['data'] = $this->ChiSoPeer->getList();
		return $this->request->json_response(json_encode($data));
	}

	function save()
	{
		$data = $this->request->getParameter('data', true);
		$arrayData = json_decode($data, true);

		if (!is_array($arrayData) || empty($arrayData[0])) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Chua co du lieu");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

		$chiso = new ChiSoChatLuong;
		foreach ($arrayData[0] as $key => $value) {
			if (property_exists($chiso, $key)) {
				$chiso->set($key, $value);
			}
		}

		if (trim($chiso->get("ten_chi_so")) === "") {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Ten chi so khong duoc de trong");
			return $this->request->json_response(json_encode(array("message" => $message)));
		}

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
}
?>
