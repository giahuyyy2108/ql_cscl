<?php

class TinhTrang
{
    var $maTrangThai;
    var $tenTrangThai;
    var $tag;

    function __construct()
    {
        $this->maTrangThai = "";
        $this->tenTrangThai = "";
        $this->tag = "";
    }

    function set($key, $value)
    {
        $this->$key = $value;
    }

    function get($key)
    {
        return $this->$key;
    }
}

?>

