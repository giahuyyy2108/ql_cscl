import re
import sys
from pathlib import Path

from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.table import WD_CELL_VERTICAL_ALIGNMENT, WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Pt, RGBColor


SOURCE = Path(sys.argv[1])
OUTPUT = Path(sys.argv[2])


def set_cell_shading(cell, fill):
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = tc_pr.find(qn("w:shd"))
    if shd is None:
        shd = OxmlElement("w:shd")
        tc_pr.append(shd)
    shd.set(qn("w:fill"), fill)


def set_cell_margins(cell, top=100, start=120, bottom=100, end=120):
    tc = cell._tc
    tc_pr = tc.get_or_add_tcPr()
    tc_mar = tc_pr.first_child_found_in("w:tcMar")
    if tc_mar is None:
        tc_mar = OxmlElement("w:tcMar")
        tc_pr.append(tc_mar)
    for name, value in (("top", top), ("start", start), ("bottom", bottom), ("end", end)):
        node = tc_mar.find(qn(f"w:{name}"))
        if node is None:
            node = OxmlElement(f"w:{name}")
            tc_mar.append(node)
        node.set(qn("w:w"), str(value))
        node.set(qn("w:type"), "dxa")


def set_repeat_table_header(row):
    tr_pr = row._tr.get_or_add_trPr()
    tbl_header = OxmlElement("w:tblHeader")
    tbl_header.set(qn("w:val"), "true")
    tr_pr.append(tbl_header)


def set_font(run, name="Times New Roman", size=None, bold=None, color=None):
    run.font.name = name
    run._element.get_or_add_rPr().rFonts.set(qn("w:ascii"), name)
    run._element.get_or_add_rPr().rFonts.set(qn("w:hAnsi"), name)
    if size is not None:
        run.font.size = Pt(size)
    if bold is not None:
        run.bold = bold
    if color is not None:
        run.font.color.rgb = RGBColor(*color)


def add_inline(paragraph, text):
    pattern = re.compile(r"(\*\*.+?\*\*|`.+?`)")
    for part in pattern.split(text):
        if not part:
            continue
        if part.startswith("**") and part.endswith("**"):
            run = paragraph.add_run(part[2:-2])
            set_font(run, bold=True)
        elif part.startswith("`") and part.endswith("`"):
            run = paragraph.add_run(part[1:-1])
            set_font(run, name="Consolas", size=9, color=(60, 60, 60))
        else:
            run = paragraph.add_run(part.replace("  ", " "))
            set_font(run)


def add_page_field(paragraph):
    run = paragraph.add_run()
    fld_begin = OxmlElement("w:fldChar")
    fld_begin.set(qn("w:fldCharType"), "begin")
    instr = OxmlElement("w:instrText")
    instr.set(qn("xml:space"), "preserve")
    instr.text = " PAGE "
    fld_end = OxmlElement("w:fldChar")
    fld_end.set(qn("w:fldCharType"), "end")
    run._r.extend([fld_begin, instr, fld_end])


def add_toc(document):
    p = document.add_paragraph()
    p.style = document.styles["Heading 1"]
    p.add_run("MỤC LỤC")
    entries = (
        "1. Mục đích",
        "2. Phạm vi áp dụng",
        "3. Trách nhiệm",
        "4. Thuật ngữ định nghĩa và chữ viết tắt",
        "5. Nội dung",
        "    Đăng nhập đăng xuất và đổi mật khẩu",
        "    Quản lý chỉ số chất lượng",
        "    Nhập dữ liệu chỉ số",
        "    Theo dõi chỉ số khoa và thống kê",
        "    Quản trị người dùng và nhóm quyền",
        "    Hướng dẫn theo từng quyền người dùng trên giao diện",
        "6. Lưu trữ hồ sơ",
        "7. Tài liệu liên quan",
    )
    for entry in entries:
        toc = document.add_paragraph()
        toc.paragraph_format.left_indent = Cm(0.7) if entry.startswith("    ") else Cm(0)
        toc.paragraph_format.space_after = Pt(3)
        run = toc.add_run(entry.strip())
        set_font(run, size=12, bold=not entry.startswith("    "))


