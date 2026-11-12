/* DATA TABLES */
var table;
var chisoBieuDo = null;
var chisoBieuDoCauHoi = null;
var chisoDuLieuCauHoi = [];
var chisoThongKeTable = null;
var chisoDuLieuTheoThang = [];
var chisoDangXem = { maChiSo: 0, soChuKy: 0 };

function chisoTenKy(ky) {
    if (chisoDangXem.soChuKy === 12) return 'Tháng ' + ky;
    if (chisoDangXem.soChuKy === 4) return 'Quý ' + ky;
    if (chisoDangXem.soChuKy === 2) return '6 tháng ' + ky;
    if (chisoDangXem.soChuKy === 1) return 'Năm';
    return 'Kỳ ' + ky;
}

function chisoDinhDangThoiGian(value) {
    if (!value) return '-';
    var parts = String(value).split(' ');
    var dateParts = parts[0].split('-');
    if (dateParts.length !== 3) return value;
    return dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0] + (parts[1] ? ' ' + parts[1].slice(0, 5) : '');
}

function chisoChiaDongTooltip(value, doDaiToiDa) {
    var cacTu = String(value || '').trim().split(/\s+/);
    var cacDong = [];
    var dong = '';
    cacTu.forEach(function (tu) {
        if (dong && (dong + ' ' + tu).length > doDaiToiDa) {
            cacDong.push(dong);
            dong = tu;
        } else {
            dong += (dong ? ' ' : '') + tu;
        }
    });
    if (dong) cacDong.push(dong);
    return cacDong;
}

function veChisoBieuDoCauHoi() {
    var cauHoi = chisoDuLieuCauHoi;
    var canvas = $('#chiso_bieu_do_cau_hoi');
    var empty = $('#chiso_bieu_do_cau_hoi_empty').hide();
    if (chisoBieuDoCauHoi) chisoBieuDoCauHoi.destroy();
    if (!cauHoi.length) {
        canvas.hide();
        empty.show();
        return;
    }

    var theoKhoaPhong = $('#chiso_kieu_thong_ke_cau_hoi').val() === 'khoa_phong';
    var datasets;
    if (theoKhoaPhong) {
        var danhSachKhoa = {};
        cauHoi.forEach(function (item) {
            (item.theo_khoa_phong || []).forEach(function (khoa) {
                danhSachKhoa[String(khoa.id_khoaphong)] = khoa.ten_khoaphong;
            });
        });
        var mauSac = ['#337ab7', '#26b99a', '#f0ad4e', '#d9534f', '#5bc0de', '#8e44ad', '#7f8c8d', '#2c3e50'];
        datasets = Object.keys(danhSachKhoa).map(function (idKhoa, index) {
            var mau = mauSac[index % mauSac.length];
            return {
                label: danhSachKhoa[idKhoa],
                data: cauHoi.map(function (item) {
                    var thongKe = (item.theo_khoa_phong || []).filter(function (khoa) {
                        return String(khoa.id_khoaphong) === idKhoa;
                    })[0];
                    return thongKe ? parseFloat(thongKe.trung_binh) : null;
                }),
                backgroundColor: mau, borderColor: mau, borderWidth: 1
            };
        });
    } else {
        datasets = [{
            label: 'Tỷ lệ trung bình (%)',
            data: cauHoi.map(function (item) { return parseFloat(item.trung_binh); }),
            backgroundColor: 'rgba(38,185,154,.65)', borderColor: '#26b99a', borderWidth: 1
        }];
    }

    canvas.show();
    chisoBieuDoCauHoi = new Chart(canvas[0].getContext('2d'), {
        type: 'bar',
        data: { labels: cauHoi.map(function (item) { return item.ky_hieu; }), datasets: datasets },
        options: {
            responsive: true, maintainAspectRatio: false,
            tooltips: { callbacks: {
                title: function (items) {
                    if (!items || !items.length) return '';
                    var thongKe = cauHoi[items[0].index];
                    return thongKe.noi_dung
                        ? [thongKe.ky_hieu].concat(chisoChiaDongTooltip(thongKe.noi_dung, 55))
                        : [thongKe.ky_hieu];
                },
                label: function (item, data) {
                    return data.datasets[item.datasetIndex].label + ': ' + item.yLabel + '%';
                },
                afterLabel: function (item) {
                    if (!theoKhoaPhong) return 'Số phiếu: ' + cauHoi[item.index].so_phieu;
                    var idKhoa = Object.keys(danhSachKhoa)[item.datasetIndex];
                    var thongKe = (cauHoi[item.index].theo_khoa_phong || []).filter(function (khoa) {
                        return String(khoa.id_khoaphong) === idKhoa;
                    })[0];
                    return 'Số phiếu: ' + (thongKe ? thongKe.so_phieu : 0);
                }
            } },
            scales: { yAxes: [{ ticks: { beginAtZero: true, max: 100 }, scaleLabel: { display: true, labelString: 'Tỷ lệ trung bình (%)' } }] }
        }
    });
}

$('#chiso_kieu_thong_ke_cau_hoi').on('change', veChisoBieuDoCauHoi);

function taiChisoBieuDo() {
    var loading = $('#chiso_bieu_do_loading').show();
    var empty = $('#chiso_bieu_do_empty').hide();
    var canvas = $('#chiso_bieu_do_chu_ky').show();
    var loadingCauHoi = $('#chiso_bieu_do_cau_hoi_loading').show();
    var emptyCauHoi = $('#chiso_bieu_do_cau_hoi_empty').hide();
    var canvasCauHoi = $('#chiso_bieu_do_cau_hoi').show();
    if (chisoBieuDo) chisoBieuDo.destroy();
    if (chisoBieuDoCauHoi) chisoBieuDoCauHoi.destroy();

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/getBieuDoChuKy/',
        type: 'POST', dataType: 'json',
        data: { ma_chi_so: chisoDangXem.maChiSo },
        success: function (response) {
            var data = response && response.success && Array.isArray(response.data)
                ? response.data.filter(function (item) { return isFinite(parseFloat(item.trung_binh)) && parseInt(item.so_phieu, 10) > 0; })
                : [];
            loading.hide();
            loadingCauHoi.hide();
            if (!data.length) {
                canvas.hide();
                empty.text(response && response.message ? response.message : 'Chưa có dữ liệu nhập liệu để hiển thị.').show();
            } else {
                chisoBieuDo = new Chart(canvas[0].getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: data.map(function (item) { return chisoTenKy(item.ky) + '/' + item.nam; }),
                        datasets: [{
                            label: 'Trung bình toàn bộ phiếu',
                            data: data.map(function (item) { return parseFloat(item.trung_binh); }),
                            borderColor: '#337ab7', backgroundColor: 'rgba(51,122,183,.12)',
                            pointBackgroundColor: '#337ab7', borderWidth: 2, pointRadius: 4, fill: true, lineTension: 0.2
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        tooltips: { callbacks: { label: function (item) { return 'Trung bình: ' + item.yLabel + '%'; } } },
                        scales: { yAxes: [{ ticks: { beginAtZero: true, max: 100 }, scaleLabel: { display: true, labelString: 'Tỷ lệ trung bình (%)' } }] }
                    }
                });
            }

            chisoDuLieuCauHoi = response && Array.isArray(response.cau_hoi) ? response.cau_hoi : [];
            veChisoBieuDoCauHoi();
        },
        error: function () {
            loading.hide(); canvas.hide(); empty.text('Không tải được dữ liệu biểu đồ.').show();
            loadingCauHoi.hide(); canvasCauHoi.hide(); emptyCauHoi.text('Không tải được dữ liệu biểu đồ câu hỏi.').show();
        }
    });
}

function khoiTaoChisoThongKe() {
    if (chisoThongKeTable) return;
    chisoThongKeTable = $('#datatable-chiso-thongke').DataTable({
        ordering: false, responsive: true, autoWidth: false, processing: true,
        lengthChange: false, paging: false, scrollY: '50vh', scrollCollapse: true,
        ajax: {
            url: $('#ULocal').val() + 'chisochatluong/getDanhSachPhieu/',
            type: 'POST',
            data: function (data) { data.ma_chi_so = chisoDangXem.maChiSo; },
            dataSrc: function (response) { return response && response.success && Array.isArray(response.data) ? response.data : []; }
        },
        columns: [
            { data: 'id' },
            { data: 'ten_khoaphong', render: function (data) { return data || '-'; } },
            { data: 'ky', render: function (data) { return chisoTenKy(data); } },
            { data: 'tong_diem' }, { data: 'diem_toi_da' },
            { data: 'ty_le_phan_tram', render: function (data) { return '<strong class="text-success">' + data + '%</strong>'; } },
            { data: 'nguoi_nhap', render: function (data) { return data || '-'; } },
            { data: 'updated_at', render: function (data) { return chisoDinhDangThoiGian(data); } }
        ],
        language: { emptyTable: 'Chưa có phiếu nhập liệu', processing: 'Đang tải dữ liệu...' }
    });
}

$('#chiso-bieudo-tab').on('shown.bs.tab', taiChisoBieuDo);
function hienThiChisoTheoThang() {
    var body = $('#chiso_theo_thang_body').empty();
    var thangDaChon = $('#chiso_chon_thang').val().split('/');
    var maThang = thangDaChon.length === 2 ? thangDaChon[1] + '-' + thangDaChon[0] : '';
    var data = chisoDuLieuTheoThang.filter(function (item) {
        return !maThang || item.thang === maThang;
    });
    if (!data.length) {
        body.append($('<tr>').append($('<td>', { colspan: 3, class: 'text-center' })
            .text(maThang ? 'Không có phiếu trong tháng đã chọn.' : 'Chưa có phiếu để thống kê.')));
        return;
    }
    data.forEach(function (item) {
        var thang = String(item.thang || '').split('-');
        body.append($('<tr>')
            .append($('<td>').text(thang.length === 2 ? thang[1] + '/' + thang[0] : item.thang))
            .append($('<td>').text(item.so_phieu))
            .append($('<td>').text(item.trung_binh)));
    });
}

$('#chiso_chon_thang').datepicker({
    format: 'mm/yyyy', startView: 1, minViewMode: 1,
    autoclose: true, forceParse: false, container: '#modalXemChiTieu'
}).datepicker('setDate', new Date()).on('changeDate', hienThiChisoTheoThang);
$('#chiso_xoa_chon_thang').on('click', function () {
    $('#chiso_chon_thang').datepicker('update', '');
    hienThiChisoTheoThang();
});

$('#chiso-theo-thang-tab').on('shown.bs.tab', function () {
    var maChiSo = chisoDangXem.maChiSo;
    $('#chiso_theo_thang_body').empty();
    var loading = $('#chiso_theo_thang_loading').show();
    var error = $('#chiso_theo_thang_error').hide();

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/getThongKeTheoThang/',
        type: 'POST', dataType: 'json',
        data: { ma_chi_so: maChiSo },
        success: function (response) {
            if (maChiSo !== chisoDangXem.maChiSo) return;
            if (!response || !response.success) {
                error.text(response && response.message ? response.message : 'Không tải được dữ liệu theo tháng.').show();
                return;
            }
            chisoDuLieuTheoThang = Array.isArray(response.data) ? response.data : [];
            hienThiChisoTheoThang();
        },
        error: function () {
            if (maChiSo === chisoDangXem.maChiSo) error.text('Không tải được dữ liệu theo tháng.').show();
        },
        complete: function () {
            if (maChiSo === chisoDangXem.maChiSo) loading.hide();
        }
    });
});

function taiChisoThongKeNhapThem() {
    var maChiSo = chisoDangXem.maChiSo;
    var khuVuc = $('#chiso_thong_ke_nhap_them').empty();
    var loading = $('#chiso_thong_ke_nhap_them_loading').show();
    var loi = $('#chiso_thong_ke_nhap_them_error').hide();
    var rong = $('#chiso_thong_ke_nhap_them_empty').hide();
    if (!maChiSo) { loading.hide(); return; }

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/getThongKeNhapThem/',
        type: 'POST', dataType: 'json', data: { ma_chi_so: maChiSo },
        success: function (response) {
            if (maChiSo !== chisoDangXem.maChiSo) return;
            if (!response || !response.success) {
                loi.text(response && response.message ? response.message : 'Không tải được thống kê nội dung nhập thêm.').show();
                return;
            }
            var danhSach = Array.isArray(response.data) ? response.data : [];
            if (!danhSach.length) {
                rong.text('Biểu mẫu chưa có phương án cho nhập thêm và chưa có câu trả lời tương ứng.').show();
                return;
            }

            var tongLuot = danhSach.reduce(function (tong, item) {
                return tong + (parseInt(item.so_luot, 10) || 0);
            }, 0);
            khuVuc.append($('<p>').append(
                $('<strong>').text('Tổng lượt chọn phương án có ô nhập: ' + tongLuot)
            ));
            var bang = $('<table>', { 'class': 'table table-striped table-bordered', width: '100%' });
            var thead = $('<thead>').append($('<tr>').append(
                $('<th>').text('Câu hỏi'), $('<th>').text('Phương án'),
                $('<th>').text('Tổng lượt chọn'), $('<th>').text('Nội dung nhập thêm'),
                $('<th>').text('Số lượt cùng nội dung')
            ));
            var tbody = $('<tbody>');
            danhSach.forEach(function (item) {
                var noiDung = Array.isArray(item.noi_dung_nhap) && item.noi_dung_nhap.length
                    ? item.noi_dung_nhap : [{ noi_dung: '-', so_luot: 0 }];
                noiDung.forEach(function (chiTiet) {
                    tbody.append($('<tr>').append(
                        $('<td>').text((item.ky_hieu || '') + ' - ' + (item.cau_hoi || '')),
                        $('<td>').text(item.phuong_an || ''),
                        $('<td>').text(item.so_luot || 0),
                        $('<td>').text(chiTiet.noi_dung || '-'),
                        $('<td>').text(chiTiet.so_luot || 0)
                    ));
                });
            });
            khuVuc.append(bang.append(thead, tbody));
        },
        error: function () {
            if (maChiSo === chisoDangXem.maChiSo) loi.text('Không tải được thống kê nội dung nhập thêm.').show();
        },
        complete: function () {
            if (maChiSo === chisoDangXem.maChiSo) loading.hide();
        }
    });
}

