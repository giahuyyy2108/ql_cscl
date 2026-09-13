<?php
$items=$request->getAttribute('listChuKy'); if(!is_array($items))$items=[];
$route='chuky'; $title='Chu kỳ'; $label='chu kỳ';
$description='Danh sách chu kỳ theo dõi các chỉ số chất lượng.';
require 'www/danhmuc/_simple_crud.php';
?>
