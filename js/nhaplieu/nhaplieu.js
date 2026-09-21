var tableNhapLieu;
var duLieuDaNhap = {};
var thangBatDauChuKy = 1;
var namBatDauChuKy = 0;
var bieuMauNhap = { version: 1, cau_hoi: [] };

function tenDanhMuc(selectId, value) {
    return $('#' + selectId + ' option[value="' + value + '"]').text() || value || '';
}

function hienThi(value) {
    return value === null || value === undefined || value === '' ? '-' : value;
}

function hienThiTomTatCauTraLoi(container, duLieu) {
    var tyLe = duLieu.ty_le_phan_tram;

    // Tương thích dữ liệu cũ dạng tử số/mẫu số.
    if (tyLe === undefined && duLieu.value !== undefined) {
        tyLe = duLieu.value;
    }

    if (tyLe === undefined || tyLe === null || tyLe === '') {
        $('<span>', { 'class': 'text-muted', text: '-' }).appendTo(container);
        return;
    }

    $('<strong>', {
        'class': 'text-success',
        text: tyLe + '%'
    }).appendTo(container);
}

function giaTriCauTraLoiDaNhap(duLieu) {
    var ketQua = {};
    if (!duLieu || !duLieu.cau_tra_loi) return ketQua;

    if (!Array.isArray(duLieu.cau_tra_loi) && typeof duLieu.cau_tra_loi === 'object') {
        return duLieu.cau_tra_loi;
    }

    duLieu.cau_tra_loi.forEach(function (item) {
        ketQua[String(item.id)] = item.gia_tri;
    });
    return ketQua;
}

function hienThiBieuMauNhap(duLieu) {
    var khuVuc = $('#nhap_bieu_mau').empty();
    var cauHoi = bieuMauNhap && Array.isArray(bieuMauNhap.cau_hoi) ? bieuMauNhap.cau_hoi : [];
    var daNhap = giaTriCauTraLoiDaNhap(duLieu);

    if (!cauHoi.length) {
        $('#nhap_ket_qua_diem').hide().empty();
        khuVuc.append($('<div>', {
            'class': 'alert alert-warning',
            text: 'Chỉ tiêu chưa được thiết kế biểu mẫu nhập liệu.'
        }));
        $('#btnLuuNhapLieu').prop('disabled', true);
        return;
    }

    $('#btnLuuNhapLieu').prop('disabled', false);
    cauHoi.forEach(function (item, index) {
        var id = String(item.id || ('q' + (index + 1)));
        var loai = item.loai || 'short_text';
        var giaTri = daNhap[id];
        var khoi = $('<div>', {
            'class': 'form-group survey-entry-question',
            'data-question-id': id,
            'data-question-type': loai
        });
        var nhan = $('<label>').text((index + 1) + '. ' + (item.noi_dung || 'Câu hỏi'));
        if (item.bat_buoc) nhan.append($('<span>', { 'class': 'text-danger', text: ' *' }));
        khoi.append(nhan);

        if (loai === 'long_text') {
            khoi.append($('<textarea>', { 'class': 'form-control survey-entry-answer', rows: 3 }).val(giaTri || ''));
        } else if (loai === 'radio' || loai === 'checkbox') {
            (item.lua_chon || []).forEach(function (luaChon, optionIndex) {
                var noiDungLuaChon = typeof luaChon === 'object' ? luaChon.noi_dung : luaChon;
                var diemLuaChon = typeof luaChon === 'object' ? luaChon.diem : 0;
                var input = $('<input>', {
                    type: loai,
                    name: 'cau_hoi_' + index + (loai === 'checkbox' ? '[]' : ''),
                    value: noiDungLuaChon,
                    'class': 'survey-entry-answer'
                });
                if (loai === 'checkbox') {
                    input.prop('checked', Array.isArray(giaTri) && giaTri.indexOf(noiDungLuaChon) !== -1);
                } else {
                    input.prop('checked', String(giaTri || '') === String(noiDungLuaChon));
                }
                khoi.append($('<div>', { 'class': loai }).append(
                    $('<label>').append(input, document.createTextNode(' ' + noiDungLuaChon + ' (' + diemLuaChon + ' điểm)'))
                ));
            });
        } else if (loai === 'select' || loai === 'score') {
            var select = $('<select>', { 'class': 'form-control survey-entry-answer' })
                .append($('<option>', { value: '', text: '-- Chọn câu trả lời --' }));
            if (loai === 'score') {
                for (var diem = 1; diem <= 10; diem++) {
                    select.append($('<option>', { value: diem, text: diem + ' điểm' }));
                }
            } else {
                (item.lua_chon || []).forEach(function (luaChon) {
                    var noiDungLuaChon = typeof luaChon === 'object' ? luaChon.noi_dung : luaChon;
                    var diemLuaChon = typeof luaChon === 'object' ? luaChon.diem : 0;
                    select.append($('<option>', {
                        value: noiDungLuaChon,
                        text: noiDungLuaChon + ' (' + diemLuaChon + ' điểm)'
                    }));
                });
            }
            select.val(giaTri || '');
            khoi.append(select);
        } else {
            khoi.append($('<input>', {
                type: loai === 'number' ? 'number' : (loai === 'date' ? 'date' : 'text'),
                step: loai === 'number' ? 'any' : undefined,
                'class': 'form-control survey-entry-answer',
                value: giaTri || ''
            }));
        }

        khuVuc.append(khoi);
    });

    tinhPhanTramTamTinh();
}

