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
        { data: 'ten_chuky' },
        {
            data: 'da_nhap',
            render: function (data,row) {
                return data
                    ? '<span class="badge dt-center rounded-pill bg-success">Đã nhập liệu</span>'
                    : '<span class="badge dt-center rounded-pill bg-danger">Chưa nhập liệu</span>';
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
    tableChiSoKhoa.ajax.reload();
});

$('#datatable-chisokhoa').on('click', '.btn-xem-chisokhoa', function () {
    var button = $(this);
    var tr = $(this).closest('tr');
    if (tr.hasClass('child')) tr = tr.prev();
    var row = tableChiSoKhoa.row(tr).data();
    if (!row) return;
    $.ajax({
        url: $('#ULocal').val() + 'chisokhoa/XemDL/',
        type: 'POST',
        dataType: 'json',
        data: { ma_chi_so: row.ma_chi_so, id_user: $('#id_user').val() },
        beforeSend: function () { button.prop('disabled', true); },
        success: function (response) {
            if (!response || !response.success) {
                Swal.fire('Thông báo', (response && response.message) || 'Không thể xem chỉ số.', 'info');
                return;
            }
            moXemChiSoKhoa(response.data);
        },
        error: function () { Swal.fire('Lỗi', 'Không thể tải dữ liệu chu kỳ.', 'error'); },
        complete: function () { button.prop('disabled', false); }
    });
});

var duLieuChuKyKhoa = {};
var idChuKyKhoa = 0;
var mucTieuChiSoKhoa = '';
var nguongCanhBaoChiSoKhoa = '';

function moXemChiSoKhoa(data) {
    mucTieuChiSoKhoa = data.muc_tieu || '';
    nguongCanhBaoChiSoKhoa = data.nguong_canh_bao || '';
    $('#xem_ck_ten').val((data.ten_chi_so || '') + (data.ten_chuky ? ' (' + data.ten_chuky + ')' : ''));
    $('#xem_ck_khia_canh').text(data.ten_khia_canh || '');
    $('#xem_ck_thanh_to').text(data.ten_thanh_to || '');
    $('#xem_ck_pham_vi').text(data.ten_pham_vi || '');
    $('#xem_ck_don_vi_tinh').text(data.ten_don_vi_tinh || '');
    $('#xem_ck_muc_tieu').text(hienThiPhanTramChiSoKhoa(data.muc_tieu));
    $('#xem_ck_nguong_canh_bao').text(hienThiPhanTramChiSoKhoa(data.nguong_canh_bao));
    $('#xem_ck_dinh_nghia').text(data.dinh_nghia || '');
    $('#xem_ck_thu_thap').text(data.thu_thap || '');
    $('#xem_ck_label_tuso').text(data.ten_tu_so || 'Tử số');
    $('#xem_ck_label_mauso').text(data.ten_mau_so || 'Mẫu số');
    idChuKyKhoa = parseInt(data.id_chuky, 10);
    duLieuChuKyKhoa = data.dulieu || {};

    var years = Object.keys(duLieuChuKyKhoa).sort().reverse();
    $('#xem_ck_nam').val(years.length ? years[0] : new Date().getFullYear());
    taoBangChuKyKhoa();
    $('#modalXemChiSoKhoa').modal('show');
}

function hienThiPhanTramChiSoKhoa(value) {
    var text = String(value == null ? '' : value).trim();
    return text && text.indexOf('%') === -1 ? text + '%' : text;
}

function tachDieuKienChiSoKhoa(value, toanTuMacDinh) {
    var text = String(value == null ? '' : value).trim().replace(/%/g, '').replace(',', '.');
    var match = text.match(/^(>=|<=|>|<|=)?\s*(-?\d+(?:\.\d+)?)$/);
    if (!match) return null;
    return { toanTu: match[1] || toanTuMacDinh, moc: Number(match[2]) };
}

function thoaDieuKienChiSoKhoa(giaTri, dieuKien) {
    if (!dieuKien) return false;
    if (dieuKien.toanTu === '>') return giaTri > dieuKien.moc;
    if (dieuKien.toanTu === '>=') return giaTri >= dieuKien.moc;
    if (dieuKien.toanTu === '<') return giaTri < dieuKien.moc;
    if (dieuKien.toanTu === '<=') return giaTri <= dieuKien.moc;
    return giaTri === dieuKien.moc;
}

function toMauGiaTriChiSoKhoa(input, value) {
    var text = String(value == null ? '' : value).trim().replace(',', '.');
    if (!/^-?\d+(?:\.\d+)?$/.test(text)) return;
    var giaTri = Number(text);
    var mucTieu = tachDieuKienChiSoKhoa(mucTieuChiSoKhoa, '>');
    var canhBao = tachDieuKienChiSoKhoa(nguongCanhBaoChiSoKhoa, '<');
    if (!mucTieu && !canhBao) return;
    if (!mucTieu) mucTieu = { toanTu: '>', moc: canhBao.moc };
    if (!canhBao) canhBao = { toanTu: '<', moc: mucTieu.moc };
    if (thoaDieuKienChiSoKhoa(giaTri, mucTieu)) input.addClass('xem-value-xanh');
    else if (thoaDieuKienChiSoKhoa(giaTri, canhBao)) input.addClass('xem-value-do');
    else input.addClass('xem-value-vang');
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
            '<td><input type="number" class="form-control" readonly tabindex="-1" value="' + (item.tu_so != null ? item.tu_so : '') + '"></td>' +
            '<td><input type="number" class="form-control" readonly tabindex="-1" value="' + (item.mau_so != null ? item.mau_so : '') + '"></td>' +
            '<td><input type="text" class="form-control xem-ck-value" readonly tabindex="-1" value="' + (item.value != null ? item.value : '') + '"></td></tr>';
    }
    $('#xem_ck_chuky_body').html(html);
    $('#xem_ck_chuky_body .xem-ck-value').each(function () {
        toMauGiaTriChiSoKhoa($(this), $(this).val());
    });
}
