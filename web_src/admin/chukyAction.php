<?php
require_once 'web_src/bean/ChuKyPeer.php';

class chukyAction
{
    var $request;
    var $peer;
    public static $listRole = 'chuky';

    function __construct()
    {
        $this->request = new Request;
        $this->peer = new ChuKyPeer;
        $this->request->setTitle('Danh sách chu kỳ');
    }

    function index()
    {
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/danhmuc-crud.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script><script src="' . _DEFAULT_URL_ . 'js/chuky.js?' . _DEFAULT_VERSION_JS_CSS_ . '.2"></script>');
        $this->request->setModel('www/danhmuc/chuky.php');
        return true;
    }

    function getData()
    {
        return $this->request->json_response(json_encode(array('data' => $this->peer->getChuKy())));
    }

    function save()
    {
        $data = json_decode($this->request->getParameter('data', true), true);
        $ten = isset($data[0]) ? trim($data[0]) : '';
        $chuky = isset($data[1]) ? (int)$data[1] : 0;
        $message = new Message;
        $response = array();

        if ($ten === '') {
            $message->set('flag', false);
            $message->set('errorMessage', 'Vui lòng nhập tên chu kỳ');
        } elseif ($chuky <= 0) {
            $message->set('flag', false);
            $message->set('errorMessage', 'Vui lòng nhập số lần trong năm lớn hơn 0');
        } else {
            $item = new ChuKy;
            $item->set('id', (int)$this->request->getParameter('id'));
            $item->set('ten', $ten);
            $item->set('chuky', $chuky);
            $response['id'] = $this->peer->save($item);
            $message->set('flag', true);
        }

        $response['message'] = $message;
        return $this->request->json_response(json_encode($response));
    }

    function delete()
    {
        return $this->request->json_response(json_encode($this->peer->deleteChuKy((int)$this->request->getParameter('id'))));
    }
}
