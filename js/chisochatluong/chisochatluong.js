/* DATA TABLES */
var table;
var canApprove = $('#datatable-chiso').attr('data-can-approve') === '1';
var canViewChiSoChart = $('#datatable-chiso').attr('data-can-view-chart') === '1';

function applyPhamViPermission() {
    var phamVi = $('#pham_vi');
    var canChoose = phamVi.attr('data-can-choose') === '1';

    if (!canChoose) {
        phamVi.val('1').prop('disabled', true);
    }
}

applyPhamViPermission();

function getOptionText(selectId, value) {
    var text = $('#' + selectId + ' option').filter(function () {
        return String($(this).val()) === String(value);
    }).text();

    return text || value || '';
}

table = $('#datatable-chiso').DataTable({
    destroy: true,
    ordering: false,
    "pageLength": -1,
    "lengthMenu": [[-1], ["Tất cả"]],
    
    searching: true,

    scrollY:        '50vh',
    scrollCollapse: true,
    
    dom:
        "<'row'<'col-sm-6'B><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'>>" ,
        

    language: {
        search: "Tìm kiếm:",
        searchPlaceholder: "Nhập nội dung cần tìm...",
        info: "Hiển thị _START_ đến _END_ trong _TOTAL_ chỉ tiêu",
        infoEmpty: "Không có chỉ tiêu"
    },


    buttons: [
        {
            text: '<i class="fa fa-plus"></i> Thêm Chỉ tiêu',
            className: 'btn btn-primary',

            action: function () {

                // reset form
                $('#formChiTieu')[0].reset();

                applyPhamViPermission();

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
            targets: -1,
            responsivePriority: 1,
            className: 'text-nowrap dt-chiso-actions',
            width: '190px'
        },
        {
            targets: 1,
            responsivePriority: 2
        },
        {
            targets: 0,
            responsivePriority: 3
        }
    ],
    columns: [
        { 
            data: 'ma_chi_so'
            // "targets": 0,
            // "width": '5%', 
            // "className": "text-center",
            // "sortable": false,
            // "render": function ( data, type, row, meta ) {	
            //     return (meta.row + 1);//[row].join('');
			// } 
        },
        { 
            "targets": 1,
			"width": '100%',
			"className": "dt-chiso-name",
            data: 'ten_chi_so' 
        },
        { 
			"width": '15%',
            data: 'ma_khia_canh',
            render: function (data, type, row) {
                return $('#ma_khia_canh option[value="' + data + '"]').text() || data;
            }
        },
        { 

			"width": '15%',
            data: 'ma_thanh_to',
            render: function (data, type, row) {
                return $('#ma_thanh_to option[value="' + data + '"]').text() || data;
            }
        },
        { 

			"width": '15%',
            data: 'pham_vi',
            render: function (data, type, row) {
                return $('#pham_vi option[value="' + data + '"]').text() || data;
            }
        },
        {
			"width": '5%',
            data: 'id_donvitinh',
            render: function (data) {
                return getOptionText('don_vi_tinh', data);
            }
        },
        { 
			"width": '15%',
            data: 'id_chuky',
            render: function (data, type, row) {
                return $('#id_chuky option[value="' + data + '"]').text() || data;
            }
        },
        {
            data: 'nguoi_gui',
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
                        ${!canApprove ? 'hidden' : ''}
                        
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
                        ${(data.trang_thai.maTrangThai==0)? 'hidden' : "" }
                        ${!canApprove ? 'hidden' : ''}
                        data-toggle="tooltip"
                        aria-label="Từ chối">
                        <i class="fa fa-remove"></i>
                    </button>
                    <button type="button"
                        class="btn btn-primary btn-sm btn-nhapdl"
                        data-id="${row.ma_chi_so}"
                        title="Nhập liệu"
                        ${(data.trang_thai.maTrangThai==2)? '' : "hidden" }
                        hidden
                        data-toggle="tooltip"
                        aria-label="nhập liệu">
                        <i class="fa fa-pencil-square-o"></i>
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

    applyPhamViPermission();

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
    var button = $(this);
    var tr = button.closest('tr');
    if (tr.hasClass('child')) tr = tr.prev();
    var row = table.row(tr).data();
    if (!row || !row.ma_chi_so) return;

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/XemDL/',
        type: 'POST',
        dataType: 'json',
        data: { ma_chi_so: row.ma_chi_so },
        beforeSend: function () { button.prop('disabled', true); },
        success: function (response) {
            if (response && response.success) {
                moPopupNhapDuLieu(response.data, true);
            } else {
                Swal.fire('Thông báo', (response && response.message) || 'Chưa có dữ liệu.', 'info');
            }
        },
        error: function () { Swal.fire('Lỗi', 'Không thể tải dữ liệu chỉ số.', 'error'); },
        complete: function () { button.prop('disabled', false); }
    });
});

function resetModalChiTieu() {

    var form = $('#formChiTieu');

    // Mở lại toàn bộ input/select/textarea
    form.find('input, textarea, select')
        .prop('disabled', false)
        .prop('readonly', false);

    applyPhamViPermission();

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

    Swal.fire({
        title: 'Lý do từ chối',
        input: 'textarea',
        inputPlaceholder: 'Nhập lý do từ chối chỉ tiêu...',
        inputAttributes: { maxlength: 500 },
        showCancelButton: true,
        confirmButtonText: 'Từ chối',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#d9534f',
        inputValidator: function (value) {
            if (!value || !value.trim()) return 'Vui lòng nhập lý do từ chối.';
            if (value.trim().length > 500) return 'Lý do không được vượt quá 500 ký tự.';
        }
    }).then(function (result) {
        if (!result.isConfirmed) return;
        button.prop('disabled', true);
        $.ajax({
            url: $('#ULocal').val() + 'chisochatluong/TuChoi/',
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
            complete: function () { button.prop('disabled', false); }
        });
    });
});

