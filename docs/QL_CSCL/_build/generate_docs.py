from __future__ import annotations

from pathlib import Path
import re

from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_CELL_VERTICAL_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Pt, RGBColor


ROOT = Path(__file__).resolve().parents[1]
PROJECT_ROOT = Path(__file__).resolve().parents[3]
DATE_TEXT = "29/10/2026"


def read_default_version() -> str:
    config_path = PROJECT_ROOT / "conf" / "config.php"
    text = config_path.read_text(encoding="utf-8", errors="replace")
    match = re.search(
        r"define\s*\(\s*['\"]_DEFAULT_VERSION_['\"]\s*,\s*['\"]([^'\"]+)['\"]\s*\)",
        text,
    )
    if not match:
        raise RuntimeError(f"Không tìm thấy _DEFAULT_VERSION_ trong {config_path}")
    return match.group(1).strip()


DEFAULT_VERSION = read_default_version()


DOCS = {
    "01_HUONG_DAN_SU_DUNG": r'''# HƯỚNG DẪN SỬ DỤNG HỆ THỐNG QLCL

**Hệ thống:** Quản lý chất lượng - Bệnh viện Thống Nhất  
**Phiên bản tài liệu:** 1.0  
**Ngày cập nhật:** 29/10/2026

## 1. Mục đích và phạm vi

Tài liệu hướng dẫn người dùng thao tác trên hệ thống QLCL: đăng nhập, quản lý chỉ số chất lượng, thiết kế biểu mẫu, gửi duyệt, duyệt/từ chối, nhập dữ liệu theo kỳ, xem thống kê, xuất Excel và các chức năng quản trị được phân quyền.

Quyền hiển thị menu và quyền thực hiện từng thao tác phụ thuộc vào nhóm quyền/quyền chi tiết của tài khoản. Tài khoản có `adminType = 1` được hệ thống cho phép vượt qua kiểm tra quyền chi tiết.

## 2. Đăng nhập, đăng xuất và đổi mật khẩu

### 2.1 Đăng nhập

1. Mở địa chỉ hệ thống do đơn vị quản trị cung cấp.
2. Nhập **Tên đăng nhập** và **Mật khẩu**.
3. Chọn **Đăng nhập**.
4. Nếu thông tin hợp lệ và tài khoản không bị khóa, hệ thống chuyển đến trang Dashboard.

Tài khoản bị khóa (`nd_block = 1`) không thể đăng nhập. Cơ chế khóa tạm theo số lần nhập sai hiện không được kích hoạt trong mã nguồn hiện tại, vì vậy việc bảo vệ tài khoản phụ thuộc chủ yếu vào trạng thái khóa do quản trị viên thiết lập.

### 2.2 Đăng xuất

Chọn chức năng **Đăng xuất** trên giao diện. Hệ thống xóa thông tin phiên đăng nhập, token phiên và chuyển về màn hình đăng nhập.

### 2.3 Đổi mật khẩu

1. Mở chức năng **Đổi mật khẩu**.
2. Nhập mật khẩu hiện tại.
3. Nhập mật khẩu mới và xác nhận lại.
4. Lưu thay đổi.

Mật khẩu mới phải trùng với ô xác nhận và không được để trống. Hệ thống hiện lưu mật khẩu bằng MD5; quản trị hệ thống nên có kế hoạch nâng cấp cơ chế băm mật khẩu theo tài liệu bảo trì.

## 3. Menu và phân quyền

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

## 4. Quản lý chỉ số chất lượng

### 4.1 Trạng thái của chỉ số

| Mã | Trạng thái | Ý nghĩa |
| --- | --- | --- |
| 0 | Nháp | Người tạo đang soạn, có thể chỉnh sửa |
| 1 | Chờ duyệt | Đã gửi cho người có quyền duyệt |
| 2 | Đã duyệt | Có thể phân phối cho khoa/phòng nhập liệu |
| 3 | Từ chối | Bị từ chối và có thể tạo lại thành bản nháp mới |

Luồng chuẩn: **Nháp -> Chờ duyệt -> Đã duyệt**. Từ trạng thái **Chờ duyệt**, người duyệt có thể chuyển sang **Từ chối**. Chỉ số bị từ chối có thể dùng chức năng **Tạo lại**; hệ thống sao chép cấu hình sang một chỉ số mới ở trạng thái Nháp và lưu liên kết về chỉ số gốc.

### 4.2 Tạo chỉ số mới

1. Mở **Chỉ số chất lượng**.
2. Chọn **Thêm mới**.
3. Nhập tối thiểu **Tên chỉ số**.
4. Chọn các thuộc tính: khía cạnh, thành tố, nhóm chỉ số, phạm vi, mục tiêu, ngưỡng cảnh báo, đơn vị tính, chu kỳ.
5. Chọn khoa/phòng áp dụng. Với phạm vi toàn bệnh viện, hệ thống tự gán danh sách tất cả khoa/phòng.
6. Nhập định nghĩa, cách thu thập, tên tử số/mẫu số nếu sử dụng.
7. Thiết kế biểu mẫu nhập liệu.
8. Lưu. Chỉ số mới ở trạng thái **Nháp**.

### 4.3 Thiết kế biểu mẫu

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

### 4.4 Sửa chỉ số

Mở chỉ số cần sửa, cập nhật thông tin và lưu. Khi sửa cấu hình biểu mẫu, cần lưu ý các phiếu đã nhập trước đó vẫn chứa dữ liệu theo mã câu hỏi cũ; thay đổi ID hoặc cấu trúc câu hỏi có thể làm việc đối chiếu dữ liệu lịch sử khó hơn.

### 4.5 Gửi duyệt

Từ chỉ số Nháp, chọn **Gửi duyệt**. Hệ thống chuyển trạng thái sang **Chờ duyệt** và ghi thời gian gửi.

### 4.6 Duyệt hoặc từ chối

Người có quyền duyệt mở chỉ số ở trạng thái Chờ duyệt:

- Chọn **Duyệt** để chuyển sang **Đã duyệt**, ghi người duyệt và thời gian duyệt.
- Chọn **Từ chối** để chuyển sang **Từ chối** và lưu lý do từ chối.

Người duyệt có thể thấy chỉ số do mình tạo ở mọi trạng thái; với chỉ số của người khác, danh sách chỉ hiển thị các trạng thái Chờ duyệt, Đã duyệt hoặc Từ chối.

### 4.7 Tạo lại chỉ số bị từ chối

Người tạo mở chỉ số bị từ chối của chính mình và chọn **Tạo lại**. Hệ thống tạo bản sao mới ở trạng thái Nháp, bỏ thông tin gửi/duyệt/từ chối và ghi mã chỉ số gốc.

### 4.8 Xóa chỉ số

Chức năng xóa thực hiện theo mã chỉ số. Dữ liệu `ct_chiso` liên quan được cấu hình khóa ngoại `ON DELETE CASCADE`, vì vậy xóa chỉ số có thể xóa cả các phiếu nhập của chỉ số đó. Chỉ thực hiện khi đã xác nhận không cần dữ liệu lịch sử.

## 5. Nhập dữ liệu chỉ số

### 5.1 Điều kiện hiển thị chỉ số

Màn hình **Nhập liệu** chỉ lấy các chỉ số:

- đã ở trạng thái **Đã duyệt**;
- được gán cho khoa/phòng của người dùng;
- có cấu hình chu kỳ và biểu mẫu hợp lệ.

### 5.2 Xác định kỳ nhập

Chu kỳ được tính từ tháng tạo chỉ số. Số kỳ phải trong khoảng 1 đến 12 và chia hết cho 12. Ví dụ: chu kỳ 4 kỳ/năm tương ứng mỗi kỳ 3 tháng.

Người dùng được nhập kỳ hiện tại hoặc kỳ trước trong cùng năm/chu kỳ đang xét. Hệ thống không cho nhập kỳ tương lai.

### 5.3 Nhập và lưu phiếu

1. Chọn chỉ số cần nhập.
2. Chọn năm/kỳ hợp lệ nếu giao diện cho phép.
3. Điền các câu hỏi trong biểu mẫu.
4. Chọn **Lưu**.
5. Nếu dữ liệu hợp lệ, hệ thống lưu một bản ghi vào `ct_chiso` gồm chỉ số, người nhập, khoa/phòng, năm, kỳ và nội dung JSON.

Hệ thống hiện cho phép nhiều phiếu trong cùng một chỉ số/khoa-phòng/năm/kỳ. Đây phù hợp với các biểu mẫu khảo sát có nhiều lượt trả lời; đơn vị sử dụng cần xác định rõ quy trình nghiệp vụ nếu chỉ mong muốn một phiếu tổng hợp cho mỗi kỳ.

### 5.4 Quy tắc tính điểm

Hệ thống tính `tong_diem`, `diem_toi_da` và `ty_le_phan_tram`:

- `radio`, `select`, `satisfaction`: dùng điểm của phương án đã chọn.
- `checkbox`: cộng điểm các phương án được chọn.
- `score`: lấy giá trị số, tối đa 10 điểm.
- câu hỏi không có cơ chế điểm không làm tăng điểm tối đa.
- tỷ lệ phần trăm = tổng điểm / điểm tối đa x 100, làm tròn 2 chữ số.

## 6. Theo dõi chỉ số khoa và thống kê

Các màn hình chỉ số/khoa cung cấp:

- danh sách chỉ số và tình trạng nhập;
- trung bình theo năm/kỳ;
- thống kê theo tháng dựa trên thời điểm tạo phiếu;
- trung bình theo từng câu hỏi có chấm điểm;
- danh sách phiếu chi tiết, người nhập, khoa/phòng và tỷ lệ phần trăm.

Khi xem dữ liệu chỉ số từ màn hình quản lý chỉ số, hệ thống kiểm tra lại quyền truy cập chỉ số trước khi trả dữ liệu thống kê.

## 7. Xuất Excel

Trong màn hình chỉ số, chọn chức năng **Xuất Excel**. Hệ thống sinh tệp `.xls` dạng HTML có thông tin chỉ số và thống kê câu hỏi, sau khi kiểm tra quyền xem chỉ số.

## 8. Quản trị người dùng và nhóm quyền

### 8.1 Người dùng

Người có quyền quản trị có thể:

- xem danh sách người dùng;
- thêm/sửa thông tin tài khoản;
- gán khoa/phòng;
- gán nhóm quyền/quyền chi tiết;
- đổi mật khẩu khi sửa người dùng;
- khóa hoặc mở khóa tài khoản;
- xóa người dùng khi được phân quyền.

### 8.2 Nhóm quyền

Nhóm quyền lưu tập quyền dưới dạng chuỗi các mã chức năng. Quản trị viên có thể tạo/sửa nhóm quyền và chọn các chức năng/thao tác thành viên.

## 9. Quản lý danh mục

Tùy quyền, người quản trị có thể quản lý khoa/phòng, khối, chu kỳ, đơn vị tính, khía cạnh/thành tố, phạm vi và trạng thái. Các danh mục này được dùng trực tiếp khi cấu hình chỉ số nên cần hạn chế xóa các bản ghi đã được tham chiếu.

## 10. Nhật ký và sao lưu/phục hồi

Màn hình **Log** dùng để tra cứu nhật ký thao tác của hệ thống. Chức năng **Backup/Restore** nhận tệp SQL và có giới hạn kích thước theo cấu hình. Thành phần sao lưu/phục hồi hiện còn mã PHP/MySQL kiểu cũ, vì vậy cần kiểm thử trên môi trường thử nghiệm trước khi dùng cho dữ liệu thật.

## 11. Các lưu ý vận hành

- Không dùng chung tài khoản giữa nhiều người để bảo đảm truy vết người nhập/người duyệt.
- Không xóa chỉ số đã có dữ liệu nếu chưa sao lưu và xác nhận ảnh hưởng.
- Khi thay đổi biểu mẫu của chỉ số đã có phiếu, giữ ổn định ID câu hỏi nếu cần so sánh lịch sử.
- Nếu menu/chức năng không xuất hiện, kiểm tra nhóm quyền và chuỗi quyền của tài khoản trước khi kết luận lỗi giao diện.
- Nếu chỉ số không xuất hiện ở màn hình Nhập liệu, kiểm tra trạng thái Đã duyệt, danh sách khoa/phòng được gán và cấu hình chu kỳ.

## 12. Quy trình sử dụng theo từng vai trò

### 12.1 Người tạo chỉ số

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

### 12.2 Người duyệt chỉ số

Người duyệt truy cập danh sách chỉ số chờ duyệt, kiểm tra nội dung chỉ số, phạm vi khoa/phòng, chu kỳ, biểu mẫu, mục tiêu và công thức. Khi chọn **Duyệt**, hệ thống cập nhật trạng thái sang `2`, lưu người duyệt và thời gian duyệt. Khi chọn **Từ chối**, người duyệt cần nhập lý do để người tạo biết nội dung cần điều chỉnh.

### 12.3 Người nhập liệu khoa/phòng

Người nhập liệu chỉ nhìn thấy các chỉ số thỏa đồng thời các điều kiện: đã duyệt, được gán cho khoa/phòng của tài khoản và có cấu hình chu kỳ hợp lệ. Khi mở một chỉ số, hệ thống trả về biểu mẫu, kỳ hiện tại và các phiếu đã nhập trong năm. Người dùng chọn kỳ hợp lệ, nhập đầy đủ các trường bắt buộc và lưu phiếu.

Hệ thống chặn kỳ tương lai. Với kỳ hiện tại hoặc kỳ cũ còn hợp lệ, một lần lưu tạo một bản ghi mới trong `ct_chiso`; source hiện tại không áp đặt ràng buộc duy nhất cho một khoa/phòng trong cùng năm/kỳ.

### 12.4 Quản trị viên

Quản trị viên duy trì người dùng, khoa/phòng, nhóm quyền và các danh mục nền. Khi tạo tài khoản cần gán đúng khoa/phòng vì `MaKhoaPhong` được dùng để xác định phạm vi nhập liệu. Khi thay đổi nhóm quyền cần kiểm tra lại menu và các thao tác chi tiết như duyệt, xóa, khóa hoặc phân quyền.

## 13. Thông báo và tình huống thường gặp

| Tình huống | Nguyên nhân cần kiểm tra | Cách xử lý |
| --- | --- | --- |
| Không đăng nhập được | Sai mật khẩu, tài khoản bị khóa, kết nối DB lỗi | Kiểm tra tài khoản, `nd_block`, cấu hình DB |
| Không thấy menu | Thiếu quyền hoặc cấu hình `chucnang` | Kiểm tra nhóm quyền và session `quyen` |
| Không thấy chỉ số ở Nhập liệu | Chưa duyệt hoặc chưa gán khoa/phòng | Kiểm tra `trang_thai = 2` và trường `phong` |
| Không lưu được phiếu | Câu bắt buộc thiếu, giá trị ngoài miền, kỳ tương lai | Kiểm tra biểu mẫu và dữ liệu nhập |
| Thống kê khác dự kiến | Có nhiều phiếu trong cùng kỳ hoặc cấu hình biểu mẫu đã thay đổi | Kiểm tra `ct_chiso`, ID câu hỏi và dữ liệu JSON |
| Xuất Excel không có dữ liệu | Không có quyền xem hoặc chưa có phiếu phù hợp | Kiểm tra quyền và danh sách phiếu |

## 14. Quy tắc dữ liệu người dùng cần biết

- Trạng thái chỉ số gồm: `0` Nháp, `1` Chờ duyệt, `2` Đã duyệt, `3` Từ chối.
- Chỉ số phải ở trạng thái **Đã duyệt** mới tham gia luồng nhập liệu khoa/phòng.
- Phần trăm chỉ nhận giá trị từ 0 đến 100; điểm số dạng `score` nhận từ 1 đến 10.
- Lựa chọn radio/select/satisfaction phải thuộc danh sách phương án đã cấu hình.
- Câu hỏi bắt buộc không được để trống.
- Kỳ nhập phụ thuộc chu kỳ chỉ số và thời điểm tạo chỉ số; cấu hình chu kỳ phải chia hết 12 tháng.
- Một chỉ số bị xóa có thể kéo theo việc xóa các phiếu `ct_chiso` liên quan do khóa ngoại cascade, vì vậy cần sao lưu trước khi xóa dữ liệu đã sử dụng.
''',

    "02_MO_TA_CHUC_NANG": r'''# TÀI LIỆU MÔ TẢ CHỨC NĂNG HỆ THỐNG QLCL

**Hệ thống:** Quản lý chất lượng - Bệnh viện Thống Nhất  
**Phiên bản tài liệu:** 1.0  
**Ngày cập nhật:** 29/10/2026

## 1. Tổng quan nghiệp vụ

Hệ thống QLCL quản lý vòng đời chỉ số chất lượng từ lúc xây dựng chỉ số, thiết kế biểu mẫu, gửi duyệt, phân phối cho khoa/phòng, nhập nhiều phiếu theo kỳ đến thống kê và xuất dữ liệu. Hệ thống đồng thời cung cấp quản trị người dùng, quyền, danh mục và nhật ký.

## 2. Tác nhân sử dụng

| Tác nhân | Vai trò chính |
| --- | --- |
| Người tạo chỉ số | Tạo, sửa, gửi duyệt, theo dõi trạng thái, tạo lại chỉ số bị từ chối |
| Người duyệt | Xem chỉ số chờ duyệt, duyệt hoặc từ chối |
| Người nhập liệu khoa/phòng | Nhập phiếu cho chỉ số đã duyệt và được gán cho khoa/phòng |
| Người theo dõi chỉ số khoa | Xem biểu đồ, thống kê, danh sách phiếu và thao tác theo quyền |
| Quản trị viên | Quản lý user, nhóm quyền, danh mục, log và các chức năng hệ thống |

Quyền thực tế được kiểm tra bằng mã chức năng trong session. `adminType = 1` được phép vượt kiểm tra quyền chi tiết.

## 3. Ma trận chức năng

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

## 4. Vòng đời chỉ số chất lượng

### 4.1 Trạng thái

| Trạng thái | Mã | Chuyển tiếp hợp lệ trong mã nguồn |
| --- | --- | --- |
| Nháp | 0 | Gửi -> Chờ duyệt |
| Chờ duyệt | 1 | Duyệt -> Đã duyệt; Từ chối -> Từ chối |
| Đã duyệt | 2 | Được dùng để nhập liệu |
| Từ chối | 3 | Tạo lại -> một bản Nháp mới |

### 4.2 Quy tắc hiển thị

- Người không có quyền duyệt chỉ thấy chỉ số do chính mình tạo.
- Người có quyền duyệt thấy chỉ số do mình tạo ở mọi trạng thái.
- Với chỉ số do người khác tạo, người duyệt chỉ thấy trạng thái 1, 2, 3.
- Chỉ số dành cho nhập liệu phải ở trạng thái 2 và khoa/phòng hiện tại phải nằm trong `phong` hoặc khớp `id_khoaphong` theo nhánh tương thích dữ liệu cũ.

### 4.3 Tạo mới và cập nhật

Khi tạo mới, hệ thống đặt `trang_thai = 0`. Trường `phong` được chuẩn hóa thành mảng số nguyên duy nhất rồi lưu JSON. Biểu mẫu được chuẩn hóa thành JSON có cấu trúc `{version: 1, cau_hoi: [...]}`.

Nếu `pham_vi = 3` (Toàn bệnh viện), hệ thống gán tất cả khoa/phòng vào trường `phong`. Tên chỉ số là bắt buộc; người tạo phải có khoa/phòng mặc định hợp lệ.

### 4.4 Gửi, duyệt và từ chối

- Gửi: đổi trạng thái sang 1 và ghi `thoi_gian_gui`.
- Duyệt: đổi trạng thái sang 2, ghi `nguoi_duyet` và `thoi_gian_duyet`.
- Từ chối: đổi trạng thái sang 3 và ghi `ly_do_tu_choi`.
- Tạo lại: chỉ áp dụng cho bản bị từ chối thuộc người dùng hiện tại; sao chép cấu hình, đặt trạng thái 0, xóa metadata gửi/duyệt/từ chối và ghi `ma_chi_so_goc`.

## 5. Biểu mẫu và kiểm tra dữ liệu

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

## 6. Nhập dữ liệu theo chu kỳ

### 6.1 Xác định chu kỳ

Hệ thống đọc số kỳ trong năm từ bảng `chuky`. Giá trị hợp lệ phải từ 1 đến 12 và 12 chia hết cho số kỳ. Số tháng mỗi kỳ = 12 / số kỳ.

Mốc tính kỳ bắt đầu từ tháng/năm `created_at` của chỉ số. Kỳ hiện tại được tính theo độ lệch tháng từ mốc này và giới hạn không vượt quá tổng số kỳ.

### 6.2 Quy tắc lưu

Người nhập phải đăng nhập, có khoa/phòng hợp lệ, chỉ số đã duyệt và được gán cho khoa/phòng. Chỉ được lưu kỳ hiện tại hoặc kỳ trước trong phạm vi năm/chu kỳ được kiểm tra; không lưu kỳ tương lai.

Mỗi lần lưu tạo một dòng mới trong `ct_chiso`. Cơ sở dữ liệu hiện không đặt unique cho `(ma_chi_so, id_khoaphong, nam, ky)`, chỉ có index, nên một kỳ có thể có nhiều phiếu.

### 6.3 Cấu trúc dữ liệu phiếu

`ct_chiso.du_lieu` lưu JSON, điển hình:

```json
{
  "cau_tra_loi": {"q1": "...", "q2": "..."},
  "tong_diem": 15,
  "diem_toi_da": 20,
  "ty_le_phan_tram": 75
}

```

## 7. Thống kê và báo cáo

### 7.1 Trung bình theo kỳ

Hệ thống đọc toàn bộ phiếu của chỉ số, nhóm theo `nam` và `ky`, sau đó lấy trung bình `du_lieu.ty_le_phan_tram`.

### 7.2 Thống kê theo tháng

Phiếu được nhóm theo tháng của `created_at` và lấy trung bình tỷ lệ phần trăm.

### 7.3 Trung bình theo câu hỏi

Chỉ các câu hỏi có cơ chế điểm mới được tổng hợp. Hệ thống tính tỷ lệ điểm từng câu hỏi, tổng hợp toàn hệ thống và tách theo khoa/phòng.

### 7.4 Danh sách phiếu

Danh sách trả về mã phiếu, khoa/phòng, năm, kỳ, người nhập, thời gian tạo/cập nhật, điểm, tỷ lệ và chi tiết câu trả lời. Hệ thống ghép dữ liệu câu trả lời với cấu hình biểu mẫu hiện hành để hiển thị nội dung câu hỏi.

### 7.5 Xuất Excel

Action `xuatExcel` xuất HTML dưới phần mở rộng `.xls`. Dữ liệu xuất được kiểm tra quyền xem chỉ số trước khi sinh tệp.

## 8. Quản trị người dùng và quyền

### 8.1 Xác thực

`UserPeer::getUserLogin()` tìm user theo username và `nd_block = 0`, sau đó so sánh `md5(password nhập)` với cột `password`. Khi đăng nhập thành công, session ghi ID, username, họ tên, adminType, token và quyền.

### 8.2 Phân quyền

Quyền được lưu trong session `quyen`. Kiểm tra quyền chấp nhận cả quyền mức class và `class.method`. Menu được dựng từ bảng `chucnang`, vì vậy cấu hình `chucnang`, `nhomquyen` và quyền user phải đồng bộ.

### 8.3 Khóa tài khoản

`UserPeer::updateLock()` cập nhật `nd_block`. User bị khóa không vượt qua truy vấn đăng nhập.

## 9. Danh mục nền

| Danh mục | Mục đích |
| --- | --- |
| Khoa/phòng | Gán user và phạm vi chỉ số |
| Khối | Nhóm khoa/phòng |
| Chu kỳ | Số kỳ nhập trong năm |
| Đơn vị tính | Thuộc tính chỉ số |
| Khía cạnh/Thành tố | Phân loại chỉ số |
| Phạm vi | Khoa/phòng hoặc toàn bệnh viện |
| Trạng thái | Tên/nhãn trạng thái |

## 10. Nhật ký và sao lưu/phục hồi

Các Peer ghi log cho một số thao tác quản trị. Phân hệ backup/restore cho phép tạo bản sao SQL và phục hồi từ tệp tải lên. Trước khi restore, action cố tạo một bản backup dự phòng. Thành phần này dùng API `mysql_*` cũ trong khi lớp DB chính dùng `mysqli`, vì vậy cần coi đây là khu vực rủi ro cao khi nâng phiên bản PHP.

## 11. Các trường hợp biên đáng chú ý

- Chỉ số đã duyệt nhưng không gán khoa/phòng hiện tại sẽ không xuất hiện ở Nhập liệu.
- Chu kỳ không chia hết 12 làm tính kỳ không hợp lệ.
- Thay đổi ID câu hỏi sau khi đã có phiếu làm việc ghép dữ liệu lịch sử kém tin cậy.
- Xóa chỉ số có thể kéo theo xóa phiếu do khóa ngoại cascade.
- `ct_chiso` cho phép nhiều phiếu cùng kỳ; chức năng `daNhapKy()` chỉ kiểm tra có ít nhất một phiếu để hiển thị trạng thái đã nhập.

## 12. Đặc tả chức năng chi tiết

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

## 13. Luồng trạng thái và quyền thao tác

| Trạng thái | Người tạo | Người duyệt | Nhập liệu khoa/phòng |
| --- | --- | --- | --- |
| Nháp (0) | Xem/sửa/gửi/xóa theo quyền | Không cần xử lý | Không thấy để nhập |
| Chờ duyệt (1) | Theo dõi | Duyệt hoặc từ chối | Không thấy để nhập |
| Đã duyệt (2) | Xem/theo dõi | Xem | Được nhập nếu đúng khoa/phòng |
| Từ chối (3) | Xem lý do, tạo lại | Xem | Không thấy để nhập |

Việc người dùng nhìn thấy một chỉ số còn phụ thuộc quyền và quan hệ sở hữu. Người duyệt có thể xem chỉ số của mình ở mọi trạng thái và các chỉ số khác ở trạng thái chờ duyệt/đã duyệt/từ chối. Người dùng thông thường chủ yếu thấy chỉ số do mình tạo.

## 14. Quy tắc phạm vi dữ liệu

Phạm vi khoa/phòng được lưu trong `chi_so_chat_luong.phong` dưới dạng JSON mảng ID. Khi lấy chỉ số phục vụ nhập liệu, hệ thống kiểm tra khoa/phòng hiện tại có nằm trong danh sách này hay không; với dữ liệu cũ có thể fallback sang `id_khoaphong`. Vì vậy việc gán đúng `user.MaKhoaPhong` và duy trì ID khoa/phòng ổn định là điều kiện quan trọng để hệ thống phân phối chỉ số đúng nơi.

## 15. Quy tắc dữ liệu biểu mẫu

Biểu mẫu được lưu dưới JSON dạng `{"version": 1, "cau_hoi": [...]}`. Mỗi câu hỏi có loại và thuộc tính riêng. Các loại category/subcategory dùng để trình bày; checkbox/radio/select/satisfaction dùng danh sách lựa chọn; number/percentage/score yêu cầu kiểm tra miền giá trị; các trường text-like nhận dữ liệu văn bản. Hệ thống dùng ID câu hỏi để nối câu trả lời lịch sử với cấu hình hiện hành, do đó thay ID sau khi đã có dữ liệu có thể làm báo cáo lịch sử khó đọc đúng.
''',

    "03_TAI_LIEU_BAO_TRI": r'''# TÀI LIỆU BẢO TRÌ HỆ THỐNG QLCL

**Hệ thống:** Quản lý chất lượng - Bệnh viện Thống Nhất  
**Phiên bản tài liệu:** 1.0  
**Ngày cập nhật:** 29/10/2026

## 1. Mục tiêu bảo trì

Tài liệu này phục vụ triển khai, vận hành, xử lý sự cố và nâng cấp hệ thống PHP/MySQL trong thư mục dự án `ql_cscl`. Trọng tâm là các điểm cấu hình, luồng request, CSDL, quyền, backup/restore, log và các rủi ro kỹ thuật cần xử lý khi nâng cấp.

## 2. Kiến trúc vận hành

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

## 3. Cấu hình quan trọng

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

## 4. Cấu trúc source cần biết khi bảo trì

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

## 5. Cơ sở dữ liệu và migration

### 5.1 Bảng lõi đang dùng

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

### 5.2 Quan hệ quan trọng

- `user.MaKhoaPhong -> khoa.id`.
- `khoa.id_khoi -> khoi.id`.
- `ct_chiso.ma_chi_so -> chi_so_chat_luong.ma_chi_so` với `ON DELETE CASCADE`.
- `chi_so_chat_luong` tham chiếu các danh mục như khoa, chu kỳ, đơn vị tính, phạm vi, trạng thái.
- `ct_chiso` có index `(ma_chi_so, id_khoaphong, nam, ky)` nhưng không có unique; nhiều phiếu cùng kỳ là hợp lệ theo cấu trúc hiện tại.

### 5.3 Migration

Các file `sql/create_ct_chiso.sql` và `sql/migrate_ct_chiso_luu_moi.sql` liên quan mô hình nhập liệu mới. Khi triển khai migration:

1. Backup database trước khi chạy.
2. Kiểm tra version schema hiện tại.
3. Chạy trên bản sao/staging.
4. Kiểm tra dữ liệu JSON và khóa ngoại.
5. Kiểm thử màn hình Nhập liệu, Chỉ số khoa và thống kê.

## 6. Xác thực, session và quyền

### 6.1 Session chính

Các biến thường dùng: `sUserID`, `sUserName`, `FullName`, `AdminType`, `sUserLogin`, `sToken`, `quyen` và khóa động `sPassword` cho tên trường mật khẩu trên form đăng nhập.

Khi đăng nhập thành công, hệ thống tạo token 9 ký tự và ghi token vào user. Khi đăng xuất, session bị xóa và cookie session được hủy.

### 6.2 Quyền

`Request::checkRoles()` so sánh quyền trong session với danh sách quyền tĩnh của Action. Hệ thống hỗ trợ quyền theo class hoặc `class.method`. `adminType = 1` bỏ qua kiểm tra quyền.

Khi thêm Action hoặc method mới có dữ liệu nhạy cảm:

1. Khai báo quyền trong `$listRole` của Action.
2. Thêm mã chức năng/permission vào `chucnang` nếu cần xuất hiện trên UI.
3. Cập nhật nhóm quyền.
4. Kiểm thử bằng tài khoản có và không có quyền.

## 7. Backup và phục hồi

`backupAction` nhận file `.sql`, kiểm tra kích thước theo `_BACKUP_SIZE_`, tạo backup trước khi restore và gọi `BackupRestorePeer`.

Các vấn đề cần xử lý trước khi phụ thuộc vào chức năng này trong môi trường thật:

- `BackupRestorePeer` vẫn dùng `mysql_query`, `mysql_list_tables`, `mysql_fetch_*` và `ereg_replace`, không tương thích PHP hiện đại.
- Lớp DB chính dùng `mysqli`, tạo ra hai cơ chế truy cập DB không đồng nhất.
- `backupAction` có tham chiếu `$db_name` không thấy khởi tạo cục bộ rõ ràng.
- Thông báo lỗi upload nhắc `xls/xlsx` dù code kiểm tra file SQL.
- Restore chạy câu SQL tuần tự; cần kiểm soát file nguồn và quyền thực thi.

Khuyến nghị vận hành: dùng backup MySQL/MariaDB chuẩn ở cấp server cho dữ liệu thật; chỉ bật chức năng restore trong web sau khi đã nâng cấp và kiểm thử kỹ.

## 8. Nhật ký và xử lý lỗi

Một số Peer gọi `setLog()` để ghi thao tác. Cần kiểm tra định kỳ kích thước bảng `log` và chính sách lưu trữ.

Lớp `db_mysql::query()` hiện có hành vi dừng xử lý và in câu SQL khi lỗi. Đây là rủi ro lộ cấu trúc DB/dữ liệu trên môi trường production. Nên thay bằng exception + log phía server và trả thông báo chung cho người dùng.

## 9. Rủi ro kỹ thuật và ưu tiên xử lý

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

## 10. Quy trình triển khai thay đổi

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

## 11. Checklist xử lý sự cố

### 11.1 Không đăng nhập được

- Kiểm tra `user.username`.
- Kiểm tra `nd_block` phải bằng 0.
- Kiểm tra hash MD5 của mật khẩu theo cơ chế hiện tại.
- Kiểm tra kết nối DB và lỗi PHP/Apache.

### 11.2 Menu không hiện

- Kiểm tra `$_SESSION['quyen']`.
- Kiểm tra bản ghi `chucnang` và URL.
- Kiểm tra nhóm quyền `nhomquyen`.
- Kiểm tra `adminType` và logic `Request::checkRoles()`.

### 11.3 Chỉ số không hiện ở Nhập liệu

- `trang_thai` phải bằng 2.
- Khoa/phòng user phải hợp lệ.
- `phong` phải là JSON hợp lệ và chứa ID khoa/phòng, hoặc dữ liệu cũ phải khớp `id_khoaphong`.
- Chu kỳ phải hợp lệ.

### 11.4 Không lưu được phiếu

- Kiểm tra kỳ có phải tương lai không.
- Kiểm tra câu hỏi bắt buộc.
- Kiểm tra percentage 0..100, score 1..10, number và độ dài số.
- Kiểm tra option gửi lên có nằm trong cấu hình biểu mẫu.

### 11.5 Thống kê sai hoặc thiếu

- Kiểm tra `du_lieu` JSON của `ct_chiso`.
- Kiểm tra trường `ty_le_phan_tram` có phải số.
- Kiểm tra ID câu hỏi giữa biểu mẫu hiện tại và dữ liệu lịch sử.
- Kiểm tra có nhiều phiếu trong cùng kỳ hay không.

## 12. Hướng nâng cấp đề xuất

Ưu tiên 1: bảo mật xác thực và DB. Ưu tiên 2: chuẩn hóa truy cập DB bằng prepared statements. Ưu tiên 3: viết lại backup/restore. Ưu tiên 4: tách cấu hình môi trường khỏi source. Ưu tiên 5: bổ sung test cho quyền, vòng đời chỉ số, tính kỳ và validate biểu mẫu.

## 13. Kế hoạch bảo trì định kỳ

| Chu kỳ | Công việc | Kết quả mong đợi |
| --- | --- | --- |
| Hàng ngày | Kiểm tra Apache/PHP error log, dung lượng đĩa, lỗi DB nghiêm trọng | Phát hiện sớm sự cố vận hành |
| Hàng tuần | Kiểm tra log ứng dụng, tài khoản bị khóa, dữ liệu nhập bất thường | Phát hiện lỗi nghiệp vụ và tài khoản |
| Hàng tháng | Sao lưu DB, kiểm tra restore trên môi trường thử nghiệm, rà soát user/quyền | Bảo đảm khả năng phục hồi và quyền hợp lệ |
| Mỗi lần release | Sao lưu source + DB, chạy kiểm tra syntax, test luồng chính và quyền | Có điểm quay lui an toàn |
| Hàng quý | Rà soát thư viện/mã legacy, bảo mật session, cấu hình DB, dung lượng bảng | Giảm nợ kỹ thuật và rủi ro bảo mật |

## 14. Checklist sau khi triển khai phiên bản mới

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

## 15. Quản lý thay đổi source và database

Mỗi thay đổi nên có mô tả rõ module, file, bảng DB và dữ liệu bị ảnh hưởng. Không sửa trực tiếp dump SQL cũ để đại diện cho migration mới; nên tạo file migration riêng có thứ tự thời gian. Trước khi đổi tên/xóa cột hoặc bảng, dùng tìm kiếm toàn source để xác định Action, Peer, View, JS và báo cáo nào còn tham chiếu.

Với trường JSON như `phong`, `bieumau`, `du_lieu`, cần giữ khả năng tương thích dữ liệu cũ. Nếu thay schema JSON, nên có trường `version` và code đọc được ít nhất định dạng hiện tại cùng định dạng trước đó trong giai đoạn chuyển đổi.

## 16. Hướng xử lý các rủi ro ưu tiên cao

### 16.1 Mật khẩu và đăng nhập

Chuyển dần từ MD5 sang `password_hash()`/`password_verify()`. Có thể nâng cấp theo kiểu tương thích ngược: khi user đăng nhập thành công bằng MD5, tạo hash mới và cập nhật bản ghi. Bổ sung rate limit đăng nhập và `session_regenerate_id(true)` sau khi xác thực thành công.

### 16.2 Truy cập cơ sở dữ liệu

Ưu tiên các endpoint nhận dữ liệu người dùng để chuyển sang prepared statements trước. Đồng thời bỏ việc in trực tiếp câu SQL khi query lỗi ở môi trường production; thay bằng log nội bộ có kiểm soát.

### 16.3 Backup/Restore

Thành phần `mysql_*` cần viết lại bằng `mysqli`/PDO hoặc dùng `mysqldump`/`mysql` command-line với đường dẫn và quyền được quản lý rõ. Restore phải được thử trên database staging trước khi áp dụng production.

### 16.4 Cấu hình môi trường

Tách DB user/password, base URL và các thông số môi trường khỏi source. Production không nên chạy bằng MySQL `root` với mật khẩu trống. Tài khoản ứng dụng chỉ nên có các quyền cần thiết trên database `ql_cscl`.
''',

    "04_TAI_LIEU_SOURCE_CODE": r'''# TÀI LIỆU SOURCE CODE HỆ THỐNG QLCL

**Hệ thống:** Quản lý chất lượng - Bệnh viện Thống Nhất  
**Phiên bản tài liệu:** 1.0  
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

## 17. Bản đồ module và trách nhiệm

| Thành phần | Trách nhiệm | Ví dụ |
| --- | --- | --- |
| `index.php` | Front controller, route, quyền, render | xác định handle/method |
| `web_src/admin/*Action.php` | Nhận request và điều phối use case | `chisochatluongAction`, `NhapLieuAction` |
| `web_src/bean/*Peer.php` | Truy cập DB và nghiệp vụ dữ liệu | `ChiSoChatLuongPeer`, `CtChiSoPeer`, `UserPeer` |
| `web_src/common/Request.php` | GET/POST, session, role, JSON response | `checkRoles()`, `json_response()` |
| `web_src/common/mysql.php` | Kết nối và thực thi SQL mysqli | `query()` |
| `www/*` | HTML/PHP view | chỉ số, nhập liệu, quản trị |
| `js/*` | AJAX và tương tác trình duyệt | save/getData/duyet/tuchoi |
| `sql/*` | Dump, schema, migration | `create_ct_chiso.sql` |

## 18. Ví dụ luồng request thực tế

### 18.1 Lấy danh sách chỉ số chất lượng

```text
Browser JS
  -> index.php?handle=chisochatluong/getData
  -> router xác định chisochatluongAction::getData()
  -> Request::checkRoles()
  -> Action xác định user/quyền/phạm vi
  -> ChiSoChatLuongPeer truy vấn DB
  -> Request::json_response(...)
  -> JavaScript render bảng
```

### 18.2 Lưu phiếu nhập liệu

```text
js/nhaplieu/nhaplieu.js
  -> NhapLieu/save
  -> kiểm tra session + khoa/phòng
  -> lấy cấu hình chỉ số đã duyệt
  -> kiểm tra năm/kỳ
  -> kiemTraCauTraLoi()
  -> tính tong_diem / diem_toi_da / ty_le_phan_tram
  -> CtChiSoPeer::save()
  -> INSERT ct_chiso
  -> JSON success/error
```

## 19. Cấu trúc JSON quan trọng

### 19.1 Phạm vi khoa/phòng

`chi_so_chat_luong.phong` lưu mảng ID khoa/phòng, ví dụ:

```json
[1, 3, 7]
```

### 19.2 Biểu mẫu

`chi_so_chat_luong.bieumau` có khung dữ liệu:

```json
{
  "version": 1,
  "cau_hoi": [
    {
      "id": "q1",
      "type": "radio",
      "required": true,
      "options": []
    }
  ]
}
```

### 19.3 Phiếu nhập

`ct_chiso.du_lieu` lưu câu trả lời cùng kết quả tính điểm. Các khóa trọng yếu gồm tập câu trả lời, `tong_diem`, `diem_toi_da`, `ty_le_phan_tram`. Khi thay đổi schema cần giữ khả năng đọc dữ liệu lịch sử.

## 20. Điểm mở rộng khi phát triển mới

- Nếu thêm Action mới, phải đồng bộ route, quyền, menu và JavaScript gọi endpoint.
- Nếu thêm loại câu hỏi mới, cần cập nhật cả phần tạo biểu mẫu, render form, validate server, tính điểm và báo cáo.
- Nếu thêm trạng thái chỉ số mới, phải cập nhật tất cả nơi đang giả định trạng thái `0..3`.
- Nếu thay logic chu kỳ, phải kiểm tra `tinhKyHienTai()`, dữ liệu lịch sử và các truy vấn thống kê theo năm/kỳ.
- Nếu chuyển `ct_chiso` sang mô hình mỗi kỳ chỉ có một phiếu, cần thêm unique constraint và xử lý dữ liệu trùng hiện có trước khi migration.
- Nếu triển khai API ngoài hệ thống, nên tách authentication/token khỏi session web và chuẩn hóa response/error code thay vì dùng trực tiếp router hiện tại.
''',
}


