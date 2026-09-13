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
    function save($item) {
        $id=(int)$item->get('id'); $ten=addslashes((string)$item->get('ten'));
        if ($id) { $this->dbsql->query("UPDATE chuky SET ten='".$ten."' WHERE id=".$id); return $id; }
        $this->dbsql->query("INSERT INTO chuky (ten) VALUES ('".$ten."')"); return $this->dbsql->insert_id();
    }
    function isInUse($id) { $r=$this->dbsql->query("SELECT ma_chi_so FROM chi_so_chat_luong WHERE id_chuky=".(int)$id." LIMIT 1"); return $this->dbsql->num_rows($r)>0; }
    function delete($id) { $this->dbsql->query("DELETE FROM chuky WHERE id=".(int)$id); }
}

?>