def configure_document(document):
    section = document.sections[0]
    section.top_margin = Cm(2.0)
    section.bottom_margin = Cm(1.8)
    section.left_margin = Cm(2.4)
    section.right_margin = Cm(2.0)
    section.header_distance = Cm(0.9)
    section.footer_distance = Cm(0.9)

    normal = document.styles["Normal"]
    normal.font.name = "Times New Roman"
    normal._element.rPr.rFonts.set(qn("w:ascii"), "Times New Roman")
    normal._element.rPr.rFonts.set(qn("w:hAnsi"), "Times New Roman")
    normal.font.size = Pt(13)
    normal.paragraph_format.space_after = Pt(5)
    normal.paragraph_format.line_spacing = 1.15

    title = document.styles["Title"]
    title.font.name = "Times New Roman"
    title._element.rPr.rFonts.set(qn("w:ascii"), "Times New Roman")
    title._element.rPr.rFonts.set(qn("w:hAnsi"), "Times New Roman")
    title.font.size = Pt(18)
    title.font.bold = True
    title.font.color.rgb = RGBColor(0, 0, 0)
    title.paragraph_format.space_after = Pt(18)

    for style_name, size, before, after in (
        ("Heading 1", 15, 14, 7),
        ("Heading 2", 13, 12, 6),
        ("Heading 3", 11.5, 10, 5),
        ("Heading 4", 10.5, 8, 4),
    ):
        style = document.styles[style_name]
        style.font.name = "Times New Roman"
        style._element.rPr.rFonts.set(qn("w:ascii"), "Times New Roman")
        style._element.rPr.rFonts.set(qn("w:hAnsi"), "Times New Roman")
        style.font.size = Pt(size)
        style.font.bold = True
        style.font.color.rgb = RGBColor(0, 0, 0)
        style.paragraph_format.space_before = Pt(before)
        style.paragraph_format.space_after = Pt(after)
        style.paragraph_format.keep_with_next = True

    header = section.header.paragraphs[0]
    header.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    run = header.add_run("HƯỚNG DẪN SỬ DỤNG HỆ THỐNG QLCL")
    set_font(run, size=9, color=(70, 70, 70))

    footer = section.footer.paragraphs[0]
    footer.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = footer.add_run("Bệnh viện Thống Nhất  |  Trang ")
    set_font(run, size=9, color=(70, 70, 70))
    add_page_field(footer)

    settings = document.settings.element
    update = settings.find(qn("w:updateFields"))
    if update is None:
        update = OxmlElement("w:updateFields")
        settings.append(update)
    update.set(qn("w:val"), "true")


def add_table(document, rows):
    cols = max(len(row) for row in rows)
    table = document.add_table(rows=len(rows), cols=cols)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = True
    table.style = "Table Grid"
    for r_idx, row in enumerate(rows):
        for c_idx in range(cols):
            cell = table.cell(r_idx, c_idx)
            cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER
            set_cell_margins(cell)
            p = cell.paragraphs[0]
            p.paragraph_format.space_after = Pt(0)
            if c_idx < len(row):
                add_inline(p, row[c_idx].strip())
            for run in p.runs:
                set_font(run, size=11, bold=(r_idx == 0), color=(0, 0, 0))
            if r_idx == 0:
                set_cell_shading(cell, "D9E2F3")
            elif r_idx % 2 == 0:
                set_cell_shading(cell, "EAF2F8")
    set_repeat_table_header(table.rows[0])
    document.add_paragraph().paragraph_format.space_after = Pt(1)


def add_centered_text(cell, text, size=13, bold=False):
    p = cell.add_paragraph() if cell.paragraphs[0].text else cell.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run(text)
    set_font(run, size=size, bold=bold)
    return p


