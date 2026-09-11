<?php

require_once("web_src/bean/PhamVi.php");

class PhamViPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    function setPhamVi($result)
    {
        $phamvi = new PhamVi();

        $phamvi->set("id", $result["id"]);
        $phamvi->set("ten", $result["ten"]);

        return $phamvi;
    }

    function getPhamVi()
    {
        $sSQL = "SELECT * FROM phamvi ";
        $sSQL .= "ORDER BY id ASC";

        $result = $this->dbsql->query($sSQL);

        $arrList = [];

        while ($row = $this->dbsql->fetch_Array($result)) {
            $arrList[] = $this->setPhamVi($row);
        }

        return $arrList;
    }
}

?>