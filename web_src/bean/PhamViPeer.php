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
    function save($item) {
        $id=(int)$item->get('id'); $ten=addslashes((string)$item->get('ten'));
        if ($id) { $this->dbsql->query("UPDATE phamvi SET ten='".$ten."' WHERE id=".$id); return $id; }
        $this->dbsql->query("INSERT INTO phamvi (ten) VALUES ('".$ten."')"); return $this->dbsql->insert_id();
    }
    function isInUse($id) { $r=$this->dbsql->query("SELECT ma_chi_so FROM chi_so_chat_luong WHERE pham_vi=".(int)$id." LIMIT 1"); return $this->dbsql->num_rows($r)>0; }
    function delete($id) { $this->dbsql->query("DELETE FROM phamvi WHERE id=".(int)$id); }
}

?>
