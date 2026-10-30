# TÀI LIỆU BẢO TRÌ HỆ THỐNG QLCL

**Đơn vị:** Bệnh viện Thống Nhất  
**Mã số:** Chưa cấp  
**Lần ban hành / Phiên bản:** v1.2  
**Ngày cập nhật:** 29/10/2026

## BẢNG THEO DÕI SỬA ĐỔI

| Phiên bản số | Vị trí sửa đổi | Nội dung sửa đổi | Ngày xem xét/sửa đổi | Người xem xét/sửa đổi |
| --- | --- | --- | --- | --- |
| v1.2 | Toàn bộ tài liệu | Xây dựng/cập nhật theo source hệ thống QLCL hiện hành | 29/10/2026 | Điền khi ban hành |

## DANH SÁCH PHÂN PHỐI TÀI LIỆU

| TT | Nơi nhận | TT | Nơi nhận |
| --- | --- | --- | --- |
| 1 | Ban Giám đốc | 2 | Phòng Công nghệ Thông tin |
| 3 | Phòng Quản lý chất lượng | 4 | Các đơn vị sử dụng hệ thống QLCL |

## 1. Mục đích

Quy định thông tin cần thiết để vận hành, sao lưu, xử lý sự cố, triển khai thay đổi và nâng cấp hệ thống QLCL an toàn, có khả năng phục hồi.

## 2. Phạm vi áp dụng

Áp dụng cho cán bộ CNTT và người được giao nhiệm vụ vận hành, bảo trì source code, máy chủ ứng dụng và cơ sở dữ liệu QLCL.

## 3. Trách nhiệm

| Đối tượng | Trách nhiệm |
| --- | --- |
| Bộ phận CNTT | Theo dõi hệ thống, sao lưu, xử lý sự cố, triển khai bản cập nhật và quản lý cấu hình. |
| Quản trị CSDL | Theo dõi MySQL/MariaDB, sao lưu, phục hồi và bảo đảm toàn vẹn dữ liệu. |
| Đơn vị nghiệp vụ | Phối hợp xác nhận dữ liệu và kiểm thử sau bảo trì. |
| Người triển khai | Ghi nhận phiên bản, thay đổi, kết quả kiểm thử và phương án rollback. |

## 4. Thuật ngữ, định nghĩa và chữ viết tắt

| Thuật ngữ/Viết tắt | Diễn giải |
| --- | --- |
| QLCL | Quản lý chất lượng |
| CNTT | Công nghệ Thông tin |
| CSDL | Cơ sở dữ liệu |
| Action | Lớp tiếp nhận request và điều phối xử lý theo module |
| Peer | Lớp truy cập dữ liệu/nghiệp vụ làm việc với CSDL |
| JSON | Định dạng dữ liệu dùng cho biểu mẫu, phạm vi khoa/phòng và phiếu nhập |

## 5. Nội dung

### Mục tiêu bảo trì

Tài liệu này phục vụ triển khai, vận hành, xử lý sự cố và nâng cấp hệ thống PHP/MySQL trong thư mục dự án `ql_cscl`. Trọng tâm là các điểm cấu hình, luồng request, CSDL, quyền, backup/restore, log và các rủi ro kỹ thuật cần xử lý khi nâng cấp.

### Kiến trúc vận hành

Hệ thống là ứng dụng PHP thuần chạy theo mô hình XAMPP/Apache + MySQL/MariaDB. `index.php` là điểm vào chính, đọc route từ request, nạp Action, kiểm tra quyền và render view trong layout chung.

Luồng tổng quát:

```text
Browser
  -> Apache / index.php
  -> Request + session + role check
  -> web_src/admin/<handle>Action.php
  -> web_src/bean/*Peer.php
  -> MySQL/MariaDB
  -> www/* view hoặc JSON/download
```

Các request AJAX sử dụng cùng router, thường trả JSON và bỏ qua shared layout.

### Cấu hình quan trọng

File `conf/config.php` chứa các thông số cần rà soát khi triển khai:

| Cấu hình | Giá trị trong source hiện tại | Khuyến nghị |
| --- | --- | --- |
| DB host | `localhost` | Đưa sang biến môi trường nếu có nhiều môi trường |
| DB name | `ql_cscl` | Đồng bộ với database triển khai |
| DB user | `root` | Tạo user riêng chỉ có quyền cần thiết |
| DB password | trống | Bắt buộc đổi ở môi trường thật |
| `_DEFAULT_URL_` | `http://192.168.31.8:80/ql_cscl/` | Không hard-code IP; cấu hình theo môi trường |
| `_DEFAULT_HANDLE_` | `dashboard` | Giữ nếu dashboard là trang mặc định |
| `_DEFAULT_VERSION_` | `v1.2` | Cập nhật có kiểm soát khi release |
| `_BACKUP_SIZE_` | 25 MB | Đảm bảo phù hợp kích thước file phục hồi |

### Cấu trúc source cần biết khi bảo trì

| Khu vực | Vai trò |
| --- | --- |
| `index.php` | Router, session, quyền, gọi Action, layout |
| `conf/config.php` | Cấu hình DB, URL và hằng số ứng dụng |
| `web_src/admin/` | Action/controller theo module |
| `web_src/bean/` | Entity/Peer truy cập dữ liệu và nghiệp vụ |
| `web_src/common/` | Request, MySQL wrapper, tiện ích, log/message |
| `www/` | View HTML/PHP và shared layout |
| `js/` | JavaScript/AJAX cho từng module |
| `sql/` | Dump CSDL và migration |

`UseCaseDispatcherLocator.php` có dấu hiệu là code cũ; luồng đang hoạt động nằm trực tiếp ở `index.php`.

### Cơ sở dữ liệu và migration

#### Bảng lõi đang dùng

| Bảng | Vai trò |
| --- | --- |
| `user` | Tài khoản, quyền, khoa/phòng, khóa tài khoản |
| `nhomquyen` | Nhóm quyền |
| `chucnang` | Menu và mã quyền chức năng |
| `chi_so_chat_luong` | Định nghĩa chỉ số, trạng thái, phạm vi, biểu mẫu |
| `ct_chiso` | Phiếu nhập theo chỉ số/khoa/năm/kỳ |
| `khoa`, `khoi` | Cấu trúc đơn vị |
| `chuky` | Số kỳ/năm |
| `danhmuc_kctt` | Khía cạnh/thành tố |
| `donvitinh`, `phamvi`, `trangthai` | Danh mục cấu hình |
| `log` | Nhật ký |

`nhap_lieu_chi_so` là bảng theo mô hình cũ; luồng `NhapLieuAction` hiện tại sử dụng `ct_chiso`.

#### Quan hệ quan trọng

- `user.MaKhoaPhong -> khoa.id`.
- `khoa.id_khoi -> khoi.id`.
- `ct_chiso.ma_chi_so -> chi_so_chat_luong.ma_chi_so` với `ON DELETE CASCADE`.
- `chi_so_chat_luong` tham chiếu các danh mục như khoa, chu kỳ, đơn vị tính, phạm vi, trạng thái.
- `ct_chiso` có index `(ma_chi_so, id_khoaphong, nam, ky)` nhưng không có unique; nhiều phiếu cùng kỳ là hợp lệ theo cấu trúc hiện tại.

#### Migration

Các file `sql/create_ct_chiso.sql` và `sql/migrate_ct_chiso_luu_moi.sql` liên quan mô hình nhập liệu mới. Khi triển khai migration:

1. Backup database trước khi chạy.
2. Kiểm tra version schema hiện tại.
3. Chạy trên bản sao/staging.
4. Kiểm tra dữ liệu JSON và khóa ngoại.
5. Kiểm thử màn hình Nhập liệu, Chỉ số khoa và thống kê.

### Xác thực, session và quyền

#### Session chính

Các biến thường dùng: `sUserID`, `sUserName`, `FullName`, `AdminType`, `sUserLogin`, `sToken`, `quyen` và khóa động `sPassword` cho tên trường mật khẩu trên form đăng nhập.

