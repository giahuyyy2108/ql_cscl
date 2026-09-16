<?php
require_once ("web_src/bean/KhoiPeer.php");

class khoiAction
{
    var $request;
    var $khoiPeer;
    public static $listRole = "khoi,save,delete,saveKhoi,deleteKhoi";

    public function __construct()
    {
        $this->request = new Request;
        $this->khoiPeer = new KhoiPeer();
        $this->request->setTitle("Danh sach khoi");
    }

    function index()
    {
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/khoi.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');
        $this->request->setModel("www/danhmuc/khoi.php");
        return true;
    }

    function getData()
    {
        return $this->request->json_response(json_encode(array("data" => $this->khoiPeer->getListKhoi())));
    }

    function saveKhoi()
    {
        $id = ($this->request->getParameter("id") != "") ? $this->request->getParameter("id") : 0;
        $arrayData = json_decode($this->request->getParameter("data", true), true);

        $khoi = new Khoi;
        $khoi->set("MaKhoi", $id);
        $khoi->set("TenKhoi", isset($arrayData[0]) ? $arrayData[0] : "");

        $response = array();
        $message = new Message();
        if (trim($khoi->get("TenKhoi")) == "") {
            $message->set("flag", false);
            $message->set("errorMessage", "Vui long nhap ten khoi");
            $response["message"] = $message;
            return $this->request->json_response(json_encode($response));
        }

        $response["id"] = $this->khoiPeer->save($khoi);
        $message->set("flag", true);
        $message->set("successMessage", "Cap nhat khoi thanh cong");
        $response["message"] = $message;
        return $this->request->json_response(json_encode($response));
    }

    function save()
    {
        return $this->saveKhoi();
    }

    function deleteKhoi()
    {
        $id = ($this->request->getParameter("id") != "") ? $this->request->getParameter("id") : 0;
        return $this->request->json_response(json_encode($this->khoiPeer->deleteKhoi($id)));
    }

    function delete()
    {
        return $this->deleteKhoi();
    }
}
?>
