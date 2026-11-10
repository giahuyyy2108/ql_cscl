# SỔ TAY THỰC HÀNH HỆ THỐNG QUẢN LÝ CHẤT LƯỢNG (QLCL)

**Đơn vị sử dụng:** Bệnh viện Thống Nhất  
**Đối tượng:** Người tạo chỉ số, người duyệt, người nhập liệu khoa/phòng và quản trị viên  
**Phiên bản:** 1.1 — **Ngày cập nhật:** 09/11/2026  
**Căn cứ:** Mã nguồn tại `C:\xampp\htdocs\ql_cscl`, bao gồm luồng kiểm tra và bắt buộc đổi mật khẩu yếu tại trang đăng nhập.  
**Trạng thái:** Đã đối chiếu mã nguồn và kiểm tra trang đăng nhập phản hồi trên môi trường cục bộ; chưa xác nhận bằng tài khoản và dữ liệu vận hành thực tế.

> **Cách dùng sổ tay:** Tìm đúng vai trò của mình, làm theo các bước trên giao diện. Tên mục menu có thể khác đôi chút theo cấu hình và quyền được cấp. Tài liệu này không thay thế quy định phê duyệt, lưu trữ số liệu của đơn vị.

## 1. Nhìn nhanh: bắt đầu từ đâu?

- **Người tạo chỉ số:** Vào **Chỉ số chất lượng** → **Thêm chỉ số** → khai báo thông tin và thiết kế biểu mẫu → **Lưu** → **Gửi**.
- **Người duyệt:** Vào **Chỉ số chất lượng** → chọn chỉ số **Chờ duyệt** → **Xem** → **Duyệt** hoặc **Từ chối** (ghi lý do).
- **Người nhập số liệu:** Vào **Nhập liệu** → tìm chỉ số **Chưa nhập** → **Nhập biểu mẫu** → chọn kỳ → hoàn thành biểu mẫu → **Lưu biểu mẫu**.
- **Người xem báo cáo:** Vào **Chỉ số khoa** hoặc **Chỉ số chất lượng** → **Xem** → xem các thẻ **Biểu đồ**, **Thống kê** → xuất báo cáo khi cần.
- **Quản trị viên:** Quản lý **Người dùng**, **Nhóm quyền** và các **Danh mục** theo quyền được cấp.

## 2. Truy cập, đăng nhập và tài khoản

### 2.1. Đăng nhập

1. Mở trình duyệt và nhập **địa chỉ hệ thống do bộ phận CNTT cung cấp**. Không dùng một địa chỉ mẫu chưa được xác nhận.
2. Tại màn hình **Đăng nhập**, điền **Tài khoản** và **Mật khẩu**.
3. Chọn **Đăng nhập**.
4. Nếu tài khoản hợp lệ và mật khẩu đủ mạnh, hệ thống hiển thị giao diện làm việc với thanh menu bên trái.
5. Nếu hệ thống hiển thị **Đổi mật khẩu** ngay trên trang đăng nhập, thực hiện theo mục 2.2. Người dùng chưa được vào giao diện làm việc cho đến khi đổi mật khẩu thành công và đăng nhập lại.
6. Nếu thông tin không hợp lệ, kiểm tra tên đăng nhập/mật khẩu và liên hệ quản trị viên để kiểm tra tình trạng khóa tài khoản. Không gửi mật khẩu qua tin nhắn nhóm.

### 2.2. Đổi mật khẩu yếu ngay tại trang đăng nhập

Mật khẩu mới phải đáp ứng đủ bốn điều kiện:

- dài hơn 8 ký tự;
- có chữ in hoa;
- có chữ số;
- có ký tự đặc biệt.

Thao tác thực hiện:

1. Tại màn hình **Đổi mật khẩu**, nhập **Mật khẩu mới**.
2. Quan sát thanh độ mạnh. Danh sách điều kiện chuyển sang trạng thái đạt ngay trong lúc nhập; tiếp tục điều chỉnh đến khi mức hiển thị là **Mạnh**.
3. Nhập lại tại ô **Xác nhận mật khẩu mới**. Chỉ tiếp tục khi màn hình báo **Mật khẩu xác nhận đã khớp**.
4. Chọn **Đổi mật khẩu**.
5. Sau thông báo thành công, hệ thống quay lại màn hình **Đăng nhập** và điền sẵn tên tài khoản.
6. Nhập mật khẩu mới và chọn **Đăng nhập** để vào hệ thống.

