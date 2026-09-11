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
        "<'row'<'col-sm-6'l><'col-sm-6 text-right'Bf>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",

    buttons: [
        {
            text: '<i class="fa fa-plus"></i> Thêm Chỉ tiêu',
            className: 'btn btn-primary',

            action: function () {

                // reset form
                $('#formChiTieu')[0].reset();

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
            data: 'ma_khia_canh',
            render: function (data, type, row) {
                return $('#ma_khia_canh option[value="' + data + '"]').text() || data;
            }
        },
        { 
            data: 'ma_thanh_to',
            render: function (data, type, row) {
                return $('#ma_thanh_to option[value="' + data + '"]').text() || data;
            }
        },
        { 
            data: 'pham_vi',
            render: function (data, type, row) {
                return $('#pham_vi option[value="' + data + '"]').text() || data;
            }
        },
        {
            data: 'id_donvitinh',
            render: function (data) {
                return getOptionText('don_vi_tinh', data);
            }
        },
        { 
            data: 'id_chuky',
            render: function (data, type, row) {
                return $('#id_chuky option[value="' + data + '"]').text() || data;
            }
        },
        { data: 'nguoi_gui' },
        { data: 'nguoi_duyet' },
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
                        aria-label="Duyệt">
                        <i class="fa fa-check"></i>
                    </button>

                    <button type="button"
                        class="btn btn-warning btn-sm btn-sua"
                        data-id="${row.ma_chi_so}"
                        title="Sửa"
                        ${(data.trang_thai.maTrangThai==2)? 'hidden' : "" }
                        ${(data.trang_thai.maTrangThai==1)? 'hidden' : "" }
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
                        data-toggle="tooltip"
                        aria-label="Xóa">
                        <i class="glyphicon glyphicon-trash"></i>
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

    // Đổ dữ liệu
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
    $('#dinh_nghia').val(row.dinh_nghia);
    $('#thu_thap').val(row.thu_thap);
    $('#ten_tu_so').val(row.ten_tu_so);
    $('#ten_mau_so').val(row.ten_mau_so);

    // Khóa toàn bộ input
    $('#formChiTieu')
        .find('input, textarea, select')
        .prop('disabled', true);

    // Ẩn nút Lưu
    $('#btnLuuChiTieu').hide();

    // Đổi tiêu đề
    $('#modalChiTieu .modal-title')
        .text('Xem Chỉ tiêu');

    // Mở cùng popup
    $('#modalChiTieu').modal('show');
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
