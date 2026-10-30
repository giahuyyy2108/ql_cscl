# TÀI LIỆU MÔ TẢ CHỨC NĂNG HỆ THỐNG QLCL

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

Mô tả đầy đủ các phân hệ, chức năng, tác nhân, điều kiện xử lý, dữ liệu đầu vào/đầu ra và các quy tắc nghiệp vụ của hệ thống QLCL.

## 2. Phạm vi áp dụng

Áp dụng cho việc phân tích, nghiệm thu, vận hành, kiểm thử và phát triển các chức năng thuộc hệ thống QLCL.

## 3. Trách nhiệm

| Đối tượng | Trách nhiệm |
| --- | --- |
| Đơn vị nghiệp vụ | Xác nhận yêu cầu, quy tắc nghiệp vụ và kết quả chức năng. |
| Bộ phận CNTT | Phân tích, triển khai, kiểm thử và duy trì chức năng theo tài liệu. |
| Người quản trị | Cấu hình tài khoản, quyền và danh mục phục vụ chức năng. |
| Người dùng | Sử dụng chức năng theo đúng vai trò và phản hồi sai lệch nghiệp vụ. |

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

### Tổng quan nghiệp vụ

Hệ thống QLCL quản lý vòng đời chỉ số chất lượng từ lúc xây dựng chỉ số, thiết kế biểu mẫu, gửi duyệt, phân phối cho khoa/phòng, nhập nhiều phiếu theo kỳ đến thống kê và xuất dữ liệu. Hệ thống đồng thời cung cấp quản trị người dùng, quyền, danh mục và nhật ký.

### Tác nhân sử dụng

| Tác nhân | Vai trò chính |
| --- | --- |
| Người tạo chỉ số | Tạo, sửa, gửi duyệt, theo dõi trạng thái, tạo lại chỉ số bị từ chối |
| Người duyệt | Xem chỉ số chờ duyệt, duyệt hoặc từ chối |
| Người nhập liệu khoa/phòng | Nhập phiếu cho chỉ số đã duyệt và được gán cho khoa/phòng |
| Người theo dõi chỉ số khoa | Xem biểu đồ, thống kê, danh sách phiếu và thao tác theo quyền |
| Quản trị viên | Quản lý user, nhóm quyền, danh mục, log và các chức năng hệ thống |

Quyền thực tế được kiểm tra bằng mã chức năng trong session. `adminType = 1` được phép vượt kiểm tra quyền chi tiết.

### Ma trận chức năng

| Phân hệ | Chức năng | Đầu vào chính | Kết quả |
| --- | --- | --- | --- |
| Đăng nhập | Xác thực tài khoản | username, password | session, token, quyền |
| Người dùng | Thêm/sửa/xóa/khóa/phân quyền | thông tin user, khoa/phòng, nhóm quyền | bản ghi `user` |
| Nhóm quyền | Quản lý nhóm và tập quyền | tên nhóm, chuỗi quyền | bản ghi `nhomquyen` |
| Chỉ số chất lượng | Tạo/sửa/xóa | cấu hình chỉ số, phòng, biểu mẫu | `chi_so_chat_luong` |
| Duyệt chỉ số | Gửi/duyệt/từ chối/tạo lại | mã chỉ số, người thao tác, lý do | trạng thái và lịch sử thời gian |
| Nhập liệu | Lưu phiếu theo kỳ | câu trả lời, năm, kỳ | `ct_chiso.du_lieu` JSON |
| Thống kê | Theo kỳ/tháng/câu hỏi | mã chỉ số | dữ liệu tổng hợp JSON |
| Xuất Excel | Xuất chỉ số và thống kê | mã chỉ số | tệp `.xls` |
| Danh mục | Quản lý dữ liệu nền | khoa, khối, chu kỳ, đơn vị, KCTT... | các bảng danh mục |
| Nhật ký | Theo dõi thao tác | điều kiện lọc | danh sách log |
| Backup/Restore | Sao lưu/phục hồi SQL | tệp SQL | dữ liệu CSDL |

### Vòng đời chỉ số chất lượng

#### Trạng thái

| Trạng thái | Mã | Chuyển tiếp hợp lệ trong mã nguồn |
| --- | --- | --- |
| Nháp | 0 | Gửi -> Chờ duyệt |
| Chờ duyệt | 1 | Duyệt -> Đã duyệt; Từ chối -> Từ chối |
| Đã duyệt | 2 | Được dùng để nhập liệu |
| Từ chối | 3 | Tạo lại -> một bản Nháp mới |

#### Quy tắc hiển thị

