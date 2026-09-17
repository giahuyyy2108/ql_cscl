<?php
require_once 'web_src/bean/DonViTinhPeer.php';

class donvitinhAction
{
    var $request;
    var $donViTinhPeer;
    public static $listRole = 'donvitinh,save,delete';

    function __construct()
    {
        $this->request = new Request;
        $this->donViTinhPeer = new DonViTinhPeer;
        $this->request->setTitle('Danh sách đơn vị tính');
    }

    public function index()
    {
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/donvitinh.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setAttribute('css', '<link href="' . _DEFAULT_URL_ . 'css/style.css?' . _DEFAULT_VERSION_JS_CSS_ . '" rel="stylesheet">');
        $this->request->setModel('www/danhmuc/donvitinh.php');

        return true;
    }

    public function getData()
    {
        return $this->request->json_response(json_encode(array('data' => $this->donViTinhPeer->getDonViTinh())));
    }

    public function saveDonViTinh()
    {
        $id = (int) $this->request->getParameter('id');
        $data = json_decode($this->request->getParameter('data', true), true);
        $ten = isset($data[0]) ? trim($data[0]) : '';
        $response = array();
        $message = new Message();

        if ($ten === '') {
            $message->set('flag', false);
            $message->set('errorMessage', 'Vui lòng nhập tên đơn vị tính');
        } else {
            $donViTinh = new DonViTinh;
            $donViTinh->set('id', $id);
            $donViTinh->set('ten', $ten);
            $response['id'] = $this->donViTinhPeer->save($donViTinh);
            $message->set('flag', true);
            $message->set('successMessage', 'Cập nhật đơn vị tính thành công');
        }

        $response['message'] = $message;
        return $this->request->json_response(json_encode($response));
    }

    public function save() { return $this->saveDonViTinh(); }

    public function deleteDonViTinh()
    {
        $id = (int) $this->request->getParameter('id');
        return $this->request->json_response(json_encode($this->donViTinhPeer->deleteDonViTinh($id)));
    }

    public function delete() { return $this->deleteDonViTinh(); }
}
