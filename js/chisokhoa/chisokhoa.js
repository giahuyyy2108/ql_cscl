/* DATA TABLES */
var table;
var allKhoaOptions = [];
var bieuDoChuKy = null;
var yeuCauBieuDoChuKy = null;
var tableCtChiSo = null;
var maChiSoThongKe = 0;
var soChuKyThongKe = 0;
var canTaiThongKe = false;

function taiBieuDoChuKy(maChiSo, soChuKy) {
    var loading = $('#bieu_do_chu_ky_loading').show();
    var empty = $('#bieu_do_chu_ky_empty').hide();
    var canvas = $('#bieu_do_chu_ky').show();

    if (bieuDoChuKy) {
        bieuDoChuKy.destroy();
        bieuDoChuKy = null;
    }

    $.ajax({
        url: $('#ULocal').val() + 'chisokhoa/getBieuDoChuKy/',
        type: 'POST',
        dataType: 'json',
        data: { ma_chi_so: maChiSo },
        success: function (response) {
            var data = response && response.success && Array.isArray(response.data)
                ? response.data.filter(function (item) {
                    return isFinite(parseFloat(item.trung_binh)) && parseInt(item.so_phieu, 10) > 0;
                })
                : [];
            loading.hide();
            if (!data.length) {
                canvas.hide();
                empty.text(response && response.message ? response.message : 'Chưa có dữ liệu nhập liệu để hiển thị.').show();
                return;
            }

            var labels = data.map(function (item) {
                var tenKy = 'Kỳ ' + item.ky;
                if (soChuKy === 12) tenKy = 'Tháng ' + item.ky;
                if (soChuKy === 4) tenKy = 'Quý ' + item.ky;
                if (soChuKy === 2) tenKy = '6 tháng ' + item.ky;
                if (soChuKy === 1) tenKy = 'Năm';
                return tenKy + '/' + item.nam;
            });
            var tongSoPhieu = data.reduce(function (tong, item) {
                return tong + (parseInt(item.so_phieu, 10) || 0);
            }, 0);

            bieuDoChuKy = new Chart(canvas[0].getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Trung bình toàn bộ phiếu (' + tongSoPhieu + ' phiếu)',
                        data: data.map(function (item) { return parseFloat(item.trung_binh); }),
                        borderColor: '#337ab7',
                        backgroundColor: 'rgba(51, 122, 183, 0.12)',
                        pointBackgroundColor: '#337ab7',
                        borderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        lineTension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem) {
                                return 'Trung bình: ' + tooltipItem.yLabel + '%';
                            },
                            afterLabel: function (tooltipItem) {
                                return 'Số phiếu: ' + data[tooltipItem.index].so_phieu;
                            }
                        }
                    },
                    scales: {
                        yAxes: [{
                            ticks: { beginAtZero: true, max: 100 },
                            scaleLabel: { display: true, labelString: 'Tỷ lệ trung bình (%)' }
                        }]
                    }
                }
            });
        },
        error: function () {
            loading.hide();
            canvas.hide();
            empty.text('Không tải được dữ liệu biểu đồ.').show();
        }
    });
}

$('#bieudo-tab').on('shown.bs.tab', function () {
    if (yeuCauBieuDoChuKy) {
        taiBieuDoChuKy(yeuCauBieuDoChuKy.maChiSo, yeuCauBieuDoChuKy.soChuKy);
        yeuCauBieuDoChuKy = null;
    } else if (bieuDoChuKy) {
        bieuDoChuKy.resize();
    }
});

function tenKyThongKe(ky) {
    if (soChuKyThongKe === 12) return 'Tháng ' + ky;
    if (soChuKyThongKe === 4) return 'Quý ' + ky;
    if (soChuKyThongKe === 2) return '6 tháng ' + ky;
    if (soChuKyThongKe === 1) return 'Năm';
    return 'Kỳ ' + ky;
}

function dinhDangThoiGian(value) {
    if (!value) return '-';
    var parts = String(value).split(' ');
    var dateParts = parts[0].split('-');
    if (dateParts.length !== 3) return value;
    return dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0] + (parts[1] ? ' ' + parts[1].slice(0, 5) : '');
}

