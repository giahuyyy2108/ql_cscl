-- Danh mục khoa thuộc Khối lâm sàng
-- Có thể chạy lại an toàn: chuẩn hóa các tên cũ và chỉ thêm khoa còn thiếu.
SET NAMES utf8mb4;

SET @id_khoi_lam_sang := (
    SELECT id
    FROM khoi
    WHERE ten = 'Khối lâm sàng'
    ORDER BY id
    LIMIT 1
);

UPDATE khoa SET ten = 'Cấp cứu', id_khoi = @id_khoi_lam_sang, UPDATE_AT = CURDATE()
WHERE ten = 'Khoa Cấp cứu';
UPDATE khoa SET ten = 'Khám bệnh TYC', id_khoi = @id_khoi_lam_sang, UPDATE_AT = CURDATE()
WHERE ten = 'Khám bệnh B (KB TYC)';
UPDATE khoa SET ten = 'Khám bệnh', id_khoi = @id_khoi_lam_sang, UPDATE_AT = CURDATE()
WHERE ten = 'Khám bệnh A';
UPDATE khoa SET ten = 'Ngoại Chấn thương - Chỉnh hình', id_khoi = @id_khoi_lam_sang, UPDATE_AT = CURDATE()
WHERE ten = 'Ngoại chấn thương chỉnh hình';
UPDATE khoa SET ten = 'Nội Cơ xương khớp', id_khoi = @id_khoi_lam_sang, UPDATE_AT = CURDATE()
WHERE BINARY ten = BINARY 'Nội cơ xương khớp';

DROP TEMPORARY TABLE IF EXISTS tmp_khoa_lam_sang;
CREATE TEMPORARY TABLE tmp_khoa_lam_sang (
    ten varchar(255) NOT NULL PRIMARY KEY
) ENGINE=Memory DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tmp_khoa_lam_sang (ten) VALUES
('Cấp cứu'),
('Điều trị cán bộ cao cấp'),
('Da liễu - Miễn dịch dị ứng'),
('Dinh dưỡng lâm sàng'),
('Hồi sức tích cực chống độc'),
('Khám bệnh'),
('Khám bệnh TYC'),
('Mắt'),
('Ngoại Chấn thương - Chỉnh hình'),
('Ngoại điều trị theo yêu cầu'),
('Ngoại Gan Mật'),
('Ngoại Thần kinh'),
('Ngoại Tiết niệu - Nam học'),
('Ngoại Tiêu hóa'),
('Ngoại Tim mạch - Lồng ngực'),
('Nhịp tim'),
('Nội Cơ xương khớp'),
('Nội điều trị theo yêu cầu'),
('Nội Hô hấp'),
('Nội nhiễm'),
('Nội thận - Lọc máu'),
('Nội Thần kinh'),
('Nội tiết'),
('Nội Tiêu hóa'),
('Nội Tim Mạch'),
('Phẫu thuật - Gây mê hồi sức'),
('Phẫu thuật hàm mặt - TH. thẩm mỹ'),
('Tai Mũi Họng'),
('Tim mạch cấp cứu & can thiệp'),
('Ung bướu'),
('Phục hồi chức năng'),
('Y học Cổ truyền'),
('Phòng BVSKCBTW-2B');

-- Chuyển các khoa đã có trong danh mục về đúng khối.
UPDATE khoa k
INNER JOIN tmp_khoa_lam_sang d ON d.ten = k.ten
SET k.id_khoi = @id_khoi_lam_sang,
    k.UPDATE_AT = CURDATE();

-- Thêm những khoa chưa tồn tại.
INSERT INTO khoa (ten, id_khoi, UPDATE_AT, CREATE_AT)
SELECT d.ten, @id_khoi_lam_sang, CURDATE(), CURDATE()
FROM tmp_khoa_lam_sang d
WHERE @id_khoi_lam_sang IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM khoa k
      WHERE k.ten = d.ten
  );

DROP TEMPORARY TABLE tmp_khoa_lam_sang;
