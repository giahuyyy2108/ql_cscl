<?PHP
require_once("web_src/bean/ChiSoChatLuongPeer.php");

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
        $this->request->setTitle("Chỉ số khoa tháng");
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/chitieu/chitieu.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setAttribute('thangHienTai', (int) date('n'));
        $this->request->setAttribute('namHienTai', (int) date('Y'));
        $this->request->setModel("www/chisokhoa/chisokhoa.php");
        return true;
    }
}