function initDanhMucCrud(config) {
    var selector = '#' + config.tableId;
    var definitions = [{ targets: 0, width: '5%', className: 'text-center', sortable: false, render: function (data, type, row, meta) { return meta.row + 1; } }];
    config.fields.forEach(function (field, index) {
        definitions.push({
            targets: index + 1,
            data: field.data,
            render: function (data, type) {
                if (field.selectId && type === 'display') {
                    var text = $('#' + field.selectId + ' option').filter(function () { return String($(this).val()) === String(data); }).text();
                    return '<input type="hidden" id="selectid" value="' + $('<div>').text(data || '').html() + '">' + $('<div>').text(text).html();
                }
                return type === 'display' ? $('<div>').text(data || '').html() : data;
            }
        });
    });
    definitions.push({ targets: config.fields.length + 1, data: config.idField, sortable: false, render: function (data) {
        return '<input type="hidden" name="id" id="id" value="' + $('<div>').text(data || '').html() + '">' +
            '<button type="button" class="add btn btn-primary btn-sm" title="Lưu"><i class="fa fa-hdd-o"></i></button>' +
            '<button type="button" class="edit btn btn-warning btn-sm" title="Sửa"><i class="glyphicon glyphicon-cog"></i></button>' +
            '<button type="button" class="delete btn btn-danger btn-sm" title="Xóa"><i class="glyphicon glyphicon-trash"></i></button>';
    }});

    var table = $(selector).DataTable({ ajax: { url: $('#ULocal').val() + config.route + '/getData/', type: 'POST' }, dom: '<"dt-toolbar">frtlpi', destroy: true, searching: false, columnDefs: definitions,
        initComplete: function () { $(selector + '_wrapper div.dt-toolbar').html('<button type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Thêm mới</button> <button type="button" class="btn btn-info save"><i class="fa fa-floppy-o"></i> Lưu dữ liệu</button>'); }
    });
    $(document).on('click', selector + '_wrapper .add-new', function () { addRowInput($(selector), 1); });
    $(document).on('click', selector + '_wrapper .save', function () { if (confirm('Bạn có chắc muốn lưu dữ liệu mới không?')) { saveData($(selector), $('#ULocal').val() + config.route + '/save/', config.success); setTimeout(function(){ table.ajax.reload(); }, 1000); } });
    $(document).on('click', selector + ' .add', function () { if (confirm('Bạn có chắc muốn cập nhật không?')) { updateData($(this), $('#ULocal').val() + config.route + '/save/', config.success); setTimeout(function(){ table.ajax.reload(); }, 1000); } });
    $(document).on('click', selector + ' .edit', function () { addInput($(selector), $(this).closest('tr')); });
    $(document).on('click', selector + ' .delete', function () { if (confirm('Bạn có chắc muốn xóa dữ liệu này không?')) { deleteData(table, $(this), $('#ULocal').val() + config.route + '/delete/', config.deleted); } });
}
