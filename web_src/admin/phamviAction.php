<?php
require_once 'web_src/bean/PhamViPeer.php';

class phamviAction
{
    public static $listRole = 'phamvi';
    var $request;
    var $peer;
    function __construct() { $this->request = new Request; $this->peer = new PhamViPeer; }

    function index()
    {
        $this->request->setAttribute('listPhamVi', $this->peer->getPhamVi());
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/danhmuc/danhmuc-crud.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setModel('www/danhmuc/phamvi.php');

        return true;
    }
    function save() {
        $id=(int)$this->request->getParameter('id'); $ten=trim((string)$this->request->getParameter('ten'));
        if ($ten==='') return $this->json(false, 'Tên phạm vi không được để trống.');
        $item=new PhamVi; $item->set('id',$id); $item->set('ten',$ten);
        return $this->json(true, $id ? 'Cập nhật phạm vi thành công.' : 'Thêm phạm vi thành công.', $this->peer->save($item));
    }
    function delete() {
        $id=(int)$this->request->getParameter('id');
        if ($id<=0) return $this->json(false,'Mã phạm vi không hợp lệ.');
        if ($this->peer->isInUse($id)) return $this->json(false,'Không thể xóa phạm vi đang được sử dụng.');
        $this->peer->delete($id); return $this->json(true,'Xóa phạm vi thành công.');
    }
    private function json($success,$message,$id=0) { return $this->request->json_response(json_encode(array('success'=>(bool)$success,'message'=>$message,'id'=>(int)$id))); }
}