for _doc_key in DOCS:
    DOCS[_doc_key] = DOCS[_doc_key].replace(
        "**Phiên bản tài liệu:** 1.0",
        f"**Phiên bản tài liệu:** {DEFAULT_VERSION}",
    )


FORM_INFO = {
    "01_HUONG_DAN_SU_DUNG": {
        "title": "HƯỚNG DẪN SỬ DỤNG HỆ THỐNG QLCL",
        "purpose": "Hướng dẫn người dùng và quản trị viên thao tác đúng trên hệ thống QLCL, từ đăng nhập, quản lý chỉ số, duyệt chỉ số, nhập liệu theo kỳ đến khai thác thống kê và quản trị hệ thống.",
        "scope": "Áp dụng cho người dùng, người duyệt, người nhập liệu khoa/phòng và quản trị viên sử dụng hệ thống QLCL tại Bệnh viện Thống Nhất.",
        "responsibilities": [
            ("Người dùng", "Thực hiện đúng thao tác được phân quyền, nhập dữ liệu đầy đủ và chịu trách nhiệm về dữ liệu đã nhập."),
            ("Người duyệt", "Kiểm tra nội dung chỉ số, duyệt hoặc từ chối và nêu rõ lý do khi từ chối."),
            ("Quản trị viên", "Quản lý tài khoản, quyền, danh mục nền và hỗ trợ người dùng."),
            ("Bộ phận CNTT", "Vận hành kỹ thuật, xử lý sự cố và phối hợp bảo trì hệ thống."),
        ],
    },
    "02_MO_TA_CHUC_NANG": {
        "title": "TÀI LIỆU MÔ TẢ CHỨC NĂNG HỆ THỐNG QLCL",
        "purpose": "Mô tả đầy đủ các phân hệ, chức năng, tác nhân, điều kiện xử lý, dữ liệu đầu vào/đầu ra và các quy tắc nghiệp vụ của hệ thống QLCL.",
        "scope": "Áp dụng cho việc phân tích, nghiệm thu, vận hành, kiểm thử và phát triển các chức năng thuộc hệ thống QLCL.",
        "responsibilities": [
            ("Đơn vị nghiệp vụ", "Xác nhận yêu cầu, quy tắc nghiệp vụ và kết quả chức năng."),
            ("Bộ phận CNTT", "Phân tích, triển khai, kiểm thử và duy trì chức năng theo tài liệu."),
            ("Người quản trị", "Cấu hình tài khoản, quyền và danh mục phục vụ chức năng."),
            ("Người dùng", "Sử dụng chức năng theo đúng vai trò và phản hồi sai lệch nghiệp vụ."),
        ],
    },
    "03_TAI_LIEU_BAO_TRI": {
        "title": "TÀI LIỆU BẢO TRÌ HỆ THỐNG QLCL",
        "purpose": "Quy định thông tin cần thiết để vận hành, sao lưu, xử lý sự cố, triển khai thay đổi và nâng cấp hệ thống QLCL an toàn, có khả năng phục hồi.",
        "scope": "Áp dụng cho cán bộ CNTT và người được giao nhiệm vụ vận hành, bảo trì source code, máy chủ ứng dụng và cơ sở dữ liệu QLCL.",
        "responsibilities": [
            ("Bộ phận CNTT", "Theo dõi hệ thống, sao lưu, xử lý sự cố, triển khai bản cập nhật và quản lý cấu hình."),
            ("Quản trị CSDL", "Theo dõi MySQL/MariaDB, sao lưu, phục hồi và bảo đảm toàn vẹn dữ liệu."),
            ("Đơn vị nghiệp vụ", "Phối hợp xác nhận dữ liệu và kiểm thử sau bảo trì."),
            ("Người triển khai", "Ghi nhận phiên bản, thay đổi, kết quả kiểm thử và phương án rollback."),
        ],
    },
    "04_TAI_LIEU_SOURCE_CODE": {
        "title": "TÀI LIỆU SOURCE CODE HỆ THỐNG QLCL",
        "purpose": "Mô tả cấu trúc source, luồng request, các lớp Action/Peer/View/JavaScript, cấu trúc dữ liệu và các điểm cần lưu ý để người phát triển có thể tiếp quản và mở rộng hệ thống QLCL.",
        "scope": "Áp dụng cho lập trình viên, cán bộ CNTT và đơn vị kỹ thuật thực hiện đọc, sửa, kiểm thử hoặc mở rộng source code QLCL.",
        "responsibilities": [
            ("Lập trình viên", "Tuân thủ cấu trúc source, quyền, quy tắc dữ liệu và cập nhật tài liệu khi thay đổi code."),
            ("Người review", "Kiểm tra ảnh hưởng tới nghiệp vụ, bảo mật, CSDL và tương thích dữ liệu cũ."),
            ("Bộ phận CNTT", "Quản lý source, phiên bản, môi trường và quy trình triển khai."),
            ("Đơn vị nghiệp vụ", "Xác nhận kết quả các thay đổi có ảnh hưởng chức năng."),
        ],
    },
}


