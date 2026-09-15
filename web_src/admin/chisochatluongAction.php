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
		$this->request->setAttribute("canChoosePhamVi", $this->request->checkRole("chisochatluong.all"));
		$this->request->setAttribute("canApprove", $this->request->checkRole("chisochatluong.all"));
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
		$chiso = $this->getChiSoFromRequest();
		if ($chiso === false) {
			return $this->request->json_response(json_encode(array("message" => $this->getErrorMessage())));
		}

		$nguoigui = !empty($_SESSION["sUserID"]) ? $_SESSION["sUserID"]
			: (isset($_SESSION["sUserID"]) ? $_SESSION["sUserID"] : "");
		$chiso->set("nguoi_gui", $nguoigui);

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

	function update()
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

		// Người không có quyền toàn viện chỉ được tạo chỉ tiêu phạm vi Khoa/Phòng (1).
		// Khong tin vao gia tri pham_vi gui tu trinh duyet.
		if (!$this->request->checkRole("chisochatluong.all")) {
			$chiso->set("pham_vi", 1);
		}

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

	public function Duyet(){
		if (!$this->request->checkRole("chisochatluong.all")) {
			$message = new Message();
			$message->set("flag", false);
			$message->set("errorMessage", "Ban khong co quyen duyet chi tieu");

			return $this->request->json_response(json_encode(array(
				"success" => false,
				"message" => $message
			)));
		}

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

	public function Gui(){
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


	public function Xoa(){
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

	public function TuChoi(){
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

	public function NhapDL()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		$idUser = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
		$chiSo = $this->ChiSoPeer->getNhapLieu($maChiSo, $idUser);
		if ($maChiSo <= 0 || $idUser <= 0 || !$chiSo) {
			return $this->jsonNhapLieuError('Không tìm thấy chỉ số đã duyệt');
		}
		return $this->request->json_response(json_encode(array('success' => true, 'data' => $chiSo)));
	}

	public function LuuNhapDL()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		$nam = (int) $this->request->getParameter('nam');
		$idUser = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
		$rows = json_decode($this->request->getParameter('du_lieu', false), true);
		$chiSo = $this->ChiSoPeer->getNhapLieu($maChiSo, $idUser);
		if ($maChiSo <= 0 || $idUser <= 0 || !$chiSo) return $this->jsonNhapLieuError('Không tìm thấy chỉ số đã duyệt');
		if ($nam < 2000 || $nam > 2100 || !is_array($rows)) return $this->jsonNhapLieuError('Dữ liệu gửi lên không hợp lệ');

		$soKy = $this->getSoKy((int) $chiSo['id_chuky']);
		if (count($rows) !== $soKy) return $this->jsonNhapLieuError('Số kỳ nhập liệu không đúng với chu kỳ');
		$duLieuKy = array();
		foreach ($rows as $index => $row) {
			$tuSoRaw = trim((string) ($row['tu_so'] ?? ''));
			$mauSoRaw = trim((string) ($row['mau_so'] ?? ''));

			$tuSo = null;
			$mauSo = null;
			$value = null;

			if ($tuSoRaw !== '' && $mauSoRaw !== '') {
				$tuSo = (float) $tuSoRaw;
				$mauSo = (float) $mauSoRaw;

				if ($mauSo <	 $tuSo) {
					return $this->jsonNhapLieuError(
						'Mẫu số phải lớn hơn tử số'
					);
				}

			$value = round(($tuSo / $mauSo) * 100, 2);
			}
			$ky = $index + 1;
			$duLieuKy[] = array(
				'ky' => $ky,
				'ten_ky' => $this->getTenKy((int) $chiSo['id_chuky'], $ky),
				'tu_so' => $tuSo,
				'mau_so' => $mauSo,
				'value' => $value
			);
		}
		$allData = is_array($chiSo['dulieu']) ? $chiSo['dulieu'] : array();
		$allData[(string) $nam] = array('nam' => $nam, 'id_chuky' => (int) $chiSo['id_chuky'],
			'ten_chuky' => $chiSo['ten_chuky'], 'du_lieu' => $duLieuKy);
		$id = $this->ChiSoPeer->saveNhapLieu($maChiSo, $idUser, $allData);
		return $this->request->json_response(json_encode(array('success' => true, 'id' => $id, 'message' => 'Lưu dữ liệu chỉ số thành công')));
	}

	private function getSoKy($idChuKy)
	{
		$map = array(1 => 12, 2 => 4, 3 => 1, 4 => 2);
		return isset($map[$idChuKy]) ? $map[$idChuKy] : 1;
	}

	private function getTenKy($idChuKy, $ky)
	{
		if ($idChuKy === 1) return 'Tháng ' . $ky;
		if ($idChuKy === 2) return 'Quý ' . $ky;
		if ($idChuKy === 4) return $ky === 1 ? '6 tháng đầu năm' : '6 tháng cuối năm';
		return 'Cả năm';
	}

	private function jsonNhapLieuError($text)
	{
		return $this->request->json_response(json_encode(array('success' => false, 'message' => $text)));
	}

	public function XemDL()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		$idUser = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
		if ($maChiSo <= 0 || $idUser <= 0) {
			return $this->jsonNhapLieuError('Dữ liệu không hợp lệ');
		}

		$data = $this->ChiSoPeer->getNhapLieu($maChiSo, $idUser, false);
		if ($data && $this->request->checkRole("chisochatluong.all")) {
			$data['chart_dulieu'] = $this->ChiSoPeer->getDuLieuChartChiSo($maChiSo);
		}
		return $this->request->json_response(json_encode(array(
			'success' => (bool) $data,
			'data' => $data ? $data : null,
			'message' => $data ? '' : 'Không tìm thấy chỉ số hoặc bạn không có quyền xem'
		)));
	}
}
?>
