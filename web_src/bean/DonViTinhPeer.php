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

    function save($donViTinh)
    {
        $id = (int) $donViTinh->get('id');
        $ten = addslashes((string) $donViTinh->get('ten'));
        if ($id > 0) {
            $this->dbsql->query("UPDATE donvitinh SET ten = '" . $ten . "' WHERE id = " . $id);
            return $id;
        }

        $this->dbsql->query("INSERT INTO donvitinh (ten) VALUES ('" . $ten . "')");
        return $this->dbsql->insert_id();
    }

    function isInUse($id)
    {
        $result = $this->dbsql->query(
            "SELECT ma_chi_so FROM chi_so_chat_luong WHERE id_donvitinh = " . (int) $id . " LIMIT 1"
        );
        return $this->dbsql->num_rows($result) > 0;
    }

    function delete($id)
    {
        $this->dbsql->query("DELETE FROM donvitinh WHERE id = " . $id);
    }
}

?>
