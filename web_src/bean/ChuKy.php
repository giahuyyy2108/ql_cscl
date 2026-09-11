<?php

class ChuKy
{
    var $id;
    var $ten;

    function __construct()
    {
        $this->id = 0;
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