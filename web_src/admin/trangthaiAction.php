<?php
require_once 'web_src/bean/TinhTrangPeer.php';

class trangthaiAction
{
    public static $listRole = 'trangthai';

    function index()
    {
        $request = new Request;
        $peer = new TinhTrangPeer;

        $request->setAttribute('listTrangThai', $peer->getTinhTrang());
        $request->setModel('www/danhmuc/trangthai.php');

        return true;
    }
}
