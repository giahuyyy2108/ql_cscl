<?php
require_once 'web_src/bean/DanhMucKCTTPeer.php';

class danhmuckcttAction
{
    public static $listRole = 'danhmuckctt';
    var $request;
    var $peer;
    function __construct() { $this->request = new Request; $this->peer = new DanhMucKCTTPeer; }

    function index()
    {
        $this->request->setAttribute('listDanhMucKCTT', $this->peer->Get_danhmucKCTT());
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/danhmuc/danhmuc-crud.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setModel('www/danhmuc/danhmuckctt.php');

        return true;
    }
    function save() {
        $id=(int)$this->request->getParameter('id');
        $ten=trim((string)$this->request->getParameter('ten'));
        $loai=(string)$this->request->getParameter('loai');
        if ($ten==='') return $this->json(false,'Tên danh mục không được để trống.');
        if (!in_array($loai,array('khia_canh','thanh_to'),true)) return $this->json(false,'Loại danh mục không hợp lệ.');
        if ($id>0 && $this->peer->isInUse($id) && $this->peer->getLoai($id)!==$loai) return $this->json(false,'Không thể đổi loại của danh mục đang được sử dụng.');
        $item=new DanhMucKCTT; $item->set('id',$id); $item->set('ten',$ten); $item->set('loai',$loai);
        return $this->json(true,$id?'Cập nhật danh mục thành công.':'Thêm danh mục thành công.',$this->peer->save($item));
    }
    function delete() {
        $id=(int)$this->request->getParameter('id');
        if ($id<=0) return $this->json(false,'Mã danh mục không hợp lệ.');
        if ($this->peer->isInUse($id)) return $this->json(false,'Không thể xóa danh mục đang được sử dụng.');
        $this->peer->delete($id); return $this->json(true,'Xóa danh mục thành công.');
    }
    private function json($success,$message,$id=0) { return $this->request->json_response(json_encode(array('success'=>(bool)$success,'message'=>$message,'id'=>(int)$id))); }
}