$('#chiso-thongke-tab').on('shown.bs.tab', function () {
    taiChisoThongKeNhapThem();
    if (!chisoThongKeTable) khoiTaoChisoThongKe();
    else chisoThongKeTable.ajax.reload(function () { chisoThongKeTable.columns.adjust().responsive.recalc(); }, false);
});

$('#btnXuatExcelChiSo').on('click', function () {
    if (!chisoDangXem.maChiSo) return;
    window.location.href = $('#ULocal').val() + 'chisochatluong/xuatExcel/?ma_chi_so=' + encodeURIComponent(chisoDangXem.maChiSo);
});

function getOptionText(selectId, value) {
    var text = $('#' + selectId + ' option').filter(function () {
        return String($(this).val()) === String(value);
    }).text();

    return text || value || '';
}

$('#chiso_loc_thang').datepicker({
    format: 'mm/yyyy', startView: 1, minViewMode: 1,
    autoclose: true, forceParse: false
}).datepicker('setDate', new Date());

table = $('#datatable-chiso').DataTable({
    destroy: true,
    ordering: false,

    
    dom:
        "<'row'<'col-sm-6'><'col-sm-6 text-right'Bf>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'><'col-sm-7'>>",

    buttons: [
        {
            text: '<i class="fa fa-plus"></i> Thêm chỉ số',
            className: 'btn btn-primary btn-them-chitieu',

            action: function () {

                // reset form
                $('#formChiTieu')[0].reset();
                khoiTaoBieuMauKhaoSat();
                capNhatKhoaPhongTheoPhamVi();

                // đánh dấu đang thêm
                $('#action').val('add');
                khoiPhucBanNhapBieuMau();

                // cho nhập mã chỉ số
                // $('#ma_chi_so').prop('readonly', false);

                // tiêu đề
                $('#modalChiTieu .modal-title')
                    .text('Thêm Chỉ tiêu');

                // mở modal
                $('#modalChiTieu').modal('show');
            }
        }
    ],

    ajax: {
        url: $("#ULocal").val() + 'chisochatluong/getData/',
        type: 'POST',
        error: function(response) {
            alert(JSON.stringify(response));
        },
        dataSrc: function(json) {
            if (!json || json.data == null) {
                return [];
            }

            // The current backend can return one object instead of an array.
            var danhSach = Array.isArray(json.data) ? json.data : [json.data];
            var thangDaChon = $('#chiso_loc_thang').val().split('/');
            if (thangDaChon.length !== 2) return danhSach;
            var maThang = thangDaChon[1] + '-' + thangDaChon[0];
            return danhSach.filter(function (item) {
                return String(item.created_at || '').slice(0, 7) === maThang;
            });
        }
    },
    responsive: true,
    autoWidth: false,
    columnDefs: [
        {
            targets: 1,
            width: '300px',
            className: 'column-wrap'
        },
        {
            targets: 2,
            width: '100px',
            className: 'column-wrap'
        },
        {
            targets: 3,
            width: '100px',
            className: 'column-wrap'
        }
    ],
    columns: [
        { data: 'ma_chi_so' },
        { data: 'ten_chi_so' },
        { 
            data: 'pham_vi',
            render: function (data, type, row) {
                return $('#pham_vi option[value="' + data + '"]').text() || data;
            }
        },
        { 
            data: 'id_chuky',
            render: function (data, type, row) {
                return $('#id_chuky option[value="' + data + '"]').text() || data;
            }
        },
        {
            data: 'nguoi_gui',
            render: function (data, type,row) {
                return data.hoTen;
            }
        },
        {
            data: 'nguoi_duyet',
            render: function (data, type,row) {
                return data.hoTen? data.hoTen : "";
            }
        },
        {
            data: 'trang_thai',
            render: function (data, type,row) {
                var tenTrangThai = data ? (data.tenTrangThai || data.maTrangThai) : '';

                if (type !== 'display') {
                    return tenTrangThai;
                }

                return $('<span>')
                    .addClass('badge')
                    .addClass('dt-center')
                    // .addClass('text-light')
                    .addClass('rounded-pill')
                    .addClass(data ? (data.tag || '') : '')
                    .text(tenTrangThai)
                    .prop('outerHTML');
            }
        },
        {
            data: null,
            orderable: true,
            searchable: true,

            render: function(data, type, row) {
                return `
                    <button type="button"
                        class="btn btn-primary btn-sm btn-gui"
                        data-id="${row.ma_chi_so}"
                        title="Gửi"
                        data-toggle="tooltip"
                        ${(data.trang_thai.maTrangThai==1 || data.trang_thai.maTrangThai==2 )? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==3)? 'hidden' : "" }
                        aria-label="Gửi">
                        <i class="fa fa-paper-plane-o"></i>
                    </button>
                    <button type="button"
                        class="btn btn-info btn-sm btn-xem"
                        data-id="${row.ma_chi_so}"
                        title="Xem"
                        data-toggle="tooltip"
                        aria-label="Xem">
                        <i class="fa fa-eye"></i>
                    </button>

                    <button type="button"
                        class="btn btn-success btn-sm btn-duyet"
                        data-id="${row.ma_chi_so}"
                        title="Duyệt"
                        data-toggle="tooltip"
                        ${(data.trang_thai.maTrangThai==2)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==0)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==3)? 'hidden' : "" }
                        
                        aria-label="Duyệt">
                        <i class="fa fa-check"></i>
                    </button>

                    <button type="button"
                        class="btn btn-warning btn-sm btn-sua"
                        data-id="${row.ma_chi_so}"
                        title="Sửa"
                        ${(data.trang_thai.maTrangThai==2)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==1)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==3)? 'hidden' : "" }
                        data-toggle="tooltip"
                        aria-label="Sửa">
                        <i class="glyphicon glyphicon-pencil"></i>
                    </button>

                    <button type="button"
                        class="btn btn-danger btn-sm btn-xoa"
                        data-id="${row.ma_chi_so}"
                        title="Xóa"
                        ${(data.trang_thai.maTrangThai==2)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==1)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==3)? 'hidden' : "" }
                        data-toggle="tooltip"
                        aria-label="Xóa">
                        <i class="glyphicon glyphicon-trash"></i>
                    </button>
                    <button type="button"
                        class="btn btn-danger btn-sm btn-tuchoi"
                        data-id="${row.ma_chi_so}"
                        title="Từ chối"
                        ${(data.trang_thai.maTrangThai==2)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==0)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==3)? 'hidden' : "" }
                        data-toggle="tooltip"
                        aria-label="Từ chối">
                        <i class="fa fa-remove"></i>
                    </button>
                    <button type="button"
                        class="btn btn-primary btn-sm btn-tao-lai"
                        data-id="${row.ma_chi_so}"
                        title="Tạo lại từ chỉ số bị từ chối"
                        data-toggle="tooltip"
                        ${Number(row.trang_thai.maTrangThai) !== 3 ? 'hidden' : ''}>
                        <i class="fa fa-copy"></i>
                    </button>
                `;
            }
        }
    ],
    drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip();
        hiddenChiSoChatLuongButtons();
    }
});

$('#chiso_loc_thang').on('changeDate', function () {
    table.ajax.reload();
});
$('#chiso_xoa_loc_thang').on('click', function () {
    $('#chiso_loc_thang').datepicker('update', '');
    table.ajax.reload();
});

function coQuyenChiSo(tenQuyen) {
    return $('#role-chisochatluong-' + tenQuyen).val() === 'true';
}

function hiddenChiSoChatLuongButtons() {
    if (!coQuyenChiSo('save')) {
        $('.btn-them-chitieu').hide();
    }

    if (!coQuyenChiSo('update')) {
        $('#datatable-chiso .btn-sua').hide();
    }

    if (!coQuyenChiSo('gui')) {
        $('#datatable-chiso .btn-gui').hide();
    }

    if (!coQuyenChiSo('duyet')) {
        $('#datatable-chiso .btn-duyet').hide();
    }

    if (!coQuyenChiSo('xoa')) {
        $('#datatable-chiso .btn-xoa').hide();
    }

    if (!coQuyenChiSo('tuchoi')) {
        $('#datatable-chiso .btn-tuchoi').hide();
    }
    if (coQuyenChiSo('khoaB')) {
        // $('#datatable-chiso .btn-tuchoi').hide();
        // alert('co quyen nè');
        $('#pham_vi').hide();
        $('#btnMoPopupCon').hide();
    }
}

$('#datatable-chiso').on('click', '.btn-sua', function () {

    var tr = $(this).closest('tr');

    if (tr.hasClass('child')) {
        tr = tr.prev();
    }

    var row = table.row(tr).data();

    if (!row) {
        return;
    }

    // Mở khóa form
    $('#formChiTieu')
        .find('input, textarea, select')
        .prop('disabled', false);

    $('#btnLuuChiTieu').show();

    $('#action').val('edit');

    $('#ma_chi_so').val(row.ma_chi_so);
    $('#ten_chi_so').val(row.ten_chi_so);
    $('#ma_khia_canh').val(row.ma_khia_canh);
    $('#ma_thanh_to').val(row.ma_thanh_to);
    $('#nhom_chi_so').val(row.nhom_chi_so);
    $('#pham_vi').val(row.pham_vi);
    $('#muc_tieu').val(row.muc_tieu);
    $('#nguong_canh_bao').val(row.nguong_canh_bao);
    $('#don_vi_tinh').val(row.id_donvitinh);
    $('#id_chuky').val(row.id_chuky);
    $('#id_khoaphong').val(row.phong || [String(row.id_khoaphong)]);
    capNhatKhoaPhongTheoPhamVi();
    $('#dinh_nghia').val(row.dinh_nghia);
    $('#thu_thap').val(row.thu_thap);
    $('#ten_tu_so').val(row.ten_tu_so);
    $('#ten_mau_so').val(row.ten_mau_so);

    // Không cho sửa mã
    $('#ma_chi_so').prop('readonly', true);

    $('#modalChiTieu .modal-title')
        .text('Sửa Chỉ tiêu');

    khoiTaoBieuMauKhaoSat();
    napDuLieuBieuMau(row.bieumau);
    khoiPhucBanNhapBieuMau();
    $('#modalChiTieu').modal('show');
});

$('#formChiTieu').on('keydown', function (e) {
    if ((e.key === 'Enter' || e.keyCode === 13) && !e.isComposing) {
        var truongNhap = $(e.target);
        if (!truongNhap.is('textarea, [contenteditable="true"]')) {
            e.preventDefault();
            e.stopPropagation();
        }
    }
});

$('#formChiTieu').on('submit', function (e) {

    e.preventDefault();
    luuBanNhapBieuMau();

    var ketQuaKiemTraCauHoi = kiemTraHopLeBieuMauCauHoi();
    if (!ketQuaKiemTraCauHoi.hopLe) {
        hienThiLoiBieuMauCauHoi(ketQuaKiemTraCauHoi);
        return;
    }

    var action = $('#action').val();

    var urlAjax;

    if (action === 'add') {

        urlAjax = $("#ULocal").val()
            + 'chisochatluong/save/';

        // alert('lưu nè');

    } else {

        urlAjax = $("#ULocal").val()
            + 'chisochatluong/update/';
    }

    $.ajax({
        url: urlAjax,
        type: 'POST',
        data: {
            data: JSON.stringify([{
                ma_chi_so: $('#ma_chi_so').val(),
                ten_chi_so: $('#ten_chi_so').val(),
                ma_khia_canh: $('#ma_khia_canh').val(),
                ma_thanh_to: $('#ma_thanh_to').val(),
                nhom_chi_so: $('#nhom_chi_so').val() || '',
                pham_vi: $('#pham_vi').val(),
                muc_tieu: $('#muc_tieu').val(),
                nguong_canh_bao: $('#nguong_canh_bao').val(),
                id_donvitinh: $('#don_vi_tinh').val(),
                id_chuky: $('#id_chuky').val() || $('#chuky').val(),
                id_khoaphong: ($('#id_khoaphong').val() || [])[0] || 0,
                phong: $('#id_khoaphong').val() || [],
                dinh_nghia: $('#dinh_nghia').val(),
                thu_thap: $('#thu_thap').val(),
                ten_tu_so: $('#ten_tu_so').val(),
                ten_mau_so: $('#ten_mau_so').val(),
                bieumau: layDuLieuBieuMau(),
                nguoi_gui: $('#fullname').val()
            }])
        },
        dataType: 'json',
        beforeSend: function () {

            $('#btnLuuChiTieu')
                .prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');
        },

        success: function (response) {

            if (response.success || (response.message && response.message.flag)) {

                boQuaLuuBanNhapKhiDong = true;
                xoaBanNhapBieuMau();
                $('#modalChiTieu').modal('hide');

                // reload DataTable nhưng không về trang 1
                table.ajax.reload(null, false);

                // alert(response.message || 'Lưu thành công');

            } else {
                var thongBaoLoi = (typeof response.message === 'string' ? response.message :
                    (response.message && (response.message.errorMessage || response.message.message))) ||
                    response.errorMessage || 'Không thể lưu dữ liệu. Bản nháp vẫn được giữ lại.';
                Swal.fire('Lưu biểu mẫu không thành công', thongBaoLoi, 'error');
            }
        },

        error: function (xhr) {
            var thongBaoLoi = 'Có lỗi xảy ra khi lưu dữ liệu. Bản nháp vẫn được giữ lại.';
            if (xhr.responseJSON) {
                thongBaoLoi = xhr.responseJSON.errorMessage || xhr.responseJSON.message || thongBaoLoi;
            }
            Swal.fire('Lưu biểu mẫu không thành công', thongBaoLoi, 'error');
        },

        complete: function () {

            $('#btnLuuChiTieu')
                .prop('disabled', false)
                .html('<i class="fa fa-save"></i> Lưu');
        },
        action: function () {

            $('#formChiTieu')[0].reset();

            $('#formChiTieu')
                .find('input, textarea, select')
                .prop('disabled', false);

            $('#btnLuuChiTieu').show();

            $('#action').val('add');

            // $('#ma_chi_so').prop('readonly', false);

            $('#modalChiTieu .modal-title')
                .text('Thêm Chỉ tiêu');

            $('#modalChiTieu').modal('show');
            
        }
    });

});

