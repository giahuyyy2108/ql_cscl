<?php

class CtChiSo
{
    var $id;
    var $ma_chi_so;
    var $id_user;
    var $id_khoaphong;
    var $nam;
    var $ky;
    var $du_lieu;
    var $created_at;
    var $updated_at;

    function __construct()
    {
        $this->id = 0;
        $this->ma_chi_so = 0;
        $this->id_user = 0;
        $this->id_khoaphong = 0;
        $this->nam = (int) date('Y');
        $this->ky = 0;
        $this->du_lieu = array();
        $this->created_at = '';
        $this->updated_at = '';
    }

    function set($key, $value) { $this->$key = $value; }
    function get($key) { return $this->$key; }
}
?>
