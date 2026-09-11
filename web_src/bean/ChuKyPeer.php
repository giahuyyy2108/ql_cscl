<?php

require_once("web_src/bean/ChuKy.php");

class ChuKyPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    function setChuKy($result)
    {
        $chuky = new ChuKy();

        $chuky->set("id", $result["id"]);
        $chuky->set("ten", $result["ten"]);

        return $chuky;
    }

    function getChuKy()
    {
        $sSQL = "SELECT * FROM chuky ";
        $sSQL .= "ORDER BY id ASC";

        $result = $this->dbsql->query($sSQL);

        $arrList = [];

        while ($row = $this->dbsql->fetch_Array($result)) {
            $arrList[] = $this->setChuKy($row);
        }

        return $arrList;
    }
}

?>