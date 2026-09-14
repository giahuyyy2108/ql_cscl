var tableChiSoKhoa = $('#datatable-chisokhoa').DataTable({
    ordering: false,
    responsive: true,
    autoWidth: false,
    ajax: {
        url: $('#ULocal').val() + 'chisokhoa/getData/',
        type: 'POST',
        data: function (request) { request.id_user = $('#id_user').val() || ''; },
        dataSrc: function (response) { return response && Array.isArray(response.data) ? response.data : []; }
    },
    columns: [
        { data: 'ma_chi_so' },
        { data: 'ten_chi_so' },
        { data: 'ten_khoa_phong' },
        { data: 'muc_tieu' },
        { data: 'nguong_canh_bao' },
        { data: 'ten_don_vi_tinh' },
        { data: 'ten_chuky' },
        {
            data: null,
            render: function (data, type, row) {
                return $('<span>').addClass('badge dt-center rounded-pill ' + (row.tag || '')).text(row.tenTrangThai || '').prop('outerHTML');
            }
        },
        {
            data: null,
            render: function () { return '<button type="button" class="btn btn-info btn-sm btn-xem-chisokhoa"><i class="fa fa-eye"></i></button>'; }
        }
    ]
});

$('#btnTimChiSoKhoa').on('click', function () {
    if (!$('#id_user').val()) {
        Swal.fire('Thông báo', 'Vui lòng chọn người dùng.', 'info');
        return;
    }
    tableChiSoKhoa.ajax.reload();
});

$('#datatable-chisokhoa').on('click', '.btn-xem-chisokhoa', function () {
    var tr = $(this).closest('tr');
    if (tr.hasClass('child')) tr = tr.prev();
    var row = tableChiSoKhoa.row(tr).data();
    if (!row) return;
    $('#xem_ck_ten').val(row.ten_chi_so || '');
    $('#xem_ck_muctieu').val(row.muc_tieu || '');
    $('#xem_ck_nguong').val(row.nguong_canh_bao || '');
    $('#xem_ck_dinhnghia').val(row.dinh_nghia || '');
    $('#xem_ck_thuthap').val(row.thu_thap || '');
    $('#modalXemChiSoKhoa').modal('show');
});
