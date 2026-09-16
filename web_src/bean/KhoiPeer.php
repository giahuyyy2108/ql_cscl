<?php
require_once ("web_src/bean/Khoi.php");

class KhoiPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    function setKhoi($result)
    {
        $khoi = new Khoi;
        $khoi->set("MaKhoi", $result["id"]);
        $khoi->set("TenKhoi", $result["ten"]);
        return $khoi;
    }

    function getKhoiID($id)
    {
        $result = $this->dbsql->query("SELECT * FROM khoi WHERE id='" . (int) $id . "'");
        if ($this->dbsql->num_rows($result) > 0) {
            return $this->setKhoi($this->dbsql->fetch_array($result));
        }
        return false;
    }

    function getListKhoi()
    {
        $result = $this->dbsql->query("SELECT * FROM khoi ORDER BY id DESC");
        $arrList = array();
        while ($row = $this->dbsql->fetch_Array($result)) {
            $arrList[] = $this->setKhoi($row);
        }
        return $arrList;
    }

    function save($_khoi)
    {
        if ($_khoi->get("MaKhoi") == 0 || $_khoi->get("MaKhoi") == "") {
            $sql = "INSERT INTO `khoi` (`ten`, `UPDATE_AT`, `CREATE_AT`)
                    VALUES ('" . addslashes($_khoi->get("TenKhoi")) . "', CURDATE(), CURDATE())";
        } else {
            $sql = "UPDATE `khoi`
                    SET `ten` = '" . addslashes($_khoi->get("TenKhoi")) . "',
                        `UPDATE_AT` = CURDATE()
                    WHERE `id` = '" . (int) $_khoi->get("MaKhoi") . "'";
        }

        $this->dbsql->query($sql);
        return ($this->dbsql->insert_id() == 0) ? $_khoi->get("MaKhoi") : $this->dbsql->insert_id();
    }

    function deleteKhoi($id)
    {
        $this->dbsql->query("DELETE FROM khoi WHERE id='" . (int) $id . "'");
        return true;
    }
}
?>