Nếu màn hình báo phiên đổi mật khẩu đã hết hạn, đăng nhập lại bằng mật khẩu hiện tại để bắt đầu lại. Không đóng trình duyệt hoặc dùng nút quay lại trong lúc đang đổi mật khẩu.

### 2.3. Chủ động đổi mật khẩu sau khi đăng nhập

1. Nhấp **tên tài khoản/ảnh đại diện** ở góc trên bên phải.
2. Chọn **Đổi mật khẩu**; nhập **Mật khẩu cũ**, **Mật khẩu mới** và **Xác nhận lại mật khẩu**.
3. Bảo đảm mật khẩu mới đáp ứng đủ bốn điều kiện tại mục 2.2 và hai ô mật khẩu mới trùng nhau.
4. Chọn **Thay đổi mật khẩu**. Nếu có lỗi, đọc thông báo trên màn hình rồi nhập lại.

### 2.4. Đăng xuất

Kết thúc phiên làm việc, mở menu tài khoản ở góc trên bên phải và chọn **Thoát khỏi hệ thống**. Không chỉ đóng thẻ trình duyệt khi sử dụng máy tính dùng chung.

**Lưu ý:** Menu và nút thao tác chỉ xuất hiện khi tài khoản được phân quyền phù hợp. Không tự suy đoán ứng dụng bị lỗi nếu không thấy một chức năng.

## 3. Làm quen giao diện chính

- **Menu bên trái:** Chuyển giữa các phân hệ. Danh sách mục phụ thuộc nhóm quyền của tài khoản.
- **Tên người dùng (góc trên):** Mở thao tác đổi mật khẩu và thoát hệ thống.
- **Biểu tượng chuông:** Hiển thị danh sách **Nhắc nhập liệu** đối với chỉ số chưa nhập kỳ hiện tại. Nhấp thông báo để đi đến chỉ số tương ứng ở màn hình Nhập liệu.
- **Bảng danh sách:** Có ô tìm kiếm và cột **Thao tác**. Một số nút chỉ có biểu tượng; di chuột để xem nhãn **Gửi**, **Xem**, **Duyệt**, **Sửa**, **Xóa**, **Từ chối** hoặc **Tạo lại**.

## 4. Người tạo chỉ số: tạo mới và gửi duyệt

### 4.1. Tạo chỉ số

1. Mở **Chỉ số chất lượng**. Màn hình có tiêu đề **Danh sách các chỉ tiêu**.
2. Chọn **Thêm chỉ số** ở phía trên bảng. Hệ thống mở cửa sổ **Thêm Chỉ tiêu**.
3. Nhập **Tên chỉ số** (trường bắt buộc). Khai báo các thông tin phù hợp: **Khía cạnh**, **Thành tố**, **Nhóm chỉ số**, **Phạm vi**, **Mục tiêu**, **Ngưỡng cảnh báo**, **Đơn vị tính**, **Chu kỳ**, **Định nghĩa**, **Phương pháp thu thập**, **Tên tử số**, **Tên mẫu số**.
4. Chọn khoa/phòng áp dụng bằng nút mở cửa sổ **Chọn Khoa/Phòng**, chọn theo khối hoặc từng khoa/phòng rồi nhấn **Chọn**. Khi chọn phạm vi **toàn bệnh viện**, hệ thống tự đánh dấu các khoa/phòng và có thể ẩn nút chọn thủ công.
5. Tại **Thiết kế biểu mẫu khảo sát**, chọn **Thêm câu hỏi** và xây dựng câu hỏi cùng các phương án trả lời. Xem mục 4.2.
6. Kiểm tra thông tin, chọn **Lưu**. Chỉ số mới được lưu ở trạng thái **Nháp**.

**Ví dụ minh họa:** “Tỷ lệ tuân thủ quy trình A” có thể khai báo chu kỳ **4 kỳ/năm**, chọn các khoa/phòng áp dụng, thêm câu hỏi dạng lựa chọn và câu hỏi ghi chú. Đây chỉ là dữ liệu minh họa, không phải cấu hình nghiệp vụ bắt buộc.

### 4.2. Thiết kế biểu mẫu khảo sát

1. Chọn **Thêm câu hỏi**, nhập nội dung và chọn loại câu hỏi phù hợp.
2. Với câu hỏi lựa chọn, khai báo danh sách phương án trả lời; nếu có chấm điểm, gán điểm theo nội dung nghiệp vụ đã được duyệt.
3. Có thể dùng **Nhóm** và **Nhóm con** để chia biểu mẫu thành từng phần; đây là tiêu đề, không phải câu trả lời.
4. Kiểm tra thứ tự câu hỏi, yêu cầu nhập dữ liệu và nội dung phương án trước khi **Lưu** chỉ số.