def add_template_front_matter(document):
    cover = document.add_table(rows=1, cols=1)
    cover.style = "Table Grid"
    cover.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = cover.cell(0, 0)
    set_cell_margins(cell, top=160, start=180, bottom=160, end=180)
    add_centered_text(cell, "BỘ Y TẾ", 14, True)
    add_centered_text(cell, "BỆNH VIỆN THỐNG NHẤT", 14, True)
    logo_path = SOURCE.parents[2] / "images" / "logo.png"
    if logo_path.exists():
        p = cell.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p.add_run().add_picture(str(logo_path), width=Cm(2.4))
    add_centered_text(cell, "HƯỚNG DẪN SỬ DỤNG", 18, True)
    add_centered_text(cell, "HỆ THỐNG QUẢN LÝ CHẤT LƯỢNG", 17, True)

    metadata = cell.add_table(rows=3, cols=2)
    metadata.style = "Table Grid"
    labels = (("Mã số", "Chưa cấp"), ("Lần ban hành", "01.00"), ("Ngày ban hành", "09/11/2026"))
    for r_idx, values in enumerate(labels):
        for c_idx, value in enumerate(values):
            c = metadata.cell(r_idx, c_idx)
            set_cell_margins(c, top=80, bottom=80)
            p = c.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            run = p.add_run(value)
            set_font(run, size=13, bold=(c_idx == 0))

    approval = cell.add_table(rows=4, cols=3)
    approval.style = "Table Grid"
    approval_data = (
        ("Người soạn thảo", "Người xem xét", "Người phê duyệt"),
        ("Chức vụ\nNhân viên", "Chức vụ\nTP. CNTT", "Chức vụ\nGiám đốc"),
        ("Ký tên\n\n\n", "Ký tên\n\n\n", "Ký tên\n\n\n"),
        ("Họ tên\nVũ Quốc Gia Huy", "Họ tên\nTrần Viết Khôi", "Họ tên\nLê Đình Thanh"),
    )
    for r_idx, row in enumerate(approval_data):
        for c_idx, value in enumerate(row):
            c = approval.cell(r_idx, c_idx)
            set_cell_margins(c, top=90, bottom=90)
            p = c.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            for line_idx, line in enumerate(value.split("\n")):
                if line_idx:
                    p.add_run().add_break()
                run = p.add_run(line)
                set_font(run, size=12, bold=(r_idx == 0 or (r_idx == 3 and line_idx == 1)))

    note = cell.add_paragraph()
    note.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    run = note.add_run("Tài liệu này chỉ được sử dụng trong nội bộ. Mọi sao chụp, in ấn, trích dẫn hoặc chuyển giao phải được sự cho phép của người phê duyệt và người quản lý tài liệu.")
    set_font(run, size=11)

    document.add_page_break()
    p = document.add_paragraph(style="Heading 1")
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run("BẢNG THEO DÕI SỬA ĐỔI")
    revision_rows = [
        ["Phiên bản số", "Vị trí sửa đổi", "Nội dung sửa đổi", "Ngày xem xét sửa đổi", "Người xem xét sửa đổi"],
        ["01.00", "Toàn bộ tài liệu", "Biên soạn hướng dẫn theo từng quyền người dùng và thao tác trên giao diện hệ thống QLCL", "09/11/2026", "Điền khi ban hành"],
    ]
    add_table(document, revision_rows)
    for _ in range(7):
        t = document.tables[-1]
        cells = t.add_row().cells
        for c in cells:
            set_cell_margins(c, top=150, bottom=150)

    document.add_page_break()
    p = document.add_paragraph(style="Heading 1")
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run("DANH SÁCH PHÂN PHỐI TÀI LIỆU")
    distribution = [
        ["TT", "NƠI NHẬN", "TT", "NƠI NHẬN"],
        ["1", "Ban Giám đốc", "2", "Phòng Kế hoạch Tổng hợp"],
        ["3", "Phòng Công nghệ Thông tin", "4", "Phòng Quản lý chất lượng"],
        ["5", "Các khoa phòng sử dụng hệ thống QLCL", "", ""],
    ]
    add_table(document, distribution)

    p = document.add_paragraph()
    run = p.add_run("Người có liên quan phải tuân thủ và thực hiện đúng nội dung của tài liệu. Mỗi đơn vị được cấp một bản kiểm soát hoặc sử dụng bản điện tử trên mạng nội bộ theo quy định quản lý tài liệu của Bệnh viện.")
    set_font(run, size=12)


def build():
    lines = SOURCE.read_text(encoding="utf-8").splitlines()
    document = Document()
    configure_document(document)
    add_template_front_matter(document)

    start_index = next((idx for idx, value in enumerate(lines) if value.strip() == "## 1. Mục đích"), 0)
    lines = lines[start_index:]

    i = 0
    title_done = True
    toc_added = False
    while i < len(lines):
        raw = lines[i].rstrip()
        line = raw.strip()
        if not line:
            i += 1
            continue

        if line.startswith("|") and i + 1 < len(lines) and re.match(r"^\|[\s:|-]+\|$", lines[i + 1].strip()):
            rows = []
            header = [x.strip() for x in line.strip("|").split("|")]
            rows.append(header)
            i += 2
            while i < len(lines) and lines[i].strip().startswith("|"):
                rows.append([x.strip() for x in lines[i].strip().strip("|").split("|")])
                i += 1
            add_table(document, rows)
            continue

        heading = re.match(r"^(#{1,4})\s+(.+)$", line)
        if heading:
            level = len(heading.group(1))
            text = heading.group(2)
            if level == 1 and not title_done:
                p = document.add_paragraph(style="Title")
                p.alignment = WD_ALIGN_PARAGRAPH.CENTER
                add_inline(p, text)
                title_done = True
            else:
                if not toc_added and text == "1. Mục đích":
                    document.add_page_break()
                    add_toc(document)
                    document.add_page_break()
                    toc_added = True
                p = document.add_paragraph(style=f"Heading {level - 1 if level > 1 else 1}")
                add_inline(p, text)
            i += 1
            continue

        list_match = re.match(r"^(\d+)\.\s+(.+)$", line)
        bullet_match = re.match(r"^-\s+(.+)$", line)
        if list_match:
            p = document.add_paragraph(style="List Number")
            add_inline(p, list_match.group(2))
            i += 1
            continue
        if bullet_match:
            p = document.add_paragraph(style="List Bullet")
            add_inline(p, bullet_match.group(1))
            i += 1
            continue

        p = document.add_paragraph()
        if title_done and not toc_added and line.startswith("**"):
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        add_inline(p, line)
        i += 1

    OUTPUT.parent.mkdir(parents=True, exist_ok=True)
    document.save(OUTPUT)


if __name__ == "__main__":
    build()