function layCauTraLoiBieuMau() {
    var ketQua = {};

    $('#nhap_bieu_mau .survey-entry-question').each(function () {
        var cauHoi = $(this);
        var id = String(cauHoi.data('question-id'));
        var loai = cauHoi.data('question-type');

        if (loai === 'checkbox') {
            ketQua[id] = cauHoi.find('.survey-entry-answer:checked').map(function () {
                return this.value;
            }).get();
        } else if (loai === 'radio') {
            ketQua[id] = cauHoi.find('.survey-entry-answer:checked').val() || '';
        } else {
            ketQua[id] = cauHoi.find('.survey-entry-answer').val() || '';
        }
    });

    return ketQua;
}

function tinhPhanTramTamTinh() {
    var cauTraLoi = layCauTraLoiBieuMau();
    var cauHoi = bieuMauNhap && Array.isArray(bieuMauNhap.cau_hoi) ? bieuMauNhap.cau_hoi : [];
    var tongDiem = 0;
    var diemToiDa = 0;

    cauHoi.forEach(function (item, index) {
        var id = String(item.id || ('q' + (index + 1)));
        var loai = item.loai || 'short_text';
        var giaTri = cauTraLoi[id];
        var bangDiem = {};

        (item.lua_chon || []).forEach(function (luaChon) {
            var noiDung = typeof luaChon === 'object' ? luaChon.noi_dung : luaChon;
            bangDiem[noiDung] = typeof luaChon === 'object' ? (parseFloat(luaChon.diem) || 0) : 0;
        });

        var cacMucDiem = Object.keys(bangDiem).map(function (key) { return bangDiem[key]; });
        if (loai === 'radio' || loai === 'select') {
            tongDiem += bangDiem[giaTri] || 0;
            diemToiDa += cacMucDiem.length ? Math.max(0, Math.max.apply(Math, cacMucDiem)) : 0;
        } else if (loai === 'checkbox') {
            (Array.isArray(giaTri) ? giaTri : []).forEach(function (daChon) {
                tongDiem += bangDiem[daChon] || 0;
            });
            cacMucDiem.forEach(function (diem) {
                if (diem > 0) diemToiDa += diem;
            });
        } else if (loai === 'score') {
            tongDiem += parseFloat(giaTri) || 0;
            diemToiDa += 10;
        }
    });

    if (diemToiDa <= 0) {
        $('#nhap_ket_qua_diem').hide().empty();
        return;
    }

    var phanTram = Math.round((tongDiem / diemToiDa * 100) * 100) / 100;
    $('#nhap_ket_qua_diem')
        .text('Tổng điểm: ' + tongDiem + '/' + diemToiDa + ' — Tỷ lệ: ' + phanTram + '%')
        .show();
}

$('#nhap_bieu_mau').on('change input', '.survey-entry-answer', tinhPhanTramTamTinh);

