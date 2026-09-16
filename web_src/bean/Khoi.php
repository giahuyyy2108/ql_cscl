<?php
class Khoi
{
    var $MaKhoi;
    var $TenKhoi;

    function Khoi()
    {
        $this->MaKhoi = 0;
        $this->TenKhoi = "";
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
