# HƯỚNG DẪN SỬ DỤNG HỆ THỐNG QLCL

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

Hướng dẫn người dùng và quản trị viên thao tác đúng trên hệ thống QLCL, từ đăng nhập, quản lý chỉ số, duyệt chỉ số, nhập liệu theo kỳ đến khai thác thống kê và quản trị hệ thống.

## 2. Phạm vi áp dụng

Áp dụng cho người dùng, người duyệt, người nhập liệu khoa/phòng và quản trị viên sử dụng hệ thống QLCL tại Bệnh viện Thống Nhất.

## 3. Trách nhiệm

| Đối tượng | Trách nhiệm |
| --- | --- |
| Người dùng | Thực hiện đúng thao tác được phân quyền, nhập dữ liệu đầy đủ và chịu trách nhiệm về dữ liệu đã nhập. |
| Người duyệt | Kiểm tra nội dung chỉ số, duyệt hoặc từ chối và nêu rõ lý do khi từ chối. |
| Quản trị viên | Quản lý tài khoản, quyền, danh mục nền và hỗ trợ người dùng. |
| Bộ phận CNTT | Vận hành kỹ thuật, xử lý sự cố và phối hợp bảo trì hệ thống. |

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

### Mục đích và phạm vi

Tài liệu hướng dẫn người dùng thao tác trên hệ thống QLCL: đăng nhập, quản lý chỉ số chất lượng, thiết kế biểu mẫu, gửi duyệt, duyệt/từ chối, nhập dữ liệu theo kỳ, xem thống kê, xuất Excel và các chức năng quản trị được phân quyền.

Quyền hiển thị menu và quyền thực hiện từng thao tác phụ thuộc vào nhóm quyền/quyền chi tiết của tài khoản. Tài khoản có `adminType = 1` được hệ thống cho phép vượt qua kiểm tra quyền chi tiết.

### Đăng nhập, đăng xuất và đổi mật khẩu

#### Đăng nhập

1. Mở địa chỉ hệ thống do đơn vị quản trị cung cấp.
2. Nhập **Tên đăng nhập** và **Mật khẩu**.
3. Chọn **Đăng nhập**.
4. Nếu thông tin hợp lệ và tài khoản không bị khóa, hệ thống chuyển đến trang Dashboard.

Tài khoản bị khóa (`nd_block = 1`) không thể đăng nhập. Cơ chế khóa tạm theo số lần nhập sai hiện không được kích hoạt trong mã nguồn hiện tại, vì vậy việc bảo vệ tài khoản phụ thuộc chủ yếu vào trạng thái khóa do quản trị viên thiết lập.

#### Đăng xuất

Chọn chức năng **Đăng xuất** trên giao diện. Hệ thống xóa thông tin phiên đăng nhập, token phiên và chuyển về màn hình đăng nhập.

#### Đổi mật khẩu

1. Mở chức năng **Đổi mật khẩu**.
2. Nhập mật khẩu hiện tại.
3. Nhập mật khẩu mới và xác nhận lại.
4. Lưu thay đổi.

Mật khẩu mới phải trùng với ô xác nhận và không được để trống. Hệ thống hiện lưu mật khẩu bằng MD5; quản trị hệ thống nên có kế hoạch nâng cấp cơ chế băm mật khẩu theo tài liệu bảo trì.

### Menu và phân quyền

Menu được lấy từ danh mục chức năng trong cơ sở dữ liệu và chỉ hiển thị các mục phù hợp với quyền của tài khoản. Quyền có thể ở mức chức năng, ví dụ `chisochatluong`, hoặc mức thao tác, ví dụ `chisochatluong.duyet`.

Các nhóm chức năng chính gồm:

| Nhóm | Chức năng tiêu biểu |
| --- | --- |
| Chỉ số chất lượng | Danh sách chỉ số, tạo/sửa, gửi duyệt, duyệt, từ chối, xóa, tạo lại |
| Nhập liệu | Danh sách chỉ số được duyệt theo khoa/phòng, nhập phiếu theo kỳ |
| Chỉ số khoa | Xem dữ liệu, biểu đồ, danh sách phiếu, xóa phiếu theo quyền |
| Quản trị người dùng | Người dùng, nhóm quyền, phân quyền, khóa/mở khóa |
| Danh mục | Khoa/phòng, khối, chu kỳ, đơn vị tính, phạm vi, trạng thái, khía cạnh/thành tố |
| Hệ thống | Nhật ký, sao lưu/phục hồi nếu được cấp quyền |

