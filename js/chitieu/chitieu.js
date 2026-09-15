var tableChiTieuThang;

tableChiTieuThang = $('#datatable-chitieu-thang').DataTable({
    ordering: false,
    responsive: true,
    autoWidth: false,
    ajax: {
        url: $('#ULocal').val() + 'chitieu/getData/',
        type: 'POST',
        data: function (request) {
            request.thang = $('#chonThangChiTieu').val();
        },
        dataSrc: function (response) {
            var items = response && response.data ? response.data : [];
            var daNhap = items.filter(function (item) { return item.da_nhap; }).length;
            $('#tongChiTieu').text(items.length);
            $('#tongDaNhap').text(daNhap);
            $('#tongChuaNhap').text(items.length - daNhap);
            return items;
        }
    },
    columns: [
        { data: 'ma_chi_so' },
        { data: 'ten_chi_so' },
        {
            data: 'ten_pham_vi',
            render: function (data) { return data || '—'; }
        },
        { data: 'ten_chuky' },
        { data: 'muc_tieu' },
        { data: 'nguong_canh_bao' },
        {
            data: 'du_lieu_thang',
            render: function (data) { return data && data.value != null ? data.value + '%' : '—'; }
        },
        {
            data: 'da_nhap',
            render: function (data) {
                return data
                    ? '<span class="badge dt-center rounded-pill bg-success">Đã nhập</span>'
                    : '<span class="badge dt-center rounded-pill bg-danger">Chưa nhập</span>';
            }
        },
        {
            data: null,
            render: function (data, type, row) {
                return '<button type="button" class="btn btn-primary btn-sm btn-nhap-thang">' +
                    '<i class="fa fa-pencil"></i> ' + (row.da_nhap ? 'Cập nhật' : 'Nhập liệu') + '</button>';
            }
        }
    ]
});

$('#chonThangChiTieu').on('change', function () {
    tableChiTieuThang.ajax.reload();
});

$('#datatable-chitieu-thang').on('click', '.btn-nhap-thang', function () {
    var tr = $(this).closest('tr');
    if (tr.hasClass('child')) tr = tr.prev();
    var row = tableChiTieuThang.row(tr).data();
    if (!row) return;
    var saved = row.du_lieu_thang || {};
    $('#ct_ma_chi_so').val(row.ma_chi_so);
    $('#ct_ten_chi_so').val(row.ten_chi_so + ' (' + (row.ten_chuky || '') + ')');
    $('#ct_label_tu_so').text(row.ten_tu_so || 'Tử số');
    $('#ct_label_mau_so').text(row.ten_mau_so || 'Mẫu số');
    $('#ct_tu_so').val(saved.tu_so != null ? saved.tu_so : '');
    $('#ct_mau_so').val(saved.mau_so != null ? saved.mau_so : '');
    $('#ct_value').val(saved.value != null ? saved.value : '');
    $('#modalNhapChiTieuThang .modal-title').text(
        'Nhập chỉ tiêu ' + (row.ten_ky_hien_tai || ('Tháng ' + $('#chonThangChiTieu').val()))
    );
    $('#modalNhapChiTieuThang').modal('show');
});

$('#ct_tu_so, #ct_mau_so').on('input', function () {
    var tuSo = parseFloat($('#ct_tu_so').val());
    var mauSo = parseFloat($('#ct_mau_so').val());
    var inputMauSo = $('#ct_mau_so')[0];
    inputMauSo.setCustomValidity('');
    if (!isNaN(tuSo) && !isNaN(mauSo) && mauSo <= tuSo) {
        inputMauSo.setCustomValidity('Mẫu số phải lớn hơn tử số');
    }
    $('#ct_value').val(!isNaN(tuSo) && mauSo > 0 ? ((tuSo / mauSo) * 100).toFixed(2) : '');
});

$('#formNhapChiTieuThang').on('submit', function (event) {
    event.preventDefault();
    $.ajax({
        url: $('#ULocal').val() + 'chitieu/save/',
        type: 'POST',
        dataType: 'json',
        data: {
            ma_chi_so: $('#ct_ma_chi_so').val(),
            thang: $('#chonThangChiTieu').val(),
            tu_so: $('#ct_tu_so').val(),
            mau_so: $('#ct_mau_so').val()
        },
        beforeSend: function () {
            $('#btnLuuChiTieuThang').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');
        },
        success: function (response) {
            if (response && response.success) {
                $('#modalNhapChiTieuThang').modal('hide');
                tableChiTieuThang.ajax.reload(null, false);
                Swal.fire('Thành công', response.message, 'success');
            } else {
                Swal.fire('Lỗi', (response && response.message) || 'Không thể lưu dữ liệu.', 'error');
            }
        },
        error: function () { Swal.fire('Lỗi', 'Có lỗi xảy ra khi lưu dữ liệu.', 'error'); },
        complete: function () {
            $('#btnLuuChiTieuThang').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu');
        }
    });
});
