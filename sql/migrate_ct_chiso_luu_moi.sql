-- Mỗi lần gửi biểu mẫu là một lượt nhập mới, không cập nhật JSON của lượt cũ.
-- Chỉ chạy lệnh DROP nếu CSDL hiện tại còn unique key từ cấu trúc cũ.
ALTER TABLE `ct_chiso`
    DROP INDEX `uq_ct_chiso_khoa`,
    ADD INDEX `idx_ct_chiso_luot_nhap` (`ma_chi_so`, `id_khoaphong`, `nam`, `ky`);