### Quản lý chỉ số chất lượng

#### Trạng thái của chỉ số

| Mã | Trạng thái | Ý nghĩa |
| --- | --- | --- |
| 0 | Nháp | Người tạo đang soạn, có thể chỉnh sửa |
| 1 | Chờ duyệt | Đã gửi cho người có quyền duyệt |
| 2 | Đã duyệt | Có thể phân phối cho khoa/phòng nhập liệu |
| 3 | Từ chối | Bị từ chối và có thể tạo lại thành bản nháp mới |

Luồng chuẩn: **Nháp -> Chờ duyệt -> Đã duyệt**. Từ trạng thái **Chờ duyệt**, người duyệt có thể chuyển sang **Từ chối**. Chỉ số bị từ chối có thể dùng chức năng **Tạo lại**; hệ thống sao chép cấu hình sang một chỉ số mới ở trạng thái Nháp và lưu liên kết về chỉ số gốc.

#### Tạo chỉ số mới

1. Mở **Chỉ số chất lượng**.
2. Chọn **Thêm mới**.
3. Nhập tối thiểu **Tên chỉ số**.
4. Chọn các thuộc tính: khía cạnh, thành tố, nhóm chỉ số, phạm vi, mục tiêu, ngưỡng cảnh báo, đơn vị tính, chu kỳ.
5. Chọn khoa/phòng áp dụng. Với phạm vi toàn bệnh viện, hệ thống tự gán danh sách tất cả khoa/phòng.
6. Nhập định nghĩa, cách thu thập, tên tử số/mẫu số nếu sử dụng.
7. Thiết kế biểu mẫu nhập liệu.
8. Lưu. Chỉ số mới ở trạng thái **Nháp**.

#### Thiết kế biểu mẫu

Biểu mẫu được lưu dưới dạng JSON, gồm danh sách câu hỏi. Các loại câu hỏi đang được xử lý trong mã nguồn gồm:

| Loại | Cách sử dụng |
| --- | --- |
| `category` | Tiêu đề nhóm lớn, không nhập dữ liệu |
| `subcategory` | Tiêu đề nhóm con, không nhập dữ liệu |
| `short_text` / văn bản | Nhập nội dung tự do |
| `number` | Nhập số; có thể giới hạn số chữ số |
| `percentage` | Nhập tỷ lệ từ 0 đến 100 |
| `score` | Nhập điểm từ 1 đến 10 |
| `radio` | Chọn một phương án, có thể gán điểm |
| `select` | Chọn một phương án, có thể gán điểm |
| `satisfaction` | Thang hài lòng, mỗi phương án có điểm |
| `checkbox` | Chọn nhiều phương án; điểm bằng tổng điểm các lựa chọn |

Có thể đánh dấu câu hỏi **bắt buộc**. Khi nhập dữ liệu, hệ thống kiểm tra câu trả lời theo cấu hình biểu mẫu trước khi lưu.

#### Sửa chỉ số

Mở chỉ số cần sửa, cập nhật thông tin và lưu. Khi sửa cấu hình biểu mẫu, cần lưu ý các phiếu đã nhập trước đó vẫn chứa dữ liệu theo mã câu hỏi cũ; thay đổi ID hoặc cấu trúc câu hỏi có thể làm việc đối chiếu dữ liệu lịch sử khó hơn.

#### Gửi duyệt

Từ chỉ số Nháp, chọn **Gửi duyệt**. Hệ thống chuyển trạng thái sang **Chờ duyệt** và ghi thời gian gửi.

#### Duyệt hoặc từ chối

Người có quyền duyệt mở chỉ số ở trạng thái Chờ duyệt:

- Chọn **Duyệt** để chuyển sang **Đã duyệt**, ghi người duyệt và thời gian duyệt.
- Chọn **Từ chối** để chuyển sang **Từ chối** và lưu lý do từ chối.

Người duyệt có thể thấy chỉ số do mình tạo ở mọi trạng thái; với chỉ số của người khác, danh sách chỉ hiển thị các trạng thái Chờ duyệt, Đã duyệt hoặc Từ chối.

