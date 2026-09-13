<?php
$items=$request->getAttribute('listTrangThai'); if(!is_array($items))$items=[];
$e=static function($v){return htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');};
?>
<div class="x_panel danhmuc-crud" data-route="trangthai" data-label="trạng thái">
 <div class="x_title"><h2>Trạng thái <small>Danh mục</small></h2><div class="clearfix"></div></div>
 <div class="x_content"><p>Danh sách trạng thái sử dụng cho các chỉ số chất lượng.</p><p><button class="btn btn-primary btn-them-danhmuc"><i class="fa fa-plus"></i> Thêm trạng thái</button></p>
 <div class="table-responsive"><table class="table table-striped table-bordered"><thead><tr><th>Mã</th><th>Trạng thái</th><th>CSS tag</th><th class="text-center">Thao tác</th></tr></thead><tbody>
 <?php if($items===[]):?><tr><td colspan="4" class="text-center text-muted">Chưa có dữ liệu.</td></tr>
 <?php else:foreach($items as $item):$id=$item->get('maTrangThai');$ten=$item->get('tenTrangThai');$tag=$item->get('tag');$fields=$e(json_encode(array('id'=>$id,'ten'=>$ten,'tag'=>$tag,'is_new'=>0),JSON_UNESCAPED_UNICODE));?>
 <tr><td><?=$e($id)?></td><td><span class="badge <?=$e($tag)?>"><?=$e($ten)?></span></td><td><?=$e($tag)?></td><td class="text-center">
 <button class="btn btn-warning btn-sm btn-sua-danhmuc" data-fields="<?=$fields?>" title="Sửa"><i class="glyphicon glyphicon-pencil"></i></button>
 <button class="btn btn-danger btn-sm btn-xoa-danhmuc" data-id="<?=$e($id)?>" data-ten="<?=$e($ten)?>" title="Xóa"><i class="glyphicon glyphicon-trash"></i></button></td></tr>
 <?php endforeach;endif;?></tbody></table></div></div>
</div>
<div class="modal fade modal-danhmuc" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form class="form-danhmuc">
 <div class="modal-header"><button class="close" type="button" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title"></h4></div>
 <div class="modal-body"><input type="hidden" name="is_new" value="1"><div class="form-group"><label>Mã trạng thái <span class="text-danger">*</span></label><input type="number" min="0" class="form-control" name="id" required></div><div class="form-group"><label>Tên trạng thái <span class="text-danger">*</span></label><input class="form-control" name="ten" maxlength="255" required></div><div class="form-group"><label>CSS tag</label><input class="form-control" name="tag" maxlength="100" placeholder="Ví dụ: label-success"></div></div>
 <div class="modal-footer"><button class="btn btn-default" type="button" data-dismiss="modal">Hủy</button><button class="btn btn-primary btn-luu-danhmuc" type="submit"><i class="fa fa-save"></i> Lưu</button></div>
</form></div></div></div>
