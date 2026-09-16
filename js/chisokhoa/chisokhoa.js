/* DATA TABLES */
var table;
var allKhoaOptions = [];

function getOptionText(selectId, value) {
    var text = $('#' + selectId + ' option').filter(function () {
        return String($(this).val()) === String(value);
    }).text();

    return text || value || '';
}

function initKhoaOptions() {
    allKhoaOptions = $('#filter_khoaphong option').map(function () {
        return {
            value: $(this).val(),
            text: $(this).text(),
            idKhoi: $(this).data('khoi') || ''
        };
    }).get();
}

function reloadKhoaPhongByKhoi() {
    var idKhoi = $('#filter_khoi').val();
    var selectedKhoa = $('#filter_khoaphong').val();
    var options = '<option value="">Tất cả Khoa/Phòng</option>';
    var hasSelectedKhoa = selectedKhoa === '';

    allKhoaOptions.forEach(function (item) {
        if (!item.value) {
            return;
        }
        if (!idKhoi || String(item.idKhoi) === String(idKhoi)) {
            options += '<option value="' + item.value + '" data-khoi="' + item.idKhoi + '">' + item.text + '</option>';
            if (String(item.value) === String(selectedKhoa)) {
                hasSelectedKhoa = true;
            }
        }
    });

    $('#filter_khoaphong').html(options);
    $('#filter_khoaphong').val(hasSelectedKhoa ? selectedKhoa : '');
}

table = $('#datatable-chisokhoa').DataTable({
    destroy: true,
    ordering: false,
    dom:
        "<'row'<'col-sm-6'l><'col-sm-6 text-right'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    ajax: {
        url: $("#ULocal").val() + 'chisokhoa/getData/',
        type: 'POST',
        data: function (data) {
            data.id_khoi = $('#filter_khoi').val();
            data.id_khoaphong = $('#filter_khoaphong').val();
        },
        error: function(response) {
            alert(JSON.stringify(response));
        },
        dataSrc: function(json) {
            if (!json || json.data == null) {
                return [];
            }
            return Array.isArray(json.data) ? json.data : [json.data];
        }
    },
    responsive: true,
    autoWidth: false,
    columns: [
        {
            data: 'ma_chi_so',
            // render: function (data, type, row, meta) {
            //     return meta.row + 1;
            // }
        },
        { data: 'ten_chi_so' },
        {
            data: 'ma_khia_canh',
            render: function (data) {
                return getOptionText('ma_khia_canh', data);
            }
        },
        {
            data: 'ma_thanh_to',
            render: function (data) {
                return getOptionText('ma_thanh_to', data);
            }
        },
        {
            data: 'pham_vi',
            render: function (data) {
                return getOptionText('pham_vi', data);
            }
        },
        {
            data: 'id_chuky',
            render: function (data) {
                return getOptionText('id_chuky', data);
            }
        },
        {
            data: 'nguoi_gui',
            render: function (data) {
                return data && data.hoTen ? data.hoTen : '';
            }
        },
        {
            data: 'nguoi_duyet',
            render: function (data) {
                return data && data.hoTen ? data.hoTen : '';
            }
        },
        {
            data: 'trang_thai',
            render: function (data, type) {
                var tenTrangThai = data ? (data.tenTrangThai || data.maTrangThai) : '';

                if (type !== 'display') {
                    return tenTrangThai;
                }

                return $('<span>')
                    .addClass('badge')
                    .addClass('dt-center')
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
                `;
            }
        }
    ],
    drawCallback: function() {
        $('[data-toggle="tooltip"]').tooltip();
    }
});

$(document).ready(function () {
    initKhoaOptions();

    $('#filter_khoi').on('change', function () {
        reloadKhoaPhongByKhoi();
        table.ajax.reload();
    });

    $('#filter_khoaphong').on('change', function () {
        table.ajax.reload();
    });
});

$('#datatable-chisokhoa').on('click', '.btn-xem', function () {
    var tr = $(this).closest('tr');

    if (tr.hasClass('child')) {
        tr = tr.prev();
    }

    var row = table.row(tr).data();

    if (!row) {
        return;
    }

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
    $('#id_khoaphong').val(row.id_khoaphong);
    $('#dinh_nghia').val(row.dinh_nghia);
    $('#thu_thap').val(row.thu_thap);
    $('#ten_tu_so').val(row.ten_tu_so);
    $('#ten_mau_so').val(row.ten_mau_so);

    $('#formChiTieu')
        .find('input, textarea, select')
        .prop('disabled', true);

    $('#btnLuuChiTieu').hide();

    $('#modalChiTieu .modal-title')
        .text('Xem Chi tieu');

    $('#modalChiTieu').modal('show');
});

$('#modalChiTieu').on('hidden.bs.modal', function () {
    $('#formChiTieu')
        .find('input, textarea, select')
        .prop('disabled', false);

    $('#btnLuuChiTieu').hide();
});