def _demote_body_headings(md: str) -> str:
    lines = md.splitlines()
    start = next((i for i, line in enumerate(lines) if line.startswith("## ")), len(lines))
    out = []
    for line in lines[start:]:
        if line.startswith("#### "):
            text = re.sub(r"^\d+(?:\.\d+)*\.?\s*", "", line[5:])
            out.append("##### " + text)
        elif line.startswith("### "):
            text = re.sub(r"^\d+(?:\.\d+)*\.?\s*", "", line[4:])
            out.append("#### " + text)
        elif line.startswith("## "):
            text = re.sub(r"^\d+(?:\.\d+)*\.?\s*", "", line[3:])
            out.append("### " + text)
        else:
            out.append(line)
    return "\n".join(out).strip()


def build_standard_form(stem: str, md: str) -> str:
    info = FORM_INFO[stem]
    resp_rows = "\n".join(f"| {role} | {desc} |" for role, desc in info["responsibilities"])
    detailed = _demote_body_headings(md)
    return f'''# {info["title"]}

**Đơn vị:** Bệnh viện Thống Nhất  
**Mã số:** Chưa cấp  
**Lần ban hành / Phiên bản:** {DEFAULT_VERSION}  
**Ngày cập nhật:** {DATE_TEXT}

## BẢNG THEO DÕI SỬA ĐỔI

| Phiên bản số | Vị trí sửa đổi | Nội dung sửa đổi | Ngày xem xét/sửa đổi | Người xem xét/sửa đổi |
| --- | --- | --- | --- | --- |
| {DEFAULT_VERSION} | Toàn bộ tài liệu | Xây dựng/cập nhật theo source hệ thống QLCL hiện hành | {DATE_TEXT} | Điền khi ban hành |

## DANH SÁCH PHÂN PHỐI TÀI LIỆU

| TT | Nơi nhận | TT | Nơi nhận |
| --- | --- | --- | --- |
| 1 | Ban Giám đốc | 2 | Phòng Công nghệ Thông tin |
| 3 | Phòng Quản lý chất lượng | 4 | Các đơn vị sử dụng hệ thống QLCL |

## 1. Mục đích

{info["purpose"]}

## 2. Phạm vi áp dụng

{info["scope"]}

## 3. Trách nhiệm

| Đối tượng | Trách nhiệm |
| --- | --- |
{resp_rows}

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

{detailed}

## 6. Lưu trữ hồ sơ

| STT | Tên hồ sơ | Mã hiệu | Nơi lưu | Thời gian lưu |
| --- | --- | --- | --- | --- |
| 1 | Tài liệu {info["title"].lower()} | Chưa cấp | Thư mục `docs/QL_CSCL` và kho tài liệu nội bộ | Theo quy định quản lý tài liệu của Bệnh viện |
| 2 | Source code liên quan | Theo repository | Máy chủ/kho source được quản lý | Theo vòng đời hệ thống |
| 3 | CSDL và bản sao lưu liên quan | Theo bản backup | Vị trí backup được quản lý | Theo chính sách sao lưu hiện hành |

## 7. Tài liệu liên quan

| STT | Mã tài liệu | Tên tài liệu |
| --- | --- | --- |
| 1 | QTQL.BVTN.09 | Quy trình yêu cầu xây dựng phần mềm |
| 2 | Chưa cấp | Các tài liệu còn lại trong bộ tài liệu QLCL |
| 3 | Source | `conf/config.php`, `index.php`, `web_src/`, `www/`, `js/`, `sql/` |
'''