function moFormTaoLaiChiTieu(row) {
    if (!row || !row.ma_chi_so) return;
    $('#formChiTieu')[0].reset();
    $('#formChiTieu').find('input, textarea, select').prop('disabled', false).prop('readonly', false);
    $('#action').val('add');
    $('#ma_chi_so').val('');
    $('#ten_chi_so').val(row.ten_chi_so);
    $('#ma_khia_canh').val(row.ma_khia_canh);
    $('#ma_thanh_to').val(row.ma_thanh_to);
    $('#nhom_chi_so').val(row.nhom_chi_so || '');
    $('#pham_vi').val(row.pham_vi);
    $('#muc_tieu').val(row.muc_tieu);
    $('#nguong_canh_bao').val(row.nguong_canh_bao);
    $('#don_vi_tinh').val(row.id_donvitinh);
    $('#id_chuky').val(row.id_chuky);
    $('#dinh_nghia').val(row.dinh_nghia);
    $('#thu_thap').val(row.thu_thap);
    $('#ten_tu_so').val(row.ten_tu_so);
    $('#ten_mau_so').val(row.ten_mau_so);
    applyPhamViPermission();
    $('#btnLuuChiTieu').show();
    $('#modalChiTieu .modal-title').text('Tạo lại chỉ tiêu bị từ chối');
    $('#modalChiTieu').modal('show');
}

$('#btnTaoLaiTuPopup').on('click', function () {
    if (!chiSoDangXem || parseInt(chiSoDangXem.trang_thai, 10) !== 3) return;
    var dataTaoLai = chiSoDangXem;
    $('#modalNhapDuLieu').one('hidden.bs.modal', function () {
        moFormTaoLaiChiTieu(dataTaoLai);
    });
    $('#modalNhapDuLieu').modal('hide');
});

$('#datatable-chiso').on('click', '.btn-nhapdl', function (e) {
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

    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/NhapDL/',
        type: 'POST',
        dataType: 'json',
        data: { ma_chi_so: row.ma_chi_so },
        beforeSend: function () { button.prop('disabled', true); },
        success: function (response) {
            if (response && response.success) moPopupNhapDuLieu(response.data, false);
            else Swal.fire('Lỗi', (response && response.message) || 'Không thể tải dữ liệu.', 'error');
        },
        error: function () { Swal.fire('Lỗi', 'Không thể tải dữ liệu nhập.', 'error'); },
        complete: function () { button.prop('disabled', false); }
    });
});

