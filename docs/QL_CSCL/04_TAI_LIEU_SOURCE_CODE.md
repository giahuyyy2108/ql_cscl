# TÀI LIỆU SOURCE CODE HỆ THỐNG QLCL

**Hệ thống:** Quản lý chất lượng - Bệnh viện Thống Nhất  
**Phiên bản tài liệu:** v1.2  
**Ngày cập nhật:** 29/10/2026

## 1. Cấu trúc dự án

```text
ql_cscl/
├─ index.php
├─ conf/
│  └─ config.php
├─ web_src/
│  ├─ admin/          # Action/controller theo module
│  ├─ bean/           # Entity + Peer truy cập dữ liệu
│  └─ common/         # Request, MySQL wrapper, util, log...
├─ www/
│  ├─ admin/          # View quản trị
│  ├─ chisochatluong/ # View chỉ số
│  ├─ chisokhoa/      # View theo khoa
│  ├─ nhaplieu/       # View nhập liệu
│  └─ View/_sharedLayout/
├─ js/                # JavaScript/AJAX theo module
├─ sql/               # Dump và migration
└─ huongdan/          # Tài liệu/tệp mẫu cũ
```

## 2. Điểm vào và router

`index.php` là front controller. Luồng xử lý chính:

1. Nạp `conf/config.php` và các lớp common/bean cần thiết.
2. Đọc session đăng nhập (`sUserID`, `sUserLogin`...).
3. Xác định handle; mặc định là `dashboard`.
4. Nếu chưa đăng nhập, chuyển sang `login/index`.
5. Ánh xạ Action theo quy ước `./web_src/admin/<handle>Action.php`.
6. Chọn method; mặc định `index`.
7. Kiểm tra `$listRole` bằng `Request::checkRoles()`.
8. Gọi method Action.
9. Nếu Action trả model/view, render trong `www/View/_sharedLayout/index.php`.
10. Method trả `null` thường tự trả JSON, file download hoặc HTML riêng và bỏ shared layout.

Các hằng quan trọng từ `conf/config.php`:

| Hằng | Vai trò |
| --- | --- |
| `_FILE_HANDLE_` | Thư mục Action, hiện là `./web_src/admin/` |
| `_DEFAULT_HANDLE_` | Route mặc định `dashboard` |
| `_CLASS_HANDLE_` | Hậu tố class `Action` |
| `_DEFAULT_URL_` | Base URL dùng redirect/link |
| `_DEFAULT_VERSION_` | Version giao diện/source |

## 3. Lớp Request và quyền

`web_src/common/Request.php` cung cấp abstraction GET/POST, attribute/model và JSON response. Quyền nằm trong `$_SESSION['quyen']`.

Kiểm tra quyền hỗ trợ hai mức:

- tên class/module, ví dụ `chisochatluong`;
- tên class + method, ví dụ `chisochatluong.duyet`.

Nếu `AdminType == 1`, logic quyền cho phép truy cập mà không cần quyền chi tiết.

## 4. Lớp truy cập DB

`web_src/common/mysql.php` định nghĩa `db_mysql` dựa trên `mysqli`, dùng kết nối dùng chung và thiết lập timeout kết nối/đọc. API kiểu Peer gọi `query()`, `fetch_array()`, `num_rows()`, `insert_id()`.

Điểm cần lưu ý: `query()` hiện dừng ứng dụng và có thể in SQL khi lỗi. Ngoài ra module backup/restore vẫn chứa API `mysql_*` cũ, không đồng nhất với lớp DB chính.

## 5. Mẫu Action/Peer/View/JS

Mỗi module thường gồm:

```text
web_src/admin/<module>Action.php   -> nhận request, kiểm tra nghiệp vụ, chọn view/JSON
web_src/bean/<Entity>Peer.php     -> SQL + đọc/ghi dữ liệu
web_src/bean/<Entity>.php         -> object dữ liệu khi có
www/<module>/...                  -> HTML/PHP view
js/<module>/...                   -> AJAX và tương tác giao diện
```