for _doc_key in list(DOCS):
    DOCS[_doc_key] = build_standard_form(_doc_key, DOCS[_doc_key])


README = r'''# Bộ tài liệu hệ thống QLCL

Bộ tài liệu được tạo ngày 29/10/2026 cho source `C:\xampp\htdocs\ql_cscl`.

| Tài liệu | Markdown | Word |
| --- | --- | --- |
| Hướng dẫn sử dụng | `01_HUONG_DAN_SU_DUNG.md` | `01_HUONG_DAN_SU_DUNG.docx` |
| Mô tả chức năng | `02_MO_TA_CHUC_NANG.md` | `02_MO_TA_CHUC_NANG.docx` |
| Tài liệu bảo trì | `03_TAI_LIEU_BAO_TRI.md` | `03_TAI_LIEU_BAO_TRI.docx` |
| Tài liệu source code | `04_TAI_LIEU_SOURCE_CODE.md` | `04_TAI_LIEU_SOURCE_CODE.docx` |

Nội dung mô tả theo source hiện tại, bao gồm router, quyền, vòng đời chỉ số, nhập liệu theo kỳ, cấu trúc CSDL, endpoint AJAX và các rủi ro bảo trì đã thấy trong mã nguồn. Bộ tài liệu được trình bày theo cấu trúc của mẫu `QTQL.BVTN.09 - Quy trình yêu cầu xây dựng phần mềm`; các trường hành chính chưa có dữ liệu chính thức được để ở trạng thái `Chưa cấp/Điền khi ban hành`.
'''


