<?php
$items=$request->getAttribute('listPhamVi'); if(!is_array($items))$items=[];
$route='phamvi'; $title='Phạm vi'; $label='phạm vi';
$description='Danh sách phạm vi áp dụng của các chỉ số chất lượng.';
require 'www/danhmuc/_simple_crud.php';
?>