Các kiểu dữ liệu đã thấy trong mã nguồn: **văn bản ngắn/dài**, **số**, **phần trăm**, **điểm**, **chọn một** (radio/select), **chọn nhiều** (checkbox), **mức độ hài lòng**, **nhóm** và **nhóm con**. Một số phương án radio cho phép người trả lời **nhập thêm nội dung**.

**Cảnh báo:** Màn hình nhập liệu hiện yêu cầu hoàn thành **toàn bộ câu hỏi có ô trả lời** trước khi lưu, kể cả trường hợp người thiết kế kỳ vọng một câu hỏi không bắt buộc. Hãy kiểm thử biểu mẫu bằng tài khoản nhập liệu trước khi sử dụng chính thức. Không đổi tùy tiện mã/ID câu hỏi sau khi đã có phiếu nhập.

### 4.3. Kiểm tra, sửa, xóa và gửi

1. Tại danh sách chỉ số, dùng **Lọc theo tháng tạo chỉ tiêu** nếu cần, hoặc chọn **Xóa lọc** để xem lại toàn bộ.
2. Chọn **Xem** để xem thông tin của chỉ số vừa tạo.
3. Nếu còn trạng thái **Nháp**, chọn **Sửa** để thay đổi thông tin; lưu lại rồi xem lại nội dung.
4. Khi hoàn tất, chọn nút **Gửi** (biểu tượng máy bay giấy). Hệ thống chuyển chỉ số sang **Chờ duyệt**.
5. Chỉ dùng **Xóa** khi được phép và đã kiểm tra hậu quả. Không xóa chỉ số đang phục vụ báo cáo; thao tác xóa có thể ảnh hưởng dữ liệu phiếu liên quan.

**Vòng đời chỉ số:** **Nháp (0)** → **Chờ duyệt (1)** → **Đã duyệt (2)**. Người duyệt có thể đưa **Chờ duyệt (1)** sang **Từ chối (3)**. Chỉ số **Đã duyệt** mới được dùng để nhập số liệu ở khoa/phòng.

### 4.4. Khi chỉ số bị từ chối

1. Mở chỉ số trạng thái **Từ chối** và chọn **Xem** để đọc **Lý do từ chối**.
2. Nhấn **Tạo lại** trên chỉ số bị từ chối (khi nút này xuất hiện theo quyền).
3. Hệ thống tạo một **chỉ số mới ở trạng thái Nháp**, sao chép thông tin của bản cũ. Sửa bản nháp mới, kiểm tra biểu mẫu rồi gửi duyệt lại.
4. Không hiểu **Tạo lại** là sửa trực tiếp chỉ số đã bị từ chối.

## 5. Người duyệt: duyệt hoặc từ chối chỉ số

1. Mở **Chỉ số chất lượng** và tìm chỉ số có tình trạng **Chờ duyệt**.
2. Chọn **Xem** và đối chiếu: tên/định nghĩa, phương pháp thu thập, khoa/phòng áp dụng, chu kỳ, mục tiêu, ngưỡng cảnh báo, câu hỏi và thang điểm.
3. Nếu đạt yêu cầu, nhấn **Duyệt**. Sau khi thành công, trạng thái chuyển sang **Đã duyệt**.
4. Nếu cần chỉnh sửa, nhấn **Từ chối**; điền **Lý do từ chối** rõ ràng rồi xác nhận.

**Ví dụ lý do từ chối hữu ích:** “Biểu mẫu chưa nêu tiêu chí chấm điểm ở câu 3; bổ sung trước khi gửi duyệt lại.” Tránh ghi lý do quá chung chung như “Sai”.

## 6. Người nhập liệu: điền biểu mẫu theo kỳ

### 6.1. Xác định việc cần nhập

1. Vào **Nhập liệu**. Trang có tiêu đề **Danh sách chỉ tiêu đã duyệt**.
2. Xem ba ô tổng quan: **Tổng chỉ tiêu**, **Đã nhập kỳ hiện tại**, **Chưa nhập kỳ hiện tại**.
3. Trong bảng, tìm chỉ số có nhãn **Chưa nhập**. Chọn **Xem** để kiểm tra thông tin; chọn **Nhập biểu mẫu** để tạo phiếu mới.
4. Nếu không thấy chỉ số, xem mục 10: chỉ số có thể chưa được duyệt hoặc chưa gán đúng khoa/phòng cho tài khoản.

