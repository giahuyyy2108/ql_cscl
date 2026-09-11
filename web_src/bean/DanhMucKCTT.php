<?php

class DanhMucKCTT
{
    var $id;
    var $loai;
    var $ten;

    function __construct()
    {
        $this->id = 0;
        $this->loai = "";
        $this->ten = "";
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