var nhapDuLieuDaLuu = {};
var dangXemDuLieu = false;
var mucTieuDangXem = '';
var nguongCanhBaoDangXem = '';
var bieuDoCotChiSo = null;
var bieuDoTronChiSo = null;
var duLieuBieuDoChiSo = [];
var chiSoDangXem = null;

function soKyTheoChuKy(id) {
    return ({ 1: 12, 2: 4, 3: 1, 4: 2 })[parseInt(id, 10)] || 1;
}

function tenKyTheoChuKy(id, ky) {
    id = parseInt(id, 10);
    if (id === 1) return 'Tháng ' + ky;
    if (id === 2) return 'Quý ' + ky;
    if (id === 4) return ky === 1 ? '6 tháng đầu năm' : '6 tháng cuối năm';
    return 'Cả năm';
}

function moPopupNhapDuLieu(data, chiXem) {
    dangXemDuLieu = chiXem === true;
    chiSoDangXem = data;
    mucTieuDangXem = data.muc_tieu || '';
    nguongCanhBaoDangXem = data.nguong_canh_bao || '';
    $('#nhap_ma_chi_so').val(data.ma_chi_so);
    $('#nhap_id_chuky').val(data.id_chuky);
    $('#nhap_ten_chi_so').val(data.ten_chi_so + ' (' + data.ten_chuky + ')');
    $('#nhap_label_tuso').text(data.ten_tu_so || 'Tử số');
    $('#nhap_label_mauso').text(data.ten_mau_so || 'Mẫu số');
    $('#xem_khia_canh').text(data.ten_khia_canh || '');
    $('#xem_thanh_to').text(data.ten_thanh_to || '');
    $('#xem_pham_vi').text(data.ten_pham_vi || '');
    $('#xem_don_vi_tinh').text(data.ten_don_vi_tinh || '');
    $('#xem_muc_tieu').text(data.muc_tieu  + "%" || '');
    $('#xem_nguong_canh_bao').text(data.nguong_canh_bao  + "%"|| '');
    $('#xem_dinh_nghia').text(data.dinh_nghia || '');
    $('#xem_thu_thap').text(data.thu_thap || '');
    $('#xem_ly_do_tu_choi').text(data.ly_do_tu_choi || '');
    $('#xem_ly_do_tu_choi_wrap').toggle(!!data.ly_do_tu_choi);
    nhapDuLieuDaLuu = data.dulieu || {};
    duLieuBieuDoChiSo = Array.isArray(data.chart_dulieu) ? data.chart_dulieu : [];
    var cacNam = Object.keys(nhapDuLieuDaLuu);
    if (dangXemDuLieu && canViewChiSoChart) {
        duLieuBieuDoChiSo.forEach(function (item) {
            Object.keys(item.dulieu || {}).forEach(function (nam) {
                if (cacNam.indexOf(nam) === -1) cacNam.push(nam);
            });
        });
    }
    cacNam.sort().reverse();
    $('#nhap_nam')
        .val(dangXemDuLieu && cacNam.length ? cacNam[0] : new Date().getFullYear())
        .prop('readonly', dangXemDuLieu);
    $('#btnLuuNhapDuLieu').toggle(!dangXemDuLieu);
    $('#btnTaoLaiTuPopup').toggle(dangXemDuLieu && parseInt(data.trang_thai, 10) === 3);
    $('#modalNhapDuLieu .modal-title').text(dangXemDuLieu ? 'Xem dữ liệu chỉ số' : 'Nhập dữ liệu chỉ số');
    taoInputTheoChuKy();
    var hienBieuDo = dangXemDuLieu && canViewChiSoChart;
    $('#nhap_bang_chuky').toggle(!hienBieuDo);
    $('#nhap_bieudo_wrap').toggle(hienBieuDo);
    if (hienBieuDo) {
        $('#modalNhapDuLieu').one('shown.bs.modal', taoBieuDoChuKy);
    } else {
        huyBieuDoChuKy();
    }
    $('#modalNhapDuLieu').modal('show');
}

