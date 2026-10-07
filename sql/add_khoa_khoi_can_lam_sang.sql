-- Danh mục khoa thuộc Khối cận lâm sàng
-- Có thể chạy lại an toàn: cập nhật đúng khối và chỉ thêm khoa còn thiếu.
SET NAMES utf8mb4;

SET @id_khoi_can_lam_sang := (
    SELECT id
    FROM khoi
    WHERE ten = 'Khối cận lâm sàng'
    ORDER BY id
    LIMIT 1
);

DROP TEMPORARY TABLE IF EXISTS tmp_khoa_can_lam_sang;
CREATE TEMPORARY TABLE tmp_khoa_can_lam_sang (
    ten varchar(255) NOT NULL PRIMARY KEY
) ENGINE=Memory DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tmp_khoa_can_lam_sang (ten) VALUES
('Hóa sinh'),
('Huyết học'),
('Vi Sinh'),
('Thăm dò chức năng'),
('Chẩn đoán hình ảnh'),
('Giải phẫu bệnh');

-- Chuyển các khoa đã có trong danh mục về đúng khối.
UPDATE khoa k
INNER JOIN tmp_khoa_can_lam_sang d ON d.ten = k.ten
SET k.id_khoi = @id_khoi_can_lam_sang,
    k.UPDATE_AT = CURDATE();

-- Thêm những khoa chưa tồn tại.
INSERT INTO khoa (ten, id_khoi, UPDATE_AT, CREATE_AT)
SELECT d.ten, @id_khoi_can_lam_sang, CURDATE(), CURDATE()
FROM tmp_khoa_can_lam_sang d
WHERE @id_khoi_can_lam_sang IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM khoa k
      WHERE k.ten = d.ten
  );

DROP TEMPORARY TABLE tmp_khoa_can_lam_sang;