function khoiTaoBangThongKe() {
    if (tableCtChiSo) return false;

    tableCtChiSo = $('#datatable-ct-chiso').DataTable({
        ordering: false,
        responsive: true,
        autoWidth: false,
        processing: true,
        lengthChange: false,
        paging: false,
        scrollY:        '50vh',
        scrollCollapse: true,
        ajax: {
            url: $('#ULocal').val() + 'chisokhoa/getDanhSachPhieu/',
            type: 'POST',
            data: function (data) {
                data.ma_chi_so = maChiSoThongKe;
            },
            dataSrc: function (response) {
                
                return response && response.success && Array.isArray(response.data) ? response.data : [];
            },
            error: function () {
                Swal.fire('Lỗi', 'Không tải được danh sách phiếu nhập liệu.', 'error');
            }
        },
        columns: [
            {
                data: 'id',
                render: function (data) { return data || '-'; }
            },
            {
                data: 'ten_khoaphong',
                render: function (data) { return data || '-'; }
            },
            {
                data: 'ky',
                render: function (data) { return tenKyThongKe(data); }
            },
            { data: 'tong_diem' },
            { data: 'diem_toi_da' },
            {
                data: 'ty_le_phan_tram',
                render: function (data) {
                    return $('<strong>', { 'class': 'text-success', text: data + '%' }).prop('outerHTML');
                }
            },
            {
                data: 'nguoi_nhap',
                render: function (data) { return data || '-'; }
            },
            {
                data: 'updated_at',
                render: function (data) { return dinhDangThoiGian(data); }
            },
            {
                data: null,
                searchable: false,
                render: function () {
                    return '<button type="button" class="btn btn-info btn-sm btn-xem-phieu" title="Xem câu trả lời">' +
                        '<i class="fa fa-eye"></i></button>';
                }
            }
        ],
        language: {
            emptyTable: 'Chưa có phiếu nhập liệu',
            processing: 'Đang tải dữ liệu...'
        }
    });
    return true;
}

$('#thongke-tab').on('shown.bs.tab', function () {
    var vuaKhoiTao = khoiTaoBangThongKe();
    if (canTaiThongKe && !vuaKhoiTao) {
        tableCtChiSo.ajax.reload(function () {
            tableCtChiSo.columns.adjust().responsive.recalc();
        }, false);
    } else if (!vuaKhoiTao) {
        tableCtChiSo.columns.adjust().responsive.recalc();
    }
    canTaiThongKe = false;
});

$('#datatable-ct-chiso').on('click', '.btn-xem-phieu', function () {
    var tr = $(this).closest('tr');
    if (tr.hasClass('child')) tr = tr.prev();
    var row = tableCtChiSo.row(tr).data();
    if (!row) return;

    var chiTiet = $('<div>', { 'class': 'survey-response-detail' });
    var thongTin = $('<div>', { 'class': 'survey-response-detail__meta' }).appendTo(chiTiet);

    [
        ['Khoa/Phòng', row.ten_khoaphong || '-'],
        ['Kỳ báo cáo', tenKyThongKe(row.ky) + ' / ' + row.nam],
        ['Người nhập', row.nguoi_nhap || '-'],
        ['Thời gian', dinhDangThoiGian(row.updated_at)]
    ].forEach(function (item) {
        $('<div>').append(
            $('<span>', { text: item[0] }),
            $('<strong>', { text: item[1] })
        ).appendTo(thongTin);
    });

    var diem = $('<div>', { 'class': 'survey-response-detail__score' }).appendTo(chiTiet);
    [
        ['Tổng điểm', row.tong_diem],
        ['Điểm tối đa', row.diem_toi_da],
        ['Tỷ lệ', row.ty_le_phan_tram + '%']
    ].forEach(function (item) {
        $('<div>').append(
            $('<span>', { text: item[0] }),
            $('<strong>', { text: item[1] })
        ).appendTo(diem);
    });

    var danhSachTraLoi = $('<div>', { 'class': 'survey-response-detail__answers' }).appendTo(chiTiet);
    var cacCauTraLoi = Array.isArray(row.chi_tiet_cau_tra_loi) ? row.chi_tiet_cau_tra_loi : [];

    cacCauTraLoi.forEach(function (item, index) {
        var giaTri = item.gia_tri;
        if (Array.isArray(giaTri)) giaTri = giaTri.join(', ');
        if (giaTri === '' || giaTri === null || giaTri === undefined) giaTri = 'Chưa trả lời';

        $('<div>', { 'class': 'survey-response-question' }).append(
            $('<span>', { 'class': 'survey-response-question__number', text: index + 1 }),
            $('<div>', { 'class': 'survey-response-question__content' }).append(
                $('<div>', { 'class': 'survey-response-question__label', text: item.noi_dung || ('Câu hỏi ' + (index + 1)) }),
                $('<div>', { 'class': 'survey-response-question__answer', text: giaTri })
            )
        ).appendTo(danhSachTraLoi);
    });

    if (!cacCauTraLoi.length) {
        $('<div>', { 'class': 'text-muted text-center', text: 'Phiếu không có câu trả lời.' }).appendTo(danhSachTraLoi);
    }

    Swal.fire({
        title: 'Chi tiết phiếu #' + row.id,
        html: chiTiet.prop('outerHTML'),
        width: 780,
        showCloseButton: true,
        confirmButtonText: 'Đóng'
    });
});

