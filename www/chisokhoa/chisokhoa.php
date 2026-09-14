<?PHP $listUser = $request->getAttribute('listUser'); ?>
<div class="x_panel">
    <div class="x_title"><h2>Chỉ số chất lượng Khoa/Phòng</h2><div class="clearfix"></div></div>
    <div class="x_content">
        <div class="row" style="margin-bottom:15px">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <label for="id_user">Người dùng</label>
                <select id="id_user" class="form-control">
                    <option value="">-- Chọn người dùng --</option>
                    <?php foreach ($listUser as $user): ?>
                        <option value="<?= (int) $user->get('id') ?>"><?= htmlspecialchars($user->get('hoTen')) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-12" style="padding-top:25px">
                <button type="button" id="btnTimChiSoKhoa" class="btn btn-primary"><i class="fa fa-search"></i> Tìm</button>
            </div>
        </div>
        <table id="datatable-chisokhoa" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
            <thead><tr>
                <th>Mã</th><th>Tên chỉ số</th><th>Người dùng</th><th>Mục tiêu</th>
                <th>Ngưỡng cảnh báo</th><th>Đơn vị tính</th><th>Chu kỳ</th><th>Trạng thái</th><th>Thao tác</th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalXemChiSoKhoa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button><h4 class="modal-title">Thông tin chỉ số</h4></div>
        <div class="modal-body"><div class="row">
            <div class="col-md-12 form-group"><label>Tên chỉ số</label><input id="xem_ck_ten" class="form-control" readonly></div>
            <div class="col-md-6 form-group"><label>Mục tiêu</label><input id="xem_ck_muctieu" class="form-control" readonly></div>
            <div class="col-md-6 form-group"><label>Ngưỡng cảnh báo</label><input id="xem_ck_nguong" class="form-control" readonly></div>
            <div class="col-md-12 form-group"><label>Định nghĩa</label><textarea id="xem_ck_dinhnghia" class="form-control" rows="3" readonly></textarea></div>
            <div class="col-md-12 form-group"><label>Phương pháp thu thập</label><textarea id="xem_ck_thuthap" class="form-control" rows="3" readonly></textarea></div>
        </div></div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button></div>
    </div></div>
</div>
