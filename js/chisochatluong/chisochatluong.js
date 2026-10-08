/* DATA TABLES */
var table;
var chisoBieuDo = null;
var chisoBieuDoCauHoi = null;
var chisoDuLieuCauHoi = [];
var chisoThongKeTable = null;
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
$('#chiso-thongke-tab').on('shown.bs.tab', function () {
    if (!chisoThongKeTable) khoiTaoChisoThongKe();
    else chisoThongKeTable.ajax.reload(function () { chisoThongKeTable.columns.adjust().responsive.recalc(); }, false);
});

function getOptionText(selectId, value) {
    var text = $('#' + selectId + ' option').filter(function () {
        return String($(this).val()) === String(value);
    }).text();

    return text || value || '';
}

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
    $('#modalChiTieu').modal('show');
});

$('#formChiTieu').on('submit', function (e) {

    e.preventDefault();

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

                $('#modalChiTieu').modal('hide');

                // reload DataTable nhưng không về trang 1
                table.ajax.reload(null, false);

                // alert(response.message || 'Lưu thành công');

            } else {

                // alert(
                //     (response.message && (response.message.errorMessage || response.message.message)) ||
                //     response.errorMessage ||
                //     'Không thể lưu dữ liệu'
                // );
            }
        },

        error: function (xhr) {

            // console.log(xhr.responseText);

            // alert('Có lỗi xảy ra khi lưu dữ liệu');
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
                luaChon.append($('<li>').text(vietHoaDauCau(noiDung) + ' (' + diem + ' điểm)'));
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
        var hienThiCauHinhSo = loai === 'number';
        item.find('.survey-question__number-settings')
            .toggle(hienThiCauHinhSo)
            .find(':input')
            .prop('disabled', !hienThiCauHinhSo);

        var hienThiPhuongAn = loaiCauHoiCanLuaChon(loai) && loai !== 'satisfaction';
        item.find('.survey-question__options :input').prop('disabled', !hienThiPhuongAn);
        if (laDanhMuc) item.find('.survey-question__required').prop('checked', false);
        $(this).find('.btn-cau-hoi-len').prop('disabled', index === 0);
        $(this).find('.btn-cau-hoi-xuong').prop('disabled', index === danhSach.length - 1);
    });
}

function taoLuaChonCauHoi(noiDung, diem) {
    var luaChon = $('<div>', { 'class': 'survey-option' });
    luaChon.append($('<i>', { 'class': 'fa fa-circle-o text-muted' }));
    luaChon.append($('<input>', {
        type: 'text',
        'class': 'form-control input-sm survey-option__text',
        placeholder: 'Nhập phương án trả lời',
        value: noiDung || ''
    }));
    luaChon.append($('<input>', {
        type: 'number',
        min: 0,
        step: 'any',
        'class': 'form-control input-sm survey-option__score',
        placeholder: 'Điểm',
        title: 'Điểm của phương án này',
        value: diem === undefined || diem === null ? 0 : diem
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
        '<div class="survey-question__header">' +
            '<strong class="survey-question__number"></strong>' +
            '<div class="survey-question__actions">' +
                '<button type="button" class="btn btn-default btn-xs btn-cau-hoi-len" title="Di chuyển lên"><i class="fa fa-arrow-up"></i></button>' +
                '<button type="button" class="btn btn-default btn-xs btn-cau-hoi-xuong" title="Di chuyển xuống"><i class="fa fa-arrow-down"></i></button>' +
                '<button type="button" class="btn btn-danger btn-xs btn-xoa-cau-hoi" title="Xóa câu hỏi"><i class="fa fa-trash"></i></button>' +
            '</div>' +
        '</div>' +
        '<div class="row">' +
            '<div class="col-md-8"><div class="form-group">' +
                '<label class="survey-question__title-label">Nội dung câu hỏi <span class="text-danger">*</span></label>' +
                '<input type="text" class="form-control survey-question__title" placeholder="Nhập nội dung câu hỏi">' +
            '</div></div>' +
            '<div class="col-md-4"><div class="form-group">' +
                '<label>Loại câu trả lời</label>' +
                '<select class="form-control survey-question__type">' +
                    '<option value="short_text">Trả lời ngắn</option>' +
                    '<option value="long_text">Đoạn văn</option>' +
                    '<option value="number">Số</option>' +
                    '<option value="percentage">Tỷ lệ (%)</option>' +
                    '<option value="score">Chọn điểm (1–10)</option>' +
                    '<option value="date">Ngày tháng</option>' +
                    '<option value="category">Danh mục cha</option>' +
                    '<option value="subcategory">Danh mục con</option>' +
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
            '<button type="button" class="btn btn-default btn-sm btn-them-lua-chon"><i class="fa fa-plus"></i> Thêm phương án</button>' +
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
    $('#danhSachCauHoi').append(cauHoiMoi);
    capNhatTrangThaiBieuMau();

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

function khoiTaoBieuMauKhaoSat() {
    soThuTuCauHoi = 0;
    $('#danhSachCauHoi').empty();
    capNhatTrangThaiBieuMau();
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
                        diem: parseFloat($(this).find('.survey-option__score').val()) || 0
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
                    danhSachLuaChon.append(taoLuaChonCauHoi(vietHoaDauCau(noiDung), diem));
                });
            }
            cauHoi.find('.survey-question__options').toggle(loai !== 'satisfaction');
        }

        $('#danhSachCauHoi').append(cauHoi);
    });

    capNhatTrangThaiBieuMau();
}

$('#btnThemCauHoi').on('click', function (event) {
    event.preventDefault();
    themCauHoi();
});

$('#danhSachCauHoi').on('change', '.survey-question__type', function () {
    var cauHoi = $(this).closest('.survey-question');
    var khuVucLuaChon = cauHoi.find('.survey-question__options');
    var loai = $(this).val();

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
});

$('#danhSachCauHoi').on('input', '.survey-option__text', function () {
    $(this).val(vietHoaDauCau($(this).val()));
});

$('#danhSachCauHoi').on('click', '.btn-them-lua-chon', function () {
    $(this).siblings('.survey-question__option-list').append(taoLuaChonCauHoi());
});

$('#danhSachCauHoi').on('click', '.btn-xoa-lua-chon', function () {
    $(this).closest('.survey-option').remove();
});

$('#modalChiTieu').on('wheel', 'input[type="number"]', function (event) {
    if (document.activeElement === this) event.preventDefault();
});

$('#danhSachCauHoi').on('click', '.btn-xoa-cau-hoi', function () {
    $(this).closest('.survey-question').remove();
    capNhatTrangThaiBieuMau();
});

$('#danhSachCauHoi').on('click', '.btn-cau-hoi-len', function () {
    var cauHoi = $(this).closest('.survey-question');
    cauHoi.prev('.survey-question').before(cauHoi);
    capNhatTrangThaiBieuMau();
});

$('#danhSachCauHoi').on('click', '.btn-cau-hoi-xuong', function () {
    var cauHoi = $(this).closest('.survey-question');
    cauHoi.next('.survey-question').after(cauHoi);
    capNhatTrangThaiBieuMau();
});

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
