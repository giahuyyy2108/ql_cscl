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

    function save($item) { $id=(int)$item->get('id'); $ten=addslashes($item->get('ten')); if ($id===0) { $this->dbsql->query("INSERT INTO chuky (`ten`) VALUES ('$ten')"); return $this->dbsql->insert_id(); } $this->dbsql->query("UPDATE chuky SET `ten`='$ten' WHERE `id`='$id'"); return $id; }
    function deleteChuKy($id) { $this->dbsql->query("DELETE FROM chuky WHERE id='".(int)$id."'"); return true; }
}

?>