tableNhapLieu = $('#datatable-nhaplieu').DataTable({
    ordering: false,
    responsive: true,
    autoWidth: false,
    ajax: {
        url: $('#ULocal').val() + 'NhapLieu/getData/',
        type: 'POST',
        dataSrc: function (json) {
            var thongKe = json && json.thong_ke ? json.thong_ke : {};
            $('#tk_tong').text(thongKe.tong || 0);
            $('#tk_da_nhap').text(thongKe.da_nhap || 0);
            $('#tk_chua_nhap').text(thongKe.chua_nhap || 0);
            return json && Array.isArray(json.data) ? json.data : [];
        }
    },
    columns: [
        { data: 'ma_chi_so' },
        { data: 'ten_chi_so' },
        { data: 'pham_vi', render: function (data) { return tenDanhMuc('dm_pham_vi', data); } },
        { data: 'id_chuky', render: function (data) { return tenDanhMuc('dm_chu_ky', data); } },
        { data: 'da_nhap_ky_hien_tai', render: function (data) {
            return data
                ? '<span class="label label-success"><i class="fa fa-check"></i> Đã nhập</span>'
                : '<span class="label label-warning"><i class="fa fa-exclamation-circle"></i> Chưa nhập</span>';
        } },
        { data: null, searchable: false, render: function () {
            return '<button type="button" class="btn btn-info btn-sm btn-xem" title="Xem"><i class="fa fa-eye"></i></button> ' +
                '<button type="button" class="btn btn-primary btn-sm btn-nhaplieu" title="Nhập biểu mẫu"><i class="fa fa-list-alt"></i> Nhập biểu mẫu</button>';
        } }
    ]
});
function layDong(button) {
    var tr = $(button).closest('tr');
    if (tr.hasClass('child')) tr = tr.prev();
    return tableNhapLieu.row(tr).data();
}

$('#datatable-nhaplieu').on('click', '.btn-xem', function () {
    var row = layDong(this);
    if (!row) return;
    var donVi = row.donvitinh && row.donvitinh.ten ? ' ' + row.donvitinh.ten : '';
    var maPhong = (row.phong || [row.id_khoaphong]).map(String);
    var cayKhoaPhong = $('#xem_khoa_phong').empty();

    $('#dm_khoi option').each(function () {
        var idKhoi = String($(this).val());
        var tenKhoi = $(this).text().trim();
        var khoaPhong = $('#dm_khoa_phong option[data-khoi="' + idKhoi + '"]').filter(function () {
            return maPhong.indexOf(String($(this).val())) !== -1;
        });

        if (!khoaPhong.length) return;

        var nhomKhoi = $('<div>').css('margin-bottom', '8px');
        $('<div>')
            .css('font-weight', 'bold')
            .append($('<i>').addClass('fa fa-folder-open-o').css('margin-right', '6px'))
            .append(document.createTextNode(tenKhoi))
            .appendTo(nhomKhoi);

        var danhSach = $('<ul>').css({ margin: '4px 0 0 24px', paddingLeft: '16px' });
        khoaPhong.each(function () {
            $('<li>').text($(this).text().trim()).appendTo(danhSach);
        });
        nhomKhoi.append(danhSach).appendTo(cayKhoaPhong);
    });

    if (!cayKhoaPhong.children().length || row.pham_vi == 3) {
        cayKhoaPhong.text('-');
    }

    $('#xem_ma_chi_so').text(hienThi(row.ma_chi_so));
    $('#xem_ten_chi_so').text(hienThi(row.ten_chi_so));
    $('#xem_trang_thai').text(hienThi(row.trang_thai && row.trang_thai.tenTrangThai));
    $('#xem_khia_canh').text(hienThi(tenDanhMuc('dm_khia_canh', row.ma_khia_canh)));
    $('#xem_thanh_to').text(hienThi(tenDanhMuc('dm_thanh_to', row.ma_thanh_to)));
    $('#xem_pham_vi').text(hienThi(tenDanhMuc('dm_pham_vi', row.pham_vi)));
    $('#xem_chu_ky').text(hienThi(tenDanhMuc('dm_chu_ky', row.id_chuky)));
    $('#xem_muc_tieu').text(hienThi(row.muc_tieu) + donVi);
    $('#xem_nguong_canh_bao').text(hienThi(row.nguong_canh_bao) + donVi);
    $('#xem_nguoi_gui').text(hienThi(row.nguoi_gui && row.nguoi_gui.hoTen));
    $('#xem_nguoi_duyet').text(hienThi(row.nguoi_duyet && row.nguoi_duyet.hoTen));
    $('#xem_dinh_nghia').text(hienThi(row.dinh_nghia));
    $('#khoa_user').text(hienThi(row.khoaphong.TenKhoaPhong));
    $('#xem_thu_thap').text(hienThi(row.thu_thap));khoa_user
    $('#xem_tu_so').text(hienThi(row.ten_tu_so));
    $('#xem_mau_so').text(hienThi(row.ten_mau_so));
    taiDuLieuChuKyXem(row.ma_chi_so);
    $('#modalXemChiTieu').modal('show');
});

