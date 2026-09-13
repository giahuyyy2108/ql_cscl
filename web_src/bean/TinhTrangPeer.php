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
    function save($item, $isNew = false) {
        $id=(int)$item->get('maTrangThai'); $ten=addslashes((string)$item->get('tenTrangThai')); $tag=addslashes((string)$item->get('tag'));
        if (!$isNew) { $this->dbsql->query("UPDATE trangthai SET tenTrangThai='".$ten."', tag='".$tag."' WHERE maTrangThai=".$id); return $id; }
        $this->dbsql->query("INSERT INTO trangthai (maTrangThai,tenTrangThai,tag) VALUES (".$id.",'".$ten."','".$tag."')"); return $id;
    }
    function exists($id) { $r=$this->dbsql->query("SELECT maTrangThai FROM trangthai WHERE maTrangThai=".(int)$id." LIMIT 1"); return $this->dbsql->num_rows($r)>0; }
    function isInUse($id) { $r=$this->dbsql->query("SELECT ma_chi_so FROM chi_so_chat_luong WHERE trang_thai=".(int)$id." LIMIT 1"); return $this->dbsql->num_rows($r)>0; }
    function delete($id) { $this->dbsql->query("DELETE FROM trangthai WHERE maTrangThai=".(int)$id); }
}

?>
