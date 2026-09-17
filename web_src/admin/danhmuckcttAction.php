<?php
require_once 'web_src/bean/DanhMucKCTTPeer.php';
class danhmuckcttAction {
    var $request; var $peer; 
    public static $listRole = 'danhmuckctt,save,delete';
    function __construct() { $this->request = new Request; $this->peer = new DanhMucKCTTPeer; $this->request->setTitle('Khía cạnh / Thành tố'); }
    function index() { $this->request->setAttribute('script', '<script src="'._DEFAULT_URL_.'js/danhmuc-crud.js?'._DEFAULT_VERSION_JS_CSS_.'"></script><script src="'._DEFAULT_URL_.'js/danhmuckctt.js?'._DEFAULT_VERSION_JS_CSS_.'"></script>'); $this->request->setModel('www/danhmuc/danhmuckctt.php'); return true; }
    function getData() { return $this->request->json_response(json_encode(array('data' => $this->peer->Get_danhmucKCTT()))); }
    function save() { $data = json_decode($this->request->getParameter('data', true), true); $loai = isset($data[0]) ? trim($data[0]) : ''; $ten = isset($data[1]) ? trim($data[1]) : ''; $message = new Message; $response = array(); if (!in_array($loai, array('khia_canh','thanh_to')) || $ten === '') { $message->set('flag', false); $message->set('errorMessage', 'Vui lòng nhập đầy đủ loại và tên danh mục'); } else { $item = new DanhMucKCTT; $item->set('id', (int)$this->request->getParameter('id')); $item->set('loai', $loai); $item->set('ten', $ten); $response['id'] = $this->peer->save($item); $message->set('flag', true); } $response['message'] = $message; return $this->request->json_response(json_encode($response)); }
    function delete() { return $this->request->json_response(json_encode($this->peer->deleteDanhMucKCTT((int)$this->request->getParameter('id')))); }
}