Khi đăng nhập thành công, hệ thống tạo token 9 ký tự và ghi token vào user. Khi đăng xuất, session bị xóa và cookie session được hủy.

#### Quyền

`Request::checkRoles()` so sánh quyền trong session với danh sách quyền tĩnh của Action. Hệ thống hỗ trợ quyền theo class hoặc `class.method`. `adminType = 1` bỏ qua kiểm tra quyền.

Khi thêm Action hoặc method mới có dữ liệu nhạy cảm:

1. Khai báo quyền trong `$listRole` của Action.
2. Thêm mã chức năng/permission vào `chucnang` nếu cần xuất hiện trên UI.
3. Cập nhật nhóm quyền.
4. Kiểm thử bằng tài khoản có và không có quyền.

### Backup và phục hồi

`backupAction` nhận file `.sql`, kiểm tra kích thước theo `_BACKUP_SIZE_`, tạo backup trước khi restore và gọi `BackupRestorePeer`.

Các vấn đề cần xử lý trước khi phụ thuộc vào chức năng này trong môi trường thật:

- `BackupRestorePeer` vẫn dùng `mysql_query`, `mysql_list_tables`, `mysql_fetch_*` và `ereg_replace`, không tương thích PHP hiện đại.
- Lớp DB chính dùng `mysqli`, tạo ra hai cơ chế truy cập DB không đồng nhất.
- `backupAction` có tham chiếu `$db_name` không thấy khởi tạo cục bộ rõ ràng.
- Thông báo lỗi upload nhắc `xls/xlsx` dù code kiểm tra file SQL.
- Restore chạy câu SQL tuần tự; cần kiểm soát file nguồn và quyền thực thi.

Khuyến nghị vận hành: dùng backup MySQL/MariaDB chuẩn ở cấp server cho dữ liệu thật; chỉ bật chức năng restore trong web sau khi đã nâng cấp và kiểm thử kỹ.

### Nhật ký và xử lý lỗi

Một số Peer gọi `setLog()` để ghi thao tác. Cần kiểm tra định kỳ kích thước bảng `log` và chính sách lưu trữ.

Lớp `db_mysql::query()` hiện có hành vi dừng xử lý và in câu SQL khi lỗi. Đây là rủi ro lộ cấu trúc DB/dữ liệu trên môi trường production. Nên thay bằng exception + log phía server và trả thông báo chung cho người dùng.

### Rủi ro kỹ thuật và ưu tiên xử lý

| Mức | Vấn đề | Ảnh hưởng | Hướng xử lý |
| --- | --- | --- | --- |
| Rất cao | Mật khẩu user băm MD5 | Dễ bị dò nếu lộ hash | Chuyển sang `password_hash` / `password_verify`, migration khi đăng nhập |
| Rất cao | DB `root` không mật khẩu | Mất an toàn khi triển khai thật | Tạo DB user riêng, mật khẩu mạnh, quyền tối thiểu |
| Cao | SQL ghép chuỗi trực tiếp | Nguy cơ SQL injection | Dùng prepared statements, ép kiểu và validate |
| Cao | In SQL khi query lỗi | Lộ schema/dữ liệu | Log server, ẩn chi tiết lỗi khỏi client |
| Cao | Backup/restore dùng `mysql_*` | Hỏng trên PHP hiện đại | Viết lại bằng `mysqli`/PDO hoặc công cụ DB chuẩn |
| Cao | `_DEFAULT_URL_` hard-code IP | Lỗi khi đổi server/domain | Cấu hình từ host/base URL theo môi trường |
| Trung bình | Cơ chế giới hạn login sai bị comment | Brute-force | Bật rate limit an toàn theo user/IP |
| Trung bình | Token ngắn 9 ký tự | Khả năng đoán token | Dùng `random_bytes` và token dài |
| Trung bình | Session chưa thể hiện đầy đủ hardening | Chiếm session | Bật cookie secure/httponly/samesite, regenerate ID sau login |
| Trung bình | Nhiều bảng/SQL legacy không còn luồng chính | Khó bảo trì | Đánh dấu deprecated và dọn sau khi xác nhận |