- Người không có quyền duyệt chỉ thấy chỉ số do chính mình tạo.
- Người có quyền duyệt thấy chỉ số do mình tạo ở mọi trạng thái.
- Với chỉ số do người khác tạo, người duyệt chỉ thấy trạng thái 1, 2, 3.
- Chỉ số dành cho nhập liệu phải ở trạng thái 2 và khoa/phòng hiện tại phải nằm trong `phong` hoặc khớp `id_khoaphong` theo nhánh tương thích dữ liệu cũ.

#### Tạo mới và cập nhật

Khi tạo mới, hệ thống đặt `trang_thai = 0`. Trường `phong` được chuẩn hóa thành mảng số nguyên duy nhất rồi lưu JSON. Biểu mẫu được chuẩn hóa thành JSON có cấu trúc `{version: 1, cau_hoi: [...]}`.

Nếu `pham_vi = 3` (Toàn bệnh viện), hệ thống gán tất cả khoa/phòng vào trường `phong`. Tên chỉ số là bắt buộc; người tạo phải có khoa/phòng mặc định hợp lệ.

#### Gửi, duyệt và từ chối

- Gửi: đổi trạng thái sang 1 và ghi `thoi_gian_gui`.
- Duyệt: đổi trạng thái sang 2, ghi `nguoi_duyet` và `thoi_gian_duyet`.
- Từ chối: đổi trạng thái sang 3 và ghi `ly_do_tu_choi`.
- Tạo lại: chỉ áp dụng cho bản bị từ chối thuộc người dùng hiện tại; sao chép cấu hình, đặt trạng thái 0, xóa metadata gửi/duyệt/từ chối và ghi `ma_chi_so_goc`.

### Biểu mẫu và kiểm tra dữ liệu

Biểu mẫu nằm trong `chi_so_chat_luong.bieumau` dạng JSON. Mỗi câu hỏi thường có `id`, `noi_dung`, `loai`, `ky_hieu`, `bat_buoc`, `do_dai_so`, `lua_chon`.

| Loại | Kiểm tra phía server | Tham gia chấm điểm |
| --- | --- | --- |
| category/subcategory | Không nhận câu trả lời | Không |
| short_text và loại văn bản | Bắt buộc nếu cấu hình | Không |
| number | Phải là số; có thể giới hạn số chữ số | Không mặc định |
| percentage | Số trong khoảng 0..100 | Không mặc định |
| score | Số trong khoảng 1..10 | Có |
| radio/select/satisfaction | Giá trị phải thuộc danh sách lựa chọn | Có nếu lựa chọn có điểm |
| checkbox | Mọi lựa chọn phải thuộc danh sách cấu hình | Có, cộng điểm |

ID câu hỏi là khóa liên kết giữa cấu hình biểu mẫu và dữ liệu đã nhập. Khi biểu mẫu thay đổi, dữ liệu lịch sử vẫn được giải mã dựa trên ID câu hỏi.

### Nhập dữ liệu theo chu kỳ

#### Xác định chu kỳ

Hệ thống đọc số kỳ trong năm từ bảng `chuky`. Giá trị hợp lệ phải từ 1 đến 12 và 12 chia hết cho số kỳ. Số tháng mỗi kỳ = 12 / số kỳ.

Mốc tính kỳ bắt đầu từ tháng/năm `created_at` của chỉ số. Kỳ hiện tại được tính theo độ lệch tháng từ mốc này và giới hạn không vượt quá tổng số kỳ.

#### Quy tắc lưu

Người nhập phải đăng nhập, có khoa/phòng hợp lệ, chỉ số đã duyệt và được gán cho khoa/phòng. Chỉ được lưu kỳ hiện tại hoặc kỳ trước trong phạm vi năm/chu kỳ được kiểm tra; không lưu kỳ tương lai.

Mỗi lần lưu tạo một dòng mới trong `ct_chiso`. Cơ sở dữ liệu hiện không đặt unique cho `(ma_chi_so, id_khoaphong, nam, ky)`, chỉ có index, nên một kỳ có thể có nhiều phiếu.

#### Cấu trúc dữ liệu phiếu

`ct_chiso.du_lieu` lưu JSON, điển hình:

```json
{
  "cau_tra_loi": {"q1": "...", "q2": "..."},
  "tong_diem": 15,
  "diem_toi_da": 20,
  "ty_le_phan_tram": 75
}

```

### Thống kê và báo cáo

#### Trung bình theo kỳ

Hệ thống đọc toàn bộ phiếu của chỉ số, nhóm theo `nam` và `ky`, sau đó lấy trung bình `du_lieu.ty_le_phan_tram`.

#### Thống kê theo tháng

