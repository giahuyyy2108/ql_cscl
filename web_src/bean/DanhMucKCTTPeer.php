<?php

require_once("web_src/bean/DanhMucKCTT.php");

class DanhMucKCTTPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    function setDanhMucKCTT($result)
    {
        $danhmuc = new DanhMucKCTT();

        $danhmuc->set("id", $result["id"]);
        $danhmuc->set("loai", $result["loai"]);
        $danhmuc->set("ten", $result["ten"]);

        return $danhmuc;
    }

    function Get_danhmucKCTT()
    {
        $sSQL = "SELECT * FROM danhmuc_kctt ";
        $sSQL .= "ORDER BY id ASC";

        $result = $this->dbsql->query($sSQL);

        $arrList = [];

        while ($row = $this->dbsql->fetch_Array($result)) {
            $arrList[] = $this->setDanhMucKCTT($row);
        }

        return $arrList;
    }

    function save($item) { $id=(int)$item->get('id'); $loai=addslashes($item->get('loai')); $ten=addslashes($item->get('ten')); if ($id===0) { $this->dbsql->query("INSERT INTO danhmuc_kctt (`loai`,`ten`) VALUES ('$loai','$ten')"); return $this->dbsql->insert_id(); } $this->dbsql->query("UPDATE danhmuc_kctt SET `loai`='$loai',`ten`='$ten' WHERE `id`='$id'"); return $id; }
    function deleteDanhMucKCTT($id) { $this->dbsql->query("DELETE FROM danhmuc_kctt WHERE id='".(int)$id."'"); return true; }
}

?>
