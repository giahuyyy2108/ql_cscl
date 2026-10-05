-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 05, 2026 lúc 10:54 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ql_cscl`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bacsi`
--

CREATE TABLE `bacsi` (
  `MaBacSi` int(111) NOT NULL,
  `TenBacSi` varchar(200) NOT NULL,
  `GioiTinh` varchar(10) NOT NULL,
  `soDienThoai` varchar(30) NOT NULL,
  `MaKhoaPhong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bang_kiem`
--

CREATE TABLE `bang_kiem` (
  `id` int(11) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0: khong cong diem, 1: co cong diem',
  `cau_hoi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`cau_hoi`)),
  `update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `create_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `bang_kiem`
--

INSERT INTO `bang_kiem` (`id`, `ten`, `type`, `cau_hoi`, `update_at`, `create_at`) VALUES
(1, 'Thông tin bệnh nhân', 0, '{\"version\":1,\"cau_hoi\":[{\"id\":\"q1\",\"ten\":\"A1\",\"noi_dung\":\"Giới tính\",\"loai\":\"radio\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Nam\",\"diem\":0},{\"noi_dung\":\"Nữ\",\"diem\":0}]},{\"id\":\"q2\",\"ten\":\"A2\",\"noi_dung\":\"Tuổi\",\"loai\":\"number\",\"bat_buoc\":false,\"lua_chon\":[]},{\"id\":\"q3\",\"ten\":\"A3\",\"noi_dung\":\"Số di động liên hệ\",\"loai\":\"number\",\"bat_buoc\":false,\"lua_chon\":[]},{\"id\":\"q4\",\"ten\":\"A4\",\"noi_dung\":\"Số ngày nằm bệnh\",\"loai\":\"number\",\"bat_buoc\":false,\"lua_chon\":[]},{\"id\":\"q5\",\"ten\":\"A5\",\"noi_dung\":\"Ông\\/Bà có sử dụng thẻ BHYT cho lần điều trị này không?\",\"loai\":\"radio\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Có\",\"diem\":0},{\"noi_dung\":\"Không\",\"diem\":0}]},{\"id\":\"q6\",\"ten\":\"A6\",\"noi_dung\":\"Nơi sống hiện nay\",\"loai\":\"radio\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Thành thị\",\"diem\":0},{\"noi_dung\":\"Nông thôn\",\"diem\":0},{\"noi_dung\":\"Vùng sâu, xa khó khăn\",\"diem\":0}]},{\"id\":\"q7\",\"ten\":\"A7.\",\"noi_dung\":\"Phân loại mức sống của gia đình\",\"loai\":\"radio\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Nghèo\",\"diem\":0},{\"noi_dung\":\"Cận nghèo\",\"diem\":0},{\"noi_dung\":\"Khác\",\"diem\":0}]},{\"id\":\"q8\",\"ten\":\"A8\",\"noi_dung\":\"Đây là lần điều trị thứ mấy của Ông\\/Bà tại bệnh viện? Lần thứ\",\"loai\":\"number\",\"bat_buoc\":false,\"lua_chon\":[]}]}', '2026-09-28 16:08:47', '2026-09-28 15:52:32'),
(5, 'A. Khả năng tiếp cận', 1, '{\"version\":1,\"cau_hoi\":[{\"id\":\"q1\",\"ten\":\"A1\",\"noi_dung\":\"Các sơ đồ, biển báo chỉ dẫn đường đến các khoa, phòng và thông báo giờ khám, chữa bệnh, giờ vào thăm rõ ràng, dễ hiểu.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q3\",\"ten\":\"A2\",\"noi_dung\":\"Các toà nhà, cầu thang bộ, thang máy, buồng bệnh được đánh số và hướng dẫn rõ ràng, dễ tìm.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q4\",\"ten\":\"A3\",\"noi_dung\":\"Các lối đi trong bệnh viện, hành lang bằng phẳng, an toàn, dễ đi.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q5\",\"ten\":\"A4\",\"noi_dung\":\"Thời gian chờ đợi thang máy, làn thủ tục và chờ đợi trong quá tình khám, chữa bệnh chấp nhận được.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q6\",\"ten\":\"A5\",\"noi_dung\":\"Người bệnh hỏi và gọi được nhân viên y tế khi cần thiết.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]}]}', '2026-09-28 16:19:46', '2026-09-28 15:37:39'),
(8, 'B. Sự minh bạch thông tin và thủ tục khám bệnh, điều trị', 1, '{\"version\":1,\"cau_hoi\":[{\"id\":\"q1\",\"ten\":\"B1\",\"noi_dung\":\"Quy trình, thủ tục hành chính (nhập, xuất viện, chuyển viện, chuyển khoa…) rõ ràng, công khai, thuận tiện.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q2\",\"ten\":\"B2\",\"noi_dung\":\"Giá dịch vụ y tế được niêm yết, thông báo công khai ở vị trí dễ quan sát, dễ đọc, dễ hiểu và được tư vấn, giải thích các chi phí cao nếu có.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q3\",\"ten\":\"B3\",\"noi_dung\":\"Quy trình, thời gian làm thủ tục thanh toán viện phí khi ra viện rõ ràng, công khai, thuận tiện.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q4\",\"ten\":\"B4\",\"noi_dung\":\"Được phổ biến về nội quy và những thông tin cần thiết khi nằm viện rõ ràng, đầy đủ.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q5\",\"ten\":\"B5\",\"noi_dung\":\"Được giải thích về tình trạng bệnh, phương pháp và thời gian dự kiến điều trị rõ ràng, đầy đủ.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q6\",\"ten\":\"B6\",\"noi_dung\":\"Được giải thích, tư vấn trước khi yêu cầu làm các xét nghiệm, thăm dò, kỹ thuật cao rõ ràng, đầy đủ.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q7\",\"ten\":\"B7\",\"noi_dung\":\"Được công khai và cập nhật thông tin về dùng thuốc và chi phí điều trị.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]}]}', '2026-09-28 16:35:59', '2026-09-28 16:35:59'),
(9, 'C. Cơ sở vật chất và phương tiện phục vụ người bệnh', 1, '{\"version\":1,\"cau_hoi\":[{\"id\":\"q1\",\"ten\":\"C1\",\"noi_dung\":\"Buồng bệnh khang trang, sạch sẽ, có đầy đủ các thiết bị điều chỉnh nhiệt độ phù hợp như quạt, máy sưởi, hoặc điều hòa.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q2\",\"ten\":\"C2\",\"noi_dung\":\"Buồng bệnh yên tĩnh, bảo đảm an toàn, an ninh, trật tự, phòng ngừa trộm cặp, yên tâm khi nằm viện.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q3\",\"ten\":\"C3\",\"noi_dung\":\"Giường bệnh, ga, gối đầy đủ cho mỗi người một giường, chắc chắn, sử dụng tốt.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q4\",\"ten\":\"C4\",\"noi_dung\":\"Được cung cấp quần áo đầy đủ, sạch sẽ.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q5\",\"ten\":\"C5\",\"noi_dung\":\"Nhà vệ sinh, nhà tắm thuận tiện, sạch sẽ, sử dụng tốt.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q6\",\"ten\":\"C6\",\"noi_dung\":\"Được cung cấp đầy đủ nước uống nóng, lạnh ngay tại khoa điều trị.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q7\",\"ten\":\"C7\",\"noi_dung\":\"Người bệnh và người nhà người bệnh truy cập dược mạng interrnet không dây (wifi) ngay tại buồng bệnh.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q8\",\"ten\":\"C8\",\"noi_dung\":\"Được bảo đảm sự riêng tư khi nằm viện như thay quần áo, khám bệnh, đi vệ sinh tại giường… có rèm che, vách ngăn hoặc nằm riêng.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q9\",\"ten\":\"C9\",\"noi_dung\":\"Căng-tin bệnh viện phục vụ ăn uống và nhu cầu sinh hoạt thiết yếu đầy đủ và chất lượng\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q10\",\"ten\":\"C10\",\"noi_dung\":\"Môi trường trong khuôn viên bệnh viện xanh, sạch, đẹp.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]},{\"id\":\"q11\",\"ten\":\"C11\",\"noi_dung\":\"Được cung cấp phương tiện vận chuyển nội viện như xe lăn, cáng, xe điện đầy đủ, kịp thời, sử dụng tốt khi có nhu cầu.\",\"loai\":\"satisfaction\",\"bat_buoc\":false,\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":1},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":2},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":3},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":4},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":5},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":0}]}]}', '2026-09-28 16:40:15', '2026-09-28 16:40:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `benhnhan`
--

CREATE TABLE `benhnhan` (
  `id` int(11) NOT NULL,
  `maBN` varchar(255) NOT NULL,
  `tenBN` longtext DEFAULT NULL,
  `namSinh` int(11) DEFAULT NULL,
  `gioiTinh` varchar(255) DEFAULT NULL,
  `soDienThoai` varchar(11) DEFAULT NULL,
  `chuanDoan` longtext DEFAULT NULL,
  `ngayTao` datetime DEFAULT NULL,
  `ngayGoi` datetime DEFAULT NULL,
  `ngayGoiMoiNhat` datetime DEFAULT NULL,
  `maTrangThai` int(11) NOT NULL,
  `bacSi` longtext DEFAULT NULL,
  `quayTiepNhan` longtext DEFAULT NULL,
  `trangThaiXoa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_so_chat_luong`
--

CREATE TABLE `chi_so_chat_luong` (
  `ma_chi_so` bigint(20) UNSIGNED NOT NULL,
  `ten_chi_so` varchar(255) NOT NULL,
  `ma_khia_canh` int(10) UNSIGNED DEFAULT NULL,
  `ma_thanh_to` int(10) UNSIGNED DEFAULT NULL,
  `nhom_chi_so` varchar(150) NOT NULL DEFAULT '',
  `pham_vi` int(11) NOT NULL DEFAULT 1,
  `muc_tieu` varchar(100) NOT NULL DEFAULT '',
  `nguong_canh_bao` varchar(100) NOT NULL DEFAULT '',
  `id_donvitinh` int(11) NOT NULL DEFAULT 1,
  `id_chuky` int(30) NOT NULL DEFAULT 1,
  `du_lieu_chu_ky` longtext DEFAULT NULL,
  `loai_cong_thuc` varchar(50) NOT NULL DEFAULT 'T??? l??? %',
  `cong_thuc` varchar(500) NOT NULL DEFAULT '',
  `trang_thai` int(11) NOT NULL DEFAULT 0,
  `nguoi_gui` int(11) NOT NULL,
  `id_khoaphong` int(11) NOT NULL,
  `phong` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`phong`)),
  `thoi_gian_gui` datetime DEFAULT NULL,
  `nguoi_duyet` varchar(100) DEFAULT NULL,
  `thoi_gian_duyet` datetime DEFAULT NULL,
  `ly_do_tu_choi` varchar(500) NOT NULL DEFAULT '',
  `dinh_nghia` text NOT NULL,
  `thu_thap` text NOT NULL,
  `ten_tu_so` varchar(255) NOT NULL DEFAULT '',
  `ten_mau_so` varchar(255) NOT NULL DEFAULT '',
  `bieumau` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[]' CHECK (json_valid(`bieumau`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ma_chi_so_goc` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_so_chat_luong`
--

INSERT INTO `chi_so_chat_luong` (`ma_chi_so`, `ten_chi_so`, `ma_khia_canh`, `ma_thanh_to`, `nhom_chi_so`, `pham_vi`, `muc_tieu`, `nguong_canh_bao`, `id_donvitinh`, `id_chuky`, `du_lieu_chu_ky`, `loai_cong_thuc`, `cong_thuc`, `trang_thai`, `nguoi_gui`, `id_khoaphong`, `phong`, `thoi_gian_gui`, `nguoi_duyet`, `thoi_gian_duyet`, `ly_do_tu_choi`, `dinh_nghia`, `thu_thap`, `ten_tu_so`, `ten_mau_so`, `bieumau`, `created_at`, `updated_at`, `ma_chi_so_goc`) VALUES
(181, '123', 2, 5, '', 1, '60', '55', 1, 1, NULL, '', '', 2, 251, 2, '[2]', '2026-09-29 09:13:33', '251', '2026-09-29 09:13:35', '', '', '', '', '', '{\"version\":\"1\",\"cau_hoi\":[{\"id\":\"q1\",\"noi_dung\":\"THÔNG TIN BỆNH NHÂN\",\"loai\":\"category\",\"ky_hieu\":\"I\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[]},{\"id\":\"q2\",\"noi_dung\":\"Họ tên\",\"loai\":\"short_text\",\"ky_hieu\":\"A1\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[]},{\"id\":\"q3\",\"noi_dung\":\"SĐT\",\"loai\":\"number\",\"ky_hieu\":\"A2\",\"bat_buoc\":\"\",\"do_dai_so\":\"10\",\"lua_chon\":[]},{\"id\":\"q4\",\"noi_dung\":\"Năm sinh\",\"loai\":\"short_text\",\"ky_hieu\":\"A3\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[]},{\"id\":\"q5\",\"noi_dung\":\"ĐÁNH GIÁ VIỆC SỬ DỤNG DỊCH VỤ Y TẾ\",\"loai\":\"category\",\"ky_hieu\":\"II\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[]},{\"id\":\"q6\",\"noi_dung\":\"Khả năng tiếp cận\",\"loai\":\"subcategory\",\"ky_hieu\":\"A\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[]},{\"id\":\"q7\",\"noi_dung\":\"Các sơ đồ, biển báo chỉ dẫn đường đến các khoa, phòng và thông báo giờ khám, chữa bệnh, giờ vào thăm rõ ràng, dễ hiểu.\",\"loai\":\"satisfaction\",\"ky_hieu\":\"A1\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":\"1\"},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":\"2\"},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":\"3\"},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":\"4\"},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":\"5\"},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":\"0\"}]},{\"id\":\"q8\",\"noi_dung\":\"Đánh giá chung, bệnh viện đã đáp ứng được bao nhiêu % so với mong đợi của Ông\\/Bà trước khi nằm viện?\",\"loai\":\"percentage\",\"ky_hieu\":\"A2\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[]},{\"id\":\"q9\",\"noi_dung\":\"Sự minh bạch thông tin và thủ tục khám bệnh, điều trị\",\"loai\":\"subcategory\",\"ky_hieu\":\"B\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[]},{\"id\":\"q10\",\"noi_dung\":\"Quy trình, thủ tục hành chính (nhập, xuất viện, chuyển viện, chuyển khoa…) rõ ràng, công khai, thuận tiện.\",\"loai\":\"satisfaction\",\"ky_hieu\":\"B1\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":\"1\"},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":\"2\"},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":\"3\"},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":\"4\"},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":\"5\"},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":\"0\"}]},{\"id\":\"q11\",\"noi_dung\":\"Giá dịch vụ y tế được niêm yết, thông báo công khai ở vị trí dễ quan sát, dễ đọc, dễ hiểu và được tư vấn, giải thích các chi phí cao nếu có.\",\"loai\":\"satisfaction\",\"ky_hieu\":\"B2\",\"bat_buoc\":\"\",\"do_dai_so\":\"0\",\"lua_chon\":[{\"noi_dung\":\"Rất không hài lòng \\/ Rất kém\",\"diem\":\"1\"},{\"noi_dung\":\"Không hài lòng \\/ Kém\",\"diem\":\"2\"},{\"noi_dung\":\"Bình thường \\/ Trung bình\",\"diem\":\"3\"},{\"noi_dung\":\"Hài lòng \\/ Tốt\",\"diem\":\"4\"},{\"noi_dung\":\"Rất hài lòng \\/ Rất tốt\",\"diem\":\"5\"},{\"noi_dung\":\"Không sử dụng, không ý kiến\",\"diem\":\"0\"}]}]}', '2026-09-29 09:05:13', '2026-09-29 09:18:46', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chucnang`
--

CREATE TABLE `chucnang` (
  `maChucNang` int(11) NOT NULL,
  `tenChucNang` varchar(255) NOT NULL,
  `parent` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `logo` varchar(200) NOT NULL,
  `parentQuyen` varchar(255) NOT NULL,
  `tenChucNangCon` text NOT NULL,
  `urlChucNangCon` text NOT NULL,
  `order` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Đang đổ dữ liệu cho bảng `chucnang`
--

INSERT INTO `chucnang` (`maChucNang`, `tenChucNang`, `parent`, `url`, `logo`, `parentQuyen`, `tenChucNangCon`, `urlChucNangCon`, `order`, `level`) VALUES
(1, 'HỆ THỐNG', 0, '', '', 'hethong', '', '', 64, 0),
(2, 'Người dùng', 1, 'user', 'fa fa-user', '', 'Xem,Thêm/Sửa,Xóa,Phân quyền, Khóa', 'user,user.saveUser,user.deleteUser,user.phanquyen,user.lock,', 66, 1),
(4, 'Xem Log', 1, 'log', 'fa fa-street-view', '', 'Xem,Xóa', 'log,log.deleteLog,', 90, 1),
(5, 'Nhóm quyền', 1, 'nhomquyen', 'fa fa-group', '', 'Xem,Thêm/Sửa,Xóa,Phân quyền', 'nhomquyen,nhomquyen.save,nhomquyen.delete,nhomquyen.phanquyen,', 66, 1),
(10, 'Trạng thái', 91, 'trangthai', '', '', 'Xem,Thêm/Sửa,Xóa', 'trangthai,trangthai.save,trangthai.delete', 83, 2),
(94, 'Khía cạnh / Thành tố', 91, 'danhmuckctt', '', '', 'Xem,Thêm/Sửa,Xóa', 'danhmuckctt,danhmuckctt.save,danhmuckctt.delete', 84, 2),
(95, 'Phạm vi', 91, 'phamvi', '', '', 'Xem,Thêm/Sửa,Xóa', 'phamvi,phamvi.save,phamvi.delete', 85, 2),
(93, 'Chu kỳ', 91, 'chuky', '', '', 'Xem,Thêm/Sửa,Xóa', 'chuky,chuky.save,chuky.delete', 82, 2),
(91, 'Danh mục', 1, '', 'fa fa-folder-open', 'danhmuc', '', '', 80, 1),
(92, 'Đơn vị tính', 91, 'donvitinh', 'fa fa-signal', '', 'Xem,Thêm/Sửa,Xóa', 'donvitinh,donvitinh.save,donvitinh.delete', 81, 2),
(87, 'Về Giao Diện', 1, 'giaodien', 'fa fa-arrow-right', '', 'Xem', '', 65, 1),
(89, 'Chỉ số chất lượng', 1, '', 'fa fa-signal', 'chiso', '', '', 67, 1),
(90, 'Dash board', 1, 'dashboard', 'fa fa-signal', '', 'Xem', '', 64, 1),
(101, 'Nhập liệu', 1, 'nhaplieu', '', '', 'Xem', 'nhaplieu', 110, 1),
(102, 'Quản lý', 1, '', 'fa fa-signal', 'kp', '', '', 100, 1),
(100, 'CSCL toàn viện', 89, 'chisochatluong', 'fa fa-signal', '', 'Xem,Thêm,Sửa,Xóa,Gửi,Duyệt,Từ chối,Khoa Phòng A (được chọn khoa),Khoa Phòng B', 'chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui,chisochatluong.duyet,chisochatluong.tuchoi,chisochatluong.khoaA,chisochatluong.khoaB', 68, 2),
(99, 'CSCL khoa/Phòng', 89, 'chisokhoa', '', '', 'Xem', '', 69, 2),
(103, 'Khoa/Phòng', 102, 'khoaphong', '', '', 'Xem,Thêm/Sửa,Xóa', 'khoaphong,khoaphong.save,khoaphong.delete', 101, 2),
(104, 'Khối', 102, 'khoi', '', '', 'Xem,Thêm/Sửa,Xóa', 'khoi,khoi.save,khoi.delete', 102, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuky`
--

CREATE TABLE `chuky` (
  `id` int(11) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `chuky` int(11) NOT NULL,
  `UPDATE_AT` date NOT NULL DEFAULT current_timestamp(),
  `CREATE_AT` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chuky`
--

INSERT INTO `chuky` (`id`, `ten`, `chuky`, `UPDATE_AT`, `CREATE_AT`) VALUES
(1, 'Tháng', 12, '2026-09-11', '2026-09-11'),
(2, 'Quý', 4, '2026-09-18', '2026-09-11'),
(3, 'Năm', 1, '2026-09-11', '2026-09-11'),
(4, '6 tháng', 2, '2026-09-11', '2026-09-11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `codemaster`
--

CREATE TABLE `codemaster` (
  `id` varchar(10) NOT NULL,
  `year` varchar(2) NOT NULL,
  `curvalue` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='luu so nhay cua cac phieu';

--
-- Đang đổ dữ liệu cho bảng `codemaster`
--

INSERT INTO `codemaster` (`id`, `year`, `curvalue`, `active`, `description`) VALUES
('PCK', '20', 9, 1, ''),
('DDH', '20', 21, 1, ''),
('PGC', '20', 5, 1, ''),
('PX', '20', 52, 1, ''),
('PN', '20', 16, 1, ''),
('HS', '20', 8, 1, ''),
('GH', '20', 3, 1, ''),
('DH', '20', 25, 1, ''),
('HD', '20', 30, 1, ''),
('pn', '20', 2, 1, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ct_chiso`
--

CREATE TABLE `ct_chiso` (
  `id` int(11) NOT NULL,
  `ma_chi_so` bigint(20) UNSIGNED NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_khoaphong` int(11) NOT NULL,
  `nam` smallint(6) NOT NULL,
  `ky` tinyint(4) NOT NULL,
  `du_lieu` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`du_lieu`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ct_chiso`
--

INSERT INTO `ct_chiso` (`id`, `ma_chi_so`, `id_user`, `id_khoaphong`, `nam`, `ky`, `du_lieu`, `created_at`, `updated_at`) VALUES
(130, 181, 251, 2, 2026, 1, '{\"cau_tra_loi\":{\"q2\":\"Vũ Quốc GIa Huy\",\"q3\":\"0123456789\",\"q4\":\"2001\",\"q7\":\"Rất hài lòng \\/ Rất tốt\",\"q8\":\"50\",\"q10\":\"Rất hài lòng \\/ Rất tốt\",\"q11\":\"Rất hài lòng \\/ Rất tốt\"},\"tong_diem\":15,\"diem_toi_da\":15,\"ty_le_phan_tram\":100}', '2026-09-29 09:15:29', '2026-09-29 09:15:29'),
(131, 181, 251, 2, 2026, 1, '{\"cau_tra_loi\":{\"q2\":\"Nguyễn Hoàng An Khánh\",\"q3\":\"0123456789\",\"q4\":\"2007\",\"q7\":\"Hài lòng \\/ Tốt\",\"q8\":\"90\",\"q10\":\"Rất không hài lòng \\/ Rất kém\",\"q11\":\"Rất không hài lòng \\/ Rất kém\"},\"tong_diem\":6,\"diem_toi_da\":15,\"ty_le_phan_tram\":40}', '2026-09-29 09:17:56', '2026-09-29 09:17:56');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danhmuc_kctt`
--

CREATE TABLE `danhmuc_kctt` (
  `id` int(10) UNSIGNED NOT NULL,
  `loai` enum('khia_canh','thanh_to') NOT NULL,
  `ten` varchar(150) NOT NULL,
  `mo_ta` varchar(500) NOT NULL DEFAULT '',
  `trang_thai` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `danhmuc_kctt`
--

INSERT INTO `danhmuc_kctt` (`id`, `loai`, `ten`, `mo_ta`, `trang_thai`, `created_at`) VALUES
(2, 'khia_canh', 'Hiệu suất', '', 1, '2026-09-08 08:36:42'),
(3, 'khia_canh', 'Người bệnh', '', 1, '2026-09-08 08:36:42'),
(4, 'khia_canh', 'Nhân viên', '', 1, '2026-09-08 08:36:42'),
(5, 'thanh_to', 'Đầu vào', '', 1, '2026-09-08 08:36:42'),
(6, 'thanh_to', 'Quá trình', '', 1, '2026-09-08 08:36:42'),
(7, 'thanh_to', 'Đầu ra', '', 1, '2026-09-08 08:36:42'),
(17, 'khia_canh', 'Năng lực chuyên môn', '', 1, '2026-09-17 14:26:45');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `donvitinh`
--

CREATE TABLE `donvitinh` (
  `id` int(11) NOT NULL,
  `ten` varchar(255) DEFAULT NULL,
  `UPDATE_AT` date NOT NULL DEFAULT current_timestamp(),
  `CREATE_AT` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `donvitinh`
--

INSERT INTO `donvitinh` (`id`, `ten`, `UPDATE_AT`, `CREATE_AT`) VALUES
(1, '%', '2026-09-11', '2026-09-11'),
(2, 'Số', '2026-09-11', '2026-09-11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoa`
--

CREATE TABLE `khoa` (
  `id` int(11) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `id_khoi` int(11) NOT NULL,
  `UPDATE_AT` date NOT NULL,
  `CREATE_AT` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khoa`
--

INSERT INTO `khoa` (`id`, `ten`, `id_khoi`, `UPDATE_AT`, `CREATE_AT`) VALUES
(1, 'Khoa Cấp cứu', 1, '2026-09-18', '2026-09-16'),
(2, 'Phòng QLCL và CTXH', 3, '2026-09-18', '2026-09-16'),
(3, 'Điều trị cán bộ cao cấp', 2, '2026-09-16', '2026-09-16'),
(4, 'Hồi sức tích cực chống độc', 1, '2026-09-17', '2026-09-17'),
(5, 'Dinh dưỡng lâm sàng', 2, '2026-09-17', '2026-09-17'),
(6, 'Huyết học', 2, '2026-09-17', '2026-09-17'),
(7, 'Dược', 2, '2026-09-17', '2026-09-17'),
(8, 'Hóa sinh', 2, '2026-09-17', '2026-09-17'),
(9, 'Khám bệnh B (KB TYC)', 1, '2026-09-17', '2026-09-17'),
(10, 'Ngoại chấn thương chỉnh hình', 1, '2026-09-17', '2026-09-17'),
(11, 'Mắt', 1, '2026-09-17', '2026-09-17'),
(12, 'Khám bệnh A', 1, '2026-09-17', '2026-09-17'),
(13, 'Phòng kế hoạch tổng hợp', 3, '2026-09-18', '2026-09-18'),
(14, 'Phòng hành chính', 3, '2026-09-18', '2026-09-18'),
(15, 'Phòng công nghệ thông tin', 3, '2026-09-18', '2026-09-18'),
(16, 'Phòng điều dưỡng', 3, '2026-09-18', '2026-09-18'),
(17, 'Nội cơ xương khớp', 1, '2026-09-18', '2026-09-18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoi`
--

CREATE TABLE `khoi` (
  `id` int(11) NOT NULL,
  `ten` varchar(255) NOT NULL,
  `UPDATE_AT` date NOT NULL DEFAULT current_timestamp(),
  `CREATE_AT` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khoi`
--

INSERT INTO `khoi` (`id`, `ten`, `UPDATE_AT`, `CREATE_AT`) VALUES
(1, 'Khối lâm sàng', '2026-09-16', '2026-09-16'),
(2, 'Khối cận lâm sàng', '2026-09-16', '2026-09-16'),
(3, 'Khối cơ quan', '2026-09-16', '2026-09-16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `log`
--

CREATE TABLE `log` (
  `logID` int(11) NOT NULL,
  `ngay` varchar(100) NOT NULL,
  `ten` varchar(100) NOT NULL,
  `chucnang` varchar(100) NOT NULL,
  `noidung` text NOT NULL,
  `noidungcu` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Đang đổ dữ liệu cho bảng `log`
--

INSERT INTO `log` (`logID`, `ngay`, `ten`, `chucnang`, `noidung`, `noidungcu`) VALUES
(1, '11/09/2026 07:08:46 AM', 'admin', 'Thêm nhóm quyền', 'Tên nhóm quyền: Mặc định', ''),
(2, '12/09/2026 05:13:50 PM', 'admin', 'Xóa user', 'Username: ; Họ và tên: ; Tên nhóm quyền: ', 'Username: phong_qlcl; Họ và tên: Phòng QLCL và CTXH; Tên nhóm quyền: Cấp cứu'),
(3, '12/09/2026 06:57:18 PM', 'admin', 'Thêm User', 'Username: giahuy; Họ và tên: gia huy; Tên nhóm quyền: Administrator', ''),
(4, '14/09/2026 10:18:25 AM', 'admin', 'Sửa nhóm quyền', 'Tên nhóm quyền:  Khoa phòng; Quyền: ', 'Tên nhóm quyền: Cấp cứu; Quyền: hethong,bacsi,bacsi,bacsi.saveBacSi,bacsi.deleteBa\ncSi,khoaphong,khoaphong,khoaphong.saveKhoaPhong,kh\noaphong.deleteKhoaPhong,benhnhan,benhnhan,benhnhan\n.saveBenhNhan,benhnhan.deleteBenhNhan,trangthai,tr\nangthai,trangthai.saveTrangThai,trangthai.deleteTr\nangThai,quaytiepnhan,quaytiepnhan,quaytiepnhan.sav\neQuayTiepNhan,quaytiepnhan.deleteQuayTiepNhan'),
(5, '14/09/2026 10:18:40 AM', 'admin', 'Đổi quyền', 'Tên nhóm quyền:  Khoa phòng; Quyền: hethong,chitieu,chitieu', 'Tên nhóm quyền: Khoa phòng; Quyền: hethong,bacsi,bacsi,bacsi.saveBacSi,bacsi.deleteBa\ncSi,khoaphong,khoaphong,khoaphong.saveKhoaPhong,kh\noaphong.deleteKhoaPhong,benhnhan,benhnhan,benhnhan\n.saveBenhNhan,benhnhan.deleteBenhNhan,trangthai,tr\nangthai,trangthai.saveTrangThai,trangthai.deleteTr\nangThai,quaytiepnhan,quaytiepnhan,quaytiepnhan.sav\neQuayTiepNhan,quaytiepnhan.deleteQuayTiepNhan'),
(6, '14/09/2026 10:18:55 AM', 'admin', 'Đổi quyền', 'Tên nhóm quyền:  Khoa phòng; Quyền: hethong,chisochatluong,chisochatluong,chisochatluo\nng.save,chisochatluong.delete,chisochatluong.edit,\nchitieu,chitieu', 'Tên nhóm quyền: Khoa phòng; Quyền: hethong,chitieu,chitieu'),
(7, '15/09/2026 07:10:43 AM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Mặc định; Quyền: hethong,chisochatluong,chisochatluong,chisochatluo\nng.save,chisochatluong.delete,chisochatluong.edit,\nchisokhoa', 'Tên nhóm quyền: Mặc định; Quyền: '),
(8, '16/09/2026 08:09:42 AM', 'admin', 'Đổi quyền', 'Tên nhóm quyền:  Mặc định; Quyền: hethong,danhmucChiSo,chisochatluong,chisochatluong\n.save,chisochatluong.delete,chisochatluong,chisokh\noa,kp,kp,kp.save,kp.update,khoaphong,khoi', 'Tên nhóm quyền: Mặc định; Quyền: hethong,chisochatluong,chisochatluong,chisochatluo\nng.save,chisochatluong.delete,chisochatluong.edit,\nchisokhoa'),
(9, '16/09/2026 08:10:19 AM', 'admin', 'Thêm User', 'Username: qlcl; Họ và tên: Quản lý chất lượng; Tên nhóm quyền: Mặc định', ''),
(10, '16/09/2026 08:26:10 AM', 'qlcl', 'Thêm User', 'Username: capcuu; Họ và tên: Cấp cứu; Tên nhóm quyền: Khoa phòng', ''),
(11, '16/09/2026 08:26:32 AM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Khoa phòng; Quyền: hethong,chiso,chiso,chiso.save,chiso.update,chiso.\nsave,chiso.duyet,chisochatluong,chisokhoa,chitieu', 'Tên nhóm quyền: Khoa phòng; Quyền: hethong,chisochatluong,chisochatluong,chisochatluo\nng.save,chisochatluong.delete,chisochatluong.edit,\nchitieu,chitieu'),
(12, '16/09/2026 08:32:24 AM', 'qlcl', 'Sửa User', 'Username: ; Họ và tên: Quản lý chất lượng; Tên nhóm quyền: Mặc định; Người dùng khóa: Chưa khóa', 'Username: qlcl; Họ và tên: Quản lý chất lượng; Tên nhóm quyền: Mặc định; Người dùng khóa: Chưa khóa'),
(13, '16/09/2026 08:33:38 AM', 'qlcl', 'Sửa User', 'Username: ; Họ và tên: Quản lý chất lượng; Tên nhóm quyền: Mặc định; Người dùng khóa: Chưa khóa', 'Username: qlcl; Họ và tên: Quản lý chất lượng; Tên nhóm quyền: Mặc định; Người dùng khóa: Chưa khóa'),
(14, '16/09/2026 11:13:03 AM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Mặc định; Quyền: hethong,chiso,chiso,chiso.save,chiso.update,chiso.\nsave,kp,khoaphong,khoaphong,khoaphong.save,khoapho\nng.delete,khoi,khoi,khoi.save,khoi.delete', 'Tên nhóm quyền: Mặc định; Quyền: hethong,danhmucChiSo,chisochatluong,chisochatluong\n.save,chisochatluong.delete,chisochatluong,chisokh\noa,kp,kp,kp.save,kp.update,khoaphong,khoi'),
(15, '16/09/2026 02:05:44 PM', 'qlcl', 'Sửa User', 'Username: ; Họ và tên: Cấp cứu; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa', 'Username: capcuu; Họ và tên: Cấp cứu; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa'),
(16, '16/09/2026 02:09:44 PM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Điều trị cán bộ cao cấp', ''),
(17, '16/09/2026 02:09:56 PM', 'qlcl', 'Sửa khoa phòng', 'Tên khoa phòng: Điều trị cán bộ cao cấp', 'Tên khoa phòng: Điều trị cán bộ cao cấp'),
(18, '16/09/2026 02:10:57 PM', 'qlcl', 'Thêm User', 'Username: cbcc; Họ và tên: Cán bộ cao cấp; Tên nhóm quyền: Khoa phòng', ''),
(19, '17/09/2026 07:25:22 AM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Khoa phòng; Quyền: hethong,chiso,chiso,chiso.save,chiso.update,chiso.\nsave,chiso.duyet,chisochatluong', 'Tên nhóm quyền: Khoa phòng; Quyền: hethong,chiso,chiso,chiso.save,chiso.update,chiso.\nsave,chiso.duyet,chisochatluong,chisokhoa,chitieu'),
(20, '17/09/2026 11:02:18 AM', 'qlcl', 'Thêm User', 'Username: giahuy; Họ và tên: Gia Huy; Tên nhóm quyền: Khoa phòng', ''),
(21, '17/09/2026 11:06:44 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Hồi sức tích cực chống độc', ''),
(22, '17/09/2026 11:06:44 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Dinh dưỡng lâm sàng', ''),
(23, '17/09/2026 11:06:55 AM', 'qlcl', 'Sửa khoa phòng', 'Tên khoa phòng: Hồi sức tích cực chống độc', 'Tên khoa phòng: Hồi sức tích cực chống độc'),
(24, '17/09/2026 11:08:24 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Huyết học', ''),
(25, '17/09/2026 11:08:24 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Dược', ''),
(26, '17/09/2026 11:08:24 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Hóa sinh', ''),
(27, '17/09/2026 11:08:24 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Khám bệnh B (KB TYC)', ''),
(28, '17/09/2026 11:08:24 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Ngoại chấn thương chỉnh hình', ''),
(29, '17/09/2026 11:08:24 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Mắt', ''),
(30, '17/09/2026 11:08:24 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Khám bệnh A', ''),
(31, '17/09/2026 01:35:04 PM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Mặc định; Quyền: hethong,chiso,chisochatluong,chisochatluong,chisoc\nhatluong.save,chisochatluong.update,chisochatluong\n.delete,chisochatluong.gui', 'Tên nhóm quyền: Mặc định; Quyền: hethong,chiso,chiso,chiso.save,chiso.update,chiso.\nsave,kp,khoaphong,khoaphong,khoaphong.save,khoapho\nng.delete,khoi,khoi,khoi.save,khoi.delete'),
(32, '17/09/2026 01:35:36 PM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Khoa phòng; Quyền: hethong,chiso,chisochatluong,chisochatluong,chisoc\nhatluong.save,chisochatluong.update,chisochatluong\n.delete,chisochatluong.gui', 'Tên nhóm quyền: Khoa phòng; Quyền: hethong,chiso,chiso,chiso.save,chiso.update,chiso.\nsave,chiso.duyet,chisochatluong'),
(33, '18/09/2026 07:02:39 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Phòng kế hoạch tổng hợp', ''),
(34, '18/09/2026 07:02:39 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Phòng hành chính', ''),
(35, '18/09/2026 07:02:39 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Phòng công nghệ thông tin', ''),
(36, '18/09/2026 07:02:39 AM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Phòng điều dưỡng', ''),
(37, '18/09/2026 07:03:17 AM', 'qlcl', 'Sửa khoa phòng', 'Tên khoa phòng: Phòng QLCL và CTXH', 'Tên khoa phòng: Quản lý chất lượng'),
(38, '18/09/2026 07:32:10 AM', 'qlcl', 'Sửa User', 'Username: ; Họ và tên: Gia Huy; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa', 'Username: giahuy; Họ và tên: Gia Huy; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa'),
(39, '18/09/2026 10:14:14 AM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Khoa phòng; Quyền: hethong,chiso,chisochatluong,chisochatluong,chisoc\nhatluong.save,chisochatluong.update,chisochatluong\n.xoa,chisochatluong.gui,nhaplieu', 'Tên nhóm quyền: Khoa phòng; Quyền: hethong,chiso,chisochatluong,chisochatluong,chisoc\nhatluong.save,chisochatluong.update,chisochatluong\n.delete,chisochatluong.gui'),
(40, '18/09/2026 03:39:49 PM', 'qlcl', 'Sửa User', 'Username: ; Họ và tên: Gia Huy; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa', 'Username: giahuy; Họ và tên: Gia Huy; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa'),
(41, '18/09/2026 03:41:55 PM', 'qlcl', 'Thêm User', 'Username: cxk; Họ và tên: Cơ xương khớp; Tên nhóm quyền: Khoa phòng', ''),
(42, '18/09/2026 03:42:46 PM', 'qlcl', 'Thêm khoa phòng', 'Tên khoa phòng: Nội cơ xương khớp', ''),
(43, '18/09/2026 03:43:01 PM', 'qlcl', 'Sửa User', 'Username: ; Họ và tên: Cơ xương khớp; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa', 'Username: cxk; Họ và tên: Cơ xương khớp; Tên nhóm quyền: Khoa phòng; Người dùng khóa: Chưa khóa'),
(44, '18/09/2026 03:45:51 PM', 'qlcl', 'Sửa khoa phòng', 'Tên khoa phòng: Khoa Cấp cứu', 'Tên khoa phòng: Khoa Cấp cứu'),
(45, '18/09/2026 04:30:04 PM', 'qlcl', 'Đổi quyền', 'Tên nhóm quyền:  Mặc định; Quyền: hethong,chiso,chisochatluong,chisochatluong,chisoc\nhatluong.save,chisochatluong.update,chisochatluong\n.xoa,chisochatluong.gui', 'Tên nhóm quyền: Mặc định; Quyền: hethong,chiso,chisochatluong,chisochatluong,chisoc\nhatluong.save,chisochatluong.update,chisochatluong\n.delete,chisochatluong.gui');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `login_attempts`
--

CREATE TABLE `login_attempts` (
  `ip` varchar(20) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `lastlogin` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhap_lieu_chi_so`
--

CREATE TABLE `nhap_lieu_chi_so` (
  `id` int(11) NOT NULL,
  `ma_chi_so` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `thang` int(11) NOT NULL,
  `so_thang_chu_ky` int(11) NOT NULL DEFAULT 1,
  `tu_so` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `mau_so` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `ket_qua` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `xep_loai` varchar(20) NOT NULL DEFAULT '',
  `tinh_trang` varchar(20) NOT NULL DEFAULT 'chua_nhap',
  `nguoi_nhap` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhomquyen`
--

CREATE TABLE `nhomquyen` (
  `maNQ` int(11) NOT NULL,
  `tenNQ` varchar(255) NOT NULL,
  `quyen` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nhomquyen`
--

INSERT INTO `nhomquyen` (`maNQ`, `tenNQ`, `quyen`) VALUES
(5, 'Administrator', 'hethong,giaodien,user,user,user.saveUser,user.deleteUser,user.phanquyen,user.lock,nhomquyen,nhomquyen,nhomquyen.save,nhomquyen.delete,nhomquyen.phanquyen,log,log,log.deleteLog,bacsi,bacsi,bacsi.saveBacSi,bacsi.deleteBacSi,khoaphong,khoaphong,khoaphong.saveKhoaPhong,khoaphong.deleteKhoaPhong,benhnhan,benhnhan,benhnhan.saveBenhNhan,benhnhan.deleteBenhNhan,trangthai,trangthai,trangthai.saveTrangThai,trangthai.deleteTrangThai'),
(51, 'Điều dưỡng', 'hethong,benhnhan,benhnhan,benhnhan.saveBenhNhan,benhnhan.deleteBenhNhan'),
(52, 'Bác sĩ', 'hethong,benhnhan,benhnhan,benhnhan.saveBenhNhan,benhnhan.deleteBenhNhan'),
(67, 'Khoa phòng', 'hethong,chiso,chisochatluong,chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui,nhaplieu'),
(69, 'Giao diện', 'hethong,giaodien,giaodien'),
(70, 'Mặc định', 'hethong,chiso,chisochatluong,chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phamvi`
--

CREATE TABLE `phamvi` (
  `id` int(11) NOT NULL,
  `ten` varchar(2555) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phamvi`
--

INSERT INTO `phamvi` (`id`, `ten`) VALUES
(1, 'Khoa/Phòng'),
(3, 'Toàn bệnh viện');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `quaytiepnhan`
--

CREATE TABLE `quaytiepnhan` (
  `maQuay` int(11) NOT NULL,
  `tenQuayTiepNhan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `quaytiepnhan`
--

INSERT INTO `quaytiepnhan` (`maQuay`, `tenQuayTiepNhan`) VALUES
(2, 'Quầy hành chính'),
(3, 'Quầy tư vấn dinh dưỡng'),
(4, 'Quầy tiếp nhận hồ sơ bệnh án'),
(5, 'Quầy đăng ký khám bảo hiểm y tế'),
(6, 'Quầy lấy mẫu xét nghiệm'),
(7, 'Quầy dịch vụ khách hàng'),
(8, 'Quầy phát kết quả xét nghiệm'),
(9, 'Quầy thuốc'),
(10, 'Quầy thu ngân');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `trangthai`
--

CREATE TABLE `trangthai` (
  `maTrangThai` int(11) NOT NULL,
  `tenTrangThai` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `trangthai`
--

INSERT INTO `trangthai` (`maTrangThai`, `tenTrangThai`, `tag`) VALUES
(0, 'Nháp', 'bg-secondary text-light'),
(1, 'Chờ duyệt', 'bg-info text-dark'),
(2, 'Đã duyệt', 'bg-success'),
(3, 'Từ Chối', 'bg-danger');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `hoTen` varchar(255) NOT NULL,
  `diaChi` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `dienThoai` varchar(100) NOT NULL,
  `adminType` varchar(10) NOT NULL DEFAULT '0',
  `quyen` text DEFAULT NULL,
  `maNQ` int(11) NOT NULL,
  `MaKhoaPhong` int(11) DEFAULT NULL,
  `nd_block` tinyint(4) NOT NULL DEFAULT 0,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `hoTen`, `diaChi`, `email`, `dienThoai`, `adminType`, `quyen`, `maNQ`, `MaKhoaPhong`, `nd_block`, `token`) VALUES
(4, 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 'Administrator', '', '', '', '1', 'hethong,giaodien,user,user,user.saveUser,user.deleteUser,user.phanquyen,user.lock,nhomquyen,nhomquyen,nhomquyen.save,nhomquyen.delete,nhomquyen.phanquyen,log,log,log.deleteLog,bacsi,bacsi,bacsi.saveBacSi,bacsi.deleteBacSi,khoaphong,khoaphong,khoaphong.saveKhoaPhong,khoaphong.deleteKhoaPhong,benhnhan,benhnhan,benhnhan.saveBenhNhan,benhnhan.deleteBenhNhan,trangthai,trangthai,trangthai.saveTrangThai,trangthai.deleteTrangThai,chisochatluong,chisochatluong.save,chisochatluong.delete,danhmuc_kctt,danhmuc_kctt.save,danhmuc_kctt.delete,danhmuc_kctt.save,danhmuc_kctt.delete,danhmuc_kctt.save,danhmuc_kctt.delete', 5, NULL, 0, 'SntP1zyet'),
(251, 'qlcl', '202cb962ac59075b964b07152d234b70', 'Quản lý chất lượng', '', '', '', '0', 'hethong,user,user,user.saveUser,user.deleteUser,user.phanquyen,user.lock,nhomquyen,nhomquyen,nhomquyen.save,nhomquyen.delete,nhomquyen.phanquyen,chiso,chisochatluong,chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui,chisochatluong.duyet,chisochatluong.tuchoi,chisokhoa,danhmuc,donvitinh,donvitinh,donvitinh.save,donvitinh.delete,chuky,chuky,chuky.save,chuky.delete,trangthai,trangthai,trangthai.save,trangthai.delete,danhmuckctt,danhmuckctt,danhmuckctt.save,danhmuckctt.delete,phamvi,phamvi,phamvi.save,phamvi.delete,kp,khoaphong,khoaphong,khoaphong.save,khoaphong.delete,khoi,khoi,khoi.save,khoi.delete,nhaplieu,nhaplieu,bangkiem,bangkiem,bangkiem.save,bangkiem.delete', 70, 2, 0, '4GyH8vcCz'),
(252, 'capcuu', '202cb962ac59075b964b07152d234b70', 'Cấp cứu', '', '', '', '0', 'hethong,chiso,chisochatluong,chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui,chisochatluong.khoaB,nhaplieu,nhaplieu', 67, 1, 0, '8mCNROZin'),
(253, 'cbcc', '202cb962ac59075b964b07152d234b70', 'Cán bộ cao cấp', '', '', '', '0', 'hethong,chiso,chisochatluong,chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui,chisochatluong.khoaB,nhaplieu,nhaplieu', 67, 3, 0, 'Vdf5S1T0O'),
(254, 'giahuy', '202cb962ac59075b964b07152d234b70', 'Gia Huy', '', '', '', '0', 'hethong,chiso,chisochatluong,chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui,chisochatluong.khoaA,nhaplieu,nhaplieu', 67, 1, 0, 'AvOKmdt1m'),
(255, 'cxk', '202cb962ac59075b964b07152d234b70', 'Cơ xương khớp', '', '', '', '0', 'hethong,chiso,chisochatluong,chisochatluong,chisochatluong.save,chisochatluong.update,chisochatluong.xoa,chisochatluong.gui,nhaplieu', 67, 17, 0, 'evRJi7MXb');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bacsi`
--
ALTER TABLE `bacsi`
  ADD PRIMARY KEY (`MaBacSi`),
  ADD KEY `MaKhoaPhong` (`MaKhoaPhong`);

--
-- Chỉ mục cho bảng `bang_kiem`
--
ALTER TABLE `bang_kiem`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `benhnhan`
--
ALTER TABLE `benhnhan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `benhnhan_ibfk_1` (`maTrangThai`);

--
-- Chỉ mục cho bảng `chi_so_chat_luong`
--
ALTER TABLE `chi_so_chat_luong`
  ADD PRIMARY KEY (`ma_chi_so`),
  ADD KEY `idx_chi_so_khia_canh` (`ma_khia_canh`),
  ADD KEY `idx_chi_so_thanh_to` (`ma_thanh_to`),
  ADD KEY `fk_chuky` (`id_chuky`),
  ADD KEY `fk_phamvi` (`pham_vi`),
  ADD KEY `fk_donvitinh` (`id_donvitinh`),
  ADD KEY `fk_trangthai` (`trang_thai`),
  ADD KEY `fk_nguoigui` (`nguoi_gui`),
  ADD KEY `fk_chiso_khoaphong` (`id_khoaphong`);

--
-- Chỉ mục cho bảng `chucnang`
--
ALTER TABLE `chucnang`
  ADD PRIMARY KEY (`maChucNang`);

--
-- Chỉ mục cho bảng `chuky`
--
ALTER TABLE `chuky`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `codemaster`
--
ALTER TABLE `codemaster`
  ADD PRIMARY KEY (`id`,`year`);

--
-- Chỉ mục cho bảng `ct_chiso`
--
ALTER TABLE `ct_chiso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_khoaphong` (`id_khoaphong`),
  ADD KEY `idx_ct_chiso_luot_nhap` (`ma_chi_so`,`id_khoaphong`,`nam`,`ky`);

--
-- Chỉ mục cho bảng `danhmuc_kctt`
--
ALTER TABLE `danhmuc_kctt`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_qims_danh_muc` (`loai`,`ten`);

--
-- Chỉ mục cho bảng `donvitinh`
--
ALTER TABLE `donvitinh`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_khoi` (`id_khoi`);

--
-- Chỉ mục cho bảng `khoi`
--
ALTER TABLE `khoi`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`logID`);

--
-- Chỉ mục cho bảng `nhap_lieu_chi_so`
--
ALTER TABLE `nhap_lieu_chi_so`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_chiso_ky` (`ma_chi_so`,`nam`,`thang`);

--
-- Chỉ mục cho bảng `nhomquyen`
--
ALTER TABLE `nhomquyen`
  ADD PRIMARY KEY (`maNQ`);

--
-- Chỉ mục cho bảng `phamvi`
--
ALTER TABLE `phamvi`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `quaytiepnhan`
--
ALTER TABLE `quaytiepnhan`
  ADD PRIMARY KEY (`maQuay`);

--
-- Chỉ mục cho bảng `trangthai`
--
ALTER TABLE `trangthai`
  ADD PRIMARY KEY (`maTrangThai`);

--
-- Chỉ mục cho bảng `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_khoaphong` (`MaKhoaPhong`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bacsi`
--
ALTER TABLE `bacsi`
  MODIFY `MaBacSi` int(111) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `bang_kiem`
--
ALTER TABLE `bang_kiem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `benhnhan`
--
ALTER TABLE `benhnhan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chi_so_chat_luong`
--
ALTER TABLE `chi_so_chat_luong`
  MODIFY `ma_chi_so` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT cho bảng `chucnang`
--
ALTER TABLE `chucnang`
  MODIFY `maChucNang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT cho bảng `chuky`
--
ALTER TABLE `chuky`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `ct_chiso`
--
ALTER TABLE `ct_chiso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT cho bảng `danhmuc_kctt`
--
ALTER TABLE `danhmuc_kctt`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `donvitinh`
--
ALTER TABLE `donvitinh`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `khoa`
--
ALTER TABLE `khoa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `khoi`
--
ALTER TABLE `khoi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `log`
--
ALTER TABLE `log`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT cho bảng `nhap_lieu_chi_so`
--
ALTER TABLE `nhap_lieu_chi_so`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nhomquyen`
--
ALTER TABLE `nhomquyen`
  MODIFY `maNQ` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `phamvi`
--
ALTER TABLE `phamvi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `quaytiepnhan`
--
ALTER TABLE `quaytiepnhan`
  MODIFY `maQuay` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `trangthai`
--
ALTER TABLE `trangthai`
  MODIFY `maTrangThai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT cho bảng `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bacsi`
--
ALTER TABLE `bacsi`
  ADD CONSTRAINT `bacsi_ibfk_1` FOREIGN KEY (`MaKhoaPhong`) REFERENCES `khoaphong` (`MaKhoaPhong`);

--
-- Các ràng buộc cho bảng `benhnhan`
--
ALTER TABLE `benhnhan`
  ADD CONSTRAINT `benhnhan_ibfk_1` FOREIGN KEY (`maTrangThai`) REFERENCES `trangthai` (`maTrangThai`);

--
-- Các ràng buộc cho bảng `chi_so_chat_luong`
--
ALTER TABLE `chi_so_chat_luong`
  ADD CONSTRAINT `fk_chi_so_khia_canh` FOREIGN KEY (`ma_khia_canh`) REFERENCES `danhmuc_kctt` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chi_so_thanh_to` FOREIGN KEY (`ma_thanh_to`) REFERENCES `danhmuc_kctt` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_chiso_khoaphong` FOREIGN KEY (`id_khoaphong`) REFERENCES `khoa` (`id`),
  ADD CONSTRAINT `fk_chuky` FOREIGN KEY (`id_chuky`) REFERENCES `chuky` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donvitinh` FOREIGN KEY (`id_donvitinh`) REFERENCES `donvitinh` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nguoigui` FOREIGN KEY (`nguoi_gui`) REFERENCES `user` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_phamvi` FOREIGN KEY (`pham_vi`) REFERENCES `phamvi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_trangthai` FOREIGN KEY (`trang_thai`) REFERENCES `trangthai` (`maTrangThai`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `ct_chiso`
--
ALTER TABLE `ct_chiso`
  ADD CONSTRAINT `fk_ct_chiso_ma_chi_so` FOREIGN KEY (`ma_chi_so`) REFERENCES `chi_so_chat_luong` (`ma_chi_so`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `khoa`
--
ALTER TABLE `khoa`
  ADD CONSTRAINT `fk_khoi` FOREIGN KEY (`id_khoi`) REFERENCES `khoi` (`id`);

--
-- Các ràng buộc cho bảng `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_khoaphong` FOREIGN KEY (`MaKhoaPhong`) REFERENCES `khoa` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
