<?php
require_once 'web_src/bean/PhamViPeer.php';
class phamviAction {
    var $request; var $peer; public static $listRole = 'phamvi';
    function __construct() { $this->request = new Request; $this->peer = new PhamViPeer; $this->request->setTitle('Danh sách phạm vi'); }
    function index() { $this->request->setAttribute('script', '<script src="'._DEFAULT_URL_.'js/danhmuc-crud.js?'._DEFAULT_VERSION_JS_CSS_.'"></script><script src="'._DEFAULT_URL_.'js/phamvi.js?'._DEFAULT_VERSION_JS_CSS_.'"></script>'); $this->request->setModel('www/danhmuc/phamvi.php'); return true; }
    function getData() { return $this->request->json_response(json_encode(array('data' => $this->peer->getPhamVi()))); }
    function save() { $data = json_decode($this->request->getParameter('data', true), true); $ten = isset($data[0]) ? trim($data[0]) : ''; $message = new Message; $response = array(); if ($ten === '') { $message->set('flag', false); $message->set('errorMessage', 'Vui lòng nhập tên phạm vi'); } else { $item = new PhamVi; $item->set('id', (int)$this->request->getParameter('id')); $item->set('ten', $ten); $response['id'] = $this->peer->save($item); $message->set('flag', true); } $response['message'] = $message; return $this->request->json_response(json_encode($response)); }
    function delete() { return $this->request->json_response(json_encode($this->peer->deletePhamVi((int)$this->request->getParameter('id')))); }
}
