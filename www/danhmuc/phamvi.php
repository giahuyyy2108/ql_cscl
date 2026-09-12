<?php
$listPhamVi = $request->getAttribute('listPhamVi');
if (!is_array($listPhamVi)) {
    $listPhamVi = [];
}
$escapePhamVi = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
?>

<div class="x_panel">
    <div class="x_title">
        <h2>Phạm vi <small>Danh mục</small></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <p>Danh sách phạm vi áp dụng của các chỉ số chất lượng.</p>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <caption class="sr-only">Danh sách phạm vi áp dụng của các chỉ số chất lượng.</caption>
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Tên phạm vi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($listPhamVi === []): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">Chưa có phạm vi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listPhamVi as $phamVi): ?>
                            <tr>
                                <td><?= $escapePhamVi($phamVi->get('id')) ?></td>
                                <td><?= $escapePhamVi($phamVi->get('ten')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
