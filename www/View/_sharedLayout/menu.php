<?php
$listChucNang = $request->getAttribute('listChucNang');
if (!is_array($listChucNang) && !($listChucNang instanceof Traversable)) {
    $listChucNang = [];
}

$strEndL0 = '';
$strEndL1 = '';
$escapeMenu = static function ($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};
?>

<div id="sidebar-menu" class="main_menu_side hidden-print d-print-none main_menu" role="navigation" aria-label="Menu chính">
    <?php foreach ($listChucNang as $chucnang): ?>
        <?php
        $url = (string) $chucnang->get('url');
        $quyen = $url !== '' ? $url : $chucnang->get('parentQuyen');
        if (!$request->checkRole($quyen)) {
            continue;
        }

        $level = (string) $chucnang->get('level');
        $tenChucNang = $escapeMenu($chucnang->get('tenChucNang'));
        ?>

        <?php if ($level === '0'): ?>
            <?= $strEndL1 ?>
            <?= $strEndL0 ?>
            <div class="menu_section">
                <h3><?= $tenChucNang ?></h3>
                <ul class="nav side-menu">
            <?php
            $strEndL0 = '</ul></div>';
            $strEndL1 = '';
            ?>
        <?php elseif ($level === '1'): ?>
            <?= $strEndL1 ?>
            <?php
            $strEndL1 = '';
            $logo = trim((string) $chucnang->get('logo'));
            $logo = $logo !== '' ? $logo : 'fa fa-th-large';
            ?>

            <?php if ($url !== ''): ?>
                <li>
                    <a href="<?= $escapeMenu(_DEFAULT_URL_ . $url . '/') ?>" title="<?= $tenChucNang ?>">
                        <i class="menu-icon <?= $escapeMenu($logo) ?>" aria-hidden="true"></i>
                        <span class="menu-label"><?= $tenChucNang ?></span>
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a title="<?= $tenChucNang ?>" role="button" tabindex="0"
                       onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }">
                        <i class="menu-icon <?= $escapeMenu($logo) ?>" aria-hidden="true"></i>
                        <span class="menu-label"><?= $tenChucNang ?></span>
                        <span class="menu-chevron fa fa-chevron-down" aria-hidden="true"></span>
                    </a>
                    <ul class="nav child_menu">
                <?php $strEndL1 = '</ul></li>'; ?>
            <?php endif; ?>
        <?php elseif ($level === '2'): ?>
            <li>
                <a href="<?= $escapeMenu(_DEFAULT_URL_ . $url . '/') ?>" title="<?= $tenChucNang ?>">
                    <span class="menu-label"><?= $tenChucNang ?></span>
                </a>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>

    <?= $strEndL1 ?>
    <?= $strEndL0 ?>
</div>
