<?php
$listTrangThai = $request->getAttribute('listTrangThai');
if (!is_array($listTrangThai)) {
    $listTrangThai = [];
}
$escapeTrangThai = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
?>

<div class="x_panel">
    <div class="x_title">
        <h2>Trạng thái <small>Danh mục</small></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <p>Danh sách trạng thái sử dụng cho các chỉ số chất lượng.</p>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <caption class="sr-only">Danh sách trạng thái sử dụng cho các chỉ số chất lượng.</caption>
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($listTrangThai === []): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">Chưa có trạng thái.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listTrangThai as $trangThai): ?>
                            <tr>
                                <td><?= $escapeTrangThai($trangThai->get('maTrangThai')) ?></td>
                                <td>
                                    <span class="badge <?= $escapeTrangThai($trangThai->get('tag')) ?>"><?= $escapeTrangThai($trangThai->get('tenTrangThai')) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
