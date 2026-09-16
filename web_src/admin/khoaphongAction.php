<?php
require_once ("web_src/bean/KhoaPhongPeer.php");
require_once ("web_src/bean/KhoiPeer.php");

class khoaphongAction
{
    var $request;
    var $khoaPhongPeer;
    public static $listRole = "khoaphong,save,delete,saveKhoaPhong,deleteKhoaPhong";

    public function __construct()
    {
        $this->request = new Request;
        $this->khoaPhongPeer = new KhoaPhongPeer();
        $this->request->setTitle("Danh sach khoa phong");
    }

    function index()
    {
        $khoiPeer = new KhoiPeer();
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/khoaphong.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');
        $this->request->setAttribute("listKhoi", $khoiPeer->getListKhoi());
        $this->request->setModel("www/danhmuc/khoaphong.php");
        return true;
    }

    function getData()
    {
        return $this->request->json_response(json_encode(array("data" => $this->khoaPhongPeer->getListKhoaPhong())));
    }

    function saveKhoaPhong()
    {
        $id = ($this->request->getParameter("id") != "") ? $this->request->getParameter("id") : 0;
        $arrayData = json_decode($this->request->getParameter("data", true), true);

        $khoaPhong = new KhoaPhong;
        $khoaPhong->set("MaKhoaPhong", $id);
        $khoaPhong->set("TenKhoaPhong", isset($arrayData[0]) ? $arrayData[0] : "");
        $khoaPhong->set("MaKhoi", isset($arrayData[1]) ? $arrayData[1] : 0);

        $response = array();
        $message = new Message();
        if (trim($khoaPhong->get("TenKhoaPhong")) == "" || (int) $khoaPhong->get("MaKhoi") <= 0) {
            $message->set("flag", false);
            $message->set("errorMessage", "Vui long nhap ten khoa/phong va chon khoi");
            $response["message"] = $message;
            return $this->request->json_response(json_encode($response));
        }

        $response["id"] = $this->khoaPhongPeer->save($khoaPhong);
        $message->set("flag", true);
        $message->set("successMessage", "Cap nhat khoa/phong thanh cong");
        $response["message"] = $message;
        return $this->request->json_response(json_encode($response));
    }

    function save()
    {
        return $this->saveKhoaPhong();
    }

    function deleteKhoaPhong()
    {
        $id = ($this->request->getParameter("id") != "") ? $this->request->getParameter("id") : 0;
        return $this->request->json_response(json_encode($this->khoaPhongPeer->deleteKhoaPhong($id)));
    }

    function delete()
    {
        return $this->deleteKhoaPhong();
    }
}
?>
