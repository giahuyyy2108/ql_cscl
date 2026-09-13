<?php
require_once 'web_src/bean/ChuKyPeer.php';

class chukyAction
{
    public static $listRole = 'chuky';
    var $request;
    var $peer;

    function __construct() { $this->request = new Request; $this->peer = new ChuKyPeer; }

    function index()
    {
        $this->request->setAttribute('listChuKy', $this->peer->getChuKy());
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/danhmuc/danhmuc-crud.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setModel('www/danhmuc/chuky.php');

        return true;
    }

    function save() { return $this->saveName(); }
    function delete() {
        $id = (int) $this->request->getParameter('id');
        if ($id <= 0) return $this->json(false, 'Mã chu kỳ không hợp lệ.');
        if ($this->peer->isInUse($id)) return $this->json(false, 'Không thể xóa chu kỳ đang được sử dụng.');
        $this->peer->delete($id); return $this->json(true, 'Xóa chu kỳ thành công.');
    }
    private function saveName() {
        $id = (int) $this->request->getParameter('id');
        $ten = trim((string) $this->request->getParameter('ten'));
        if ($ten === '') return $this->json(false, 'Tên chu kỳ không được để trống.');
        $item = new ChuKy; $item->set('id', $id); $item->set('ten', $ten);
        return $this->json(true, $id ? 'Cập nhật chu kỳ thành công.' : 'Thêm chu kỳ thành công.', $this->peer->save($item));
    }
    private function json($success, $message, $id = 0) {
        return $this->request->json_response(json_encode(array('success'=>(bool)$success, 'message'=>$message, 'id'=>(int)$id)));
    }
}
