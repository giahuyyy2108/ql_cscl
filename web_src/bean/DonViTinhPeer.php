<?php

require_once("web_src/bean/DonViTinh.php");

class DonViTinhPeer
{
    var $dbsql;

    function __construct()
    {
        $this->dbsql = new db_mysql;
        $this->dbsql->connect();
    }

    function setDonViTinh($result)
    {
        $donvitinh = new DonViTinh();

        $donvitinh->set("id", $result["id"]);
        $donvitinh->set("ten", $result["ten"]);

        return $donvitinh;
    }

    function getDonViTinh()
    {
        $sSQL = "SELECT * FROM donvitinh ";
        $sSQL .= "ORDER BY id ASC";

        $result = $this->dbsql->query($sSQL);

        $arrList = [];

        while ($row = $this->dbsql->fetch_Array($result)) {
            $arrList[] = $this->setDonViTinh($row);
        }

        return $arrList;
    }

    function getDonViTinhbyID($id)
    {
        $sSQL = "SELECT * FROM donvitinh where $id";


        $this->dbsql->query($sSQL);

        if ($this->dbsql->num_rows() > 0) {
            $result = $this->dbsql->fetch_array();
            return $this->setDonViTinh($result);
        }
        return false;

    }

    function save($donViTinh)
    {
        $id = (int) $donViTinh->get('id');
        $ten = addslashes($donViTinh->get('ten'));
        if ($id === 0) {
            $this->dbsql->query("INSERT INTO donvitinh (`ten`) VALUES ('" . $ten . "')");
            return $this->dbsql->insert_id();
        }
        $this->dbsql->query("UPDATE donvitinh SET `ten` = '" . $ten . "' WHERE `id` = '" . $id . "'");
        return $id;
    }

    function deleteDonViTinh($id)
    {
        $this->dbsql->query("DELETE FROM donvitinh WHERE id='" . (int) $id . "'");
        return true;
    }
}

?>