### 6.2. Chọn kỳ báo cáo

1. Cửa sổ **Biểu mẫu nhập liệu chỉ số chất lượng** có phần **Thông tin kỳ báo cáo**.
2. Mở danh sách kỳ (trên giao diện nhãn trường hiện có thể là **Chọn quý**) và chọn kỳ cần nhập.
3. Đọc dòng **Kỳ nhập hiện tại** và **chu kỳ bắt đầu từ tháng/năm**. Danh sách kỳ và các tên hiển thị thay đổi theo cấu hình: **Tháng**, **Quý**, **6 tháng**, **Năm**.
4. Chỉ nhập kỳ đã mở; hệ thống không cho chọn kỳ tương lai. Có thể nhập lại một kỳ trước trong phạm vi năm chu kỳ đang xét.

**Quan trọng:** Kỳ được tính theo **tháng tạo chỉ số**, không mặc nhiên trùng quý/năm dương lịch. Ví dụ chỉ số có **4 kỳ/năm**, tạo vào **tháng 3**, thì **kỳ 1** bắt đầu tháng 3, **kỳ 2** bắt đầu tháng 6. Khi đối chiếu số liệu, hãy nhìn mốc bắt đầu hiển thị trên phiếu, không chỉ đọc chữ “Quý 1”.

### 6.3. Nhập nội dung và lưu phiếu

1. Đọc phần **Thông tin chỉ số** (mã, tên, chu kỳ, mục tiêu, ngưỡng cảnh báo).
2. Trong **Nội dung biểu mẫu**, trả lời lần lượt các câu hỏi. Các dấu **\*** màu đỏ thể hiện dữ liệu cần nhập trên giao diện hiện tại.
3. Với câu hỏi **số/phần trăm/điểm**, nhập đúng giới hạn; tỷ lệ phần trăm trong khoảng **0–100**, điểm dạng `score` trong khoảng **1–10**. Với lựa chọn “khác/nhập thêm”, điền phần nội dung bổ sung nếu được yêu cầu.
4. Kiểm tra dữ liệu, chọn **Lưu biểu mẫu**. Đọc thông báo **Thành công** trước khi đóng cửa sổ.
5. Quay lại danh sách; nhãn trạng thái kỳ hiện tại được cập nhật thành **Đã nhập** nếu kỳ hiện tại đã có phiếu.

**Cần biết:** Mỗi lần chọn **Lưu biểu mẫu** thành công có thể tạo **một phiếu mới**, kể cả khi kỳ này đã có dữ liệu; đó không phải thao tác ghi đè phiếu cũ. Trước khi nhấn lưu thêm lần nữa, xác nhận có cần thêm một lượt khảo sát/bản ghi hay không.

### 6.4. Thông báo nhắc nhập liệu

1. Nhấp **biểu tượng chuông** ở thanh trên cùng.
2. Đọc danh sách **Nhắc nhập liệu** và số chỉ số còn thiếu ở kỳ hiện tại.
3. Nhấp một mục thông báo để mở đúng chỉ số trong màn hình **Nhập liệu**.
4. Sau khi lưu phiếu thành công, danh sách nhắc nhập được tải lại. Nếu chưa thay đổi, làm mới trang và kiểm tra đúng kỳ đã nhập.

## 7. Xem kết quả, biểu đồ và xuất báo cáo

### 7.1. Trong “Chỉ số chất lượng”

1. Chọn **Xem** tại một chỉ số có dữ liệu.
2. Chuyển giữa các thẻ **Thông tin**, **Biểu đồ**, **Thống kê**, **Theo tháng**.
3. Tại phần thống kê theo câu hỏi, chọn **Kiểu thống kê** (nếu có); tại **Theo tháng**, chọn **tháng/năm** hoặc **Xóa lọc**.
4. Chọn **Xuất Excel** trong cửa sổ xem chỉ số để tải báo cáo `.xls` (định dạng HTML tương thích với Excel).

### 7.2. Trong “Chỉ số khoa”

1. Mở **Chỉ số khoa** (tiêu đề **Danh sách chỉ số đã được phê duyệt theo khoa phòng**).
2. Dùng bộ lọc **Khối** và **Khoa/Phòng**, sau đó chọn **Xem** tại dòng cần theo dõi.
3. Xem các thẻ **Thông tin**, **Biểu đồ**, **Thống kê**. Tại **Thống kê** có danh sách phiếu, nút **Xem câu trả lời** và **Xóa phiếu** theo quyền.
4. Chọn nút **Xuất thống kê** để tải tệp `.xlsx` thống kê theo khoa/phòng.

