<?PHP
require_once("web_src/bean/ChiSoChatLuongPeer.php");

class chitieuAction
{
    var $request;
    var $chiSoPeer;

    public function __construct()
    {
        $this->request = new Request;
        $this->chiSoPeer = new ChiSoChatLuongPeer;
    }

    public function index()
    {
        $this->request->setTitle("Nhập chỉ tiêu tháng");
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/chitieu/chitieu.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setAttribute('thangHienTai', (int) date('n'));
        $this->request->setAttribute('namHienTai', (int) date('Y'));
        $this->request->setModel("www/chitieu.php");
        return true;
    }

    public function getData()
    {
        $idUser = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
        if ($idUser <= 0) return $this->jsonError('Phiên đăng nhập không hợp lệ');
        $thang = (int) $this->request->getParameter('thang');
        $thangHienTai = (int) date('n');
        if ($thang < 1 || $thang > $thangHienTai) return $this->jsonError('Không được xem hoặc nhập tháng tương lai');
        $items = $this->chiSoPeer->getChiTieuThang($idUser, (int) date('Y'), $thang);
        return $this->request->json_response(json_encode(array(
            'success' => true,
            'thang' => $thang,
            'nam' => (int) date('Y'),
            'data' => $items
        )));
    }

    public function save()
    {
        $idUser = isset($_SESSION['sUserID']) ? (int) $_SESSION['sUserID'] : 0;
        $maChiSo = (int) $this->request->getParameter('ma_chi_so');
        $thang = (int) $this->request->getParameter('thang');
        $tuSoRaw = trim((string) $this->request->getParameter('tu_so', false));
        $mauSoRaw = trim((string) $this->request->getParameter('mau_so', false));
        if ($idUser <= 0 || $maChiSo <= 0) return $this->jsonError('Dữ liệu không hợp lệ');
        if ($thang < 1 || $thang > (int) date('n')) return $this->jsonError('Không được nhập dữ liệu tháng tương lai');
        if ($tuSoRaw === '' || $mauSoRaw === '' || !is_numeric($tuSoRaw) || !is_numeric($mauSoRaw)) {
            return $this->jsonError('Vui lòng nhập đầy đủ tử số và mẫu số');
        }
        $tuSo = (float) $tuSoRaw;
        $mauSo = (float) $mauSoRaw;
        if ($tuSo < 0 || $mauSo <= $tuSo) return $this->jsonError('Mẫu số phải lớn hơn tử số');

        $id = $this->chiSoPeer->saveChiTieuThang(
            $maChiSo, $idUser, (int) date('Y'), $thang, $tuSo, $mauSo
        );
        if (!$id) return $this->jsonError('Không thể lưu chỉ tiêu hoặc bạn không có quyền nhập');
        return $this->request->json_response(json_encode(array(
            'success' => true, 'id' => $id,
            'message' => 'Đã lưu dữ liệu tháng ' . $thang . '/' . date('Y')
        )));
    }

    private function jsonError($message)
    {
        return $this->request->json_response(json_encode(array('success' => false, 'message' => $message)));
    }
}
?>
