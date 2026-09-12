<?php
require_once 'web_src/bean/DanhMucKCTTPeer.php';

class danhmuckcttAction
{
    public static $listRole = 'danhmuckctt';

    function index()
    {
        $request = new Request;
        $peer = new DanhMucKCTTPeer;

        $request->setAttribute('listDanhMucKCTT', $peer->Get_danhmucKCTT());
        $request->setModel('www/danhmuc/danhmuckctt.php');

        return true;
    }
}
