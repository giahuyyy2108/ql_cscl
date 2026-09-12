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
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <caption class="sr-only">Danh sách đơn vị tính</caption>
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Tên đơn vị tính</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($listDonViTinh === []): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">Chưa có đơn vị tính.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listDonViTinh as $donViTinh): ?>
                            <tr>
                                <td><?= $escapeDonViTinh($donViTinh->get('id')) ?></td>
                                <td><?= $escapeDonViTinh($donViTinh->get('ten')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