def set_cell_shading(cell, fill: str) -> None:
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = tc_pr.find(qn("w:shd"))
    if shd is None:
        shd = OxmlElement("w:shd")
        tc_pr.append(shd)
    shd.set(qn("w:fill"), fill)


def set_repeat_table_header(row) -> None:
    tr_pr = row._tr.get_or_add_trPr()
    tbl_header = OxmlElement("w:tblHeader")
    tbl_header.set(qn("w:val"), "true")
    tr_pr.append(tbl_header)


def add_page_number(paragraph) -> None:
    paragraph.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run = paragraph.add_run("Trang ")
    run.font.size = Pt(9)
    fld = OxmlElement("w:fldSimple")
    fld.set(qn("w:instr"), "PAGE")
    paragraph._p.append(fld)


def set_run_font(run, name="Times New Roman", size=None, bold=None, italic=None, color=None):
    run.font.name = name
    run._element.get_or_add_rPr().rFonts.set(qn("w:ascii"), name)
    run._element.get_or_add_rPr().rFonts.set(qn("w:hAnsi"), name)
    run._element.get_or_add_rPr().rFonts.set(qn("w:eastAsia"), name)
    if size is not None:
        run.font.size = Pt(size)
    if bold is not None:
        run.bold = bold
    if italic is not None:
        run.italic = italic
    if color:
        run.font.color.rgb = RGBColor.from_string(color)


