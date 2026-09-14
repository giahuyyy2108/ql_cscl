<?PHP
require_once("web_src/bean/ChiSoChatLuongPeer.php");
require_once("web_src/bean/UserPeer.php");

class chisokhoaAction
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
        $userPeer = new UserPeer;
        $listUser = array_values(array_filter($userPeer->getListUserActive(), function ($user) {
            return (int) $user->get('adminType') !== 1;
        }));
        $this->request->setTitle("Chỉ số chất lượng Khoa/Phòng");
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/chisokhoa/chisokhoa.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setAttribute('listUser', $listUser);
        $this->request->setModel("www/chisokhoa/chisokhoa.php");
        return true;
    }

    public function getData()
    {
        if (!$this->request->checkRole('chisochatluong.all')) {
            return $this->request->json_response(json_encode(array('data' => array())));
        }
        $idUser = (int) $this->request->getParameter('id_user');
        $data = $idUser > 0 ? $this->chiSoPeer->getListChiSoUser($idUser) : array();
        return $this->request->json_response(json_encode(array('data' => $data)));
    }

    public function XemDL()
    {
        if (!$this->request->checkRole('chisochatluong.all')) {
            return $this->request->json_response(json_encode(array('success' => false, 'message' => 'Bạn không có quyền xem dữ liệu Khoa/Phòng')));
        }
        $maChiSo = (int) $this->request->getParameter('ma_chi_so');
        $idUser = (int) $this->request->getParameter('id_user');
        if ($maChiSo <= 0 || $idUser <= 0) {
            return $this->request->json_response(json_encode(array('success' => false, 'message' => 'Dữ liệu không hợp lệ')));
        }
        if (!$this->chiSoPeer->isChiSoPhamViUser($maChiSo, $idUser)) {
            return $this->request->json_response(json_encode(array('success' => false, 'message' => 'Chỉ số không thuộc người dùng đã chọn')));
        }
        $data = $this->chiSoPeer->getNhapLieu($maChiSo, $idUser, false);
        return $this->request->json_response(json_encode(array(
            'success' => (bool) $data,
            'data' => $data ? $data : null,
            'message' => $data ? '' : 'Không tìm thấy chỉ số'
        )));
    }
}
