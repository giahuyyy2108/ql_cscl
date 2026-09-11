<?php

require_once("web_src/bean/TinhTrang.php");

class TinhTrangPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    function setTinhTrang($result)
    {
        $tinhtrang = new TinhTrang();

        $tinhtrang->set("maTrangThai", $result["maTrangThai"]);
        $tinhtrang->set("tenTrangThai", $result["tenTrangThai"]);
        $tinhtrang->set("tag", $result["tag"]);

        return $tinhtrang;
    }

    function getTinhTrang()
    {
        $sSQL = "SELECT * FROM trangthai ";
        $sSQL .= "ORDER BY maTrangThai ASC";

        $result = $this->dbsql->query($sSQL);

        $arrList = [];

        while ($row = $this->dbsql->fetch_Array($result)) {
            $arrList[] = $this->setTinhTrang($row);
        }

        return $arrList;
    }
}

?>