#### Tạo lại chỉ số bị từ chối

Người tạo mở chỉ số bị từ chối của chính mình và chọn **Tạo lại**. Hệ thống tạo bản sao mới ở trạng thái Nháp, bỏ thông tin gửi/duyệt/từ chối và ghi mã chỉ số gốc.

#### Xóa chỉ số

Chức năng xóa thực hiện theo mã chỉ số. Dữ liệu `ct_chiso` liên quan được cấu hình khóa ngoại `ON DELETE CASCADE`, vì vậy xóa chỉ số có thể xóa cả các phiếu nhập của chỉ số đó. Chỉ thực hiện khi đã xác nhận không cần dữ liệu lịch sử.

### Nhập dữ liệu chỉ số

#### Điều kiện hiển thị chỉ số

Màn hình **Nhập liệu** chỉ lấy các chỉ số:

- đã ở trạng thái **Đã duyệt**;
- được gán cho khoa/phòng của người dùng;
- có cấu hình chu kỳ và biểu mẫu hợp lệ.

#### Xác định kỳ nhập

Chu kỳ được tính từ tháng tạo chỉ số. Số kỳ phải trong khoảng 1 đến 12 và chia hết cho 12. Ví dụ: chu kỳ 4 kỳ/năm tương ứng mỗi kỳ 3 tháng.

Người dùng được nhập kỳ hiện tại hoặc kỳ trước trong cùng năm/chu kỳ đang xét. Hệ thống không cho nhập kỳ tương lai.

#### Nhập và lưu phiếu

1. Chọn chỉ số cần nhập.
2. Chọn năm/kỳ hợp lệ nếu giao diện cho phép.
3. Điền các câu hỏi trong biểu mẫu.
4. Chọn **Lưu**.
5. Nếu dữ liệu hợp lệ, hệ thống lưu một bản ghi vào `ct_chiso` gồm chỉ số, người nhập, khoa/phòng, năm, kỳ và nội dung JSON.

Hệ thống hiện cho phép nhiều phiếu trong cùng một chỉ số/khoa-phòng/năm/kỳ. Đây phù hợp với các biểu mẫu khảo sát có nhiều lượt trả lời; đơn vị sử dụng cần xác định rõ quy trình nghiệp vụ nếu chỉ mong muốn một phiếu tổng hợp cho mỗi kỳ.

#### Quy tắc tính điểm

Hệ thống tính `tong_diem`, `diem_toi_da` và `ty_le_phan_tram`:

- `radio`, `select`, `satisfaction`: dùng điểm của phương án đã chọn.
- `checkbox`: cộng điểm các phương án được chọn.
- `score`: lấy giá trị số, tối đa 10 điểm.
- câu hỏi không có cơ chế điểm không làm tăng điểm tối đa.
- tỷ lệ phần trăm = tổng điểm / điểm tối đa x 100, làm tròn 2 chữ số.

### Theo dõi chỉ số khoa và thống kê

Các màn hình chỉ số/khoa cung cấp:

- danh sách chỉ số và tình trạng nhập;
- trung bình theo năm/kỳ;
- thống kê theo tháng dựa trên thời điểm tạo phiếu;
- trung bình theo từng câu hỏi có chấm điểm;
- danh sách phiếu chi tiết, người nhập, khoa/phòng và tỷ lệ phần trăm.

Khi xem dữ liệu chỉ số từ màn hình quản lý chỉ số, hệ thống kiểm tra lại quyền truy cập chỉ số trước khi trả dữ liệu thống kê.

### Xuất Excel

Trong màn hình chỉ số, chọn chức năng **Xuất Excel**. Hệ thống sinh tệp `.xls` dạng HTML có thông tin chỉ số và thống kê câu hỏi, sau khi kiểm tra quyền xem chỉ số.

### Quản trị người dùng và nhóm quyền

#### Người dùng

Người có quyền quản trị có thể:

- xem danh sách người dùng;
- thêm/sửa thông tin tài khoản;
- gán khoa/phòng;
- gán nhóm quyền/quyền chi tiết;
- đổi mật khẩu khi sửa người dùng;
- khóa hoặc mở khóa tài khoản;
- xóa người dùng khi được phân quyền.