function hienThiBieuMauKhaoSat(duLieu) {
    var khuVuc = $('#xem_bieu_mau').empty();
    var tenLoai = {
        short_text: 'Trả lời ngắn',
        long_text: 'Đoạn văn',
        number: 'Số',
        percentage: 'Tỷ lệ (%)',
        score: 'Chọn điểm (1–10)',
        date: 'Ngày tháng',
        category: 'Danh mục cha',
        subcategory: 'Danh mục con',
        satisfaction: 'Độ hài lòng',
        radio: 'Một lựa chọn',
        checkbox: 'Nhiều lựa chọn',
        select: 'Danh sách thả xuống'
    };

    if (typeof duLieu === 'string') {
        try {
            duLieu = JSON.parse(duLieu);
        } catch (error) {
            duLieu = null;
        }
    }

    var danhSach = Array.isArray(duLieu) ? duLieu : (duLieu && duLieu.cau_hoi);
    if (!Array.isArray(danhSach) || !danhSach.length) {
        khuVuc.append($('<span>', { 'class': 'text-muted', text: 'Chưa có câu hỏi.' }));
        return;
    }

    var soCauHoiNgoaiMuc = 0;
    var soDanhMucCha = 0;
    var soDanhMucCon = 0;
    var soCauHoiTrongMuc = 0;
    var kyHieuDanhMucCon = '';
    var danhMucChaCoDanhMucCon = false;
    var capDanhMuc = 0;
    danhSach.forEach(function (cauHoi, index) {
        if (laLoaiDanhMuc(cauHoi.loai)) {
            capDanhMuc = cauHoi.loai === 'subcategory' ? 2 : 1;
            var kyHieuDanhMuc;
            if (cauHoi.loai === 'category') {
                soDanhMucCha += 1;
                soDanhMucCon = 0;
                soCauHoiTrongMuc = 0;
                kyHieuDanhMucCon = '';
                danhMucChaCoDanhMucCon = coDanhMucConTrongNhom(danhSach, index);
                kyHieuDanhMuc = doiSoLaMa(soDanhMucCha);
            } else {
                soDanhMucCon += 1;
                soCauHoiTrongMuc = 0;
                kyHieuDanhMucCon = doiSoChuCai(soDanhMucCon);
                kyHieuDanhMuc = kyHieuDanhMucCon;
            }
            khuVuc.append($('<div>', {
                'class': 'survey-view-category' + (cauHoi.loai === 'subcategory' ? ' survey-view-category--child' : ''),
                text: kyHieuDanhMuc + '. ' + (
                    cauHoi.loai === 'category'
                        ? String(cauHoi.noi_dung || 'Danh mục chưa có tên').toUpperCase()
                        : vietHoaDauCau(cauHoi.noi_dung || 'Danh mục chưa có tên')
                )
            }));
            return;
        }

        var khoiCauHoi = $('<div>', {
            'class': 'survey-view-question' + (capDanhMuc ? ' survey-view-question--level-' + capDanhMuc : '')
        });
        var tieuDe = $('<div>', { 'class': 'survey-view-question__title' });
        var kyHieuCauHoi;
        if (capDanhMuc) {
            soCauHoiTrongMuc += 1;
            kyHieuCauHoi = (
                kyHieuDanhMucCon || (danhMucChaCoDanhMucCon ? doiSoLaMa(soDanhMucCha) : 'A')
            ) + soCauHoiTrongMuc;
        } else {
            kyHieuCauHoi = String(++soCauHoiNgoaiMuc);
        }

        tieuDe.append(document.createTextNode(kyHieuCauHoi + '. ' + vietHoaDauCau(cauHoi.noi_dung || 'Câu hỏi chưa có nội dung')));
        if (cauHoi.bat_buoc) {
            tieuDe.append($('<span>', { 'class': 'text-danger', text: ' *' }));
        }
        khoiCauHoi.append(tieuDe);
        khoiCauHoi.append($('<span>', {
            'class': 'survey-view-question__type',
            text: tenLoai[cauHoi.loai] || cauHoi.loai || 'Không xác định'
        }));

        if (cauHoi.loai !== 'satisfaction' && Array.isArray(cauHoi.lua_chon) && cauHoi.lua_chon.length) {
            var luaChon = $('<ul>', { 'class': 'survey-view-question__options' });
            cauHoi.lua_chon.forEach(function (itemLuaChon) {
                var noiDung = typeof itemLuaChon === 'object' ? itemLuaChon.noi_dung : itemLuaChon;
                var diem = typeof itemLuaChon === 'object' ? itemLuaChon.diem : 0;
                var phuongAn = $('<li>').text(vietHoaDauCau(noiDung) + ' (' + diem + ' điểm)');
                if (cauHoi.loai === 'radio' && itemLuaChon && itemLuaChon.cho_nhap_them) {
                    phuongAn.append($('<input>', {
                        type: 'text',
                        'class': 'form-control input-sm survey-view-option__other',
                        placeholder: 'Người trả lời nhập nội dung khác tại đây',
                        disabled: true
                    }));
                }
                luaChon.append(phuongAn);
            });
            khoiCauHoi.append(luaChon);
        }

        khuVuc.append(khoiCauHoi);
    });
}

$('#datatable-chiso').on('click', '.btn-xem', function () {
    var tr = $(this).closest('tr');
    if (tr.hasClass('child')) {
        tr = tr.prev();
    }
    var row = table.row(tr).data();
    if (!row) {
        return;
    }

    var soChuKy = parseInt($('#id_chuky option[value="' + row.id_chuky + '"]').data('so-ky'), 10) || 0;
    chisoDangXem = { maChiSo: row.ma_chi_so, soChuKy: soChuKy };
    if (chisoBieuDo) {
        chisoBieuDo.destroy();
        chisoBieuDo = null;
    }
    if (chisoBieuDoCauHoi) {
        chisoBieuDoCauHoi.destroy();
        chisoBieuDoCauHoi = null;
    }
    if (chisoThongKeTable) chisoThongKeTable.clear().draw();
    $('#chiso_thong_ke_nhap_them').empty();
    $('#chiso_thong_ke_nhap_them_loading, #chiso_thong_ke_nhap_them_error, #chiso_thong_ke_nhap_them_empty').hide();
    chisoDuLieuTheoThang = [];
    $('#chiso_chon_thang').datepicker('setDate', new Date());
    $('#chiso_theo_thang_body').empty();
    $('#chiso_theo_thang_error').hide();
    $('#chiso_bieu_do_loading').show();
    $('#chiso_bieu_do_empty').hide();
    $('#chiso_bieu_do_chu_ky').hide();
    $('#chiso_bieu_do_cau_hoi_loading').show();
    $('#chiso_bieu_do_cau_hoi_empty').hide();
    $('#chiso_bieu_do_cau_hoi').hide();
    $('#chiso-thongtin-tab').tab('show');

    var maPhong = row.phong || [String(row.id_khoaphong)];
    function hienThi(value,text="") {
        return value === null || value === undefined || value === '' ? '-' : value+text;
    }

    var maPhongDaChon = maPhong.map(String);
    var danhSachKhoaPhong = $('#xem_khoa_phong').empty();

    $('.check-khoi').each(function () {
        var idKhoi = String($(this).data('khoi'));
        var tenKhoi = $(this).closest('label').text().trim();
        var khoaPhongTrongKhoi = $('.check-khoa-phong[data-khoi="' + idKhoi + '"]').filter(function () {
            return maPhongDaChon.indexOf(String(this.value)) !== -1;
        });

        if (!khoaPhongTrongKhoi.length) {
            return;
        }

        var nhomKhoi = $('<div>').addClass('khoa-phong-tree').css('margin-bottom', '8px');
        $('<div>')
            .css('font-weight', 'bold')
            .append($('<i>').addClass('fa fa-folder-open-o').css('margin-right', '6px'))
            .append(document.createTextNode(tenKhoi))
            .appendTo(nhomKhoi);

        var danhSachPhong = $('<ul>').css({ margin: '4px 0 0 24px', paddingLeft: '16px' });
        khoaPhongTrongKhoi.each(function () {
            $('<li>')
                .text($(this).closest('label').text().trim())
                .appendTo(danhSachPhong);
        });

        nhomKhoi.append(danhSachPhong).appendTo(danhSachKhoaPhong);


    });

    if (!danhSachKhoaPhong.children().length || row.pham_vi == 3) {
        danhSachKhoaPhong.text('-');
    }

    $('#xem_ma_chi_so').text(hienThi(row.ma_chi_so));
    $('#xem_ten_chi_so').text(hienThi(row.ten_chi_so));
    $('#xem_khia_canh').text(hienThi(getOptionText('ma_khia_canh', row.ma_khia_canh)));
    $('#xem_thanh_to').text(hienThi(getOptionText('ma_thanh_to', row.ma_thanh_to)));
    $('#xem_pham_vi').text(hienThi(getOptionText('pham_vi', row.pham_vi)));
    $('#xem_muc_tieu').text(hienThi(row.muc_tieu," " + row.donvitinh.ten));
    $('#xem_nguong_canh_bao').text(hienThi(row.nguong_canh_bao," " + row.donvitinh.ten));
    $('#xem_khoa').text(hienThi(row.khoaphong.TenKhoaPhong));
    $('#xem_chu_ky').text(hienThi(getOptionText('id_chuky', row.id_chuky)));
    $('#xem_dinh_nghia').text(hienThi(row.dinh_nghia));
    $('#xem_thu_thap').text(hienThi(row.thu_thap));
    $('#xem_tu_so').text(hienThi(row.ten_tu_so));
    $('#xem_mau_so').text(hienThi(row.ten_mau_so));
    hienThiBieuMauKhaoSat(row.bieumau);
    $('#xem_nguoi_gui').text(hienThi(row.nguoi_gui && row.nguoi_gui.hoTen));
    $('#xem_nguoi_duyet').text(hienThi(row.nguoi_duyet.hoTen));
    $('#xem_trang_thai').text(hienThi(row.trang_thai && row.trang_thai.tenTrangThai));
    
    var lyDoTuChoi = (row.ly_do_tu_choi || '').trim();

    if (lyDoTuChoi) {
        $('#xem_ly_do_tu_choi').text(lyDoTuChoi);
        $('#row_xem_ly_do_tu_choi').show();
    } else {
        $('#xem_ly_do_tu_choi').text('');
        $('#row_xem_ly_do_tu_choi').hide();
    }

    $('#modalXemChiTieu').modal('show');
});

function resetModalChiTieu() {

    var form = $('#formChiTieu');

    // Mở lại toàn bộ input/select/textarea
    form.find('input, textarea, select')
        .prop('disabled', false)
        .prop('readonly', false);

    // Mã chỉ số luôn không cho nhập
    // $('#ma_chi_so').prop('readonly', true);

    // Hiện lại nút lưu
    $('#btnLuuChiTieu').show();
}

$('#modalChiTieu').on('hidden.bs.modal', function () {

    resetModalChiTieu();

});

//Duyệt
$('#datatable-chiso').on('click', '.btn-duyet', function (e) {
    e.preventDefault();

    var button = $(this);
    var tr = button.closest('tr');

    if (tr.hasClass('child')) {
        tr = tr.prev();
    }

    var row = table.row(tr).data();

    if (!row || !row.ma_chi_so || button.prop('disabled')) {
        return;
    }

    var originalHtml = button.html();

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/duyet/',
        type: 'POST',
        dataType: 'json',
        data: {
            data: JSON.stringify([{
                ma_chi_so: row.ma_chi_so,
                ten_chi_so: row.ten_chi_so,
                nguoi_duyet: $('#fullname').val()
            }])
        },
        beforeSend: function () {
            button.prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i>');
        },
        success: function (response) {
            var message = response && response.message;
            debugger;
            if (response && (response.success || (message && message.flag))) {
                table.ajax.reload(null, false);
                hienThiToast('success', response.message.succesMessage);
                // Swal.fire('Thành công', 'Duyệt chỉ tiêu thành công.', 'success');
            } else {
                hienThiToast('danger', response.message.errorMessage);
            }
        },
        error: function (xhr) {
            var response = xhr.responseJSON;
            var message = response && response.message;

            Swal.fire('Lỗi',
                (message && message.errorMessage) || 'Có lỗi xảy ra khi duyệt chỉ tiêu. Vui lòng thử lại.',
                'error');
        },
        complete: function () {
            button.prop('disabled', false).html(originalHtml);
        }
    });
});


//Duyệt
$('#datatable-chiso').on('click', '.btn-gui', function (e) {
    e.preventDefault();

    var button = $(this);
    var tr = button.closest('tr');

    if (tr.hasClass('child')) {
        tr = tr.prev();
    }

    var row = table.row(tr).data();

    if (!row || !row.ma_chi_so || button.prop('disabled')) {
        return;
    }

    var originalHtml = button.html();

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/gui/',
        type: 'POST',
        dataType: 'json',
        data: {
            data: JSON.stringify([{
                ma_chi_so: row.ma_chi_so,
                ten_chi_so: row.ten_chi_so,
                nguoi_duyet: $('#fullname').val()
            }])
        },
        beforeSend: function () {
            button.prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i>');
        },
        success: function (response) {
            var message = response && response.message;

            if (response && (response.success || (message && message.flag))) {
                table.ajax.reload(null, false);
                Swal.fire('Thành công', 'Duyệt chỉ tiêu thành công.', 'success');
            } else {
                Swal.fire('Không thể duyệt',
                    (message && message.errorMessage) || 'Không thể duyệt chỉ tiêu. Vui lòng thử lại.',
                    'error');
            }
        },
        error: function (xhr) {
            var response = xhr.responseJSON;
            var message = response && response.message;

            Swal.fire('Lỗi',
                (message && message.errorMessage) || 'Có lỗi xảy ra khi duyệt chỉ tiêu. Vui lòng thử lại.',
                'error');
        },
        complete: function () {
            button.prop('disabled', false).html(originalHtml);
        }
    });
});

//Xóa
$('#datatable-chiso').on('click', '.btn-xoa', function (e) {
    e.preventDefault();

    var button = $(this);
    var tr = button.closest('tr');

    if (tr.hasClass('child')) {
        tr = tr.prev();
    }

    var row = table.row(tr).data();

    if (!row || !row.ma_chi_so || button.prop('disabled')) {
        return;
    }

    var originalHtml = button.html();

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/xoa/',
        type: 'POST',
        dataType: 'json',
        data: {
            data: JSON.stringify([{
                ma_chi_so: row.ma_chi_so,
                ten_chi_so: row.ten_chi_so,
                nguoi_duyet: $('#fullname').val()
            }])
        },
        success: function (response) {
            var message = response && response.message;

            if (response && (response.success || (message && message.flag))) {
                table.ajax.reload(null, false);
                Swal.fire('Thành công', 'Xóa chỉ tiêu thành công.', 'success');
            } else {
                Swal.fire('Không thể duyệt',
                    (message && message.errorMessage) || 'Không thể Xóa chỉ tiêu. Vui lòng thử lại.',
                    'error');
            }
        },
        error: function (xhr) {
            var response = xhr.responseJSON;
            var message = response && response.message;

            Swal.fire('Lỗi',
                (message && message.errorMessage) || 'Có lỗi xảy ra khi duyệt chỉ tiêu. Vui lòng thử lại.',
                'error');
        },
    });
});