## 6. Danh sách Action chính

| Action | Quyền tiêu biểu | Method/chức năng chính | View |
| --- | --- | --- | --- |
| `dashboardAction` | theo truy cập hệ thống | `index` | `www/admin/dashboard/viewDashboard.htm` |
| `loginAction` | public/session | `index`, `login`, `logout`, `changePass`, `savePass` | login/change password |
| `userAction` | `user`, `saveUser`, `deleteUser`, `phanquyen`, `lock` | CRUD user, phân quyền, khóa | `www/admin/nguoidung/nguoiDung.htm` |
| `nhomquyenAction` | `nhomquyen`, `save`, `delete`, `phanquyen` | CRUD nhóm, lưu quyền | `www/admin/nguoidung/nhomquyen.htm` |
| `logAction` | `log`, `deleteLog` | xem/xóa log | `www/admin/log/viewListLog.htm` |
| `backupAction` | `backup` | `index`, `backup` với backup/restore | `www/admin/backuprestore/viewBackupRestore.htm` |
| `khoaphongAction` | `khoaphong`, `save`, `delete` | CRUD khoa/phòng | admin view tương ứng |
| `khoiAction` | `khoi`, `save`, `delete` | CRUD khối | admin view tương ứng |
| `chukyAction` | `chuky` | quản lý chu kỳ | admin view tương ứng |
| `donvitinhAction` | `donvitinh`, `save`, `delete` | CRUD đơn vị tính | admin view tương ứng |
| `danhmuckcttAction` | `danhmuckctt`, `save`, `delete` | khía cạnh/thành tố | admin view tương ứng |
| `phamviAction` | `phamvi` | phạm vi chỉ số | admin view tương ứng |
| `trangthaiAction` | `trangthai` | trạng thái | admin view tương ứng |
| `chisochatluongAction` | xem/lưu/cập nhật/gửi/duyệt/xóa/từ chối | quản lý vòng đời chỉ số, biểu đồ, Excel | `www/chisochatluong/index.php` |
| `chisokhoaAction` | `chisokhoa` | dữ liệu khoa, biểu đồ, danh sách/xóa phiếu | `www/chisokhoa/index.php` |
| `NhapLieuAction` | `chisokhoa` | dữ liệu nhập, trạng thái, lưu phiếu | `www/nhaplieu/index.php` |

## 7. chisochatluongAction

Các method chính:

| Method | Chức năng |
| --- | --- |
| `index` | Nạp danh mục và view |
| `getData` | Danh sách chỉ số theo quyền |
| `save` | Tạo chỉ số mới và chuẩn hóa JSON |
| `update` | Cập nhật chỉ số |
| `gui` | Gửi duyệt |
| `duyet` | Duyệt |
| `tuchoi` | Từ chối |
| `taolai` | Clone chỉ số bị từ chối thành Nháp |
| `xoa` | Xóa chỉ số |
| `getBieuDoChuKy` | Dữ liệu biểu đồ theo kỳ |
| `getThongKeTheoThang` | Trung bình theo tháng |
| `getDanhSachPhieu` | Chi tiết các phiếu |
| `xuatExcel` | Xuất `.xls` |

Các helper `coQuyenXemChiSo()`, `getChiSoDuocXem()` và `getChiSoFromRequest()` chặn truy cập dữ liệu thống kê trái quyền.

## 8. ChiSoChatLuongPeer

`ChiSoChatLuongPeer` chứa phần lớn logic DB của chỉ số:

- `Save()` tạo chỉ số ở trạng thái 0.
- `Update()` cập nhật định nghĩa/cấu hình/biểu mẫu.
- `Gui()` -> trạng thái 1.
- `Duyet()` -> trạng thái 2.
- `TuChoi()` -> trạng thái 3.
- `taoLaiTuDonBiTuChoi()` sao chép chỉ số bị từ chối của chính người dùng sang Nháp mới.
- `getList()` áp dụng điều kiện hiển thị theo người tạo/quyền duyệt.
- `getListDaDuyetByKhoa()` lấy chỉ số đã duyệt được gán cho khoa/phòng.
- `getKhoaPhongIdByUserId()` lấy khoa/phòng của user.