function huyBieuDoChuKy() {
    if (bieuDoTronChiSo) {
        bieuDoTronChiSo.destroy();
        bieuDoTronChiSo = null;
    }
    if (bieuDoCotChiSo) {
        bieuDoCotChiSo.destroy();
        bieuDoCotChiSo = null;
    }
}

function mauGiaTriBieuDo(value) {
    var mucTieu = tachDieuKien(mucTieuDangXem, '>');
    var canhBao = tachDieuKien(nguongCanhBaoDangXem, '<');
    if (!mucTieu && !canhBao) return '#337ab7';
    if (!mucTieu) mucTieu = { toanTu: '>', moc: canhBao.moc };
    if (!canhBao) canhBao = { toanTu: '<', moc: mucTieu.moc };
    if (thoaDieuKien(value, mucTieu)) return '#5cb85c';
    if (thoaDieuKien(value, canhBao)) return '#d9534f';
    return '#f0ad4e';
}

function taoBieuDoChuKy() {
    huyBieuDoChuKy();
    var labels = [];
    for (var i = 1; i <= soKyTheoChuKy($('#nhap_id_chuky').val()); i++) {
        labels.push(tenKyTheoChuKy($('#nhap_id_chuky').val(), i));
    }
    var palette = ['#337ab7', '#5cb85c', '#f0ad4e', '#d9534f', '#5bc0de', '#8e44ad', '#16a085', '#e67e22'];
    var nam = String($('#nhap_nam').val());
    var datasets = duLieuBieuDoChiSo.map(function (userData, index) {
        var saved = (userData.dulieu || {})[nam] || {};
        var rows = saved.du_lieu || [];
        var color = palette[index % palette.length];
        var hasValue = false;
        var values = labels.map(function (_, kyIndex) {
            var value = rows[kyIndex] ? rows[kyIndex].value : null;
            if (value !== null && value !== '' && value !== undefined && !isNaN(Number(value))) {
                hasValue = true;
                return Number(value);
            }
            return NaN;
        });
        if (!hasValue) return null;
        return {
            label: userData.ten_user || ('Người dùng ' + userData.id_user),
            data: values,
            borderColor: color,
            backgroundColor: color,
            fill: false,
            lineTension: 0.15,
            pointRadius: 4,
            pointHoverRadius: 6
        };
    }).filter(function (dataset) { return dataset !== null; });

    if (!datasets.length) {
        $('#nhap_bieudo_cot_empty, #nhap_bieudo_tron_empty').show();
        $('#nhap_bieudo_cot, #nhap_bieudo_tron').parent().hide();
        return;
    }

    if (typeof Chart === 'undefined') return;
    taoBieuDoCotChiSo(labels, datasets);
    taoBieuDoTronChiSo(nam);
}

function taoBieuDoCotChiSo(labels, lineDatasets) {
    var coDuLieu = lineDatasets.length > 0;
    $('#nhap_bieudo_cot_empty').toggle(!coDuLieu);
    $('#nhap_bieudo_cot').parent().toggle(coDuLieu);
    if (!coDuLieu) return;

    var oldCanvas = document.getElementById('nhap_bieudo_cot');
    if (!oldCanvas || !oldCanvas.parentNode || typeof Chart === 'undefined') return;
    var newCanvas = oldCanvas.cloneNode(false);
    oldCanvas.parentNode.replaceChild(newCanvas, oldCanvas);
    var datasets = lineDatasets.map(function (dataset) {
        return {
            label: dataset.label,
            data: dataset.data.slice(),
            backgroundColor: dataset.backgroundColor,
            borderColor: dataset.borderColor,
            borderWidth: 1
        };
    });
    bieuDoCotChiSo = new Chart(newCanvas.getContext('2d'), {
        type: 'bar',
        data: { labels: labels, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: { beginAtZero: true },
                    scaleLabel: { display: true, labelString: 'Giá trị (%)' }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, chartData) {
                        var dataset = chartData.datasets[tooltipItem.datasetIndex] || {};
                        return (dataset.label || 'Người dùng') + ': ' + tooltipItem.yLabel + '%';
                    }
                }
            }
        }
    });
}

