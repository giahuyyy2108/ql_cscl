/* DATA TABLES */
var table;
function init_DataTables() {
    table = $('#datatable-khoi').DataTable({
        ajax: {
            url: $("#ULocal").val() + 'khoi/getData/',
            type: 'POST'
        },
        dom: '<"dt-toolbar">frtlpi',
        destroy: true,
        searching: false,
        columnDefs: [
            {
                targets: 0,
                width: '5%',
                className: "text-center",
                sortable: false,
                render: function (data, type, row, meta) {
                    return (meta.row + 1);
                }
            },
            {
                targets: 1,
                width: '80%',
                data: "TenKhoi"
            },
            {
                targets: 2,
                width: '15%',
                data: "MaKhoi",
                sortable: false,
                render: function (data) {
                    var id = '<input type="hidden" name="id" id="id" value="' + data + '">';
                    var save = '<button id="btn-update" class="add btn btn-primary btn-sm" title="Luu" data-toggle="tooltip"><i class="fa fa-hdd-o"></i></button>';
                    var update = '<button class="edit btn btn-warning btn-sm" title="Sua" data-toggle="tooltip"><i class="glyphicon glyphicon-cog"></i></button>';
                    var del = '<button class="delete btn btn-danger btn-sm" title="Xoa" data-toggle="tooltip"><i class="glyphicon glyphicon-trash"></i></button>';
                    return [id, save, update, del].join('');
                }
            }
        ],
        initComplete: function () {
            $("div.dt-toolbar").css({ display: "flex", justifyContent: "space-between", alignItems: "center" }).html(
                '<div class="left-toolbar">' +
                '<button type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Them moi</button>' +
                '<button type="button" class="btn btn-info save"><i class="fa fa-floppy-o"></i> Luu du lieu</button>' +
                '</div>'
            );
            hiddenButton();
            $(".add-new").click(function () {
                addRowInput($('#datatable-khoi'), 1);
            });
            $(".save").click(function () {
                if (confirm("Ban co chac muon cap nhat khong?")) {
                    saveData($('#datatable-khoi'), $("#ULocal").val() + 'khoi/save/', 'Khoi da duoc cap nhat thanh cong.');
                    setTimeout(function () {
                        $('#datatable-khoi').DataTable().ajax.reload(hiddenButton);
                    }, 1000);
                }
            });
        }
    });
}

$(document).ready(function () {
    init_DataTables();

    $(document).on("click", ".add", function () {
        if (confirm("Ban co chac muon cap nhat khong?")) {
            updateData($(this), $("#ULocal").val() + 'khoi/save/', 'Khoi da duoc cap nhat thanh cong.');
            setTimeout(function () {
                $('#datatable-khoi').DataTable().ajax.reload(hiddenButton);
            }, 1000);
        }
    });

    $(document).on("click", ".edit", function () {
        addInput($('#datatable-khoi'), $(this).closest('tr'));
    });

    $(document).on("click", ".delete", function () {
        if (confirm("Ban co chac muon xoa khoi nay khong?")) {
            deleteData(table, $(this), $("#ULocal").val() + 'khoi/delete/', 'Khoi da duoc xoa thanh cong.');
            $('#datatable-khoi').DataTable().ajax.reload(hiddenButton);
        }
    });
});

function hiddenButton() {
    if ($("#role-khoi-saveKhoi").val() == "false" && $("#role-khoi-save").val() == "false") {
        $(".add-new").hide();
        $(".edit").hide();
        $(".add").hide();
        $(".save").hide();
    }
    if ($("#role-khoi-deleteKhoi").val() == "false" && $("#role-khoi-delete").val() == "false") {
        $(".delete").hide();
    }
}