INLINE_RE = re.compile(r"(\*\*[^*]+\*\*|`[^`]+`)")


def add_inline(paragraph, text: str, size=12):
    parts = INLINE_RE.split(text)
    for part in parts:
        if not part:
            continue
        if part.startswith("**") and part.endswith("**"):
            run = paragraph.add_run(part[2:-2])
            set_run_font(run, size=size, bold=True)
        elif part.startswith("`") and part.endswith("`"):
            run = paragraph.add_run(part[1:-1])
            set_run_font(run, name="Consolas", size=max(9, size - 1))
        else:
            run = paragraph.add_run(part)
            set_run_font(run, size=size)


def setup_document(doc: Document, title: str):
    section = doc.sections[0]
    section.top_margin = Cm(2.0)
    section.bottom_margin = Cm(2.0)
    section.left_margin = Cm(3.0)
    section.right_margin = Cm(2.0)

    styles = doc.styles
    normal = styles["Normal"]
    normal.font.name = "Times New Roman"
    normal._element.rPr.rFonts.set(qn("w:ascii"), "Times New Roman")
    normal._element.rPr.rFonts.set(qn("w:hAnsi"), "Times New Roman")
    normal.font.size = Pt(12)
    normal.paragraph_format.space_after = Pt(5)
    normal.paragraph_format.line_spacing = 1.15

    for style_name, size in [("Title", 18), ("Heading 1", 13), ("Heading 2", 12.5), ("Heading 3", 12)]:
        st = styles[style_name]
        st.font.name = "Times New Roman"
        st._element.rPr.rFonts.set(qn("w:ascii"), "Times New Roman")
        st._element.rPr.rFonts.set(qn("w:hAnsi"), "Times New Roman")
        st.font.size = Pt(size)
        st.font.bold = True
        st.font.color.rgb = RGBColor(0, 0, 0)

    footer = section.footer
    fp = footer.paragraphs[0]
    fp.text = "Hệ thống QLCL - Bệnh viện Thống Nhất   |   "
    for r in fp.runs:
        set_run_font(r, size=9, color="666666")
    add_page_number(fp)