Phiếu được nhóm theo tháng của `created_at` và lấy trung bình tỷ lệ phần trăm.

#### Trung bình theo câu hỏi

Chỉ các câu hỏi có cơ chế điểm mới được tổng hợp. Hệ thống tính tỷ lệ điểm từng câu hỏi, tổng hợp toàn hệ thống và tách theo khoa/phòng.

#### Danh sách phiếu

Danh sách trả về mã phiếu, khoa/phòng, năm, kỳ, người nhập, thời gian tạo/cập nhật, điểm, tỷ lệ và chi tiết câu trả lời. Hệ thống ghép dữ liệu câu trả lời với cấu hình biểu mẫu hiện hành để hiển thị nội dung câu hỏi.

#### Xuất Excel

Action `xuatExcel` xuất HTML dưới phần mở rộng `.xls`. Dữ liệu xuất được kiểm tra quyền xem chỉ số trước khi sinh tệp.

### Quản trị người dùng và quyền

#### Xác thực

`UserPeer::getUserLogin()` tìm user theo username và `nd_block = 0`, sau đó so sánh `md5(password nhập)` với cột `password`. Khi đăng nhập thành công, session ghi ID, username, họ tên, adminType, token và quyền.

#### Phân quyền

Quyền được lưu trong session `quyen`. Kiểm tra quyền chấp nhận cả quyền mức class và `class.method`. Menu được dựng từ bảng `chucnang`, vì vậy cấu hình `chucnang`, `nhomquyen` và quyền user phải đồng bộ.

#### Khóa tài khoản

`UserPeer::updateLock()` cập nhật `nd_block`. User bị khóa không vượt qua truy vấn đăng nhập.

### Danh mục nền

| Danh mục | Mục đích |
| --- | --- |
| Khoa/phòng | Gán user và phạm vi chỉ số |
| Khối | Nhóm khoa/phòng |
| Chu kỳ | Số kỳ nhập trong năm |
| Đơn vị tính | Thuộc tính chỉ số |
| Khía cạnh/Thành tố | Phân loại chỉ số |
| Phạm vi | Khoa/phòng hoặc toàn bệnh viện |
| Trạng thái | Tên/nhãn trạng thái |

### Nhật ký và sao lưu/phục hồi

Các Peer ghi log cho một số thao tác quản trị. Phân hệ backup/restore cho phép tạo bản sao SQL và phục hồi từ tệp tải lên. Trước khi restore, action cố tạo một bản backup dự phòng. Thành phần này dùng API `mysql_*` cũ trong khi lớp DB chính dùng `mysqli`, vì vậy cần coi đây là khu vực rủi ro cao khi nâng phiên bản PHP.

### Các trường hợp biên đáng chú ý

- Chỉ số đã duyệt nhưng không gán khoa/phòng hiện tại sẽ không xuất hiện ở Nhập liệu.
- Chu kỳ không chia hết 12 làm tính kỳ không hợp lệ.
- Thay đổi ID câu hỏi sau khi đã có phiếu làm việc ghép dữ liệu lịch sử kém tin cậy.
- Xóa chỉ số có thể kéo theo xóa phiếu do khóa ngoại cascade.
- `ct_chiso` cho phép nhiều phiếu cùng kỳ; chức năng `daNhapKy()` chỉ kiểm tra có ít nhất một phiếu để hiển thị trạng thái đã nhập.

### Đặc tả chức năng chi tiết

| Chức năng | Điều kiện trước | Đầu vào chính | Xử lý nghiệp vụ | Kết quả |
| --- | --- | --- | --- | --- |
| Đăng nhập | Tài khoản tồn tại và không bị khóa | username, password | Tìm user, so sánh MD5, tạo session/token/quyền | Vào Dashboard hoặc báo lỗi |
| Tạo chỉ số | Đã đăng nhập, có quyền tạo/sửa | thông tin chỉ số, phạm vi, biểu mẫu | Chuẩn hóa dữ liệu, kiểm tra tên, xác định khoa tạo | Chỉ số mới trạng thái Nháp |
| Cập nhật chỉ số | Có quyền và truy cập được chỉ số | mã chỉ số và cấu hình mới | Cập nhật thông tin định nghĩa/cấu hình/form | Chỉ số được cập nhật |
| Gửi duyệt | Chỉ số thuộc phạm vi người dùng, có quyền gửi | mã chỉ số | Đổi trạng thái 0/3 sang 1, ghi thời gian gửi | Chờ duyệt |
| Duyệt | Có quyền duyệt | mã chỉ số | Đổi trạng thái sang 2, lưu người duyệt/thời gian | Chỉ số sẵn sàng nhập liệu |
| Từ chối | Có quyền duyệt | mã chỉ số, lý do | Đổi trạng thái sang 3, lưu lý do | Người tạo có thể tạo lại |
| Tạo lại | Chỉ số bị từ chối và thuộc người dùng hiện tại | mã chỉ số cũ | Sao chép cấu hình, xóa metadata duyệt, gắn `ma_chi_so_goc` | Bản nháp mới |
| Lấy danh sách nhập liệu | User có khoa/phòng | khoa/phòng, thời điểm hiện tại | Lọc chỉ số đã duyệt và được gán, tính kỳ | Danh sách chỉ số + trạng thái nhập |
| Lưu phiếu | Chỉ số được duyệt, được gán đúng khoa, kỳ hợp lệ | câu trả lời, năm, kỳ | Validate câu trả lời, tính điểm, INSERT `ct_chiso` | Phiếu mới |
| Xem thống kê | Có quyền xem chỉ số | mã chỉ số | Tổng hợp theo kỳ/tháng/câu hỏi | Dữ liệu biểu đồ/bảng |
| Xóa phiếu | Có quyền tương ứng | id phiếu, mã chỉ số | Kiểm tra phiếu rồi DELETE | Phiếu bị xóa |

