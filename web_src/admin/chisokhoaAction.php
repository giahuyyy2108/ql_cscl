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
        $idUser = (int) $this->request->getParameter('id_user');
        $data = $idUser > 0 ? $this->chiSoPeer->getListChiSoUser($idUser) : array();
        return $this->request->json_response(json_encode(array('data' => $data)));
    }
}