function nhomKetQuaChiSo(value) {
    var mucTieu = tachDieuKien(mucTieuDangXem, '>');
    var canhBao = tachDieuKien(nguongCanhBaoDangXem, '<');
    if (!mucTieu && !canhBao) return 2;
    if (!mucTieu) mucTieu = { toanTu: '>', moc: canhBao.moc };
    if (!canhBao) canhBao = { toanTu: '<', moc: mucTieu.moc };
    if (thoaDieuKien(value, mucTieu)) return 0;
    if (thoaDieuKien(value, canhBao)) return 1;
    return 2;
}

function taoBieuDoTronChiSo(nam) {
    var labels = ['Đạt mục tiêu', 'Không đạt', 'Đủ'];
    var counts = [0, 0, 0];
    var usersTheoNhom = [[], [], []];

    duLieuBieuDoChiSo.forEach(function (userData) {
        var saved = (userData.dulieu || {})[nam] || {};
        var rows = saved.du_lieu || [];
        var giaTriMoiNhat = null;
        for (var i = rows.length - 1; i >= 0; i--) {
            var value = rows[i] ? rows[i].value : null;
            if (value !== null && value !== '' && value !== undefined && !isNaN(Number(value))) {
                giaTriMoiNhat = Number(value);
                break;
            }
        }
        if (giaTriMoiNhat === null) return;
        var nhom = nhomKetQuaChiSo(giaTriMoiNhat);
        counts[nhom]++;
        usersTheoNhom[nhom].push(userData.ten_user || ('Người dùng ' + userData.id_user));
    });

    var coDuLieu = counts.some(function (count) { return count > 0; });
    $('#nhap_bieudo_tron_empty').toggle(!coDuLieu);
    $('#nhap_bieudo_tron').parent().toggle(coDuLieu);
    if (!coDuLieu) return;

    var oldCanvas = document.getElementById('nhap_bieudo_tron');
    if (!oldCanvas || !oldCanvas.parentNode || typeof Chart === 'undefined') return;
    var newCanvas = oldCanvas.cloneNode(false);
    oldCanvas.parentNode.replaceChild(newCanvas, oldCanvas);
    bieuDoTronChiSo = new Chart(newCanvas.getContext('2d'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: ['#5cb85c', '#d9534f', '#f0ad4e'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            tooltips: {
                callbacks: {
                    label: function (tooltipItem) {
                        var index = tooltipItem.index;
                        var users = usersTheoNhom[index];
                        return labels[index] + ': ' + counts[index] +
                            (users.length ? ' - ' + users.join(', ') : '');
                    }
                }
            }
        }
    });
}

function tachDieuKien(value, toanTuMacDinh) {
    var text = String(value == null ? '' : value).trim().replace(/%/g, '').replace(',', '.');
    var match = text.match(/^(>=|<=|>|<|=)?\s*(-?\d+(?:\.\d+)?)$/);
    if (!match) return null;
    return { toanTu: match[1] || toanTuMacDinh, moc: Number(match[2]) };
}

function thoaDieuKien(giaTri, dieuKien) {
    if (!dieuKien) return false;
    if (dieuKien.toanTu === '>') return giaTri > dieuKien.moc;
    if (dieuKien.toanTu === '>=') return giaTri >= dieuKien.moc;
    if (dieuKien.toanTu === '<') return giaTri < dieuKien.moc;
    if (dieuKien.toanTu === '<=') return giaTri <= dieuKien.moc;
    return giaTri === dieuKien.moc;
}

function toMauGiaTri(input, value) {
    input.removeClass('nhap-value-xanh nhap-value-do nhap-value-vang');
    var text = String(value == null ? '' : value).trim().replace(',', '.');
    if (!dangXemDuLieu || !/^-?\d+(?:\.\d+)?$/.test(text)) return;

    var giaTri = Number(text);
    var mucTieu = tachDieuKien(mucTieuDangXem, '>');
    var canhBao = tachDieuKien(nguongCanhBaoDangXem, '<');
    if (!mucTieu && !canhBao) return;
    if (!mucTieu) mucTieu = { toanTu: '>', moc: canhBao.moc };
    if (!canhBao) canhBao = { toanTu: '<', moc: mucTieu.moc };

    if (thoaDieuKien(giaTri, mucTieu)) input.addClass('nhap-value-xanh');
    else if (thoaDieuKien(giaTri, canhBao)) input.addClass('nhap-value-do');
    else if (mucTieu && canhBao) input.addClass('nhap-value-vang');
}