### Luồng trạng thái và quyền thao tác

| Trạng thái | Người tạo | Người duyệt | Nhập liệu khoa/phòng |
| --- | --- | --- | --- |
| Nháp (0) | Xem/sửa/gửi/xóa theo quyền | Không cần xử lý | Không thấy để nhập |
| Chờ duyệt (1) | Theo dõi | Duyệt hoặc từ chối | Không thấy để nhập |
| Đã duyệt (2) | Xem/theo dõi | Xem | Được nhập nếu đúng khoa/phòng |
| Từ chối (3) | Xem lý do, tạo lại | Xem | Không thấy để nhập |

Việc người dùng nhìn thấy một chỉ số còn phụ thuộc quyền và quan hệ sở hữu. Người duyệt có thể xem chỉ số của mình ở mọi trạng thái và các chỉ số khác ở trạng thái chờ duyệt/đã duyệt/từ chối. Người dùng thông thường chủ yếu thấy chỉ số do mình tạo.

### Quy tắc phạm vi dữ liệu

Phạm vi khoa/phòng được lưu trong `chi_so_chat_luong.phong` dưới dạng JSON mảng ID. Khi lấy chỉ số phục vụ nhập liệu, hệ thống kiểm tra khoa/phòng hiện tại có nằm trong danh sách này hay không; với dữ liệu cũ có thể fallback sang `id_khoaphong`. Vì vậy việc gán đúng `user.MaKhoaPhong` và duy trì ID khoa/phòng ổn định là điều kiện quan trọng để hệ thống phân phối chỉ số đúng nơi.

### Quy tắc dữ liệu biểu mẫu

Biểu mẫu được lưu dưới JSON dạng `{"version": 1, "cau_hoi": [...]}`. Mỗi câu hỏi có loại và thuộc tính riêng. Các loại category/subcategory dùng để trình bày; checkbox/radio/select/satisfaction dùng danh sách lựa chọn; number/percentage/score yêu cầu kiểm tra miền giá trị; các trường text-like nhận dữ liệu văn bản. Hệ thống dùng ID câu hỏi để nối câu trả lời lịch sử với cấu hình hiện hành, do đó thay ID sau khi đã có dữ liệu có thể làm báo cáo lịch sử khó đọc đúng.

## 6. Lưu trữ hồ sơ

| STT | Tên hồ sơ | Mã hiệu | Nơi lưu | Thời gian lưu |
| --- | --- | --- | --- | --- |
| 1 | Tài liệu tài liệu mô tả chức năng hệ thống qlcl | Chưa cấp | Thư mục `docs/QL_CSCL` và kho tài liệu nội bộ | Theo quy định quản lý tài liệu của Bệnh viện |
| 2 | Source code liên quan | Theo repository | Máy chủ/kho source được quản lý | Theo vòng đời hệ thống |
| 3 | CSDL và bản sao lưu liên quan | Theo bản backup | Vị trí backup được quản lý | Theo chính sách sao lưu hiện hành |

## 7. Tài liệu liên quan

| STT | Mã tài liệu | Tên tài liệu |
| --- | --- | --- |
| 1 | QTQL.BVTN.09 | Quy trình yêu cầu xây dựng phần mềm |
| 2 | Chưa cấp | Các tài liệu còn lại trong bộ tài liệu QLCL |
| 3 | Source | `conf/config.php`, `index.php`, `web_src/`, `www/`, `js/`, `sql/` |