function getOptionText(selectId, value) {
    var text = $('#' + selectId + ' option').filter(function () {
        return String($(this).val()) === String(value);
    }).text();

    return text || value || '';
}

function initKhoaOptions() {
    allKhoaOptions = $('#filter_khoaphong option').map(function () {
        return {
            value: $(this).val(),
            text: $(this).text(),
            idKhoi: $(this).data('khoi') || ''
        };
    }).get();
}

function reloadKhoaPhongByKhoi() {
    var idKhoi = $('#filter_khoi').val();
    var selectedKhoa = $('#filter_khoaphong').val();
    var options = '<option value="">Tất cả Khoa/Phòng</option>';
    var hasSelectedKhoa = selectedKhoa === '';

    allKhoaOptions.forEach(function (item) {
        if (!item.value) {
            return;
        }
        if (!idKhoi || String(item.idKhoi) === String(idKhoi)) {
            options += '<option value="' + item.value + '" data-khoi="' + item.idKhoi + '">' + item.text + '</option>';
            if (String(item.value) === String(selectedKhoa)) {
                hasSelectedKhoa = true;
            }
        }
    });

    $('#filter_khoaphong').html(options);
    $('#filter_khoaphong').val(hasSelectedKhoa ? selectedKhoa : '');
}

table = $('#datatable-chisokhoa').DataTable({
    destroy: true,
    ordering: false,
    dom:
        "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    ajax: {
        url: $("#ULocal").val() + 'chisokhoa/getData/',
        type: 'POST',
        data: function (data) {
            data.id_khoaphong = $('#filter_khoaphong').val();
        },
        error: function(response) {
            alert(JSON.stringify(response));
        },
        dataSrc: function(json) {
            if (!json || json.data == null) {
                return [];
            }
            return Array.isArray(json.data) ? json.data : [json.data];
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
        {
            data: 'ma_chi_so',
            // render: function (data, type, row, meta) {
            //     return meta.row + 1;
            // }
        },
        { data: 'ten_chi_so' },
        {
            data: 'pham_vi',
            render: function (data) {
                return getOptionText('pham_vi', data);
            }
        },
        {
            data: 'id_chuky',
            render: function (data) {
                return getOptionText('id_chuky', data);
            }
        },
        {
            data: 'nguoi_gui',
            render: function (data) {
                return data && data.hoTen ? data.hoTen : '';
            }
        },
        {
            data: 'nguoi_duyet',
            render: function (data) {
                return data && data.hoTen ? data.hoTen : '';
            }
        },
        {
            data: 'trang_thai',
            render: function (data, type) {
                var tenTrangThai = data ? (data.tenTrangThai || data.maTrangThai) : '';
                // if (type !== 'display') {
                //     return tenTrangThai;
                // }

                return $('<span>')
                    .addClass('badge')
                    .addClass('dt-center')
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
                        class="btn btn-info btn-sm btn-xem"
                        data-id="${row.ma_chi_so}"
                        title="Xem"
                        data-toggle="tooltip"
                        aria-label="Xem">
                        <i class="fa fa-eye"></i>
                    </button>
                `;
            }
        }
    ],
    drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip();
    }
});

$(document).ready(function () {
    initKhoaOptions();

    $('#filter_khoi').on('change', function () {
        reloadKhoaPhongByKhoi();
        table.ajax.reload();
    });

    $('#filter_khoaphong').on('change', function () {
        table.ajax.reload();
    });
});

$('#datatable-chisokhoa').on('click', '.btn-xem', function () {
    var tr = $(this).closest('tr');

    if (tr.hasClass('child')) {
        tr = tr.prev();
    }

    var row = table.row(tr).data();

    if (!row) {
        return;
    }

    function hienThi(value) {
        return value === null || value === undefined || value === '' ? '-' : value;
    }

    var maPhong = (row.phong || [String(row.id_khoaphong)]).map(String);
    var cayKhoaPhong = $('#xem_khoa_phong').empty();

    $('#filter_khoi option[value!=""]').each(function () {
        var idKhoi = String($(this).val());
        var tenKhoi = $(this).text().trim();
        var khoaPhong = allKhoaOptions.filter(function (item) {
            return String(item.idKhoi) === idKhoi &&
                maPhong.indexOf(String(item.value)) !== -1;
        });

        if (!khoaPhong.length) {
            return;
        }

        var nhomKhoi = $('<div>').css('margin-bottom', '8px');
        $('<div>')
            .css('font-weight', 'bold')
            .append($('<i>').addClass('fa fa-folder-open-o').css('margin-right', '6px'))
            .append(document.createTextNode(tenKhoi))
            .appendTo(nhomKhoi);

        var danhSach = $('<ul>').css({ margin: '4px 0 0 24px', paddingLeft: '16px' });
        khoaPhong.forEach(function (item) {
            $('<li>').text(item.text.trim()).appendTo(danhSach);
        });

        nhomKhoi.append(danhSach).appendTo(cayKhoaPhong);
    });

    if (!cayKhoaPhong.children().length || row.pham_vi == 3) {
        cayKhoaPhong.text('-');
    }

    $('#xem_ma_chi_so').text(hienThi(row.ma_chi_so));
    $('#xem_ten_chi_so').text(hienThi(row.ten_chi_so));
    $('#xem_khia_canh').text(hienThi(getOptionText('ma_khia_canh', row.ma_khia_canh)));
    $('#xem_thanh_to').text(hienThi(getOptionText('ma_thanh_to', row.ma_thanh_to)));
    $('#xem_pham_vi').text(hienThi(getOptionText('pham_vi', row.pham_vi)));
    $('#xem_chu_ky').text(hienThi(getOptionText('id_chuky', row.id_chuky)));
    var tenChuKy = getOptionText('id_chuky', row.id_chuky);

    $('#tieu_de_bieu_do_chu_ky').text('Trung bình toàn bộ phiếu theo ' + tenChuKy.toLowerCase());
    $('#xem_muc_tieu').text(hienThi(row.muc_tieu +" " + row.donvitinh.ten));
    $('#xem_nguong_canh_bao').text(hienThi(row.nguong_canh_bao +" " + row.donvitinh.ten));
    $('#xem_nguoi_gui').text(hienThi(row.nguoi_gui && row.nguoi_gui.hoTen));
    $('#xem_nguoi_duyet').text(hienThi(row.nguoi_duyet && row.nguoi_duyet.hoTen));
    $('#xem_trang_thai').text(hienThi(row.trang_thai && row.trang_thai.tenTrangThai));
    var khoaChinh = allKhoaOptions.filter(function (item) {
        return String(item.value) === String(row.id_khoaphong);
    })[0];
    $('#xem_khoa').text(hienThi(khoaChinh ? khoaChinh.text.trim() : row.id_khoaphong));
    $('#xem_dinh_nghia').text(hienThi(row.dinh_nghia));
    $('#xem_thu_thap').text(hienThi(row.thu_thap));
    $('#xem_tu_so').text(hienThi(row.ten_tu_so));
    $('#xem_mau_so').text(hienThi(row.ten_mau_so));

    var soChuKy = parseInt($('#id_chuky option[value="' + row.id_chuky + '"]').data('so-ky'), 10) || 0;
    maChiSoThongKe = row.ma_chi_so;
    soChuKyThongKe = soChuKy;
    canTaiThongKe = true;
    if (tableCtChiSo) tableCtChiSo.clear().draw();
    yeuCauBieuDoChuKy = {
        maChiSo: row.ma_chi_so,
        soChuKy: soChuKy
    };

    if (bieuDoChuKy) {
        bieuDoChuKy.destroy();
        bieuDoChuKy = null;
    }
    $('#bieu_do_chu_ky_loading').show();
    $('#bieu_do_chu_ky_empty').hide();
    $('#bieu_do_chu_ky').hide();
    $('#thongtin-tab').tab('show');
    $('#modalChiTieu').modal('show');
});
