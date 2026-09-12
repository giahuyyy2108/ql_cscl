-- Configure the existing menu records in this project's database:
-- HE THONG (1) -> Danh muc (91) -> Don vi tinh (92).
-- The current renderer requires the child immediately after its parent by `order`.

UPDATE `chucnang`
SET `tenChucNang` = 'Danh mục',
    `parent` = 1,
    `url` = '',
    `parentQuyen` = 'danhmuc',
    `logo` = 'fa fa-folder-open',
    `level` = 1,
    `order` = 80
WHERE `maChucNang` = 91
  AND (`url` = 'danhmuc' OR (`url` = '' AND `parentQuyen` = 'danhmuc'));

UPDATE `chucnang`
SET `parent` = 91,
    `parentQuyen` = '',
    `level` = 2,
    `order` = 81
WHERE `maChucNang` = 92
  AND `url` = 'donvitinh';