function taiDuLieuChuKyXem(maChiSo) {
    var tieuDe = $('#xem_tieu_de_chu_ky').empty();
    var noiDung = $('#xem_du_lieu_chu_ky').empty();
    var thongBaoLoi = $('#xem_loi_du_lieu_chu_ky').hide().text('');
    tieuDe.append($('<th>').text('Đang tải...'));
    noiDung.append($('<td>').html('<i class="fa fa-spinner fa-spin"></i>'));

    $.ajax({
        url: $('#ULocal').val() + 'NhapLieu/getTrangThaiNhap/',
        type: 'POST',
        dataType: 'json',
        data: { ma_chi_so: maChiSo },
        success: function (response) {
            tieuDe.empty();
            noiDung.empty();

            if (!response.success) {
                thongBaoLoi.text(response.message || 'Không tải được dữ liệu chu kỳ.').show();
                return;
            }

            var soChuKy = parseInt(response.so_chu_ky, 10) || 0;
            var kyHienTai = parseInt(response.ky, 10) || 0;
            var thangBatDau = parseInt(response.thang_bat_dau, 10) || 1;
            var namBatDau = parseInt(response.nam_bat_dau, 10) || parseInt(response.nam, 10);
            var duLieuTheoKy = {};
            bieuMauNhap = response.bieumau && Array.isArray(response.bieumau.cau_hoi)
                ? response.bieumau
                : { version: 1, cau_hoi: [] };

            (response.da_nhap || []).forEach(function (item) {
                var ky = String(item.ky);
                duLieuTheoKy[ky] = item.du_lieu && item.du_lieu[ky]
                    ? item.du_lieu[ky]
                    : item.du_lieu;
            });

            for (var i = 1; i <= soChuKy; i++) {
                $('<th>')
                    .css({ minWidth: '130px', textAlign: 'center' })
                    .text(tenKyTheoMoc(i, soChuKy, thangBatDau, namBatDau))
                    .appendTo(tieuDe);

                var cell = $('<td>').css('vertical-align', 'middle');
                var duLieu = duLieuTheoKy[String(i)];
                if (duLieu) {
                    hienThiTomTatCauTraLoi(cell, duLieu);
                } else if (i > kyHienTai) {
                    $('<span>').addClass('label label-default').text('Chưa đến kỳ').appendTo(cell);
                } else {
                    $('<span>').addClass('label label-warning').text('Chưa nhập').appendTo(cell);
                }
                cell.appendTo(noiDung);
            }
        },
        error: function () {
            tieuDe.empty();
            noiDung.empty();
            thongBaoLoi.text('Có lỗi xảy ra khi tải dữ liệu chu kỳ.').show();
        }
    });
}

$('#datatable-nhaplieu').on('click', '.btn-nhaplieu', function () {
    var row = layDong(this);
    if (!row) return;
    var donVi = row.donvitinh && row.donvitinh.ten ? ' ' + row.donvitinh.ten : '';
    $('#nhap_ma_chi_so').val(row.ma_chi_so);
    $('#nhap_hien_thi_ma_chi_so').text(hienThi(row.ma_chi_so));
    $('#nhap_ten_chi_so').text(row.ten_chi_so || '-');
    $('#nhap_chu_ky').text(hienThi(tenDanhMuc('dm_chu_ky', row.id_chuky)));
    $('#nhap_muc_tieu').text(hienThi(row.muc_tieu) + donVi);
    $('#nhap_nguong_canh_bao').text(hienThi(row.nguong_canh_bao) + donVi);
    $('#nhap_nam').val(new Date().getFullYear());
    $('#nhap_ky').empty();
    $('#nhap_bieu_mau').empty();
    taiTrangThaiNhap();
    $('#modalNhapLieu').modal('show');
});

function tenKy(ky, soChuKy) {
    if (soChuKy === 12 && namBatDauChuKy) {
        var ngayKy = new Date(namBatDauChuKy, thangBatDauChuKy - 1 + ky - 1, 1);
        return 'Tháng ' + (ngayKy.getMonth() + 1) + '/' + ngayKy.getFullYear();
    }
    if (soChuKy === 4) return 'Quý ' + ky;
    if (soChuKy === 2) return '6 tháng ' + ky;
    if (soChuKy === 1) return 'Năm';
    return 'Kỳ ' + ky;
}

function tenKyTheoMoc(ky, soChuKy, thangBatDau, namBatDau) {
    if (soChuKy === 12) {
        var ngayKy = new Date(namBatDau, thangBatDau - 1 + ky - 1, 1);
        return 'Tháng ' + (ngayKy.getMonth() + 1) + '/' + ngayKy.getFullYear();
    }
    if (soChuKy === 4) return 'Quý ' + ky;
    if (soChuKy === 2) return '6 tháng ' + ky;
    if (soChuKy === 1) return 'Năm';
    return 'Kỳ ' + ky;
}

