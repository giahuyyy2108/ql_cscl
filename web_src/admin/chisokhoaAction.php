<?PHP
require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");
require_once ("web_src/bean/TinhTrangPeer.php");
require_once ("web_src/bean/CtChiSoPeer.php");

class chisokhoaAction
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
		$this->request->setTitle("Chi so theo khoa/phong");
	}

	function index()
	{
		$KCTTpeer = new DanhMucKCTTPeer();
		$PVpeer = new PhamViPeer();
		$ChuKyPeer = new ChuKyPeer();
		$donvitinhPeer = new DonViTinhPeer();
		$tinhtrangPeer = new TinhTrangPeer();

		$this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/chisokhoa/chisokhoa.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
		$this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');

		$this->request->setAttribute("listKCCT", $KCTTpeer->Get_danhmucKCTT());
		$this->request->setAttribute("listPV", $PVpeer->getPhamVi());
		$this->request->setAttribute("listCKy", $ChuKyPeer->getChuKy());
		$this->request->setAttribute("listDvt", $donvitinhPeer->getDonViTinh());
		$this->request->setAttribute("listTT", $tinhtrangPeer->getTinhTrang());
		$this->request->setAttribute("listKhoi", $this->ChiSoPeer->getListKhoi());
		$this->request->setAttribute("listKhoaPhong", $this->ChiSoPeer->getListKhoaPhongByKhoi());

		$this->request->setModel("www/chisokhoa/index.php");
		return true;
	}

	function getData()
	{
		$idKhoaPhong = $this->request->getParameter("id_khoaphong") != "" ? $this->request->getParameter("id_khoaphong") : 0;

		$data['data'] = $this->ChiSoPeer->getListByKhoa($idKhoaPhong);
		return $this->request->json_response(json_encode($data));
	}

	function getBieuDoChuKy()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0) {
			return $this->request->json_response(json_encode(array('success' => false, 'message' => 'Mã chỉ số không hợp lệ')));
		}
		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getTrungBinhTheoKy($maChiSo)
		)));
	}
}
?>