### Quy trình triển khai thay đổi

1. Sao lưu source và database.
2. Ghi rõ ticket/thay đổi và file dự kiến sửa.
3. Sửa trên môi trường dev/staging.
4. Kiểm tra PHP syntax cho file thay đổi.
5. Kiểm tra migration nếu có thay đổi schema.
6. Kiểm thử đăng nhập, quyền và module bị ảnh hưởng.
7. Kiểm thử luồng chỉ số: tạo -> gửi -> duyệt -> nhập -> thống kê.
8. Kiểm thử bằng tài khoản khoa/phòng khác để phát hiện rò quyền dữ liệu.
9. Triển khai production ngoài giờ cao điểm nếu thay đổi lớn.
10. Theo dõi log và lỗi ứng dụng sau triển khai.

### Checklist xử lý sự cố

#### Không đăng nhập được

- Kiểm tra `user.username`.
- Kiểm tra `nd_block` phải bằng 0.
- Kiểm tra hash MD5 của mật khẩu theo cơ chế hiện tại.
- Kiểm tra kết nối DB và lỗi PHP/Apache.

#### Menu không hiện

- Kiểm tra `$_SESSION['quyen']`.
- Kiểm tra bản ghi `chucnang` và URL.
- Kiểm tra nhóm quyền `nhomquyen`.
- Kiểm tra `adminType` và logic `Request::checkRoles()`.

#### Chỉ số không hiện ở Nhập liệu

- `trang_thai` phải bằng 2.
- Khoa/phòng user phải hợp lệ.
- `phong` phải là JSON hợp lệ và chứa ID khoa/phòng, hoặc dữ liệu cũ phải khớp `id_khoaphong`.
- Chu kỳ phải hợp lệ.

#### Không lưu được phiếu

- Kiểm tra kỳ có phải tương lai không.
- Kiểm tra câu hỏi bắt buộc.
- Kiểm tra percentage 0..100, score 1..10, number và độ dài số.
- Kiểm tra option gửi lên có nằm trong cấu hình biểu mẫu.

#### Thống kê sai hoặc thiếu

- Kiểm tra `du_lieu` JSON của `ct_chiso`.
- Kiểm tra trường `ty_le_phan_tram` có phải số.
- Kiểm tra ID câu hỏi giữa biểu mẫu hiện tại và dữ liệu lịch sử.
- Kiểm tra có nhiều phiếu trong cùng kỳ hay không.

### Hướng nâng cấp đề xuất

Ưu tiên 1: bảo mật xác thực và DB. Ưu tiên 2: chuẩn hóa truy cập DB bằng prepared statements. Ưu tiên 3: viết lại backup/restore. Ưu tiên 4: tách cấu hình môi trường khỏi source. Ưu tiên 5: bổ sung test cho quyền, vòng đời chỉ số, tính kỳ và validate biểu mẫu.

### Kế hoạch bảo trì định kỳ

| Chu kỳ | Công việc | Kết quả mong đợi |
| --- | --- | --- |
| Hàng ngày | Kiểm tra Apache/PHP error log, dung lượng đĩa, lỗi DB nghiêm trọng | Phát hiện sớm sự cố vận hành |
| Hàng tuần | Kiểm tra log ứng dụng, tài khoản bị khóa, dữ liệu nhập bất thường | Phát hiện lỗi nghiệp vụ và tài khoản |
| Hàng tháng | Sao lưu DB, kiểm tra restore trên môi trường thử nghiệm, rà soát user/quyền | Bảo đảm khả năng phục hồi và quyền hợp lệ |
| Mỗi lần release | Sao lưu source + DB, chạy kiểm tra syntax, test luồng chính và quyền | Có điểm quay lui an toàn |
| Hàng quý | Rà soát thư viện/mã legacy, bảo mật session, cấu hình DB, dung lượng bảng | Giảm nợ kỹ thuật và rủi ro bảo mật |

### Checklist sau khi triển khai phiên bản mới

