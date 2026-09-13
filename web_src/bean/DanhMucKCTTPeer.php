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
    function save($item) {
        $id=(int)$item->get('id'); $ten=addslashes((string)$item->get('ten')); $loai=addslashes((string)$item->get('loai'));
        if ($id) { $this->dbsql->query("UPDATE danhmuc_kctt SET ten='".$ten."', loai='".$loai."' WHERE id=".$id); return $id; }
        $this->dbsql->query("INSERT INTO danhmuc_kctt (loai,ten) VALUES ('".$loai."','".$ten."')"); return $this->dbsql->insert_id();
    }
    function getLoai($id) {
        $r=$this->dbsql->query("SELECT loai FROM danhmuc_kctt WHERE id=".(int)$id." LIMIT 1");
        if ($this->dbsql->num_rows($r)===0) return ''; $row=$this->dbsql->fetch_Array($r); return $row['loai'];
    }
    function isInUse($id) { $id=(int)$id; $r=$this->dbsql->query("SELECT ma_chi_so FROM chi_so_chat_luong WHERE ma_khia_canh=".$id." OR ma_thanh_to=".$id." LIMIT 1"); return $this->dbsql->num_rows($r)>0; }
    function delete($id) { $this->dbsql->query("DELETE FROM danhmuc_kctt WHERE id=".(int)$id); }
}

?>
