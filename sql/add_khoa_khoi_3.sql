-- Danh mục khoa/phòng thuộc khối có id = 3
-- Có thể chạy lại an toàn: chuẩn hóa tên, cập nhật đúng khối và chỉ thêm đơn vị còn thiếu.
SET NAMES utf8mb4;

SET @id_khoi := 3;

DROP TEMPORARY TABLE IF EXISTS tmp_khoa_khoi_3;
CREATE TEMPORARY TABLE tmp_khoa_khoi_3 (
    ten varchar(255) NOT NULL PRIMARY KEY
) ENGINE=Memory DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tmp_khoa_khoi_3 (ten) VALUES
('Kiểm soát nhiễm khuẩn'),
('Dược'),
('Phòng Đào tạo - Chỉ đạo tuyến'),
('Phòng Công nghệ thông tin'),
('Phòng Điều dưỡng'),
('Phòng Hành chính'),
('Phòng Kế hoạch tổng hợp'),
('Phòng QLCL và CTXH'),
('Phòng Quản trị'),
('Phòng Tài chính kế toán'),
('Phòng Tổ chức cán bộ'),
('Phòng Thiết bị y tế');

-- Chuẩn hóa tên và chuyển các đơn vị đã có về khối 3.
UPDATE khoa k
INNER JOIN tmp_khoa_khoi_3 d ON d.ten = k.ten
SET k.ten = d.ten,
    k.id_khoi = @id_khoi,
    k.UPDATE_AT = CURDATE();

-- Thêm những đơn vị chưa tồn tại.
INSERT INTO khoa (ten, id_khoi, UPDATE_AT, CREATE_AT)
SELECT d.ten, @id_khoi, CURDATE(), CURDATE()
FROM tmp_khoa_khoi_3 d
WHERE EXISTS (SELECT 1 FROM khoi WHERE id = @id_khoi)
  AND NOT EXISTS (
      SELECT 1
      FROM khoa k
      WHERE k.ten = d.ten
  );

DROP TEMPORARY TABLE tmp_khoa_khoi_3;
