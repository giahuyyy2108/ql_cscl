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

    function save($item, $oldId)
    {
        $ma=addslashes($item->get('maTrangThai')); $ten=addslashes($item->get('tenTrangThai')); $tag=addslashes($item->get('tag')); $oldId=addslashes($oldId);
        if ($oldId === '') { $this->dbsql->query("INSERT INTO trangthai (`maTrangThai`,`tenTrangThai`,`tag`) VALUES ('$ma','$ten','$tag')"); return $ma; }
        $this->dbsql->query("UPDATE trangthai SET `maTrangThai`='$ma',`tenTrangThai`='$ten',`tag`='$tag' WHERE `maTrangThai`='$oldId'"); return $ma;
    }
    function deleteTinhTrang($id) { $this->dbsql->query("DELETE FROM trangthai WHERE maTrangThai='".addslashes($id)."'"); return true; }
}

?>