Trường `phong` là JSON chứa mảng ID khoa/phòng. Code có nhánh fallback về `id_khoaphong` để tương thích dữ liệu cũ.

## 9. NhapLieuAction

### 9.1 `getData()`

Lấy khoa/phòng của user, danh sách chỉ số đã duyệt, tính kỳ hiện tại và gắn cờ `da_nhap_ky_hien_tai`. Trả thống kê `tong`, `da_nhap`, `chua_nhap`.

### 9.2 `getTrangThaiNhap()`

Trả metadata kỳ hiện tại, biểu mẫu và các phiếu đã nhập trong năm.

### 9.3 `save()`

Kiểm tra user, khoa/phòng, cấu hình chỉ số, kỳ nhập và câu trả lời. Dữ liệu hợp lệ được đóng gói trong `CtChiSo` và gọi `CtChiSoPeer::save()`.

### 9.4 `kiemTraCauTraLoi()`

Thực hiện validate bắt buộc, kiểu số, phần trăm, score, option và tính điểm. Kết quả JSON lưu cả câu trả lời và số liệu điểm.

### 9.5 `tinhKyHienTai()`

Đọc `chuky`, kiểm tra 1..12 và chia hết 12; tính kỳ dựa trên tháng tạo chỉ số.

## 10. CtChiSoPeer

| Method | Ý nghĩa |
| --- | --- |
| `getCauHinhNhap()` | Chỉ trả cấu hình chỉ số đã duyệt và đúng khoa/phòng |
| `getTheoNam()` | Danh sách phiếu theo năm, giải mã JSON |
| `daNhapKy()` | Kiểm tra có ít nhất một phiếu cho kỳ |
| `getTrungBinhTheoKy()` | Trung bình `ty_le_phan_tram` theo năm/kỳ |
| `getThongKeTheoThang()` | Trung bình theo tháng `created_at` |
| `getTrungBinhTheoCauHoi()` | Thống kê câu hỏi có điểm, tổng và theo khoa |
| `getDanhSachPhieu()` | Ghép phiếu + khoa + user + nội dung biểu mẫu |
| `xoaPhieu()` | Xóa theo `id` và `ma_chi_so` |
| `save()` | `INSERT` một phiếu mới vào `ct_chiso` |

`save()` không kiểm tra duplicate theo kỳ; hành vi hiện tại cho phép nhiều bản ghi cho cùng `(ma_chi_so, id_khoaphong, nam, ky)`.

## 11. Xác thực trong UserPeer

`UserPeer::getUserLogin($username, $pass)` chạy truy vấn user theo username và `nd_block = 0`, sau đó so sánh `md5($pass)` với cột `password`.

`UserPeer::save()` cũng băm mật khẩu bằng MD5 khi tạo user, đổi mật khẩu hoặc cập nhật có đổi mật khẩu. `updateLock()` thay đổi `nd_block` và ghi log.

Trong `loginAction`, code giới hạn đăng nhập sai theo IP đang bị comment/đặt `$count = 0`, nên hiện không hoạt động.

## 12. Endpoint AJAX chính

| JavaScript | Endpoint tiêu biểu |
| --- | --- |
| `js/chisochatluong/chisochatluong.js` | `chisochatluong/getData/`, `save/`, `update/`, `gui/`, `duyet/`, `tuchoi/`, `taolai/`, `xoa/`, `getBieuDoChuKy/`, `getDanhSachPhieu/`, `xuatExcel/` |
| `js/nhaplieu/nhaplieu.js` | `NhapLieu/getData/`, `getTrangThaiNhap/`, `save/` |
| `js/chisokhoa/chisokhoa.js` | `chisokhoa/getData/`, `getBieuDoChuKy/`, `getDanhSachPhieu/`, `xoaPhieu/` |
| `js/user.js` | `user/getData/`, `saveUser/`, `savePhanquyen/`, khóa/xóa |
| `js/nhomquyen.js` | `nhomquyen/getData/`, `save/`, `savePhanquyen/` |