def add_title_page(doc: Document, stem: str, md_lines: list[str]):
    info = FORM_INFO[stem]
    title = info["title"]

    head = doc.add_table(rows=2, cols=2)
    head.style = "Table Grid"
    head.alignment = WD_TABLE_ALIGNMENT.CENTER
    head.autofit = True

    p = head.cell(0, 0).paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r = p.add_run("BỘ Y TẾ\nBỆNH VIỆN THỐNG NHẤT")
    set_run_font(r, size=12, bold=True)

    p = head.cell(0, 1).paragraphs[0]
    add_inline(
        p,
        f"**Mã số:** Chưa cấp\n**Lần ban hành / Phiên bản:** {DEFAULT_VERSION}\n**Ngày cập nhật:** {DATE_TEXT}",
        size=11,
    )

    title_cell = head.cell(1, 0).merge(head.cell(1, 1))
    p = title_cell.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.paragraph_format.space_before = Pt(10)
    p.paragraph_format.space_after = Pt(10)
    r = p.add_run("TÀI LIỆU\n" + title)
    set_run_font(r, size=17, bold=True)

    doc.add_paragraph()
    approval = doc.add_table(rows=4, cols=4)
    approval.style = "Table Grid"
    approval.alignment = WD_TABLE_ALIGNMENT.CENTER
    approval.autofit = True
    labels = [
        ["", "Người soạn thảo", "Người xem xét", "Người phê duyệt"],
        ["Chức vụ", "Điền khi ban hành", "Điền khi ban hành", "Điền khi ban hành"],
        ["Ký tên", "", "", ""],
        ["Họ tên", "Điền khi ban hành", "Điền khi ban hành", "Điền khi ban hành"],
    ]
    for ri, row in enumerate(labels):
        for ci, value in enumerate(row):
            cell = approval.cell(ri, ci)
            cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER
            p = cell.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            r = p.add_run(value)
            set_run_font(r, size=10.5, bold=(ri == 0 or ci == 0))
    approval.rows[2].height = Cm(1.8)

    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(14)
    p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    r = p.add_run(
        "Tài liệu này phục vụ quản lý, vận hành và phát triển nội bộ hệ thống QLCL. "
        "Việc sao chụp, phát hành hoặc chuyển giao thực hiện theo quy định quản lý tài liệu của Bệnh viện."
    )
    set_run_font(r, size=10.5, italic=True)
    doc.add_page_break()