function taiTrangThaiNhap() {
    var maChiSo = $('#nhap_ma_chi_so').val();
    if (!maChiSo) return;

    $.ajax({
        url: $('#ULocal').val() + 'NhapLieu/getTrangThaiNhap/',
        type: 'POST',
        dataType: 'json',
        data: { ma_chi_so: maChiSo },
        success: function (response) {
            if (!response.success) {
                Swal.fire('Lỗi', response.message || 'Không tải được dữ liệu nhập.', 'error');
                return;
            }

            var soChuKy = parseInt(response.so_chu_ky, 10) || 0;
            var nam = parseInt(response.nam, 10);
            var kyHienTai = parseInt(response.ky, 10);
            var kyDangChon = parseInt($('#nhap_ky').val(), 10);
            thangBatDauChuKy = parseInt(response.thang_bat_dau, 10);
            namBatDauChuKy = parseInt(response.nam_bat_dau, 10);
            bieuMauNhap = response.bieumau && Array.isArray(response.bieumau.cau_hoi)
                ? response.bieumau
                : { version: 1, cau_hoi: [] };
            duLieuDaNhap = {};

            (response.da_nhap || []).forEach(function (item) {
                var ky = String(item.ky);
                duLieuDaNhap[ky] = item.du_lieu && item.du_lieu[ky]
                    ? item.du_lieu[ky]
                    : item.du_lieu;
            });

            $('#nhap_so_chu_ky').val(soChuKy);
            $('#nhap_nam').val(nam);
            var options = '';
            for (var i = 1; i <= kyHienTai; i++) {
                options += '<option value="' + i + '">' + tenKy(i, soChuKy) + '</option>';
            }
            $('#nhap_ky').html(options).val(
                kyDangChon >= 1 && kyDangChon <= kyHienTai ? kyDangChon : kyHienTai
            );
            $('#nhap_ky_hien_tai').text(
                'Kỳ nhập hiện tại: ' + tenKy(kyHienTai, soChuKy) +
                ' – chu kỳ bắt đầu từ tháng ' + response.thang_bat_dau + '/' + response.nam_bat_dau
            );

            var daNhap = Object.keys(duLieuDaNhap).length;
            $('#tong_quan_chu_ky').text(
                'Đã nhập ' + daNhap + '/' + kyHienTai + ' chu kỳ đến thời điểm hiện tại trong năm chu kỳ ' + nam + '.'
            );
            hienThiDuLieuKy();
        }
    });
}

function hienThiDuLieuKy() {
    var ky = String($('#nhap_ky').val() || '');
    var duLieu = duLieuDaNhap[ky];
    var trangThai = $('#trang_thai_ky');

    if (duLieu) {
        trangThai.removeClass('alert-info').addClass('alert-warning')
            .text('Chu kỳ này đã được nhập. Lưu lại sẽ cập nhật dữ liệu cũ.').show();
    } else {
        trangThai.removeClass('alert-warning').addClass('alert-info')
            .text('Chu kỳ này chưa được nhập liệu.').show();
    }

    hienThiBieuMauNhap(duLieu);
}

$('#nhap_ky').on('change', hienThiDuLieuKy);

$('#formNhapLieu').on('submit', function (event) {
    event.preventDefault();
    var button = $('#btnLuuNhapLieu');

    $.ajax({
        url: $('#ULocal').val() + 'NhapLieu/save/',
        type: 'POST',
        dataType: 'json',
        data: {
            ma_chi_so: $('#nhap_ma_chi_so').val(),
            nam: $('#nhap_nam').val(),
            ky: $('#nhap_ky').val(),
            du_lieu: JSON.stringify({ cau_tra_loi: layCauTraLoiBieuMau() })
        },
        beforeSend: function () {
            button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');
        },
        success: function (response) {
            if (response.success) {
                Swal.fire('Thành công', response.message, 'success');
                taiTrangThaiNhap();
                tableNhapLieu.ajax.reload(null, false);
            } else {
                Swal.fire('Không thể lưu', response.message || 'Dữ liệu không hợp lệ.', 'error');
            }
        },
        error: function () {
            Swal.fire('Lỗi', 'Có lỗi xảy ra khi lưu nhập liệu.', 'error');
        },
        complete: function () {
            var coCauHoi = bieuMauNhap && Array.isArray(bieuMauNhap.cau_hoi) && bieuMauNhap.cau_hoi.length > 0;
            button.prop('disabled', !coCauHoi).html('<i class="fa fa-save"></i> Lưu biểu mẫu');
        }
    });
});
