<?php

require_once("web_src/bean/DonViTinh.php");

class DonViTinhPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    function setDonViTinh($result)
    {
        $donvitinh = new DonViTinh();

        $donvitinh->set("id", $result["id"]);
        $donvitinh->set("ten", $result["ten"]);

        return $donvitinh;
    }

    function getDonViTinh()
    {
        $sSQL = "SELECT * FROM donvitinh ";
        $sSQL .= "ORDER BY id ASC";

        $result = $this->dbsql->query($sSQL);

        $arrList = [];

        while ($row = $this->dbsql->fetch_Array($result)) {
            $arrList[] = $this->setDonViTinh($row);
        }

        return $arrList;
    }
}

?>