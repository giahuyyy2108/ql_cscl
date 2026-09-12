<?php
$listChuKy = $request->getAttribute('listChuKy');
if (!is_array($listChuKy)) {
    $listChuKy = [];
}
$escapeChuKy = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
?>

<div class="x_panel">
    <div class="x_title">
        <h2>Chu kỳ <small>Danh mục</small></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <p>Danh sách chu kỳ theo dõi các chỉ số chất lượng.</p>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <caption class="sr-only">Danh sách chu kỳ theo dõi các chỉ số chất lượng.</caption>
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Tên chu kỳ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($listChuKy === []): ?>
                        <tr>
                            <td colspan="2" class="text-center text-muted">Chưa có chu kỳ.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listChuKy as $chuKy): ?>
                            <tr>
                                <td><?= $escapeChuKy($chuKy->get('id')) ?></td>
                                <td><?= $escapeChuKy($chuKy->get('ten')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