**Lưu ý với Xóa phiếu:** Hệ thống cảnh báo dữ liệu đã xóa không thể khôi phục từ giao diện. Chỉ xóa khi xác nhận phiếu nhập sai và đã thực hiện quy trình kiểm soát dữ liệu của đơn vị.

### 7.3. Trong “Nhập liệu”

1. Tại dòng chỉ số, chọn **Xuất thống kê** để xuất dữ liệu phiếu của khoa/phòng tài khoản được phép truy cập.
2. Tệp tải về có đuôi `.xlsx`; tên tệp gồm thông tin chỉ số và khoa/phòng.

**Phân biệt:** **Xuất Excel** từ Chỉ số chất lượng tạo `.xls`; **Xuất thống kê** từ Nhập liệu hoặc Chỉ số khoa tạo `.xlsx`. Nếu Excel hiển thị cảnh báo định dạng đối với `.xls`, xác minh tệp được tải từ hệ thống trước khi mở.

## 8. Quản trị viên: người dùng, quyền và danh mục

### 8.1. Tạo và quản lý người dùng

1. Vào mục **Người dùng** (màn hình **Danh sách người dùng**).
2. Tìm theo **Họ và Tên** hoặc **Tên đăng nhập** nếu cần.
3. Chọn **Thêm mới**; nhập họ tên, tên đăng nhập và mật khẩu, chọn **Nhóm quyền**, **Khoa/Phòng** rồi lưu.
4. Dùng các nút trên dòng tài khoản để **Sửa**, **Khóa/Mở khóa**, **Phân quyền** hoặc **Xóa** theo chính sách quản trị.
5. Sau khi phân quyền hoặc chuyển khoa/phòng, yêu cầu người dùng đăng nhập lại và kiểm tra đúng chức năng/đúng danh sách chỉ số.

**Không nên:** Gán quyền quản trị rộng cho người chỉ có nhiệm vụ nhập số liệu; sử dụng chung tài khoản giữa nhiều nhân sự.

### 8.2. Nhóm quyền và danh mục nền

- **Nhóm quyền:** Tạo/sửa nhóm và các quyền thao tác; người dùng chỉ nhìn thấy các chức năng được cấp.
- **Danh mục:** Duy trì khoa/phòng, khối, chu kỳ, đơn vị tính, khía cạnh/thành tố, phạm vi và trạng thái.
- Trước khi xóa hoặc đổi danh mục đang dùng, kiểm tra các chỉ số và biểu mẫu tham chiếu. Thực hiện theo quy trình thay đổi dữ liệu, không xóa thử trên hệ thống thật.
- **Nhật ký**, **Sao lưu/Phục hồi** dành cho người có nhiệm vụ/quyền tương ứng. Tác vụ phục hồi dữ liệu cần được CNTT kiểm thử trên môi trường riêng trước khi áp dụng chính thức.

## 9. Checklist kết thúc công việc

### 9.1. Người tạo chỉ số

- Đã khai báo đúng **Tên**, **Phạm vi**, **Khoa/Phòng** và **Chu kỳ**.
- Đã kiểm tra câu hỏi, phương án, thang điểm, dữ liệu yêu cầu nhập.
- Đã **Lưu** nháp, **Xem** kiểm tra và **Gửi**; tình trạng là **Chờ duyệt**.

### 9.2. Người duyệt

- Đã đọc thông tin và biểu mẫu; xác nhận chỉ số đúng đối tượng áp dụng.
- Đã **Duyệt**, hoặc **Từ chối** kèm lý do cụ thể.

### 9.3. Người nhập liệu

- Đã chọn đúng chỉ số và **kỳ theo mốc bắt đầu**.
- Đã hoàn thành tất cả câu trả lời, nhấn **Lưu biểu mẫu** và thấy thông báo thành công.
- Đã kiểm tra nhãn **Đã nhập** và chỉ lưu thêm phiếu khi thực sự có thêm lượt thu thập dữ liệu.

## 10. Tình huống thường gặp và cách xử lý

**Không đăng nhập được.** Kiểm tra tài khoản/mật khẩu, trạng thái khóa tài khoản; nếu nhiều người đồng thời gặp lỗi, báo CNTT kiểm tra hệ thống.