function taoInputTheoChuKy() {
    var id = $('#nhap_id_chuky').val();
    var saved = nhapDuLieuDaLuu[String($('#nhap_nam').val())] || {};
    var rows = saved.du_lieu || [];
    var html = '';
    for (var i = 1; i <= soKyTheoChuKy(id); i++) {
        var item = rows[i - 1] || {};
        var readonly = dangXemDuLieu ? ' readonly tabindex="-1"' : '';
        html += '<tr class="nhap-ky-row">' +
    '<td>' + tenKyTheoChuKy(id, i) + '</td>' +

    '<td>' +
        '<input type="number" min="0" step="any" ' +
        'class="form-control nhap-tu-so" value="' + (item.tu_so != null ? item.tu_so : '') + '"' + readonly + '>' +
    '</td>' +

    '<td>' +
        '<input type="number" min="0.0000000001" step="any" ' +
        'class="form-control nhap-mau-so" value="' + (item.mau_so != null ? item.mau_so : '') + '"' + readonly + '>' +
    '</td>' +

    '<td>' +
        '<input type="text" class="form-control nhap-value" ' +
        'readonly tabindex="-1" value="' + (item.value != null ? item.value : '') + '">' +
    '</td>' +
'</tr>';
    }
    $('#nhap_dulieu_body').html(html);
    $('#nhap_dulieu_body .nhap-value').each(function () {
        toMauGiaTri($(this), $(this).val());
    });
}

$('#nhap_nam').on('change', taoInputTheoChuKy);
$('#nhap_dulieu_body').on(
    'input',
    '.nhap-tu-so, .nhap-mau-so',
    function () {
        var tr = $(this).closest('tr');
        var inputMauSo = tr.find('.nhap-mau-so')[0];

        var tuSo = parseFloat(
            tr.find('.nhap-tu-so').val()
        );

        var mauSo = parseFloat(
            tr.find('.nhap-mau-so').val()
        );

        // Xóa thông báo lỗi cũ
        inputMauSo.setCustomValidity('');

        if (!isNaN(tuSo) && !isNaN(mauSo) && mauSo < tuSo) {
            inputMauSo.setCustomValidity(
                'Mẫu số phải lớn hơn hoặc bằng tử số'
            );

            inputMauSo.reportValidity();
        }

        tr.find('.nhap-value').val(
            !isNaN(tuSo) && mauSo > 0
                ? ((tuSo / mauSo) * 100).toFixed(2)
                : ''
        );
    }
);

$('#formNhapDuLieu').on('submit', function (e) {
    e.preventDefault();
    var rows = [];
    console.log('o day');
    $('#nhap_dulieu_body .nhap-ky-row').each(function () {
        rows.push(
            {   tu_so: $(this).find('.nhap-tu-so').val(), 
                mau_so: $(this).find('.nhap-mau-so').val(), 
                thoigian: $(this).find('.nhap-mau-so').val(), 
            });
    });
    $.ajax({
        url: $('#ULocal').val() + 'chisochatluong/LuuNhapDL/', type: 'POST', dataType: 'json',
        data: 
            {   ma_chi_so: $('#nhap_ma_chi_so').val(), 
                nam: $('#nhap_nam').val(), 
                du_lieu: JSON.stringify(rows) },
        beforeSend: function () { $('#btnLuuNhapDuLieu').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...'); },
        success: function (response) {
            if (response && response.success) { $('#modalNhapDuLieu').modal('hide'); Swal.fire('Thành công', response.message, 'success'); }
            else Swal.fire('Lỗi', (response && response.message) || 'Không thể lưu dữ liệu.', 'error');
        },
        error: function () { Swal.fire('Lỗi', 'Có lỗi xảy ra khi lưu dữ liệu.', 'error'); },
        complete: function () { $('#btnLuuNhapDuLieu').prop('disabled', false).html('<i class="fa fa-save"></i> Lưu dữ liệu'); }
    });
});
