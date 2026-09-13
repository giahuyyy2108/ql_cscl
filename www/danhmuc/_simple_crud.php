<?php
$escape = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
?>
<div class="x_panel danhmuc-crud" data-route="<?= $escape($route) ?>" data-label="<?= $escape($label) ?>">
    <div class="x_title"><h2><?= $escape($title) ?> <small>Danh mục</small></h2><div class="clearfix"></div></div>
    <div class="x_content">
        <p><?= $escape($description) ?></p>
        <p><button type="button" class="btn btn-primary btn-them-danhmuc"><i class="fa fa-plus"></i> Thêm <?= $escape($label) ?></button></p>
        <div class="table-responsive"><table class="table table-striped table-bordered">
            <thead><tr><th>Mã</th><th>Tên <?= $escape($label) ?></th><th class="text-center">Thao tác</th></tr></thead>
            <tbody>
            <?php if ($items === []): ?><tr><td colspan="3" class="text-center text-muted">Chưa có dữ liệu.</td></tr>
            <?php else: foreach ($items as $item):
                $id=$item->get('id'); $ten=$item->get('ten');
                $fields=$escape(json_encode(array('id'=>$id,'ten'=>$ten), JSON_UNESCAPED_UNICODE)); ?>
                <tr><td><?= $escape($id) ?></td><td><?= $escape($ten) ?></td><td class="text-center">
                    <button type="button" class="btn btn-warning btn-sm btn-sua-danhmuc" data-fields="<?= $fields ?>" title="Sửa"><i class="glyphicon glyphicon-pencil"></i></button>
                    <button type="button" class="btn btn-danger btn-sm btn-xoa-danhmuc" data-id="<?= $escape($id) ?>" data-ten="<?= $escape($ten) ?>" title="Xóa"><i class="glyphicon glyphicon-trash"></i></button>
                </td></tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table></div>
    </div>
</div>
<div class="modal fade modal-danhmuc" tabindex="-1" role="dialog"><div class="modal-dialog"><div class="modal-content">
    <form class="form-danhmuc"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title"></h4></div>
    <div class="modal-body"><input type="hidden" name="id" value="0"><div class="form-group"><label>Tên <?= $escape($label) ?> <span class="text-danger">*</span></label><input class="form-control" name="ten" maxlength="255" required autocomplete="off"></div></div>
    <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button><button type="submit" class="btn btn-primary btn-luu-danhmuc"><i class="fa fa-save"></i> Lưu</button></div></form>
</div></div></div>
