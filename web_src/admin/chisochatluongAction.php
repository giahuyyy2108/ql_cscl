<?PHP
require_once ("web_src/bean/ChiSoChatLuongPeer.php");
require_once ("web_src/bean/ChucNangPeer.php");
require_once ("web_src/bean/NhomQuyenPeer.php");
require_once ("web_src/bean/DanhMucKCTTPeer.php");
require_once ("web_src/bean/PhamViPeer.php");
require_once ("web_src/bean/ChuKyPeer.php");
require_once ("web_src/bean/DonviTinhPeer.php");
require_once ("web_src/bean/TinhTrangPeer.php");
require_once ("web_src/bean/CtChiSoPeer.php");

class chisochatluongAction
{
	var $request;
	var $ChiSoPeer;
	var $CtChiSoPeer;
	var $lastErrorMessage;
  	public static $listRole ="chisochatluong,save,update,gui,duyet,xoa,tuchoi,khoaA,khoaB";
	public function __construct()
	{
		$this->request = new Request;
		$this->ChiSoPeer = new ChiSoChatLuongPeer();
		$this->CtChiSoPeer = new CtChiSoPeer();
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
			$idKhoaPhong,
			$userId
		);

		return $this->request->json_response(
			json_encode($data)
		);
	}

	public function getBieuDoChuKy()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0 || !$this->coQuyenXemChiSo($maChiSo)) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'message' => 'Mã chỉ số không hợp lệ'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getTrungBinhTheoKy($maChiSo),
			'cau_hoi' => $this->CtChiSoPeer->getTrungBinhTheoCauHoi($maChiSo)
		)));
	}

	public function getDanhSachPhieu()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		if ($maChiSo <= 0 || !$this->coQuyenXemChiSo($maChiSo)) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'data' => array(),
				'message' => 'Mã chỉ số không hợp lệ'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'data' => $this->CtChiSoPeer->getDanhSachPhieu($maChiSo)
		)));
	}

	private function coQuyenXemChiSo($maChiSo)
	{
		return $this->getChiSoDuocXem($maChiSo) !== false;
	}

	private function getChiSoDuocXem($maChiSo)
	{
		$userId = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
		$coQuyenDuyet = $this->request->checkRole('chisochatluong.duyet');
		$idKhoaPhong = $this->ChiSoPeer->getKhoaPhongIdByUserId($userId);
		$danhSach = $this->ChiSoPeer->getList($coQuyenDuyet, $idKhoaPhong, $userId);

		foreach ($danhSach as $chiSo) {
			if ((int) $chiSo->get('ma_chi_so') === (int) $maChiSo) {
				return $chiSo;
			}
		}
		return false;
	}

	public function xuatExcel()
	{
		$maChiSo = (int) $this->request->getParameter('ma_chi_so');
		$chiSo = $this->getChiSoDuocXem($maChiSo);
		if ($maChiSo <= 0 || $chiSo === false) {
			header('HTTP/1.1 403 Forbidden');
			echo 'Bạn không có quyền xuất chỉ số này.';
			exit;
		}

		$escape = function ($value) {
			return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
		};
		$thongKe = $this->CtChiSoPeer->getTrungBinhTheoCauHoi($maChiSo);
		$tenFile = trim((string) $chiSo->get('ten_chi_so'));
		$tenFile = preg_replace('/[\\\\\/:*?"<>|\x00-\x1F]+/u', '_', $tenFile);
		$tenFile = trim($tenFile, " ._\t\n\r\0\x0B");
		if ($tenFile === '') $tenFile = 'chi-so-' . $maChiSo;
		$tenFileDayDu = $tenFile . '.xls';

		if (ob_get_length()) ob_end_clean();
		header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
		header('Content-Disposition: attachment; filename="chi-so-' . $maChiSo . '.xls"; filename*=UTF-8\'\'' . rawurlencode($tenFileDayDu));
		header('Cache-Control: max-age=0');
		echo "\xEF\xBB\xBF";
		echo '<html><head><meta charset="UTF-8"><style>'
			. 'body,table,th,td{font-family:"Times New Roman",Times,serif;font-size:14pt}'
			. 'table{border-collapse:collapse;width:100%;table-layout:fixed}th,td{border:1px solid #777;padding:3px 6px;height:18.75pt;vertical-align:top;white-space:normal}'
			. 'th{background:#fff;color:#000;font-weight:bold;text-align:center}.label{font-weight:bold;background:#eee}.spacer{border:0;height:10.8pt}'
			. '</style></head><body>';
		echo '<table><colgroup><col style="width:19%"><col style="width:19%"><col style="width:24%"><col style="width:23%"><col style="width:15%"></colgroup>';
		echo '<tr><th colspan="5">THÔNG TIN CHỈ SỐ CHẤT LƯỢNG</th></tr>';
		$thongTin = array(
			'Mã chỉ số' => $chiSo->get('ma_chi_so'), 'Tên chỉ số' => $chiSo->get('ten_chi_so'),
			'Mục tiêu' => $chiSo->get('muc_tieu'), 'Ngưỡng cảnh báo' => $chiSo->get('nguong_canh_bao'),
			'Định nghĩa' => $chiSo->get('dinh_nghia'), 'Phương pháp thu thập' => $chiSo->get('thu_thap'),
			'Tên tử số' => $chiSo->get('ten_tu_so'), 'Tên mẫu số' => $chiSo->get('ten_mau_so')
		);
		foreach ($thongTin as $nhan => $giaTri) echo '<tr><td class="label">' . $escape($nhan) . '</td><td colspan="4">' . $escape($giaTri) . '</td></tr>';
		echo '<tr><td colspan="5" class="spacer"></td></tr>';
		echo '<tr><th colspan="5">THỐNG KÊ CÂU HỎI</th></tr>';
		echo '<tr><th>Ký hiệu</th><th>Nội dung câu hỏi</th><th>Khoa/Phòng</th><th>Tỷ lệ trung bình (%)</th><th>Số phiếu</th></tr>';
		foreach ($thongKe as $item) {
			echo '<tr><td>' . $escape($item['ky_hieu']) . '</td><td>' . $escape($item['noi_dung']) . '</td><td>Tổng số phiếu</td><td>' . $escape($item['trung_binh']) . '</td><td>' . $escape($item['so_phieu']) . '</td></tr>';
			foreach ($item['theo_khoa_phong'] as $khoaPhong) echo '<tr><td>' . $escape($item['ky_hieu']) . '</td><td>' . $escape($item['noi_dung']) . '</td><td>' . $escape($khoaPhong['ten_khoaphong']) . '</td><td>' . $escape($khoaPhong['trung_binh']) . '</td><td>' . $escape($khoaPhong['so_phieu']) . '</td></tr>';
		}
		echo '</table></body></html>';
		exit;
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

		$bieumau = $chiso->get("bieumau");
		if (is_string($bieumau)) {
			$bieumau = json_decode($bieumau, true);
		}
		if (!is_array($bieumau)) {
			$bieumau = array("version" => 1, "cau_hoi" => array());
		}
		$chiso->set("bieumau", json_encode($bieumau, JSON_UNESCAPED_UNICODE));

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
		$message->set("succesMessage", "Duyệt chỉ tiêu thành công");

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
	public function taolai()
	{
		$maChiSoCu = (int) $this->request->getParameter('ma_chi_so');
		$userId = isset($_SESSION['sUserID'])
			? (int) $_SESSION['sUserID']
			: 0;

		$maChiSoMoi = $this->ChiSoPeer->taoLaiTuDonBiTuChoi(
			$maChiSoCu,
			$userId
		);

		if ($maChiSoMoi <= 0) {
			return $this->request->json_response(json_encode(array(
				'success' => false,
				'message' => 'Không thể tạo lại chỉ số này'
			)));
		}

		return $this->request->json_response(json_encode(array(
			'success' => true,
			'id' => $maChiSoMoi,
			'message' => 'Đã tạo một bản nháp mới'
		)));
	}
}
?>
