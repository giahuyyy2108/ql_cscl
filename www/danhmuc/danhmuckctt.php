<?php
$listDanhMucKCTT = $request->getAttribute('listDanhMucKCTT');
if (!is_array($listDanhMucKCTT)) {
    $listDanhMucKCTT = [];
}
$escapeDanhMucKCTT = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$loaiDanhMuc = ['khia_canh' => 'Khía cạnh', 'thanh_to' => 'Thành tố'];
?>

<div class="x_panel">
    <div class="x_title">
        <h2>Khía cạnh / Thành tố <small>Danh mục</small></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <p>Danh sách khía cạnh và thành tố dùng để phân loại các chỉ số chất lượng.</p>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <caption class="sr-only">Danh sách khía cạnh và thành tố dùng để phân loại các chỉ số chất lượng.</caption>
                <thead>
                    <tr>
                        <th scope="col">Mã</th>
                        <th scope="col">Loại</th>
                        <th scope="col">Tên</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($listDanhMucKCTT === []): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Chưa có khía cạnh hoặc thành tố.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($listDanhMucKCTT as $danhMucKCTT): ?>
                            <tr>
                                <td><?= $escapeDanhMucKCTT($danhMucKCTT->get('id')) ?></td>
                                <td><?= $escapeDanhMucKCTT($loaiDanhMuc[$danhMucKCTT->get('loai')] ?? $danhMucKCTT->get('loai')) ?></td>
                                <td><?= $escapeDanhMucKCTT($danhMucKCTT->get('ten')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