1. Kiểm tra `_DEFAULT_VERSION_` và tiêu đề giao diện hiển thị đúng phiên bản.
2. Mở trang đăng nhập, đăng nhập bằng tài khoản thường và tài khoản quản trị.
3. Kiểm tra menu theo ít nhất hai nhóm quyền khác nhau.
4. Tạo một chỉ số thử nghiệm, gửi duyệt và duyệt.
5. Đăng nhập tài khoản thuộc khoa/phòng được gán, kiểm tra chỉ số xuất hiện trong Nhập liệu.
6. Lưu một phiếu và xác nhận `ct_chiso` có bản ghi mới.
7. Kiểm tra biểu đồ, danh sách phiếu và xuất Excel.
8. Kiểm tra log ứng dụng/PHP/Apache không phát sinh lỗi mới.
9. Nếu có migration, đối chiếu schema thực tế với file migration.
10. Ghi nhận phiên bản, thời gian triển khai, người triển khai và phương án rollback.

### Quản lý thay đổi source và database

Mỗi thay đổi nên có mô tả rõ module, file, bảng DB và dữ liệu bị ảnh hưởng. Không sửa trực tiếp dump SQL cũ để đại diện cho migration mới; nên tạo file migration riêng có thứ tự thời gian. Trước khi đổi tên/xóa cột hoặc bảng, dùng tìm kiếm toàn source để xác định Action, Peer, View, JS và báo cáo nào còn tham chiếu.

Với trường JSON như `phong`, `bieumau`, `du_lieu`, cần giữ khả năng tương thích dữ liệu cũ. Nếu thay schema JSON, nên có trường `version` và code đọc được ít nhất định dạng hiện tại cùng định dạng trước đó trong giai đoạn chuyển đổi.

### Hướng xử lý các rủi ro ưu tiên cao

#### Mật khẩu và đăng nhập

Chuyển dần từ MD5 sang `password_hash()`/`password_verify()`. Có thể nâng cấp theo kiểu tương thích ngược: khi user đăng nhập thành công bằng MD5, tạo hash mới và cập nhật bản ghi. Bổ sung rate limit đăng nhập và `session_regenerate_id(true)` sau khi xác thực thành công.

#### Truy cập cơ sở dữ liệu

Ưu tiên các endpoint nhận dữ liệu người dùng để chuyển sang prepared statements trước. Đồng thời bỏ việc in trực tiếp câu SQL khi query lỗi ở môi trường production; thay bằng log nội bộ có kiểm soát.

#### Backup/Restore

Thành phần `mysql_*` cần viết lại bằng `mysqli`/PDO hoặc dùng `mysqldump`/`mysql` command-line với đường dẫn và quyền được quản lý rõ. Restore phải được thử trên database staging trước khi áp dụng production.

#### Cấu hình môi trường

Tách DB user/password, base URL và các thông số môi trường khỏi source. Production không nên chạy bằng MySQL `root` với mật khẩu trống. Tài khoản ứng dụng chỉ nên có các quyền cần thiết trên database `ql_cscl`.

## 6. Lưu trữ hồ sơ

| STT | Tên hồ sơ | Mã hiệu | Nơi lưu | Thời gian lưu |
| --- | --- | --- | --- | --- |
| 1 | Tài liệu tài liệu bảo trì hệ thống qlcl | Chưa cấp | Thư mục `docs/QL_CSCL` và kho tài liệu nội bộ | Theo quy định quản lý tài liệu của Bệnh viện |
| 2 | Source code liên quan | Theo repository | Máy chủ/kho source được quản lý | Theo vòng đời hệ thống |
| 3 | CSDL và bản sao lưu liên quan | Theo bản backup | Vị trí backup được quản lý | Theo chính sách sao lưu hiện hành |

## 7. Tài liệu liên quan

| STT | Mã tài liệu | Tên tài liệu |
| --- | --- | --- |
| 1 | QTQL.BVTN.09 | Quy trình yêu cầu xây dựng phần mềm |
| 2 | Chưa cấp | Các tài liệu còn lại trong bộ tài liệu QLCL |
| 3 | Source | `conf/config.php`, `index.php`, `web_src/`, `www/`, `js/`, `sql/` |
