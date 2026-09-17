/* DATA TABLES */
var table;

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
            text: '<i class="fa fa-plus"></i> Thêm Chỉ tiêu',
            className: 'btn btn-primary',

            action: function () {

                // reset form
                $('#formChiTieu')[0].reset();
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
                        ${(data.trang_thai.maTrangThai==3)? 'hidden' : "" }
                        data-toggle="tooltip"
                        aria-label="Từ chối">
                        <i class="fa fa-remove"></i>
                    </button>
                `;
            }
        }
    ],
    drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip();
    }
});

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

$('#datatable-chiso').on('click', '.btn-xem', function () {
    var tr = $(this).closest('tr');
    if (tr.hasClass('child')) {
        tr = tr.prev();
    }
    var row = table.row(tr).data();
    if (!row) {
        return;
    }

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
    $('#xem_nguoi_gui').text(hienThi(row.nguoi_gui && row.nguoi_gui.hoTen));
    $('#xem_nguoi_duyet').text(hienThi(row.nguoi_duyet.hoTen));
    $('#xem_trang_thai').text(hienThi(row.trang_thai && row.trang_thai.tenTrangThai));

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
        url: $('#ULocal').val() + 'chisochatluong/Duyet/',
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
        url: $('#ULocal').val() + 'chisochatluong/Xoa/',
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

    var originalHtml = button.html();

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/TuChoi/',
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
                Swal.fire('Thành công', 'Từ chối chỉ tiêu thành công.', 'success');
            } else {
                Swal.fire('Không thể duyệt',
                    (message && message.errorMessage) || 'Không thể từ chối chỉ tiêu. Vui lòng thử lại.',
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

function capNhatKhoaPhongTheoPhamVi() {
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

capNhatKhoaPhongTheoPhamVi();
