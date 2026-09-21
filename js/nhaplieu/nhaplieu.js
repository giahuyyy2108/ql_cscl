var tableNhapLieu;
var duLieuDaNhap = {};
var thangBatDauChuKy = 1;
var namBatDauChuKy = 0;

function tenDanhMuc(selectId, value) {
    return $('#' + selectId + ' option[value="' + value + '"]').text() || value || '';
}

function hienThi(value) {
    return value === null || value === undefined || value === '' ? '-' : value;
}

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
                '<button type="button" class="btn btn-primary btn-sm btn-nhaplieu" title="Nhập liệu"><i class="fa fa-keyboard-o"></i> Nhập liệu</button>';
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

    if (!cayKhoaPhong.children().length) {
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
                    $('<div>').append($('<strong>').text('Tử số: '), document.createTextNode(duLieu.tu_so)).appendTo(cell);
                    $('<div>').append($('<strong>').text('Mẫu số: '), document.createTextNode(duLieu.mau_so)).appendTo(cell);
                    $('<div>').append($('<strong>').text('Kết quả: '), document.createTextNode(duLieu.value + '%')).appendTo(cell);
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
    $('#nhap_tu_so, #nhap_mau_so, #nhap_value').val('');
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
        $('#nhap_tu_so').val(duLieu.tu_so);
        $('#nhap_mau_so').val(duLieu.mau_so);
        $('#nhap_value').val(duLieu.value);
        trangThai.removeClass('alert-info').addClass('alert-warning')
            .text('Chu kỳ này đã được nhập. Lưu lại sẽ cập nhật dữ liệu cũ.').show();
    } else {
        $('#nhap_tu_so, #nhap_mau_so, #nhap_value').val('');
        trangThai.removeClass('alert-warning').addClass('alert-info')
            .text('Chu kỳ này chưa được nhập liệu.').show();
    }
}

$('#nhap_ky').on('change', hienThiDuLieuKy);

$('#nhap_tu_so, #nhap_mau_so').on('input', function () {
    var tuSo = parseFloat($('#nhap_tu_so').val());
    var mauSo = parseFloat($('#nhap_mau_so').val());
    $('#nhap_value').val(!isNaN(tuSo) && tuSo !== 0 && !isNaN(mauSo)
        ? (mauSo / tuSo * 100).toFixed(2)
        : '');
});

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
            tu_so: $('#nhap_tu_so').val(),
            mau_so: $('#nhap_mau_so').val()
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
            button.prop('disabled', false).html('<i class="fa fa-save"></i> Lưu');
        }
    });
});