**Đăng nhập đúng nhưng hệ thống yêu cầu đổi mật khẩu.** Đây là bước bảo mật bắt buộc khi mật khẩu hiện tại thiếu một trong bốn điều kiện. Đổi mật khẩu ngay trên trang đăng nhập, sau đó đăng nhập lại bằng mật khẩu mới.

**Không thể chọn Đổi mật khẩu hoặc trình duyệt báo mật khẩu không hợp lệ.** Kiểm tra mật khẩu có tối thiểu 9 ký tự, chữ in hoa, chữ số và ký tự đặc biệt; đồng thời bảo đảm hai ô mật khẩu mới trùng nhau.

**Phiên đổi mật khẩu hết hạn.** Quay lại trang đăng nhập, nhập lại tài khoản và mật khẩu hiện tại để hệ thống tạo phiên đổi mật khẩu mới.

**Không thấy menu hoặc nút Duyệt/Sửa.** Kiểm tra nhóm quyền và trạng thái chỉ số. Ví dụ nút Sửa thường chỉ áp dụng cho bản Nháp; trạng thái Đã duyệt không còn là bản đang soạn.

**Không thấy chỉ số trong Nhập liệu.** Xác minh chỉ số đã **Đã duyệt** và khoa/phòng của người đăng nhập có trong danh sách áp dụng. Kiểm tra chu kỳ và biểu mẫu.

**Không chọn được kỳ cần nhập.** Kiểm tra **tháng/năm bắt đầu chu kỳ**, kỳ hiện tại và giới hạn không nhập tương lai. Tránh nhầm kỳ theo mốc chỉ số với quý dương lịch.

**Không lưu được biểu mẫu.** Xem thông báo trường chưa nhập; kiểm tra câu lựa chọn, số, phần trăm và điểm. Hiện giao diện yêu cầu hoàn thành tất cả câu hỏi nhập liệu.

**Đã nhập nhưng số phiếu tăng thêm.** Mỗi lần lưu thành công có thể tạo phiếu mới; không nên nhấn lưu liên tiếp để “cập nhật” phiếu cũ.

**Chỉ số kỳ hiện tại vẫn báo Chưa nhập.** Kiểm tra có lưu đúng kỳ hiện tại chưa; đã nhập kỳ trước không đồng nghĩa hoàn thành kỳ này. Thử tải lại danh sách.

**Số liệu thống kê không như dự kiến.** Kiểm tra nhiều phiếu trong cùng kỳ, phạm vi khoa/phòng, tháng tạo phiếu và sự thay đổi biểu mẫu. Liên hệ người phụ trách dữ liệu trước khi xóa phiếu.

**Không xuất được Excel.** Kiểm tra quyền, dữ liệu có tồn tại, trình duyệt có chặn tải xuống không. Phân biệt `.xls` và `.xlsx` theo màn hình xuất.

## 11. Phụ lục đối chiếu mã nguồn

Tài liệu này được biên soạn từ các màn hình và đoạn xử lý sau:

- Đăng nhập, đổi mật khẩu, menu và chuông thông báo: `www/admin/login.htm`, `www/admin/changePass.htm`, `www/View/_sharedLayout/index.php`, `www/View/_sharedLayout/menu.php`, `js/common.js`.
- Quản lý chỉ số và biểu mẫu: `www/chisochatluong/index.php`, `js/chisochatluong/chisochatluong.js`, `web_src/admin/chisochatluongAction.php`.
- Nhập liệu và quy tắc kỳ: `www/nhaplieu/index.php`, `js/nhaplieu/nhaplieu.js`, `web_src/admin/NhapLieuAction.php`.
- Theo dõi chỉ số khoa và xuất báo cáo: `www/chisokhoa/index.php`, `js/chisokhoa/chisokhoa.js`, `web_src/admin/chisokhoaAction.php`.
- Người dùng và phân quyền: `www/admin/nguoidung/nguoiDung.htm`, `js/user.js`, `web_src/admin/userAction.php`.

**Phạm vi xác minh:** Đã đối chiếu tên nút, các màn hình, luồng trạng thái, hành vi lưu phiếu, thông báo và kiểu tệp xuất với mã nguồn. Chưa xác minh trực tiếp tài khoản, dữ liệu thật hoặc chạy thử toàn bộ giao diện; trước khi ban hành nội bộ nên bổ sung ảnh chụp màn hình trên môi trường kiểm thử và người phụ trách phê duyệt tài liệu.
