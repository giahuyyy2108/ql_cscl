<?php
require_once 'web_src/bean/TinhTrangPeer.php';

class trangthaiAction
{
    public static $listRole = 'trangthai';
    var $request;
    var $peer;
    function __construct() { $this->request = new Request; $this->peer = new TinhTrangPeer; }

    function index()
    {
        $this->request->setAttribute('listTrangThai', $this->peer->getTinhTrang());
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/danhmuc/danhmuc-crud.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setModel('www/danhmuc/trangthai.php');

        return true;
    }
    function save() {
        $id=(int)$this->request->getParameter('id'); $ten=trim((string)$this->request->getParameter('ten')); $tag=trim((string)$this->request->getParameter('tag'));
        $isNew=(int)$this->request->getParameter('is_new')===1;
        if ($id<0) return $this->json(false,'Mã trạng thái không hợp lệ.');
        if ($ten==='') return $this->json(false,'Tên trạng thái không được để trống.');
        if ($isNew && $this->peer->exists($id)) return $this->json(false,'Mã trạng thái đã tồn tại.');
        $item=new TinhTrang; $item->set('maTrangThai',$id); $item->set('tenTrangThai',$ten); $item->set('tag',$tag);
        return $this->json(true,$isNew?'Thêm trạng thái thành công.':'Cập nhật trạng thái thành công.',$this->peer->save($item,$isNew));
    }
    function delete() {
        $id=(int)$this->request->getParameter('id');
        if ($id<0) return $this->json(false,'Mã trạng thái không hợp lệ.');
        if ($this->peer->isInUse($id)) return $this->json(false,'Không thể xóa trạng thái đang được sử dụng.');
        $this->peer->delete($id); return $this->json(true,'Xóa trạng thái thành công.');
    }
    private function json($success,$message,$id=0) { return $this->request->json_response(json_encode(array('success'=>(bool)$success,'message'=>$message,'id'=>(int)$id))); }
}
