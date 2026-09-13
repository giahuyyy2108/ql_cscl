<?php
$items=$request->getAttribute('listDanhMucKCTT'); if(!is_array($items))$items=[];
$e=static function($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');};
$labels=array('khia_canh'=>'Khía cạnh','thanh_to'=>'Thành tố');
?>
<div class="x_panel danhmuc-crud" data-route="danhmuckctt" data-label="danh mục">
 <div class="x_title"><h2>Khía cạnh / Thành tố <small>Danh mục</small></h2><div class="clearfix"></div></div>
 <div class="x_content"><p>Danh sách khía cạnh và thành tố dùng để phân loại các chỉ số chất lượng.</p>
 <p><button class="btn btn-primary btn-them-danhmuc"><i class="fa fa-plus"></i> Thêm danh mục</button></p>
 <div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Mã</th><th>Loại</th><th>Tên</th><th class="text-center">Thao tác</th></tr></thead><tbody>
 <?php if($items===[]):?><tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu.</td></tr>
 <?php else:foreach($items as $item):$id=$item->get('id');$ten=$item->get('ten');$loai=$item->get('loai');$fields=$e(json_encode(array('id'=>$id,'ten'=>$ten,'loai'=>$loai),JSON_UNESCAPED_UNICODE));?>
 <tr><td><?=$e($id)?></td><td><?=$e(isset($labels[$loai])?$labels[$loai]:$loai)?></td><td><?=$e($ten)?></td><td class="text-center">
 <button class="btn btn-warning btn-sm btn-sua-danhmuc" data-fields="<?=$fields?>" title="Sửa"><i class="glyphicon glyphicon-pencil"></i></button>
 <button class="btn btn-danger btn-sm btn-xoa-danhmuc" data-id="<?=$e($id)?>" data-ten="<?=$e($ten)?>" title="Xóa"><i class="glyphicon glyphicon-trash"></i></button></td></tr>
 <?php endforeach;endif;?></tbody></table></div></div>
</div>
<div class="modal fade modal-danhmuc" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form class="form-danhmuc">
 <div class="modal-header"><button class="close" type="button" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title"></h4></div>
 <div class="modal-body"><input type="hidden" name="id" value="0"><div class="form-group"><label>Loại</label><select class="form-control" name="loai" required><option value="khia_canh">Khía cạnh</option><option value="thanh_to">Thành tố</option></select></div><div class="form-group"><label>Tên <span class="text-danger">*</span></label><input class="form-control" name="ten" maxlength="255" required></div></div>
 <div class="modal-footer"><button class="btn btn-default" type="button" data-dismiss="modal">Hủy</button><button class="btn btn-primary btn-luu-danhmuc" type="submit"><i class="fa fa-save"></i> Lưu</button></div>
</form></div></div></div>
