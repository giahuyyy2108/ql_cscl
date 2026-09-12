<?php
require_once 'web_src/bean/DonViTinhPeer.php';

class donvitinhAction
{
    public static $listRole = 'donvitinh';

    function index()
    {
        $request = new Request;
        $donViTinhPeer = new DonViTinhPeer;

        $request->setAttribute('listDonViTinh', $donViTinhPeer->getDonViTinh());
        $request->setModel('www/danhmuc/donvitinh.php');

        return true;
    }
}
