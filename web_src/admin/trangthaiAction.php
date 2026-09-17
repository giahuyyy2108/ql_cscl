<?php
require_once 'web_src/bean/TinhTrangPeer.php';
class trangthaiAction {
    var $request; var $peer; public static $listRole = 'trangthai';
    function __construct() { $this->request = new Request; $this->peer = new TinhTrangPeer; $this->request->setTitle('Danh sách trạng thái'); }
    function index() { $this->request->setAttribute('script', '<script src="'._DEFAULT_URL_.'js/danhmuc-crud.js?'._DEFAULT_VERSION_JS_CSS_.'"></script><script src="'._DEFAULT_URL_.'js/trangthai.js?'._DEFAULT_VERSION_JS_CSS_.'"></script>'); $this->request->setModel('www/danhmuc/trangthai.php'); return true; }
    function getData() { return $this->request->json_response(json_encode(array('data' => $this->peer->getTinhTrang()))); }
    function save() { $data = json_decode($this->request->getParameter('data', true), true); $ma = isset($data[0]) ? trim($data[0]) : ''; $ten = isset($data[1]) ? trim($data[1]) : ''; $tag = isset($data[2]) ? trim($data[2]) : ''; $message = new Message; $response = array(); if ($ma === '' || $ten === '') { $message->set('flag', false); $message->set('errorMessage', 'Vui lòng nhập mã và tên trạng thái'); } else { $item = new TinhTrang; $item->set('maTrangThai', $ma); $item->set('tenTrangThai', $ten); $item->set('tag', $tag); $response['id'] = $this->peer->save($item, trim($this->request->getParameter('id'))); $message->set('flag', true); } $response['message'] = $message; return $this->request->json_response(json_encode($response)); }
    function delete() { return $this->request->json_response(json_encode($this->peer->deleteTinhTrang(trim($this->request->getParameter('id'))))); }
}
