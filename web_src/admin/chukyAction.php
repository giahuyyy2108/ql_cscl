<?php
require_once 'web_src/bean/ChuKyPeer.php';

class chukyAction
{
    public static $listRole = 'chuky';

    function index()
    {
        $request = new Request;
        $peer = new ChuKyPeer;

        $request->setAttribute('listChuKy', $peer->getChuKy());
        $request->setModel('www/danhmuc/chuky.php');

        return true;
    }
}
