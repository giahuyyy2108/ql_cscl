/* CRUD đơn vị tính */
var table;

function initDataTableDonViTinh() {
    table = $('#datatable-donvitinh').DataTable({
        ajax: { url: $('#ULocal').val() + 'donvitinh/getData/', type: 'POST' },
        dom: '<"dt-toolbar">frtlpi',
        destroy: true,
        searching: false,
        columnDefs: [
            { targets: 0, width: '5%', className: 'text-center', sortable: false,
                render: function (data, type, row, meta) { return meta.row + 1; } },
            { targets: 1, width: '80%', data: 'ten',
                render: function (data, type) {
                    return type === 'display' ? $('<div>').text(data || '').html() : data;
                } },
            { targets: 2, width: '15%', data: 'id', sortable: false,
                render: function (data) {
                    var id = '<input type="hidden" name="id" id="id" value="' + data + '">';
                    var save = '<button type="button" class="add btn btn-primary btn-sm" title="Lưu" data-toggle="tooltip"><i class="fa fa-hdd-o"></i></button>';
                    var edit = '<button type="button" class="edit btn btn-warning btn-sm" title="Sửa" data-toggle="tooltip"><i class="glyphicon glyphicon-cog"></i></button>';
                    var del = '<button type="button" class="delete btn btn-danger btn-sm" title="Xóa" data-toggle="tooltip"><i class="glyphicon glyphicon-trash"></i></button>';
                    return [id, save, edit, del].join('');
                } }
        ],
        initComplete: function () {
            $('div.dt-toolbar').css({ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }).html(
                '<div class="left-toolbar"><button type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Thêm mới</button> ' +
                '<button type="button" class="btn btn-info save"><i class="fa fa-floppy-o"></i> Lưu dữ liệu</button></div>'
            );
            hiddenDonViTinhButtons();
        },
        drawCallback: function () {
            $('[data-toggle="tooltip"]').tooltip();
            hiddenDonViTinhButtons();
        }
    });
}

$(document).ready(function () {
    initDataTableDonViTinh();
    $(document).on('click', '#datatable-donvitinh_wrapper .add-new', function () {
        addRowInput($('#datatable-donvitinh'), 1);
    });
    $(document).on('click', '#datatable-donvitinh_wrapper .save', function () {
        if (confirm('Bạn có chắc muốn lưu dữ liệu mới không?')) {
            saveData($('#datatable-donvitinh'), $('#ULocal').val() + 'donvitinh/save/', 'Đơn vị tính đã được cập nhật thành công.');
            setTimeout(function () { table.ajax.reload(hiddenDonViTinhButtons); }, 1000);
        }
    });
    $(document).on('click', '#datatable-donvitinh .add', function () {
        if (confirm('Bạn có chắc muốn cập nhật không?')) {
            updateData($(this), $('#ULocal').val() + 'donvitinh/save/', 'Đơn vị tính đã được cập nhật thành công.');
            setTimeout(function () { table.ajax.reload(hiddenDonViTinhButtons); }, 1000);
        }
    });
    $(document).on('click', '#datatable-donvitinh .edit', function () {
        addInput($('#datatable-donvitinh'), $(this).closest('tr'));
    });
    $(document).on('click', '#datatable-donvitinh .delete', function () {
        if (confirm('Bạn có chắc muốn xóa đơn vị tính này không?')) {
            deleteData(table, $(this), $('#ULocal').val() + 'donvitinh/delete/', 'Đơn vị tính đã được xóa thành công.');
        }
    });
});

function hiddenDonViTinhButtons() {
    if ($('#role-donvitinh-saveDonViTinh').val() === 'false' && $('#role-donvitinh-save').val() === 'false') {
        $('#datatable-donvitinh_wrapper .add-new, #datatable-donvitinh_wrapper .save, #datatable-donvitinh .edit, #datatable-donvitinh .add').hide();
    }
    if ($('#role-donvitinh-deleteDonViTinh').val() === 'false' && $('#role-donvitinh-delete').val() === 'false') {
        $('#datatable-donvitinh .delete').hide();
    }
}