#### Nhóm quyền

Nhóm quyền lưu tập quyền dưới dạng chuỗi các mã chức năng. Quản trị viên có thể tạo/sửa nhóm quyền và chọn các chức năng/thao tác thành viên.

### Quản lý danh mục

Tùy quyền, người quản trị có thể quản lý khoa/phòng, khối, chu kỳ, đơn vị tính, khía cạnh/thành tố, phạm vi và trạng thái. Các danh mục này được dùng trực tiếp khi cấu hình chỉ số nên cần hạn chế xóa các bản ghi đã được tham chiếu.

### Nhật ký và sao lưu/phục hồi

Màn hình **Log** dùng để tra cứu nhật ký thao tác của hệ thống. Chức năng **Backup/Restore** nhận tệp SQL và có giới hạn kích thước theo cấu hình. Thành phần sao lưu/phục hồi hiện còn mã PHP/MySQL kiểu cũ, vì vậy cần kiểm thử trên môi trường thử nghiệm trước khi dùng cho dữ liệu thật.

### Các lưu ý vận hành

- Không dùng chung tài khoản giữa nhiều người để bảo đảm truy vết người nhập/người duyệt.
- Không xóa chỉ số đã có dữ liệu nếu chưa sao lưu và xác nhận ảnh hưởng.
- Khi thay đổi biểu mẫu của chỉ số đã có phiếu, giữ ổn định ID câu hỏi nếu cần so sánh lịch sử.
- Nếu menu/chức năng không xuất hiện, kiểm tra nhóm quyền và chuỗi quyền của tài khoản trước khi kết luận lỗi giao diện.
- Nếu chỉ số không xuất hiện ở màn hình Nhập liệu, kiểm tra trạng thái Đã duyệt, danh sách khoa/phòng được gán và cấu hình chu kỳ.

### Quy trình sử dụng theo từng vai trò

#### Người tạo chỉ số

Quy trình chuẩn của người tạo chỉ số gồm các bước sau:

1. Mở phân hệ **Chỉ số chất lượng** và chọn tạo mới.
2. Khai báo tên chỉ số, nhóm/phân loại, đơn vị tính, chu kỳ, mục tiêu, ngưỡng cảnh báo và phạm vi áp dụng.
3. Chọn khoa/phòng áp dụng. Nếu phạm vi là toàn bệnh viện, hệ thống chuẩn hóa danh sách khoa/phòng trước khi lưu.
4. Khai báo định nghĩa, cách thu thập dữ liệu, tử số/mẫu số hoặc công thức nếu chỉ số có sử dụng.
5. Thiết kế biểu mẫu nhập liệu. Mỗi câu hỏi nên có ID ổn định để giữ khả năng đọc dữ liệu lịch sử.
6. Lưu nháp và kiểm tra lại toàn bộ cấu hình.
7. Gửi duyệt. Sau khi gửi, chỉ số chuyển sang trạng thái **Chờ duyệt**.
8. Theo dõi kết quả duyệt. Nếu bị từ chối, xem lý do và sử dụng chức năng tạo lại để hình thành bản nháp mới.

Điểm cần lưu ý: chỉ số mới được lưu ở trạng thái nháp; người dùng khác không nên sử dụng chỉ số để nhập liệu trước khi chỉ số được duyệt.

#### Người duyệt chỉ số

Người duyệt truy cập danh sách chỉ số chờ duyệt, kiểm tra nội dung chỉ số, phạm vi khoa/phòng, chu kỳ, biểu mẫu, mục tiêu và công thức. Khi chọn **Duyệt**, hệ thống cập nhật trạng thái sang `2`, lưu người duyệt và thời gian duyệt. Khi chọn **Từ chối**, người duyệt cần nhập lý do để người tạo biết nội dung cần điều chỉnh.

#### Người nhập liệu khoa/phòng

Người nhập liệu chỉ nhìn thấy các chỉ số thỏa đồng thời các điều kiện: đã duyệt, được gán cho khoa/phòng của tài khoản và có cấu hình chu kỳ hợp lệ. Khi mở một chỉ số, hệ thống trả về biểu mẫu, kỳ hiện tại và các phiếu đã nhập trong năm. Người dùng chọn kỳ hợp lệ, nhập đầy đủ các trường bắt buộc và lưu phiếu.

