<?php
require_once 'web_src/bean/PhamViPeer.php';

class phamviAction
{
    public static $listRole = 'phamvi';

    function index()
    {
        $request = new Request;
        $peer = new PhamViPeer;

        $request->setAttribute('listPhamVi', $peer->getPhamVi());
        $request->setModel('www/danhmuc/phamvi.php');

        return true;
    }
}
