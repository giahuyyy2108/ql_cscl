-- Add list pages beneath the existing Danh muc menu, after Don vi tinh.
-- Existing routes, including trangthai, are reused when this script is rerun.
-- The danhmuc_kctt table uses the alphanumeric route danhmuckctt for .htaccess.

SET @danhmuc_parent = (
    SELECT `maChucNang`
    FROM `chucnang`
    WHERE `level` = 1 AND `url` = '' AND `parentQuyen` = 'danhmuc'
    ORDER BY `maChucNang`
    LIMIT 1
);

SET @danhmuc_next_order = (
    SELECT COALESCE(MAX(`order`), 0) + 1
    FROM `chucnang`
    WHERE `maChucNang` = @danhmuc_parent
       OR (`parent` = @danhmuc_parent AND `url` = 'donvitinh')
);

INSERT INTO `chucnang`
    (`tenChucNang`, `parent`, `url`, `logo`, `parentQuyen`,
     `tenChucNangCon`, `urlChucNangCon`, `order`, `level`)
SELECT menu.`ten`, @danhmuc_parent, menu.`url`, '', '', '', '',
       @danhmuc_next_order + menu.`position`, 2
FROM (
    SELECT 'chuky' AS `url`, 'Chu kỳ' AS `ten`, 0 AS `position`
    UNION ALL SELECT 'trangthai', 'Trạng thái', 1
    UNION ALL SELECT 'danhmuckctt', 'Khía cạnh / Thành tố', 2
    UNION ALL SELECT 'phamvi', 'Phạm vi', 3
) AS menu
LEFT JOIN `chucnang` AS existing ON existing.`url` = menu.`url`
WHERE @danhmuc_parent IS NOT NULL AND existing.`maChucNang` IS NULL;

UPDATE `chucnang` AS item
JOIN (
    SELECT 'chuky' AS `url`, 'Chu kỳ' AS `ten`, 0 AS `position`
    UNION ALL SELECT 'trangthai', 'Trạng thái', 1
    UNION ALL SELECT 'danhmuckctt', 'Khía cạnh / Thành tố', 2
    UNION ALL SELECT 'phamvi', 'Phạm vi', 3
) AS menu ON item.`url` = menu.`url`
SET item.`tenChucNang` = menu.`ten`,
    item.`parent` = @danhmuc_parent,
    item.`parentQuyen` = '',
    item.`level` = 2,
    item.`order` = @danhmuc_next_order + menu.`position`
WHERE @danhmuc_parent IS NOT NULL;
