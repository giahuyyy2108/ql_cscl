<?php

class ChuKy
{
    var $id;
    var $ten;
    var $chuky;

    function __construct()
    {
        $this->id = 0;
        $this->ten = "";
        $this->chuky = 0;
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
