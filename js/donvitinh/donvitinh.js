$(function () {
    function showMessage(title, message, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire(title, message, type);
        } else {
            alert(message);
        }
    }

    $('#btnThemDonViTinh').on('click', function () {
        $('#formDonViTinh')[0].reset();
        $('#donViTinhId').val('0');
        $('#modalDonViTinh .modal-title').text('Thêm đơn vị tính');
        $('#modalDonViTinh').modal('show');
    });

    $('.btn-sua-don-vi-tinh').on('click', function () {
        $('#donViTinhId').val($(this).attr('data-id'));
        $('#donViTinhTen').val($(this).attr('data-ten'));
        $('#modalDonViTinh .modal-title').text('Sửa đơn vị tính');
        $('#modalDonViTinh').modal('show');
    });

    $('#modalDonViTinh').on('shown.bs.modal', function () {
        $('#donViTinhTen').focus();
    });

    $('#formDonViTinh').on('submit', function (event) {
        event.preventDefault();
        var ten = $.trim($('#donViTinhTen').val());
        if (!ten) {
            showMessage('Thiếu thông tin', 'Tên đơn vị tính không được để trống.', 'warning');
            return;
        }

        var button = $('#btnLuuDonViTinh').prop('disabled', true);
        $.ajax({
            url: $('#ULocal').val() + 'donvitinh/save/',
            type: 'POST', dataType: 'json',
            data: { id: $('#donViTinhId').val(), ten: ten },
            success: function (response) {
                if (response && response.success) {
                    location.reload();
                } else {
                    showMessage('Không thể lưu', (response && response.message) || 'Có lỗi xảy ra.', 'error');
                }
            },
            error: function () { showMessage('Lỗi', 'Không thể kết nối đến máy chủ.', 'error'); },
            complete: function () { button.prop('disabled', false); }
        });
    });

    $('.btn-xoa-don-vi-tinh').on('click', function () {
        var id = $(this).attr('data-id');
        var ten = $(this).attr('data-ten');
        function removeItem() {
            $.ajax({
                url: $('#ULocal').val() + 'donvitinh/delete/',
                type: 'POST', dataType: 'json', data: { id: id },
                success: function (response) {
                    if (response && response.success) {
                        location.reload();
                    } else {
                        showMessage('Không thể xóa', (response && response.message) || 'Có lỗi xảy ra.', 'error');
                    }
                },
                error: function () { showMessage('Lỗi', 'Không thể kết nối đến máy chủ.', 'error'); }
            });
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Xóa đơn vị tính?', text: 'Bạn có chắc muốn xóa “' + ten + '”?',
                icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Xóa', cancelButtonText: 'Hủy'
            }).then(function (result) {
                if (result.isConfirmed || result.value) { removeItem(); }
            });
        } else if (confirm('Bạn có chắc muốn xóa "' + ten + '"?')) {
            removeItem();
        }
    });
});
