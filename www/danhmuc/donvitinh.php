<?php
$listDonViTinh = $request->getAttribute('listDonViTinh');
if (!is_array($listDonViTinh)) {
    $listDonViTinh = [];
}
$escapeDonViTinh = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
?>
<div class="x_panel">
    <div class="x_title">
        <h2>Đơn vị tính <small>Danh mục</small></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <p>Danh sách đơn vị tính sử dụng cho các chỉ số chất lượng.</p>
        <p><button type="button" class="btn btn-primary" id="btnThemDonViTinh">
            <i class="fa fa-plus"></i> Thêm đơn vị tính
        </button></p>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <caption class="sr-only">Danh sách đơn vị tính</caption>
                <thead><tr>
                    <th scope="col">Mã</th>
                    <th scope="col">Tên đơn vị tính</th>
                    <th scope="col" class="text-center">Thao tác</th>
                </tr></thead>
                <tbody>
                    <?php if ($listDonViTinh === []): ?>
                        <tr><td colspan="3" class="text-center text-muted">Chưa có đơn vị tính.</td></tr>
                    <?php else: ?>
                        <?php foreach ($listDonViTinh as $donViTinh): ?>
                            <tr>
                                <td><?= $escapeDonViTinh($donViTinh->get('id')) ?></td>
                                <td><?= $escapeDonViTinh($donViTinh->get('ten')) ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm btn-sua-don-vi-tinh"
                                        data-id="<?= $escapeDonViTinh($donViTinh->get('id')) ?>"
                                        data-ten="<?= $escapeDonViTinh($donViTinh->get('ten')) ?>" title="Sửa">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm btn-xoa-don-vi-tinh"
                                        data-id="<?= $escapeDonViTinh($donViTinh->get('id')) ?>"
                                        data-ten="<?= $escapeDonViTinh($donViTinh->get('ten')) ?>" title="Xóa">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDonViTinh" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <form id="formDonViTinh">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Thêm đơn vị tính</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="donViTinhId" value="0">
                <div class="form-group">
                    <label for="donViTinhTen">Tên đơn vị tính <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="donViTinhTen" maxlength="255" required autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnLuuDonViTinh"><i class="fa fa-save"></i> Lưu</button>
            </div>
        </form>
    </div></div>
</div>