def parse_table(lines: list[str], start: int):
    rows = []
    i = start
    while i < len(lines) and lines[i].strip().startswith("|"):
        parts = [x.strip() for x in lines[i].strip().strip("|").split("|")]
        rows.append(parts)
        i += 1
    if len(rows) >= 2 and all(re.fullmatch(r":?-{3,}:?", c or "---") for c in rows[1]):
        rows.pop(1)
    return rows, i


def add_table(doc: Document, rows: list[list[str]]):
    if not rows:
        return
    cols = max(len(r) for r in rows)
    table = doc.add_table(rows=len(rows), cols=cols)
    table.style = "Table Grid"
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = True
    set_repeat_table_header(table.rows[0])
    for ri, row in enumerate(rows):
        for ci in range(cols):
            cell = table.cell(ri, ci)
            cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER
            text = row[ci] if ci < len(row) else ""
            p = cell.paragraphs[0]
            p.paragraph_format.space_after = Pt(0)
            p.paragraph_format.line_spacing = 1.0
            add_inline(p, text, size=9.5)
            if ri == 0:
                set_cell_shading(cell, "E7E6E6")
                for rr in p.runs:
                    rr.bold = True
    doc.add_paragraph().paragraph_format.space_after = Pt(2)


def add_markdown_body(doc: Document, stem: str, md: str):
    lines = md.splitlines()
    add_title_page(doc, stem, lines)
    i = next((idx for idx, line in enumerate(lines) if line.startswith("## ")), 1)
    in_code = False
    code_lines = []
    while i < len(lines):
        raw = lines[i]
        line = raw.rstrip()
        stripped = line.strip()

        if stripped.startswith("```"):
            if not in_code:
                in_code = True
                code_lines = []
            else:
                p = doc.add_paragraph()
                p.paragraph_format.left_indent = Cm(0.5)
                p.paragraph_format.space_before = Pt(4)
                p.paragraph_format.space_after = Pt(6)
                r = p.add_run("\n".join(code_lines))
                set_run_font(r, name="Consolas", size=9)
                pPr = p._p.get_or_add_pPr()
                shd = OxmlElement("w:shd")
                shd.set(qn("w:fill"), "F2F2F2")
                pPr.append(shd)
                in_code = False
            i += 1
            continue
        if in_code:
            code_lines.append(line)
            i += 1
            continue

        if stripped.startswith("|"):
            rows, i = parse_table(lines, i)
            add_table(doc, rows)
            continue

        if not stripped:
            i += 1
            continue

        if stripped.startswith("#### "):
            p = doc.add_paragraph(style="Heading 3")
            add_inline(p, stripped[5:], size=12)
        elif stripped.startswith("### "):
            p = doc.add_paragraph(style="Heading 2")
            add_inline(p, stripped[4:], size=13)
        elif stripped.startswith("## "):
            p = doc.add_paragraph(style="Heading 1")
            add_inline(p, stripped[3:], size=15)
        elif stripped.startswith("# "):
            pass
        elif re.match(r"^\d+\.\s+", stripped):
            p = doc.add_paragraph(style="List Number")
            add_inline(p, re.sub(r"^\d+\.\s+", "", stripped), size=12)
        elif stripped.startswith("- "):
            p = doc.add_paragraph(style="List Bullet")
            add_inline(p, stripped[2:], size=12)
        else:
            p = doc.add_paragraph()
            p.paragraph_format.first_line_indent = Cm(0.6)
            add_inline(p, stripped.replace("  ", " "), size=12)
        i += 1


def write_docx(stem: str, md: str):
    doc = Document()
    setup_document(doc, stem)
    add_markdown_body(doc, stem, md)
    doc.core_properties.title = stem
    doc.core_properties.subject = "Tài liệu hệ thống QLCL"
    doc.core_properties.author = "Bệnh viện Thống Nhất"
    out = ROOT / f"{stem}.docx"
    doc.save(out)
    return out


def main():
    ROOT.mkdir(parents=True, exist_ok=True)
    (ROOT / "00_README_TAI_LIEU.md").write_text(README, encoding="utf-8")
    created = []
    for stem, md in DOCS.items():
        md_path = ROOT / f"{stem}.md"
        md_path.write_text(md.strip() + "\n", encoding="utf-8")
        created.append(md_path)
        created.append(write_docx(stem, md.strip() + "\n"))
    for p in created:
        print(p)


if __name__ == "__main__":
    main()