//Từ chối
$('#datatable-chiso').on('click', '.btn-tuchoi', function (e) {
    e.preventDefault();

    var button = $(this);
    var tr = button.closest('tr');

    if (tr.hasClass('child')) {
        tr = tr.prev();
    }

    var row = table.row(tr).data();

    if (!row || !row.ma_chi_so || button.prop('disabled')) {
        return;
    }

    Swal.fire({
        title: 'Từ chối chỉ tiêu',
        input: 'textarea',
        inputLabel: 'Lý do từ chối',
        inputPlaceholder: 'Nhập lý do từ chối...',
        inputAttributes: {
            'aria-label': 'Lý do từ chối',
            maxlength: 1000
        },
        showCancelButton: true,
        confirmButtonText: 'Từ chối',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d9534f',
        inputValidator: function (value) {
            if (!value || !value.trim()) {
                return 'Vui lòng nhập lý do từ chối.';
            }
        }
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        button.prop('disabled', true);

        $.ajax({
            url: $('#ULocal').val() + 'chisochatluong/tuchoi/',
            type: 'POST',
            dataType: 'json',
            data: {
                data: JSON.stringify([{
                    ma_chi_so: row.ma_chi_so,
                    ten_chi_so: row.ten_chi_so,
                    nguoi_duyet: $('#fullname').val(),
                    ly_do_tu_choi: result.value.trim()
                }])
            },
            success: function (response) {
                var message = response && response.message;

                if (response && (response.success || (message && message.flag))) {
                    table.ajax.reload(null, false);
                    Swal.fire('Thành công', 'Từ chối chỉ tiêu thành công.', 'success');
                } else {
                    Swal.fire('Không thể từ chối',
                        (message && message.errorMessage) || 'Không thể từ chối chỉ tiêu. Vui lòng thử lại.',
                        'error');
                }
            },
            error: function (xhr) {
                var response = xhr.responseJSON;
                var message = response && response.message;

                Swal.fire('Lỗi',
                    (message && message.errorMessage) || 'Có lỗi xảy ra khi từ chối chỉ tiêu. Vui lòng thử lại.',
                    'error');
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });
});

function capNhatKhoaPhongTheoPhamVi() {
    // Quyền khoaB không được phép mở popup chọn Khoa/Phòng.
    // Kiểm tra tại đây vì hàm này có thể được gọi lại khi thêm, sửa
    // hoặc thay đổi phạm vi; nếu không, lệnh show() bên dưới sẽ hiện lại nút.
    if (coQuyenChiSo('khoaB')) {
        $('#btnMoPopupCon').hide();
        return;
    }

    var laPhamViToanBo = String($('#pham_vi').val()) === '3';

    if (laPhamViToanBo) {
        $('#id_khoaphong option').prop('selected', true);
        $('.check-khoa-phong').prop('checked', true);
        $('#btnMoPopupCon').hide();
        capNhatCheckboxKhoa();
        return;
    }

    $('#btnMoPopupCon').show();
}

$('#pham_vi').on('change', capNhatKhoaPhongTheoPhamVi);

// Mở popup con
$('#btnMoPopupCon').on('click', function () {
    var idKhoaPhong = $('#id_khoaphong').val() || [];

    $('.check-khoa-phong').each(function () {
        $(this).prop('checked', idKhoaPhong.indexOf($(this).val()) !== -1);
    });
    capNhatCheckboxKhoa();

    $('#modalChiTieu').modal('hide');

    $('#modalChiTieu').one('hidden.bs.modal', function () {
        $('#modalCon').modal('show');
    });
});

// Đóng popup con thì mở lại popup cha
$('#modalCon').on('hidden.bs.modal', function () {
    $('#modalChiTieu').modal('show');
});

function capNhatCheckboxKhoa() {
    $('.check-khoi').each(function () {
        var checkboxKhoi = $(this);
        var idKhoi = checkboxKhoi.data('khoi');
        var checkboxKhoaPhong = $('.check-khoa-phong[data-khoi="' + idKhoi + '"]');
        var daChon = checkboxKhoaPhong.filter(':checked').length;

        checkboxKhoi
            .prop('checked', checkboxKhoaPhong.length > 0 && daChon === checkboxKhoaPhong.length)
            .prop('indeterminate', daChon > 0 && daChon < checkboxKhoaPhong.length);
    });
}

$('#tableKhoaPhong').on('change', '.check-khoi', function () {
    var idKhoi = $(this).data('khoi');

    $('.check-khoa-phong[data-khoi="' + idKhoi + '"]').prop('checked', this.checked);
    capNhatCheckboxKhoa();
});

$('#tableKhoaPhong').on('change', '.check-khoa-phong', capNhatCheckboxKhoa);

// Đưa Khoa/Phòng đã chọn về popup cha
$('#btnChonPopupCon').on('click', function () {
    var idKhoaPhong = $('.check-khoa-phong:checked').map(function () {
        return this.value;
    }).get();

    if (!idKhoaPhong.length) {
        Swal.fire('Thông báo', 'Vui lòng chọn Khoa/Phòng.', 'warning');
        return;
    }

    $('#id_khoaphong').val(idKhoaPhong).trigger('change');
    $('#modalCon').modal('hide');
});

var soThuTuCauHoi = 0;
var henGioLuuBanNhapBieuMau = null;
var dangKhoiPhucBanNhapBieuMau = false;
var boQuaLuuBanNhapKhiDong = false;

function khoaBanNhapBieuMau() {
    var action = $('#action').val() || 'add';
    var maChiSo = action === 'edit' ? ($('#ma_chi_so').val() || 'khong_xac_dinh') : 'moi';
    return 'ql_cscl:bieu_mau_nhap:' + action + ':' + maChiSo;
}

function layDuLieuThongTinChiTieu() {
    var duLieu = [];
    $('#formChiTieu :input')
        .not(':button, [type="submit"], [type="file"], #action, #surveyBuilder :input')
        .each(function (index) {
        var input = $(this);
        var type = String(input.attr('type') || this.tagName || '').toLowerCase();
        duLieu.push({
            id: this.id || '',
            name: input.attr('name') || '',
            index: index,
            type: type,
            value: input.val(),
            checked: type === 'checkbox' || type === 'radio' ? input.prop('checked') : null
        });
        });
    return duLieu;
}

function khoiPhucThongTinChiTieu(duLieu) {
    if (!Array.isArray(duLieu)) return;
    var tatCaInput = $('#formChiTieu :input')
        .not(':button, [type="submit"], [type="file"], #action, #surveyBuilder :input');
    duLieu.forEach(function (item) {
        if (!item.id && !item.name) return;
        var input = item.id ? $(document.getElementById(item.id)) : $();
        if (!input.length && item.name) {
            input = tatCaInput.filter('[name="' + String(item.name).replace(/"/g, '\\"') + '"]').first();
        }
        if (!input.length && item.index !== undefined) input = tatCaInput.eq(item.index);
        if (!input.length) return;
        if (item.type === 'checkbox' || item.type === 'radio') {
            input.prop('checked', !!item.checked);
        } else {
            input.val(item.value);
        }
    });
    capNhatKhoaPhongTheoPhamVi();
}

function luuBanNhapBieuMau() {
    if (dangKhoiPhucBanNhapBieuMau || !window.localStorage) return;
    try {
        var thoiGianLuu = new Date();
        window.localStorage.setItem(khoaBanNhapBieuMau(), JSON.stringify({
            saved_at: thoiGianLuu.toISOString(),
            thong_tin: layDuLieuThongTinChiTieu(),
            bieumau: layDuLieuBieuMau()
        }));
        $('#trangThaiBanNhapBieuMau').html(
            '<i class="fa fa-check-circle"></i> Đã lưu bản nháp lúc ' +
            thoiGianLuu.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' })
        );
    } catch (error) {
        $('#trangThaiBanNhapBieuMau').html(
            '<i class="fa fa-exclamation-circle"></i> Không thể tự động lưu'
        );
    }
}

function lenLichLuuBanNhapBieuMau() {
    window.clearTimeout(henGioLuuBanNhapBieuMau);
    henGioLuuBanNhapBieuMau = window.setTimeout(luuBanNhapBieuMau, 300);
}

function khoiPhucBanNhapBieuMau() {
    if (!window.localStorage) return false;
    try {
        var noiDung = window.localStorage.getItem(khoaBanNhapBieuMau());
        if (!noiDung) return false;
        var banNhap = JSON.parse(noiDung);
        if (!banNhap || !banNhap.bieumau) return false;
        dangKhoiPhucBanNhapBieuMau = true;
        khoiPhucThongTinChiTieu(banNhap.thong_tin);
        khoiTaoBieuMauKhaoSat();
        napDuLieuBieuMau(banNhap.bieumau);
        dangKhoiPhucBanNhapBieuMau = false;
        $('#trangThaiBanNhapBieuMau').html('<i class="fa fa-history"></i> Đã khôi phục bản nháp');
        return true;
    } catch (error) {
        dangKhoiPhucBanNhapBieuMau = false;
        return false;
    }
}

function xoaBanNhapBieuMau() {
    window.clearTimeout(henGioLuuBanNhapBieuMau);
    try {
        if (window.localStorage) window.localStorage.removeItem(khoaBanNhapBieuMau());
        $('#trangThaiBanNhapBieuMau').html('<i class="fa fa-cloud"></i> Tự động lưu bản nháp');
    } catch (error) {
        // Không cần chặn luồng lưu chính nếu trình duyệt không cho xóa bản nháp.
    }
}

$('#modalChiTieu').on('hide.bs.modal.autosave', function () {
    window.clearTimeout(henGioLuuBanNhapBieuMau);
    if (boQuaLuuBanNhapKhiDong) {
        boQuaLuuBanNhapKhiDong = false;
        return;
    }
    luuBanNhapBieuMau();
});

window.addEventListener('beforeunload', function () {
    if ($('#modalChiTieu').hasClass('in')) luuBanNhapBieuMau();
});

document.addEventListener('visibilitychange', function () {
    if (document.hidden && $('#modalChiTieu').hasClass('in')) luuBanNhapBieuMau();
});

function loaiCauHoiCanLuaChon(loai) {
    return ['radio', 'checkbox', 'select', 'satisfaction'].indexOf(loai) !== -1;
}

function laLoaiDanhMuc(loai) {
    return ['category', 'subcategory'].indexOf(loai) !== -1;
}

function vietHoaDauCau(giaTri) {
    giaTri = String(giaTri || '');
    var viTriDau = giaTri.search(/\S/);
    if (viTriDau === -1) return giaTri;
    return giaTri.slice(0, viTriDau)
        + giaTri.charAt(viTriDau).toUpperCase()
        + giaTri.slice(viTriDau + 1);
}

function doiSoLaMa(so) {
    var bangSo = [
        [1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'],
        [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'],
        [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']
    ];
    var ketQua = '';
    bangSo.forEach(function (item) {
        while (so >= item[0]) {
            ketQua += item[1];
            so -= item[0];
        }
    });
    return ketQua;
}

function doiSoChuCai(so) {
    var ketQua = '';
    while (so > 0) {
        so -= 1;
        ketQua = String.fromCharCode(65 + (so % 26)) + ketQua;
        so = Math.floor(so / 26);
    }
    return ketQua;
}

function coDanhMucConTrongNhom(danhSach, viTriDanhMucCha) {
    for (var i = viTriDanhMucCha + 1; i < danhSach.length; i++) {
        var item = danhSach[i];
        var loai = typeof item === 'string' ? item : (item && item.loai);
        if (loai === 'category') return false;
        if (loai === 'subcategory') return true;
    }
    return false;
}

function capNhatTrangThaiBieuMau() {
    var danhSach = $('#danhSachCauHoi .survey-question');
    var soCauHoiNgoaiMuc = 0;
    var soDanhMucCha = 0;
    var soDanhMucCon = 0;
    var soCauHoiTrongMuc = 0;
    var kyHieuDanhMucCon = '';
    var danhMucChaCoDanhMucCon = false;
    var capDanhMuc = 0;
    var danhSachLoai = danhSach.map(function () {
        return $(this).find('.survey-question__type').val();
    }).get();

    $('#surveyBuilderEmpty').toggle(danhSach.length === 0);
    danhSach.each(function (index) {
        var item = $(this);
        var loai = item.find('.survey-question__type').val();
        var laDanhMuc = laLoaiDanhMuc(loai);
        var laDanhMucCon = loai === 'subcategory';
        item.toggleClass('survey-question--category', laDanhMuc);
        item.toggleClass('survey-question--subcategory', laDanhMucCon);
        if (!laDanhMuc) item.removeClass('survey-question--questions-open');
        item.removeClass('survey-question--level-1 survey-question--level-2');

        if (loai === 'category') {
            capDanhMuc = 1;
            item.find('.survey-question__title').val(function (_, giaTri) {
                return String(giaTri || '').toUpperCase();
            });
            soDanhMucCha += 1;
            soDanhMucCon = 0;
            soCauHoiTrongMuc = 0;
            kyHieuDanhMucCon = '';
            danhMucChaCoDanhMucCon = coDanhMucConTrongNhom(danhSachLoai, index);
            item.attr('data-tree-label', doiSoLaMa(soDanhMucCha));
            item.find('.survey-question__number').text('Danh mục cha ' + doiSoLaMa(soDanhMucCha));
        } else if (laDanhMucCon) {
            capDanhMuc = 2;
            soDanhMucCon += 1;
            soCauHoiTrongMuc = 0;
            kyHieuDanhMucCon = doiSoChuCai(soDanhMucCon);
            item.attr('data-tree-label', kyHieuDanhMucCon);
            item.find('.survey-question__number').text('Danh mục con ' + kyHieuDanhMucCon);
        } else {
            if (capDanhMuc) item.addClass('survey-question--level-' + capDanhMuc);
            var kyHieuCauHoi;
            if (capDanhMuc) {
                soCauHoiTrongMuc += 1;
                kyHieuCauHoi = (
                    kyHieuDanhMucCon || (danhMucChaCoDanhMucCon ? doiSoLaMa(soDanhMucCha) : 'A')
                ) + soCauHoiTrongMuc;
            } else {
                kyHieuCauHoi = String(++soCauHoiNgoaiMuc);
            }
            item.attr('data-tree-label', kyHieuCauHoi);
            item.find('.survey-question__number').text('Câu hỏi ' + kyHieuCauHoi);
        }

        item.find('.survey-question__title-label').html(
            laDanhMuc
                ? ('Tên danh mục ' + (laDanhMucCon ? 'con' : 'cha') + ' <span class="text-danger">*</span>')
                : 'Nội dung câu hỏi <span class="text-danger">*</span>'
        );
        item.find('.survey-question__title').attr(
            'placeholder',
            laDanhMuc ? ('Nhập tên danh mục ' + (laDanhMucCon ? 'con' : 'cha')) : 'Nhập nội dung câu hỏi'
        );
        item.find('.survey-question__footer').toggle(!laDanhMuc);
        item.find('.btn-toggle-cau-hoi-danh-muc').toggle(laDanhMuc);
        item.find('.survey-question__type-column').toggle(!laDanhMuc);
        item.find('.survey-question__title-column')
            .toggleClass('col-md-8', !laDanhMuc)
            .toggleClass('col-md-12', laDanhMuc);
        var hienThiCauHinhSo = loai === 'number';
        item.find('.survey-question__number-settings')
            .toggle(hienThiCauHinhSo)
            .find(':input')
            .prop('disabled', !hienThiCauHinhSo);

        var hienThiPhuongAn = loaiCauHoiCanLuaChon(loai) && loai !== 'satisfaction';
        item.find('.survey-question__options :input').prop('disabled', !hienThiPhuongAn);
        var coPhuongAnKhac = item.find('.survey-option__allow-other').filter(function () {
            return $(this).val() === '1';
        }).length > 0;
        item.find('.survey-option--other').appendTo(item.find('.survey-question__option-list'));
        item.find('.survey-option__other-add').toggle(loai === 'radio' && !coPhuongAnKhac);
        if (laDanhMuc) item.find('.survey-question__required').prop('checked', false);
        capNhatTomTatCauHoi(item);
    });
    capNhatCauHoiTheoDanhMuc();
}

function taoLuaChonCauHoi(noiDung, diem, choNhapThem) {
    var luaChon = $('<div>', {
        'class': 'survey-option' + (choNhapThem ? ' survey-option--other' : '')
    });
    luaChon.append($('<i>', { 'class': 'fa fa-circle-o text-muted' }));
    luaChon.append($('<input>', {
        type: 'text',
        'class': 'form-control input-sm survey-option__text',
        placeholder: 'Nhập phương án trả lời',
        value: choNhapThem ? 'Khác' : (noiDung || '')
    }).prop('readonly', !!choNhapThem));
    luaChon.append($('<input>', {
        type: 'number',
        min: 0,
        step: 'any',
        'class': 'form-control input-sm survey-option__score',
        placeholder: 'Điểm',
        title: 'Điểm của phương án này',
        value: diem === undefined || diem === null ? 0 : diem
    }));
    luaChon.append($('<input>', {
        type: 'hidden',
        'class': 'survey-option__allow-other',
        value: choNhapThem ? '1' : '0'
    }));
    luaChon.append(
        $('<button>', {
            type: 'button',
            'class': 'btn btn-link text-danger btn-xoa-lua-chon',
            title: 'Xóa phương án'
        }).append($('<i>', { 'class': 'fa fa-times' }))
    );
    return luaChon;
}

function themThangDiemHaiLong(danhSach) {
    var thangDiem = [
        ['Rất không hài lòng / Rất kém', 1],
        ['Không hài lòng / Kém', 2],
        ['Bình thường / Trung bình', 3],
        ['Hài lòng / Tốt', 4],
        ['Rất hài lòng / Rất tốt', 5],
        ['Không sử dụng, không ý kiến', 0]
    ];

    danhSach.empty();
    thangDiem.forEach(function (item) {
        var luaChon = taoLuaChonCauHoi(item[0], item[1]);
        luaChon.addClass('survey-option--locked');
        luaChon.find('.survey-option__text, .survey-option__score').prop('readonly', true);
        luaChon.find('.btn-xoa-lua-chon').hide();
        danhSach.append(luaChon);
    });
}

function taoCauHoi(idCauHoi) {
    if (idCauHoi) {
        soThuTuCauHoi += 1;
    } else {
        do {
            soThuTuCauHoi += 1;
            idCauHoi = 'q' + soThuTuCauHoi;
        } while ($('#danhSachCauHoi .survey-question[data-question-id="' + idCauHoi + '"]').length);
    }

    var cauHoi = $('<div>', {
        'class': 'survey-question',
        'data-question-id': idCauHoi
    });

    cauHoi.html(
        '<div class="survey-question__drag-handle" title="Kéo để thay đổi thứ tự" aria-label="Kéo để thay đổi thứ tự">' +
            '<i class="fa fa-ellipsis-h"></i>' +
        '</div>' +
        '<div class="survey-question__header">' +
            '<div class="survey-question__heading">' +
                '<strong class="survey-question__number"></strong>' +
                '<span class="survey-question__summary"></span>' +
            '</div>' +
            '<div class="survey-question__actions">' +
                '<button type="button" class="btn btn-default btn-xs btn-toggle-cau-hoi-danh-muc" title="Hiện câu hỏi" aria-label="Hiện câu hỏi" aria-expanded="false" style="display:none"><i class="fa fa-chevron-down"></i></button>' +
                '<button type="button" class="btn btn-default btn-xs btn-nhan-ban-cau-hoi" title="Nhân bản"><i class="fa fa-copy"></i></button>' +
                '<button type="button" class="btn btn-danger btn-xs btn-xoa-cau-hoi" title="Xóa câu hỏi"><i class="fa fa-trash"></i></button>' +
            '</div>' +
        '</div>' +
        '<div class="row">' +
            '<div class="col-md-8 survey-question__title-column"><div class="form-group">' +
                '<label class="survey-question__title-label">Nội dung câu hỏi <span class="text-danger">*</span></label>' +
                '<input type="text" class="form-control survey-question__title" placeholder="Nhập nội dung câu hỏi">' +
            '</div></div>' +
            '<div class="col-md-4 survey-question__type-column"><div class="form-group">' +
                '<label>Loại câu trả lời</label>' +
                '<select class="form-control survey-question__type">' +
                    '<option value="short_text">Trả lời ngắn</option>' +
                    '<option value="long_text">Đoạn văn</option>' +
                    '<option value="number">Số</option>' +
                    '<option value="percentage">Tỷ lệ (%)</option>' +
                    '<option value="score">Chọn điểm (1–10)</option>' +
                    '<option value="date">Ngày tháng</option>' +
                    '<option value="category" hidden>Danh mục cha</option>' +
                    '<option value="subcategory" hidden>Danh mục con</option>' +
                    '<option value="satisfaction">Độ hài lòng</option>' +
                    '<option value="radio">Một lựa chọn</option>' +
                    '<option value="checkbox">Nhiều lựa chọn</option>' +
                    '<option value="select">Danh sách thả xuống</option>' +
                '</select>' +
            '</div></div>' +
        '</div>' +
        '<div class="survey-question__options" style="display:none">' +
            '<label>Phương án trả lời <span class="text-muted">(điểm ở ô bên phải)</span></label>' +
            '<div class="survey-question__option-list"></div>' +
            '<div class="survey-option__add-row">' +
                '<button type="button" class="btn btn-link btn-them-lua-chon">Thêm phương án</button>' +
                '<span class="survey-option__other-add"> hoặc <button type="button" class="btn btn-link btn-them-khac">thêm “Khác”</button></span>' +
            '</div>' +
        '</div>' +
        '<div class="survey-question__number-settings" style="display:none">' +
            '<div class="row"><div class="col-md-4"><div class="form-group">' +
                '<label>Số chữ số tối đa</label>' +
                '<input type="number" min="1" max="50" step="1" class="form-control survey-question__number-length" placeholder="Không giới hạn">' +
                '<span class="help-block">Để trống nếu không giới hạn độ dài.</span>' +
            '</div></div></div>' +
        '</div>' +
        '<div class="survey-question__footer">' +
            '<label><input type="checkbox" class="survey-question__required"> Bắt buộc trả lời</label>' +
        '</div>'
    );

    return cauHoi;
}

function themCauHoi() {
    var cauHoiMoi = taoCauHoi();
    var itemDangChon = $('#danhSachCauHoi > .survey-question--active').first();
    if (itemDangChon.length) {
        var loaiDangChon = itemDangChon.find('.survey-question__type').val();
        var viTriChen = itemDangChon;

        if (laLoaiDanhMuc(loaiDangChon)) {
            var itemKeTiep = itemDangChon.next('.survey-question');
            while (itemKeTiep.length) {
                var loaiKeTiep = itemKeTiep.find('.survey-question__type').val();
                if (laLoaiDanhMuc(loaiKeTiep)) break;
                viTriChen = itemKeTiep;
                itemKeTiep = itemKeTiep.next('.survey-question');
            }
        }
        viTriChen.after(cauHoiMoi);
    } else {
        $('#danhSachCauHoi').append(cauHoiMoi);
    }

    var danhMucChuaCauHoi = cauHoiMoi.prevAll('.survey-question').filter(function () {
        return laLoaiDanhMuc($(this).find('.survey-question__type').val());
    }).first();
    if (danhMucChuaCauHoi.length) {
        danhMucChuaCauHoi.addClass('survey-question--questions-open');
    }
    capNhatTrangThaiBieuMau();
    chonCauHoi(cauHoiMoi);

    if (cauHoiMoi[0] && typeof cauHoiMoi[0].scrollIntoView === 'function') {
        cauHoiMoi[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    window.setTimeout(function () {
        cauHoiMoi.find('.survey-question__title').focus();
    }, 350);
}

function focusNoiDungCauHoi(cauHoi) {
    window.setTimeout(function () {
        cauHoi.find('.survey-question__title').focus();
    }, 0);
}

function capNhatCauHoiTheoDanhMuc() {
    var danhSach = $('#danhSachCauHoi > .survey-question')
        .not('.survey-question__placeholder, .ui-sortable-placeholder');
    var danhMucGanNhat = $();
    var danhMucChaDangMo = true;

    danhSach.each(function () {
        var item = $(this);
        var loai = item.find('.survey-question__type').val();
        if (laLoaiDanhMuc(loai)) {
            if (loai === 'category') {
                danhMucChaDangMo = item.hasClass('survey-question--questions-open');
                item.removeClass('survey-question--parent-hidden');
            } else {
                item.toggleClass('survey-question--parent-hidden', !danhMucChaDangMo);
            }
            danhMucGanNhat = item;
            var dangMo = item.hasClass('survey-question--questions-open');
            item.find('.btn-toggle-cau-hoi-danh-muc')
                .attr('aria-expanded', dangMo ? 'true' : 'false')
                .attr('title', dangMo ? 'Ẩn câu hỏi' : 'Hiện câu hỏi')
                .attr('aria-label', dangMo ? 'Ẩn câu hỏi' : 'Hiện câu hỏi')
                .find('i')
                .toggleClass('fa-chevron-up', dangMo)
                .toggleClass('fa-chevron-down', !dangMo);
            return;
        }

        item.toggleClass(
            'survey-question--group-hidden',
            danhMucGanNhat.length && (
                !danhMucChaDangMo || !danhMucGanNhat.hasClass('survey-question--questions-open')
            )
        );
    });
}

function chonCauHoi(cauHoi) {
    if (!cauHoi || !cauHoi.length) return;
    var danhSachCauHoi = $('#danhSachCauHoi .survey-question');
    danhSachCauHoi.each(function () {
        var item = $(this);
        var dangChon = item.is(cauHoi);
        capNhatTomTatCauHoi(item);
        item.toggleClass('survey-question--active', dangChon);
        item.toggleClass('survey-question--collapsed', !dangChon);
    });

    var thanhCongCu = $('.survey-builder__toolbar');
    var canvas = $('.survey-builder__canvas');
    if (thanhCongCu.length && canvas.length) {
        var viTriTren = cauHoi.offset().top - canvas.offset().top;
        thanhCongCu.css('top', Math.max(0, viTriTren) + 'px');
    }
    $('#btnImportCauHoi').toggle(cauHoi.find('.survey-question__type').val() === 'category');
}

function capNhatTomTatCauHoi(cauHoi) {
    var noiDung = $.trim(cauHoi.find('.survey-question__title').val()) || 'Câu hỏi chưa có nội dung';
    cauHoi.find('.survey-question__summary').text(noiDung);
}

function khoiTaoBieuMauKhaoSat() {
    soThuTuCauHoi = 0;
    $('#danhSachCauHoi').empty();
    capNhatTrangThaiBieuMau();
}

function kiemTraHopLeBieuMauCauHoi() {
    var danhSach = $('#danhSachCauHoi > .survey-question')
        .not('.survey-question__placeholder, .ui-sortable-placeholder');
    var soCauHoiThuong = 0;
    var loi = null;

    danhSach.each(function (index) {
        if (loi) return false;
        var item = $(this);
        var loai = item.find('.survey-question__type').val();
        var noiDung = $.trim(item.find('.survey-question__title').val());
        var tenDoiTuong = loai === 'category'
            ? 'danh mục cha'
            : (loai === 'subcategory' ? 'danh mục con' : 'câu hỏi');

        if (!noiDung) {
            loi = {
                hopLe: false,
                item: item,
                truong: item.find('.survey-question__title'),
                thongBao: 'Vui lòng nhập nội dung ' + tenDoiTuong + ' số ' + (index + 1) + '.'
            };
            return false;
        }

        if (loai === 'subcategory') {
            var coDanhMucCha = item.prevAll('.survey-question').filter(function () {
                return $(this).find('.survey-question__type').val() === 'category';
            }).length > 0;
            if (!coDanhMucCha) {
                loi = {
                    hopLe: false,
                    item: item,
                    truong: item.find('.survey-question__title'),
                    thongBao: 'Danh mục con “' + noiDung + '” chưa thuộc danh mục cha.'
                };
                return false;
            }
        }

        if (laLoaiDanhMuc(loai)) return;
        soCauHoiThuong += 1;

        if (!loai) {
            loi = {
                hopLe: false,
                item: item,
                truong: item.find('.survey-question__type'),
                thongBao: 'Vui lòng chọn loại câu trả lời cho câu hỏi “' + noiDung + '”.'
            };
            return false;
        }

        if (['radio', 'checkbox', 'select'].indexOf(loai) !== -1) {
            var danhSachPhuongAn = item.find('.survey-option');
            if (!danhSachPhuongAn.length) {
                loi = {
                    hopLe: false,
                    item: item,
                    truong: item.find('.btn-them-lua-chon'),
                    thongBao: 'Câu hỏi “' + noiDung + '” cần ít nhất một phương án trả lời.'
                };
                return false;
            }

            var phuongAnTrong = danhSachPhuongAn.filter(function () {
                return $.trim($(this).find('.survey-option__text').val()) === '';
            }).first();
            if (phuongAnTrong.length) {
                loi = {
                    hopLe: false,
                    item: item,
                    truong: phuongAnTrong.find('.survey-option__text'),
                    thongBao: 'Câu hỏi “' + noiDung + '” có phương án trả lời chưa nhập nội dung.'
                };
                return false;
            }
        }
    });

    if (loi) return loi;
    if (danhSach.length && !soCauHoiThuong) {
        return {
            hopLe: false,
            item: danhSach.first(),
            truong: danhSach.first().find('.survey-question__title'),
            thongBao: 'Biểu mẫu khảo sát cần có ít nhất một câu hỏi.'
        };
    }
    return { hopLe: true };
}

function hienThiLoiBieuMauCauHoi(ketQua) {
    var item = ketQua.item;
    if (item && item.length) {
        var danhMucCha = item.prevAll('.survey-question').filter(function () {
            return $(this).find('.survey-question__type').val() === 'category';
        }).first();
        var danhMucGanNhat = item.prevAll('.survey-question').filter(function () {
            return laLoaiDanhMuc($(this).find('.survey-question__type').val());
        }).first();
        danhMucCha.add(danhMucGanNhat).addClass('survey-question--questions-open');
        capNhatCauHoiTheoDanhMuc();
        chonCauHoi(item);
        if (item[0] && typeof item[0].scrollIntoView === 'function') {
            item[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    Swal.fire('Câu hỏi chưa hợp lệ', ketQua.thongBao, 'warning').then(function () {
        if (ketQua.truong && ketQua.truong.length) ketQua.truong.first().focus();
    });
}

function layDuLieuBieuMau() {
    var cauHoi = [];

    $('#danhSachCauHoi .survey-question').each(function (index) {
        var item = $(this);
        var loai = item.find('.survey-question__type').val();
        var doDaiSo = parseInt(item.find('.survey-question__number-length').val(), 10);
        var luaChon = [];

        if (loaiCauHoiCanLuaChon(loai)) {
            item.find('.survey-option').each(function () {
                var noiDung = vietHoaDauCau($.trim($(this).find('.survey-option__text').val()));
                if (noiDung !== '') {
                    luaChon.push({
                        noi_dung: noiDung,
                        diem: parseFloat($(this).find('.survey-option__score').val()) || 0,
                        cho_nhap_them: loai === 'radio' && $(this).find('.survey-option__allow-other').val() === '1'
                    });
                }
            });
        }

        var noiDung = $.trim(item.find('.survey-question__title').val());
        noiDung = loai === 'category' ? noiDung.toUpperCase() : vietHoaDauCau(noiDung);

        cauHoi.push({
            id: item.attr('data-question-id') || ('q' + (index + 1)),
            noi_dung: noiDung,
            loai: loai,
            ky_hieu: item.attr('data-tree-label') || '',
            bat_buoc: !laLoaiDanhMuc(loai) && item.find('.survey-question__required').prop('checked'),
            do_dai_so: loai === 'number' && doDaiSo > 0 ? Math.min(doDaiSo, 50) : 0,
            lua_chon: luaChon
        });
    });

    return {
        version: 1,
        cau_hoi: cauHoi
    };
}

function napDuLieuBieuMau(duLieu) {
    if (!duLieu) {
        return;
    }

    if (typeof duLieu === 'string') {
        try {
            duLieu = JSON.parse(duLieu);
        } catch (error) {
            return;
        }
    }

    var danhSach = Array.isArray(duLieu) ? duLieu : duLieu.cau_hoi;
    if (!Array.isArray(danhSach)) {
        return;
    }

    danhSach.forEach(function (duLieuCauHoi) {
        var cauHoi = taoCauHoi(duLieuCauHoi.id);
        var loai = duLieuCauHoi.loai || 'short_text';

        cauHoi.find('.survey-question__title').val(
            loai === 'category'
                ? String(duLieuCauHoi.noi_dung || '').toUpperCase()
                : vietHoaDauCau(duLieuCauHoi.noi_dung || '')
        );
        cauHoi.find('.survey-question__required').prop('checked', !!duLieuCauHoi.bat_buoc);
        cauHoi.find('.survey-question__number-length').val(duLieuCauHoi.do_dai_so || '');
        cauHoi.find('.survey-question__type').val(loai);

        if (loaiCauHoiCanLuaChon(loai)) {
            var danhSachLuaChon = cauHoi.find('.survey-question__option-list');
            var luaChon = Array.isArray(duLieuCauHoi.lua_chon) ? duLieuCauHoi.lua_chon : [];

            if (loai === 'satisfaction') {
                themThangDiemHaiLong(danhSachLuaChon);
                cauHoi.find('.btn-them-lua-chon').hide();
            } else {
                luaChon.forEach(function (luaChonItem) {
                    var noiDung = typeof luaChonItem === 'object' ? luaChonItem.noi_dung : luaChonItem;
                    var diem = typeof luaChonItem === 'object' ? luaChonItem.diem : 0;
                    var choNhapThem = luaChonItem && typeof luaChonItem === 'object' && !!luaChonItem.cho_nhap_them;
                    danhSachLuaChon.append(taoLuaChonCauHoi(vietHoaDauCau(noiDung), diem, choNhapThem));
                });
            }
            cauHoi.find('.survey-question__options').toggle(loai !== 'satisfaction');
        }

        $('#danhSachCauHoi').append(cauHoi);
    });

    capNhatTrangThaiBieuMau();
    var cauHoiDauTien = $('#danhSachCauHoi .survey-question').first();
    if (cauHoiDauTien.length) chonCauHoi(cauHoiDauTien);
}

$('#btnThemCauHoi').on('click', function (event) {
    event.preventDefault();
    themCauHoi();
});

$('#btnThemDanhMuc').on('click', function (event) {
    event.preventDefault();
    var danhMucMoi = taoCauHoi();
    danhMucMoi.find('.survey-question__type').val('category');
    $('#danhSachCauHoi').append(danhMucMoi);
    capNhatTrangThaiBieuMau();
    chonCauHoi(danhMucMoi);
    danhMucMoi.find('.survey-question__title').focus();
});

$('#btnThemDanhMucCon').on('click', function (event) {
    event.preventDefault();
    var dangChon = $('#danhSachCauHoi .survey-question--active');
    var danhMucCha = dangChon.filter(function () {
        return $(this).find('.survey-question__type').val() === 'category';
    });
    if (!danhMucCha.length && dangChon.length) {
        danhMucCha = dangChon.prevAll('.survey-question').filter(function () {
            return $(this).find('.survey-question__type').val() === 'category';
        }).first();
    }

    if (!danhMucCha.length) {
        Swal.fire('Chưa có danh mục cha', 'Hãy thêm hoặc chọn một danh mục cha trước.', 'info');
        return;
    }

    var danhMucConMoi = taoCauHoi();
    danhMucConMoi.find('.survey-question__type').val('subcategory');
    if (dangChon.length && (dangChon.is(danhMucCha) || dangChon.prevAll().is(danhMucCha))) {
        dangChon.after(danhMucConMoi);
    } else {
        danhMucCha.after(danhMucConMoi);
    }
    capNhatTrangThaiBieuMau();
    chonCauHoi(danhMucConMoi);
    danhMucConMoi.find('.survey-question__title').focus();
});

$('#btnImportCauHoi').on('click', function (event) {
    event.preventDefault();
    var danhMucCha = $('#danhSachCauHoi .survey-question--active');
    if (!danhMucCha.length || danhMucCha.find('.survey-question__type').val() !== 'category') return;
    $('#fileImportCauHoi').val('').trigger('click');
});

function chuanHoaTieuDeImport(value) {
    return String(value || '').toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/g, 'd').replace(/[^a-z0-9]+/g, ' ').trim();
}

function loaiCauHoiTuExcel(value, coLuaChon, macDinhHaiLong) {
    var loai = chuanHoaTieuDeImport(value);
    if (loai.indexOf('hop kiem') !== -1 || loai.indexOf('nhieu lua chon') !== -1) return 'checkbox';
    if (loai.indexOf('doan van') !== -1) return 'long_text';
    if (loai.indexOf('tra loi ngan') !== -1) return 'short_text';
    if (loai.indexOf('danh sach') !== -1 || loai.indexOf('tha xuong') !== -1) return 'select';
    if (loai.indexOf('hai long') !== -1 || loai.indexOf('danh gia') !== -1) return 'satisfaction';
    if (loai.indexOf('trac nghiem') !== -1 || loai.indexOf('mot lua chon') !== -1 || coLuaChon) return 'radio';
    return macDinhHaiLong ? 'satisfaction' : 'short_text';
}

function docFileXlsxCauHoi(arrayBuffer) {
    if (!window.JSZip) return Promise.reject(new Error('Thiếu thư viện đọc XLSX.'));
    var moFileZip;
    if (typeof JSZip.loadAsync === 'function') {
        moFileZip = JSZip.loadAsync(arrayBuffer);
    } else {
        try {
            moFileZip = Promise.resolve(new JSZip(arrayBuffer));
        } catch (error) {
            moFileZip = Promise.reject(error);
        }
    }

    return moFileZip.then(function (zip) {
        function docXml(path) {
            var entry = zip.file(path);
            if (!entry) return Promise.reject(new Error('Tệp XLSX không hợp lệ: ' + path));
            var noiDung = typeof entry.async === 'function'
                ? entry.async('string')
                : Promise.resolve(entry.asText());
            return noiDung.then(function (xml) {
                return new DOMParser().parseFromString(xml, 'application/xml');
            });
        }

        return Promise.all([
            docXml('xl/workbook.xml'),
            docXml('xl/_rels/workbook.xml.rels'),
            zip.file('xl/sharedStrings.xml') ? docXml('xl/sharedStrings.xml') : Promise.resolve(null)
        ]).then(function (parts) {
            var workbook = parts[0];
            var relationships = parts[1];
            var sharedXml = parts[2];
            var shared = [];
            var relMap = {};

            if (sharedXml) {
                Array.prototype.forEach.call(sharedXml.getElementsByTagName('si'), function (si) {
                    shared.push(si.textContent || '');
                });
            }
            Array.prototype.forEach.call(relationships.getElementsByTagName('Relationship'), function (rel) {
                relMap[rel.getAttribute('Id')] = rel.getAttribute('Target');
            });

            var sheetPromises = Array.prototype.map.call(workbook.getElementsByTagName('sheet'), function (sheet) {
                var target = relMap[sheet.getAttribute('r:id')];
                target = String(target || '').replace(/^\//, '').replace(/^xl\//, '');
                return docXml('xl/' + target).then(function (sheetXml) {
                    var rows = [];
                    Array.prototype.forEach.call(sheetXml.getElementsByTagName('row'), function (rowXml) {
                        var row = [];
                        Array.prototype.forEach.call(rowXml.getElementsByTagName('c'), function (cell) {
                            var ref = cell.getAttribute('r') || 'A1';
                            var letters = ref.replace(/[0-9]/g, '');
                            var column = 0;
                            for (var i = 0; i < letters.length; i++) column = column * 26 + letters.charCodeAt(i) - 64;
                            var type = cell.getAttribute('t');
                            var valueNode = cell.getElementsByTagName('v')[0];
                            var value = type === 'inlineStr'
                                ? ((cell.getElementsByTagName('is')[0] || {}).textContent || '')
                                : (valueNode ? valueNode.textContent : '');
                            if (type === 's') value = shared[parseInt(value, 10)] || '';
                            row[column - 1] = value;
                        });
                        rows.push(row);
                    });
                    return { name: sheet.getAttribute('name') || 'Sheet', rows: rows };
                });
            });
            return Promise.all(sheetPromises);
        });
    });
}

var importCauHoiDangCho = null;

function taiFileMauImportCauHoi() {
    var link = document.createElement('a');
    link.href = '../template/Mau_import_cau_hoi_khao_sat.xlsx';
    link.download = 'Mau_import_cau_hoi_khao_sat.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function thongBaoLoiDinhDangImport(thongBao) {
    return Swal.fire({
        title: 'Sai định dạng file',
        text: thongBao,
        icon: 'error',
        showDenyButton: true,
        confirmButtonText: 'Đóng',
        denyButtonText: '<i class="fa fa-download"></i> Tải file mẫu'
    }).then(function (result) {
        if (result.isDenied) taiFileMauImportCauHoi();
    });
}

function docBangCsvCauHoi(noiDung) {
    var cacDong = String(noiDung || '').replace(/^\uFEFF/, '').split(/\r?\n/)
        .filter(function (dong) { return $.trim(dong) !== ''; });
    if (!cacDong.length) return [];
    var kyTuPhanCach = cacDong[0].split(';').length >= cacDong[0].split(',').length ? ';' : ',';

    return cacDong.map(function (dong) {
        var ketQua = [];
        var giaTri = '';
        var trongNgoac = false;
        for (var i = 0; i < dong.length; i++) {
            var kyTu = dong.charAt(i);
            if (kyTu === '"') {
                if (trongNgoac && dong.charAt(i + 1) === '"') {
                    giaTri += '"';
                    i += 1;
                } else {
                    trongNgoac = !trongNgoac;
                }
            } else if (kyTu === kyTuPhanCach && !trongNgoac) {
                ketQua.push(giaTri);
                giaTri = '';
            } else {
                giaTri += kyTu;
            }
        }
        ketQua.push(giaTri);
        return ketQua;
    });
}

function kiemTraDinhDangBangExcel(rows) {
    if (!Array.isArray(rows) || !rows.length) {
        return { hopLe: false, thongBao: 'Sheet không có dữ liệu.' };
    }

    var dongCoDuLieu = rows.map(function (row, index) {
        return { row: Array.isArray(row) ? row : [], soDong: index + 1 };
    }).filter(function (item) {
        return item.row.some(function (cell) { return $.trim(String(cell || '')) !== ''; });
    });
    if (!dongCoDuLieu.length) return { hopLe: false, thongBao: 'Sheet không có dữ liệu.' };

    var tieuDe = dongCoDuLieu[0].row.map(chuanHoaTieuDeImport);
    var coTieuDe = tieuDe.indexOf('noi dung cau hoi') !== -1 || tieuDe.indexOf('ma cau hoi') !== -1;
    var cotNoiDung = coTieuDe ? tieuDe.indexOf('noi dung cau hoi') : 3;
    if (coTieuDe && cotNoiDung < 0) {
        return {
            hopLe: false,
            thongBao: 'Thiếu cột bắt buộc “Nội dung câu hỏi” trong dòng tiêu đề.'
        };
    }

    var duLieu = coTieuDe ? dongCoDuLieu.slice(1) : dongCoDuLieu;
    if (!duLieu.length) return { hopLe: false, thongBao: 'File chỉ có tiêu đề, chưa có câu hỏi.' };
    var dongLoi = duLieu.filter(function (item) {
        return $.trim(String(item.row[cotNoiDung] || '')) === '';
    }).map(function (item) { return item.soDong; });

    if (dongLoi.length) {
        return {
            hopLe: false,
            thongBao: 'Thiếu nội dung câu hỏi tại dòng ' + dongLoi.slice(0, 5).join(', ') +
                (dongLoi.length > 5 ? '…' : '') + '.'
        };
    }
    if (!coTieuDe && duLieu.some(function (item) { return item.row.length < 4; })) {
        return {
            hopLe: false,
            thongBao: 'File không có tiêu đề phải đủ 4 cột theo thứ tự: STT, Mã, Nhóm, Nội dung câu hỏi.'
        };
    }
    return { hopLe: true };
}

function phanTichCauHoiTuBangExcel(rows) {
    if (!rows || !rows.length) return [];
    var first = rows[0].map(chuanHoaTieuDeImport);
    var coTieuDe = first.indexOf('noi dung cau hoi') !== -1 || first.indexOf('ma cau hoi') !== -1;
    var cot = {
        ma: coTieuDe ? first.indexOf('ma cau hoi') : 1,
        nhom: coTieuDe ? first.indexOf('nhom cau hoi') : 2,
        noiDung: coTieuDe ? first.indexOf('noi dung cau hoi') : 3,
        loai: coTieuDe ? first.indexOf('loai cau hoi') : -1,
        batBuoc: coTieuDe ? first.indexOf('bat buoc') : -1,
        luaChon: coTieuDe ? first.indexOf('lua chon tra loi ngan cach boi') : -1
    };
    var duLieu = coTieuDe ? rows.slice(1) : rows;
    var nhomHienTai = '';
    var ketQua = [];

    duLieu.forEach(function (row) {
        var noiDung = $.trim(String(row[cot.noiDung] || ''));
        if (!noiDung) return;
        var nhom = $.trim(String(row[cot.nhom] || ''));
        if (nhom && nhom !== nhomHienTai) {
            ketQua.push({ loai: 'subcategory', noi_dung: nhom, ma: '', bat_buoc: false, lua_chon: [] });
            nhomHienTai = nhom;
        }

        var luaChonText = cot.luaChon >= 0 ? String(row[cot.luaChon] || '') : '';
        var luaChon = luaChonText.split('|').map(function (item) { return $.trim(item); })
            .filter(function (item) { return item !== ''; });
        var loai = loaiCauHoiTuExcel(cot.loai >= 0 ? row[cot.loai] : '', luaChon.length > 0, !coTieuDe);
        ketQua.push({
            loai: loai,
            ma: cot.ma >= 0 ? $.trim(String(row[cot.ma] || '')) : '',
            noi_dung: noiDung,
            bat_buoc: cot.batBuoc >= 0 && chuanHoaTieuDeImport(row[cot.batBuoc]) === 'co',
            lua_chon: luaChon
        });
    });
    return ketQua;
}

function hienThiXemTruocImport(items, danhMucCha) {
    var danhSach = $('#danhSachXemTruocImport').empty();
    var tenLoai = {
        short_text: 'Trả lời ngắn', long_text: 'Đoạn văn', radio: 'Một lựa chọn',
        checkbox: 'Nhiều lựa chọn', select: 'Danh sách', satisfaction: 'Độ hài lòng'
    };
    var soCauHoi = 0;
    items.forEach(function (item) {
        var laDanhMuc = item.loai === 'subcategory';
        if (!laDanhMuc) soCauHoi += 1;
        var dong = $('<div>', {
            'class': 'import-preview__item' + (laDanhMuc ? ' import-preview__item--category' : '')
        }).data('import-item', item);
        dong.append($('<span>', { 'class': 'import-preview__handle' }).append(
            $('<i>', { 'class': 'fa fa-ellipsis-v' })
        ));
        var noiDung = $('<div>', { 'class': 'import-preview__content' });
        if (!laDanhMuc && item.ma) noiDung.append($('<span>', { 'class': 'import-preview__code', text: item.ma }));
        noiDung.append(document.createTextNode(item.noi_dung));
        dong.append(noiDung);
        dong.append($('<span>', {
            'class': 'import-preview__type',
            text: laDanhMuc ? 'Danh mục con' : (tenLoai[item.loai] || item.loai)
        }));
        danhSach.append(dong);
    });

    importCauHoiDangCho = { danhMucCha: danhMucCha };
    $('#tongXemTruocImport').text(soCauHoi + ' câu hỏi • ' + (items.length - soCauHoi) + ' danh mục con');
    if ($.fn.sortable) {
        danhSach.sortable({
            items: '> .import-preview__item:not(.import-preview__placeholder):not(.ui-sortable-placeholder)',
            handle: '.import-preview__handle',
            axis: 'y',
            tolerance: 'pointer',
            placeholder: 'import-preview__placeholder',
            forcePlaceholderSize: true,
            start: function (event, ui) {
                if (!ui.item.hasClass('import-preview__item--category')) return;
                var cauHoiTrongMuc = ui.item.nextUntil('.import-preview__item--category').detach();
                ui.item.data('import-children', cauHoiTrongMuc);
                var cauHoiTamAn = danhSach.children('.import-preview__item:not(.import-preview__item--category)')
                    .not('.import-preview__placeholder, .ui-sortable-placeholder')
                    .hide();
                ui.item.data('import-hidden-questions', cauHoiTamAn);
                danhSach.sortable('refreshPositions');
            },
            sort: function (event, ui) {
                if (!ui.item.hasClass('import-preview__item--category')) return;
                var danhMucKeTiep = ui.placeholder.nextAll('.import-preview__item--category').first();
                if (danhMucKeTiep.length) ui.placeholder.insertBefore(danhMucKeTiep);
                else danhSach.append(ui.placeholder);
            },
            stop: function (event, ui) {
                var cauHoiTrongMuc = ui.item.data('import-children');
                if (cauHoiTrongMuc && cauHoiTrongMuc.length) ui.item.after(cauHoiTrongMuc);
                var cauHoiTamAn = ui.item.data('import-hidden-questions');
                if (cauHoiTamAn && cauHoiTamAn.length) cauHoiTamAn.show();
                ui.item.removeData('import-children');
                ui.item.removeData('import-hidden-questions');
                setTimeout(function () {
                    danhSach.children('.import-preview__placeholder, .ui-sortable-placeholder').remove();
                }, 0);
            }
        });
    }

    $('#modalChiTieu').one('hidden.bs.modal.importPreview', function () {
        $('#modalXemTruocImport').modal('show');
    }).modal('hide');
}

function themCauHoiXemTruocVaoBieuMau() {
    if (!importCauHoiDangCho || !importCauHoiDangCho.danhMucCha.length) return 0;
    var viTriChen = importCauHoiDangCho.danhMucCha;
    var soCauHoi = 0;
    $('#danhSachXemTruocImport .import-preview__item').each(function () {
        var item = $(this).data('import-item');
        if (!item) return;
        var cauHoiMoi = taoCauHoi();
        cauHoiMoi.find('.survey-question__title').val(
            item.loai === 'category' ? String(item.noi_dung || '').toUpperCase() : vietHoaDauCau(item.noi_dung)
        );
        cauHoiMoi.find('.survey-question__type').val(item.loai);
        cauHoiMoi.find('.survey-question__required').prop('checked', !!item.bat_buoc);
        if (item.loai === 'satisfaction') {
            themThangDiemHaiLong(cauHoiMoi.find('.survey-question__option-list'));
            cauHoiMoi.find('.btn-them-lua-chon').hide();
        } else if (loaiCauHoiCanLuaChon(item.loai)) {
            var optionList = cauHoiMoi.find('.survey-question__option-list');
            (item.lua_chon || []).forEach(function (option) {
                optionList.append(taoLuaChonCauHoi(option, 0));
            });
            cauHoiMoi.find('.survey-question__options').show();
        }
        viTriChen.after(cauHoiMoi);
        viTriChen = cauHoiMoi;
        if (item.loai !== 'subcategory') soCauHoi += 1;
    });

    if (soCauHoi) {
        capNhatTrangThaiBieuMau();
        chonCauHoi(viTriChen);
        lenLichLuuBanNhapBieuMau();
    }
    return soCauHoi;
}

$('#btnXacNhanImportCauHoi').on('click', function () {
    var soLuong = themCauHoiXemTruocVaoBieuMau();
    if (!soLuong) return;
    $('#modalXemTruocImport').modal('hide');
    Swal.fire('Đã thêm vào phiếu', 'Đã thêm ' + soLuong + ' câu hỏi theo thứ tự đã sắp xếp.', 'success');
});

$('#modalXemTruocImport').on('hidden.bs.modal', function () {
    importCauHoiDangCho = null;
    $('#modalChiTieu').modal('show');
});

$('#fileImportCauHoi').on('change', function () {
    var file = this.files && this.files[0];
    var danhMucCha = $('#danhSachCauHoi .survey-question--active');
    if (!file || !danhMucCha.length || danhMucCha.find('.survey-question__type').val() !== 'category') return;

    if (!/\.(xlsx|txt|csv)$/i.test(file.name || '')) {
        thongBaoLoiDinhDangImport('Chỉ hỗ trợ file XLSX, TXT hoặc CSV.');
        this.value = '';
        return;
    }

    var reader = new FileReader();
    var laXlsx = /\.xlsx$/i.test(file.name || '');
    reader.onload = function (event) {
        if (laXlsx) {
            docFileXlsxCauHoi(event.target.result).then(function (sheets) {
                var importSheet = function (sheet) {
                    var kiemTra = kiemTraDinhDangBangExcel(sheet.rows);
                    if (!kiemTra.hopLe) {
                        thongBaoLoiDinhDangImport(
                            kiemTra.thongBao + ' Dữ liệu hiện tại chưa được thêm vào phiếu khảo sát.'
                        );
                        return;
                    }
                    var items = phanTichCauHoiTuBangExcel(sheet.rows);
                    var soLuong = items.filter(function (item) { return item.loai !== 'subcategory'; }).length;
                    if (!soLuong) {
                        Swal.fire('Không có dữ liệu', 'Sheet đã chọn không có câu hỏi hợp lệ.', 'info');
                        return;
                    }
                    hienThiXemTruocImport(items, danhMucCha);
                };

                var sheetCoDuLieu = sheets.filter(function (sheet) {
                    return sheet.rows && sheet.rows.some(function (row) {
                        return row && row.some(function (cell) { return $.trim(String(cell || '')) !== ''; });
                    });
                });
                if (!sheetCoDuLieu.length) {
                    Swal.fire('Không có dữ liệu', 'File Excel không có sheet câu hỏi hợp lệ.', 'info');
                    return;
                }
                if (sheetCoDuLieu.length === 1) {
                    importSheet(sheetCoDuLieu[0]);
                    return;
                }

                var options = {};
                sheetCoDuLieu.forEach(function (sheet, index) { options[index] = sheet.name; });
                return Swal.fire({
                    title: 'Chọn sheet câu hỏi',
                    input: 'select',
                    inputOptions: options,
                    inputPlaceholder: 'Chọn một sheet',
                    showCancelButton: true,
                    confirmButtonText: 'Import',
                    cancelButtonText: 'Hủy',
                    inputValidator: function (value) {
                        return value === '' ? 'Vui lòng chọn sheet cần import.' : undefined;
                    }
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    importSheet(sheetCoDuLieu[parseInt(result.value, 10)]);
                });
            }).catch(function (error) {
                thongBaoLoiDinhDangImport(error.message || 'Tệp XLSX không hợp lệ.');
            });
            return;
        }

        if (/\.csv$/i.test(file.name || '')) {
            var bangCsv = docBangCsvCauHoi(event.target.result);
            var kiemTraCsv = kiemTraDinhDangBangExcel(bangCsv);
            if (!kiemTraCsv.hopLe) {
                thongBaoLoiDinhDangImport(
                    kiemTraCsv.thongBao + ' Dữ liệu hiện tại chưa được thêm vào phiếu khảo sát.'
                );
                return;
            }
            hienThiXemTruocImport(phanTichCauHoiTuBangExcel(bangCsv), danhMucCha);
            return;
        }

        var cacDong = String(event.target.result || '').replace(/^\uFEFF/, '').split(/\r?\n/);
        var cauHoi = cacDong.map(function (dong) {
            return $.trim(dong.replace(/^\s*["']|["']\s*$/g, ''));
        }).filter(function (dong) {
            return dong !== '';
        });

        if (!cauHoi.length) {
            Swal.fire('Không có dữ liệu', 'Tệp chưa có câu hỏi hợp lệ.', 'info');
            return;
        }

        hienThiXemTruocImport(cauHoi.map(function (noiDung) {
            return { loai: 'short_text', ma: '', noi_dung: noiDung, bat_buoc: false, lua_chon: [] };
        }), danhMucCha);
    };
    reader.onerror = function () {
        Swal.fire('Không thể đọc tệp', 'Vui lòng kiểm tra lại tệp XLSX, TXT hoặc CSV.', 'error');
    };
    if (laXlsx) reader.readAsArrayBuffer(file);
    else reader.readAsText(file, 'UTF-8');
});

$('#btnXoaBanNhapBieuMau').on('click', function (event) {
    event.preventDefault();
    event.stopPropagation();

    Swal.fire({
        title: 'Xóa bản nháp?',
        text: 'Toàn bộ thông tin chỉ tiêu và câu hỏi chưa lưu sẽ bị xóa.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Xóa bản nháp',
        cancelButtonText: 'Hủy'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        xoaBanNhapBieuMau();
        window.location.reload();
    });
});

$('#danhSachCauHoi').on('click', '.btn-toggle-cau-hoi-danh-muc', function (event) {
    event.preventDefault();
    event.stopPropagation();

    var danhMuc = $(this).closest('.survey-question');
    var loai = danhMuc.find('.survey-question__type').val();
    var seMo = !danhMuc.hasClass('survey-question--questions-open');
    var cacDanhMucCanDoi = danhMuc;

    if (loai === 'category') {
        var itemKeTiep = danhMuc.next('.survey-question');
        while (itemKeTiep.length) {
            var loaiKeTiep = itemKeTiep.find('.survey-question__type').val();
            if (loaiKeTiep === 'category') break;
            if (loaiKeTiep === 'subcategory') cacDanhMucCanDoi = cacDanhMucCanDoi.add(itemKeTiep);
            itemKeTiep = itemKeTiep.next('.survey-question');
        }
    }

    cacDanhMucCanDoi.toggleClass('survey-question--questions-open', seMo);
    capNhatCauHoiTheoDanhMuc();
    if ($.fn.sortable) $('#danhSachCauHoi').sortable('refreshPositions');
});

$('#danhSachCauHoi').on('click', '.survey-question', function (event) {
    if ($(event.target).closest('.btn-toggle-cau-hoi-danh-muc').length) return;
    chonCauHoi($(this));
});

$('#danhSachCauHoi').on('focusin', '.survey-question input, .survey-question textarea, .survey-question select', function () {
    chonCauHoi($(this).closest('.survey-question'));
});

$('#danhSachCauHoi').on('click', '.survey-question--collapsed', function (event) {
    if ($(event.target).closest('button').length) return;
    chonCauHoi($(this));
});

$('#danhSachCauHoi').on('change', '.survey-question__type', function () {
    var cauHoi = $(this).closest('.survey-question');
    var khuVucLuaChon = cauHoi.find('.survey-question__options');
    var loai = $(this).val();
    if (loai !== 'radio') cauHoi.find('.survey-option--other').remove();

    if (!loaiCauHoiCanLuaChon(loai)) {
        khuVucLuaChon.hide();
        capNhatTrangThaiBieuMau();
        focusNoiDungCauHoi(cauHoi);
        return;
    }

    var danhSachLuaChon = cauHoi.find('.survey-question__option-list');
    if (loai === 'satisfaction') {
        themThangDiemHaiLong(danhSachLuaChon);
        cauHoi.find('.btn-them-lua-chon').hide();
        khuVucLuaChon.hide();
        capNhatTrangThaiBieuMau();
        focusNoiDungCauHoi(cauHoi);
        return;
    }

    danhSachLuaChon.find('.survey-option').removeClass('survey-option--locked');
    danhSachLuaChon.find('.survey-option__text, .survey-option__score').prop('readonly', false);
    danhSachLuaChon.find('.survey-option--other .survey-option__text').prop('readonly', true);
    danhSachLuaChon.find('.btn-xoa-lua-chon').show();
    cauHoi.find('.btn-them-lua-chon').show();
    if (!danhSachLuaChon.children().length) {
        danhSachLuaChon.append(taoLuaChonCauHoi('Lựa chọn 1'));
        danhSachLuaChon.append(taoLuaChonCauHoi('Lựa chọn 2'));
    }
    khuVucLuaChon.show();
    capNhatTrangThaiBieuMau();
    focusNoiDungCauHoi(cauHoi);
});

$('#danhSachCauHoi').on('input', '.survey-question__title', function () {
    var input = $(this);
    var loai = input.closest('.survey-question').find('.survey-question__type').val();
    input.val(loai === 'category' ? String(input.val()).toUpperCase() : vietHoaDauCau(input.val()));
    capNhatTomTatCauHoi(input.closest('.survey-question'));
});

$('#danhSachCauHoi').on('input', '.survey-option__text', function () {
    $(this).val(vietHoaDauCau($(this).val()));
});

$('#danhSachCauHoi').on('click', '.btn-them-lua-chon', function () {
    var danhSachLuaChon = $(this).closest('.survey-question__options').find('.survey-question__option-list');
    var phuongAnKhac = danhSachLuaChon.children('.survey-option--other').first();
    var phuongAnMoi = taoLuaChonCauHoi();
    if (phuongAnKhac.length) phuongAnMoi.insertBefore(phuongAnKhac);
    else danhSachLuaChon.append(phuongAnMoi);
    capNhatTrangThaiBieuMau();
});

$('#danhSachCauHoi').on('click', '.btn-them-khac', function () {
    var cauHoi = $(this).closest('.survey-question');
    if (cauHoi.find('.survey-option__allow-other[value="1"]').length) return;
    cauHoi.find('.survey-question__option-list').append(taoLuaChonCauHoi('Khác', 0, true));
    capNhatTrangThaiBieuMau();
});

$('#danhSachCauHoi').on('click', '.btn-xoa-lua-chon', function () {
    $(this).closest('.survey-option').remove();
    capNhatTrangThaiBieuMau();
});

$('#modalChiTieu').on('wheel', 'input[type="number"]', function (event) {
    if (document.activeElement === this) event.preventDefault();
});

$('#danhSachCauHoi').on('click', '.btn-xoa-cau-hoi', function () {
    var cauHoi = $(this).closest('.survey-question');
    var cauHoiKeTiep = cauHoi.next('.survey-question').length
        ? cauHoi.next('.survey-question')
        : cauHoi.prev('.survey-question');
    cauHoi.remove();
    capNhatTrangThaiBieuMau();
    if (cauHoiKeTiep.length) chonCauHoi(cauHoiKeTiep);
});

$('#danhSachCauHoi').on('click', '.btn-nhan-ban-cau-hoi', function () {
    var cauHoiGoc = $(this).closest('.survey-question');
    var cauHoiMoi = cauHoiGoc.clone(false, false);
    var idCauHoi;
    do {
        soThuTuCauHoi += 1;
        idCauHoi = 'q' + soThuTuCauHoi;
    } while ($('#danhSachCauHoi .survey-question[data-question-id="' + idCauHoi + '"]').length);
    cauHoiMoi.attr('data-question-id', idCauHoi).removeAttr('data-tree-label');
    cauHoiGoc.after(cauHoiMoi);
    capNhatTrangThaiBieuMau();
    chonCauHoi(cauHoiMoi);
    cauHoiMoi.find('.survey-question__title').focus();
});

$('#formChiTieu').on('input change', ':input', function () {
    lenLichLuuBanNhapBieuMau();
});

$('#surveyBuilder').on(
    'click',
    '#btnThemCauHoi, #btnThemDanhMuc, #btnThemDanhMucCon, .btn-them-lua-chon, .btn-them-khac, .btn-xoa-lua-chon, .btn-xoa-cau-hoi, .btn-nhan-ban-cau-hoi',
    function () {
        window.setTimeout(lenLichLuuBanNhapBieuMau, 0);
    }
);

$('#danhSachCauHoi > .survey-question__placeholder, #danhSachCauHoi > .ui-sortable-placeholder').remove();

if ($.fn.sortable) {
    $('#danhSachCauHoi').sortable({
        items: '> .survey-question:not(.survey-question__placeholder):not(.ui-sortable-placeholder)',
        handle: '.survey-question__drag-handle',
        cancel: 'input, textarea, select, button, label, a',
        axis: 'y',
        tolerance: 'pointer',
        distance: 5,
        placeholder: 'survey-question__placeholder',
        forcePlaceholderSize: true,
        start: function (event, ui) {
            ui.item.addClass('survey-question--dragging');
            var loai = ui.item.find('.survey-question__type').val();
            ui.item.data('survey-original-prev', ui.item.prev()[0] || null);
            if (loai === 'subcategory') {
                var danhMucChaGoc = ui.item.prevAll('.survey-question').filter(function () {
                    return $(this).find('.survey-question__type').val() === 'category';
                }).first();
                ui.item.data('survey-original-parent', danhMucChaGoc[0] || null);
            }
            var thanhPhanCon = $();
            if (loai === 'category' || loai === 'subcategory') {
                var itemKeTiep = ui.item.next();
                while (itemKeTiep.length) {
                    if (itemKeTiep.hasClass('survey-question__placeholder') || itemKeTiep.hasClass('ui-sortable-placeholder')) {
                        itemKeTiep = itemKeTiep.next();
                        continue;
                    }
                    var loaiKeTiep = itemKeTiep.find('.survey-question__type').val();
                    if (loaiKeTiep === 'category' || (loai === 'subcategory' && loaiKeTiep === 'subcategory')) break;
                    thanhPhanCon = thanhPhanCon.add(itemKeTiep);
                    itemKeTiep = itemKeTiep.next();
                }
                thanhPhanCon.detach();
                var cauHoiTamAn = $('#danhSachCauHoi > .survey-question')
                    .not('.survey-question__placeholder, .ui-sortable-placeholder')
                    .filter(function () {
                    var loaiItem = $(this).find('.survey-question__type').val();
                    return loaiItem !== 'category' && loaiItem !== 'subcategory';
                    }).hide();
                ui.item.data('survey-hidden-questions', cauHoiTamAn);
            }
            ui.item.data('survey-children', thanhPhanCon);
            $('#danhSachCauHoi').sortable('refreshPositions');
        },
        sort: function (event, ui) {
            var loai = ui.item.find('.survey-question__type').val();
            if (loai !== 'category' && loai !== 'subcategory') return;

            if (loai === 'subcategory') {
                var danhMucChaGoc = ui.item.data('survey-original-parent');
                var danhMucChaTaiViTriTha = ui.placeholder.prevAll('.survey-question').filter(function () {
                    return $(this).find('.survey-question__type').val() === 'category';
                }).first()[0] || null;
                if (danhMucChaTaiViTriTha !== danhMucChaGoc) {
                    var danhMucChaKeTiep = $(danhMucChaGoc).nextAll('.survey-question').filter(function () {
                        return $(this).find('.survey-question__type').val() === 'category';
                    }).first();
                    if (danhMucChaKeTiep.length && ui.placeholder.index() > $(danhMucChaGoc).index()) {
                        ui.placeholder.insertBefore(danhMucChaKeTiep);
                    } else {
                        ui.placeholder.insertAfter($(danhMucChaGoc));
                    }
                    return;
                }
            }

            var diemDung = ui.placeholder.nextAll('.survey-question').filter(function () {
                var loaiItem = $(this).find('.survey-question__type').val();
                return loai === 'category'
                    ? loaiItem === 'category'
                    : (loaiItem === 'subcategory' || loaiItem === 'category');
            }).first();

            if (diemDung.length) {
                if (!ui.placeholder.next().is(diemDung)) ui.placeholder.insertBefore(diemDung);
            } else if (!ui.placeholder.is(':last-child')) {
                $('#danhSachCauHoi').append(ui.placeholder);
            }
        },
        stop: function (event, ui) {
            var thanhPhanCon = ui.item.data('survey-children');
            var loai = ui.item.find('.survey-question__type').val();
            if (loai === 'subcategory') {
                var danhMucChaMoi = ui.item.prevAll('.survey-question').filter(function () {
                    return $(this).find('.survey-question__type').val() === 'category';
                }).first()[0] || null;
                var danhMucChaGoc = ui.item.data('survey-original-parent') || null;
                if (danhMucChaMoi !== danhMucChaGoc) {
                    var viTriCu = ui.item.data('survey-original-prev');
                    if (viTriCu && $.contains(document, viTriCu)) $(viTriCu).after(ui.item);
                    else $('#danhSachCauHoi').prepend(ui.item);
                }
            }
            if (thanhPhanCon && thanhPhanCon.length) ui.item.after(thanhPhanCon);
            var cauHoiTamAn = ui.item.data('survey-hidden-questions');
            if (cauHoiTamAn && cauHoiTamAn.length) cauHoiTamAn.show();
            ui.item.removeData('survey-children');
            ui.item.removeData('survey-hidden-questions');
            ui.item.removeData('survey-original-prev');
            ui.item.removeData('survey-original-parent');
            ui.item.removeClass('survey-question--dragging');
            setTimeout(function () {
                $('#danhSachCauHoi > .survey-question__placeholder, #danhSachCauHoi > .ui-sortable-placeholder').remove();
            }, 0);
            capNhatTrangThaiBieuMau();
            chonCauHoi(ui.item);
            lenLichLuuBanNhapBieuMau();
        }
    });
}

khoiTaoBieuMauKhaoSat();
capNhatKhoaPhongTheoPhamVi();



//test
$("#xem_ma_chi_so").on("click", function(e) {
    alert($("#xem_ma_chi_so").text()); 
});


$('#datatable-chiso').on('click', '.btn-tao-lai', function () {
    var button = $(this);
    var maChiSo = button.data('id');

    Swal.fire({
        title: 'Tạo lại chỉ số?',
        text: 'Một bản nháp mới sẽ được tạo từ chỉ số bị từ chối.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Tạo lại',
        cancelButtonText: 'Hủy'
    }).then(function (result) {
        if (!result.isConfirmed) {
            return;
        }

        button.prop('disabled', true);

        $.ajax({
            url: $('#ULocal').val() + 'chisochatluong/taolai/',
            type: 'POST',
            dataType: 'json',
            data: {
                ma_chi_so: maChiSo
            },
            success: function (response) {
                if (!response || !response.success) {
                    Swal.fire(
                        'Không thành công',
                        response.message || 'Không thể tạo lại chỉ số.',
                        'error'
                    );
                    return;
                }

                table.ajax.reload(null, false);

                Swal.fire(
                    'Thành công',
                    'Đã tạo bản nháp mới. Bạn có thể chỉnh sửa và gửi lại.',
                    'success'
                );
            },
            error: function () {
                Swal.fire(
                    'Lỗi',
                    'Không thể kết nối đến máy chủ.',
                    'error'
                );
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });
});
