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

$('#dropdownUserKhoa').on('shown.bs.dropdown', function () {
    $('#timUserKhoa').val('').trigger('input').focus();
});

$('#timUserKhoa').on('input', function () {
    var keyword = $(this).val().toLowerCase().trim();
    var visibleCount = 0;
    $('.user-khoa-option').each(function () {
        var visible = String($(this).attr('data-name')).toLowerCase().indexOf(keyword) !== -1;
        $(this).toggle(visible);
        if (visible) visibleCount++;
    });
    $('.user-khoa-empty').toggle(visibleCount === 0);
});

$('#dropdownUserKhoa').on('click', '.user-khoa-option', function (event) {
    event.preventDefault();
    $('#id_user').val($(this).attr('data-id'));
    $('#tenUserKhoa').text($(this).attr('data-name'));
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
    $.ajax({
        url: $('#ULocal').val() + 'chisokhoa/XemDL/',
        type: 'POST',
        dataType: 'json',
        data: { ma_chi_so: row.ma_chi_so, id_user: $('#id_user').val() },
        success: function (response) {
            if (!response || !response.success) {
                Swal.fire('Thông báo', (response && response.message) || 'Không thể xem chỉ số.', 'info');
                return;
            }
            moXemChiSoKhoa(response.data);
        },
        error: function () { Swal.fire('Lỗi', 'Không thể tải dữ liệu chu kỳ.', 'error'); }
    });
});

var duLieuChuKyKhoa = {};
var idChuKyKhoa = 0;

function moXemChiSoKhoa(data) {
    $('#xem_ck_ten').val(data.ten_chi_so || '');
    $('#xem_ck_muctieu').val(data.muc_tieu || '');
    $('#xem_ck_nguong').val(data.nguong_canh_bao || '');
    $('#xem_ck_dinhnghia').val(data.dinh_nghia || '');
    $('#xem_ck_thuthap').val(data.thu_thap || '');
    $('#xem_ck_label_tuso').text(data.ten_tu_so || 'Tử số');
    $('#xem_ck_label_mauso').text(data.ten_mau_so || 'Mẫu số');
    idChuKyKhoa = parseInt(data.id_chuky, 10);
    duLieuChuKyKhoa = data.dulieu || {};

    var years = Object.keys(duLieuChuKyKhoa).sort().reverse();
    if (!years.length) years = [String(new Date().getFullYear())];
    $('#xem_ck_nam').html(years.map(function (year) {
        return '<option value="' + year + '">' + year + '</option>';
    }).join(''));
    taoBangChuKyKhoa();
    $('#modalXemChiSoKhoa').modal('show');
}

function soKyCuaChiSoKhoa(id) {
    return ({ 1: 12, 2: 4, 3: 1, 4: 2 })[id] || 1;
}

function tenKyCuaChiSoKhoa(id, ky) {
    if (id === 1) return 'Tháng ' + ky;
    if (id === 2) return 'Quý ' + ky;
    if (id === 4) return ky === 1 ? '6 tháng đầu năm' : '6 tháng cuối năm';
    return 'Cả năm';
}

function taoBangChuKyKhoa() {
    var yearData = duLieuChuKyKhoa[$('#xem_ck_nam').val()] || {};
    var rows = yearData.du_lieu || [];
    var html = '';
    for (var ky = 1; ky <= soKyCuaChiSoKhoa(idChuKyKhoa); ky++) {
        var item = rows[ky - 1] || {};
        html += '<tr><td>' + tenKyCuaChiSoKhoa(idChuKyKhoa, ky) + '</td>' +
            '<td>' + (item.tu_so != null ? item.tu_so : '—') + '</td>' +
            '<td>' + (item.mau_so != null ? item.mau_so : '—') + '</td>' +
            '<td>' + (item.value != null ? item.value : '—') + '</td></tr>';
    }
    $('#xem_ck_chuky_body').html(html);
}

$('#xem_ck_nam').on('change', taoBangChuKyKhoa);
