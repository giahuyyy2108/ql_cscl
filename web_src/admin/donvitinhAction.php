<?php
require_once 'web_src/bean/DonViTinhPeer.php';

class donvitinhAction
{
    public static $listRole = 'donvitinh';

    var $request;
    var $donViTinhPeer;

    function __construct()
    {
        $this->request = new Request;
        $this->donViTinhPeer = new DonViTinhPeer;
        $this->request->setTitle('Danh mục đơn vị tính');
    }

    function index()
    {
        $this->request->setAttribute('listDonViTinh', $this->donViTinhPeer->getDonViTinh());
        $this->request->setAttribute('script', '<script src="' . _DEFAULT_URL_ . 'js/donvitinh/donvitinh.js?' . _DEFAULT_VERSION_JS_CSS_ . '"></script>');
        $this->request->setModel('www/danhmuc/donvitinh.php');

        return true;
    }

    function save()
    {
        $id = (int) $this->request->getParameter('id');
        $ten = trim((string) $this->request->getParameter('ten'));
        if ($ten === '') {
            return $this->jsonResult(false, 'Tên đơn vị tính không được để trống.');
        }

        $donViTinh = new DonViTinh;
        $donViTinh->set('id', $id);
        $donViTinh->set('ten', $ten);
        $savedId = $this->donViTinhPeer->save($donViTinh);

        return $this->jsonResult(true, $id > 0
            ? 'Cập nhật đơn vị tính thành công.'
            : 'Thêm đơn vị tính thành công.', $savedId);
    }

    function delete()
    {
        $id = (int) $this->request->getParameter('id');
        if ($id <= 0) {
            return $this->jsonResult(false, 'Mã đơn vị tính không hợp lệ.');
        }
        if ($this->donViTinhPeer->isInUse($id)) {
            return $this->jsonResult(false, 'Không thể xóa đơn vị tính đang được sử dụng.');
        }

        $this->donViTinhPeer->delete($id);
        return $this->jsonResult(true, 'Xóa đơn vị tính thành công.');
    }

    private function jsonResult($success, $message, $id = 0)
    {
        return $this->request->json_response(json_encode(array(
            'success' => (bool) $success,
            'message' => $message,
            'id' => (int) $id
        )));
    }
}