## 13. Cấu trúc bảng lõi

### 13.1 `chi_so_chat_luong`

Nhóm trường chính:

- nhận diện: `ma_chi_so`, `ten_chi_so`;
- phân loại: `ma_khia_canh`, `ma_thanh_to`, `nhom_chi_so`, `pham_vi`;
- mục tiêu/công thức: `muc_tieu`, `nguong_canh_bao`, `id_donvitinh`, `id_chuky`, `cong_thuc`;
- workflow: `trang_thai`, `nguoi_gui`, `thoi_gian_gui`, `nguoi_duyet`, `thoi_gian_duyet`, `ly_do_tu_choi`;
- phạm vi khoa: `id_khoaphong`, `phong` JSON;
- nội dung: `dinh_nghia`, `thu_thap`, `ten_tu_so`, `ten_mau_so`, `bieumau` JSON;
- audit: `created_at`, `updated_at`, `ma_chi_so_goc`.

### 13.2 `ct_chiso`

| Cột | Vai trò |
| --- | --- |
| `id` | Khóa chính |
| `ma_chi_so` | FK đến chỉ số |
| `id_user` | Người nhập |
| `id_khoaphong` | Khoa/phòng nhập |
| `nam` | Năm kỳ nhập |
| `ky` | Số kỳ |
| `du_lieu` | JSON câu trả lời và kết quả điểm |
| `created_at`, `updated_at` | Thời gian |

## 14. Các bảng khác trong dump

Dump còn có `bacsi`, `bang_kiem`, `benhnhan`, `codemaster`, `quaytiepnhan`, `nhap_lieu_chi_so` và các bảng liên quan hệ thống cũ/khác. Khi dọn source hoặc schema, cần xác minh toàn bộ tham chiếu bằng `rg` và kiểm tra runtime trước khi xóa.

## 15. Điểm kỹ thuật cần lưu ý khi sửa code

- SQL ở nhiều Peer được ghép chuỗi trực tiếp; mọi tham số mới phải được ép kiểu/escape tối thiểu và nên chuyển dần sang prepared statements.
- Không thay ID câu hỏi biểu mẫu tùy tiện nếu cần đọc dữ liệu lịch sử.
- Route phụ thuộc quy ước tên file/class; đổi tên module phải cập nhật JS, menu và quyền.
- Xóa `chi_so_chat_luong` có thể cascade xóa `ct_chiso`.
- Menu được sinh từ DB, nên code mới chưa chắc xuất hiện nếu chưa thêm `chucnang`/quyền.
- Backup/restore là khu vực legacy và cần test riêng trên phiên bản PHP đang chạy.
- `_DEFAULT_URL_` hard-code khiến redirect phụ thuộc máy chủ hiện tại.

## 16. Quy trình thêm một chức năng mới

1. Tạo hoặc mở rộng Action trong `web_src/admin/`.
2. Khai báo `$listRole` cho method có giới hạn quyền.
3. Viết logic dữ liệu trong Peer, ưu tiên prepared statement khi refactor.
4. Thêm view trong `www/` nếu cần giao diện.
5. Thêm JS/AJAX trong `js/` nếu cần.
6. Khai báo `chucnang` và nhóm quyền để menu/quyền hoạt động.
7. Nếu thay schema, thêm migration SQL riêng.
8. Kiểm thử cả trường hợp có quyền và không có quyền.
9. Kiểm thử dữ liệu khác khoa/phòng để tránh vượt phạm vi.
10. Cập nhật tài liệu chức năng và bảo trì.