Hệ thống chặn kỳ tương lai. Với kỳ hiện tại hoặc kỳ cũ còn hợp lệ, một lần lưu tạo một bản ghi mới trong `ct_chiso`; source hiện tại không áp đặt ràng buộc duy nhất cho một khoa/phòng trong cùng năm/kỳ.

#### Quản trị viên

Quản trị viên duy trì người dùng, khoa/phòng, nhóm quyền và các danh mục nền. Khi tạo tài khoản cần gán đúng khoa/phòng vì `MaKhoaPhong` được dùng để xác định phạm vi nhập liệu. Khi thay đổi nhóm quyền cần kiểm tra lại menu và các thao tác chi tiết như duyệt, xóa, khóa hoặc phân quyền.

### Thông báo và tình huống thường gặp

| Tình huống | Nguyên nhân cần kiểm tra | Cách xử lý |
| --- | --- | --- |
| Không đăng nhập được | Sai mật khẩu, tài khoản bị khóa, kết nối DB lỗi | Kiểm tra tài khoản, `nd_block`, cấu hình DB |
| Không thấy menu | Thiếu quyền hoặc cấu hình `chucnang` | Kiểm tra nhóm quyền và session `quyen` |
| Không thấy chỉ số ở Nhập liệu | Chưa duyệt hoặc chưa gán khoa/phòng | Kiểm tra `trang_thai = 2` và trường `phong` |
| Không lưu được phiếu | Câu bắt buộc thiếu, giá trị ngoài miền, kỳ tương lai | Kiểm tra biểu mẫu và dữ liệu nhập |
| Thống kê khác dự kiến | Có nhiều phiếu trong cùng kỳ hoặc cấu hình biểu mẫu đã thay đổi | Kiểm tra `ct_chiso`, ID câu hỏi và dữ liệu JSON |
| Xuất Excel không có dữ liệu | Không có quyền xem hoặc chưa có phiếu phù hợp | Kiểm tra quyền và danh sách phiếu |

### Quy tắc dữ liệu người dùng cần biết

- Trạng thái chỉ số gồm: `0` Nháp, `1` Chờ duyệt, `2` Đã duyệt, `3` Từ chối.
- Chỉ số phải ở trạng thái **Đã duyệt** mới tham gia luồng nhập liệu khoa/phòng.
- Phần trăm chỉ nhận giá trị từ 0 đến 100; điểm số dạng `score` nhận từ 1 đến 10.
- Lựa chọn radio/select/satisfaction phải thuộc danh sách phương án đã cấu hình.
- Câu hỏi bắt buộc không được để trống.
- Kỳ nhập phụ thuộc chu kỳ chỉ số và thời điểm tạo chỉ số; cấu hình chu kỳ phải chia hết 12 tháng.
- Một chỉ số bị xóa có thể kéo theo việc xóa các phiếu `ct_chiso` liên quan do khóa ngoại cascade, vì vậy cần sao lưu trước khi xóa dữ liệu đã sử dụng.

## 6. Lưu trữ hồ sơ

| STT | Tên hồ sơ | Mã hiệu | Nơi lưu | Thời gian lưu |
| --- | --- | --- | --- | --- |
| 1 | Tài liệu hướng dẫn sử dụng hệ thống qlcl | Chưa cấp | Thư mục `docs/QL_CSCL` và kho tài liệu nội bộ | Theo quy định quản lý tài liệu của Bệnh viện |
| 2 | Source code liên quan | Theo repository | Máy chủ/kho source được quản lý | Theo vòng đời hệ thống |
| 3 | CSDL và bản sao lưu liên quan | Theo bản backup | Vị trí backup được quản lý | Theo chính sách sao lưu hiện hành |

## 7. Tài liệu liên quan

| STT | Mã tài liệu | Tên tài liệu |
| --- | --- | --- |
| 1 | QTQL.BVTN.09 | Quy trình yêu cầu xây dựng phần mềm |
| 2 | Chưa cấp | Các tài liệu còn lại trong bộ tài liệu QLCL |
| 3 | Source | `conf/config.php`, `index.php`, `web_src/`, `www/`, `js/`, `sql/` |
