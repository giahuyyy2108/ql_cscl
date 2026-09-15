<?PHP $listUser = $request->getAttribute('listUser'); ?>
<div class="x_panel">
    <div class="x_title"><h2>Chỉ số chất lượng Khoa/Phòng</h2><div class="clearfix"></div></div>
    <div class="x_content">
        <div class="row" style="margin-bottom:15px">
            <div class="col-md-6 col-sm-8 col-xs-12">
                <label>Khoa Phòng</label>
                <input type="hidden" id="id_user" value="">
                <div class="dropdown" id="dropdownUserKhoa">
                    <button type="button" class="btn btn-default btn-block dropdown-toggle text-left" data-toggle="dropdown" style="text-align:left">
                        <span id="tenUserKhoa">-- Chọn người dùng --</span>
                        <span class="caret pull-right" style="margin-top:8px"></span>
                    </button>
                    <ul class="dropdown-menu" style="width:100%;max-height:320px;overflow-y:auto">
                        <li style="padding:8px" onclick="event.stopPropagation()">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                <input type="text" id="timUserKhoa" class="form-control" placeholder="Tìm người dùng..." autocomplete="off">
                            </div>
                        </li>
                        <li class="divider"></li>
                        <?php foreach ($listUser as $user): ?>
                            <li class="user-khoa-option" data-id="<?= (int) $user->get('id') ?>" data-name="<?= htmlspecialchars($user->get('hoTen')) ?>">
                                <a href="#"><?= htmlspecialchars($user->get('hoTen')) ?></a>
                            </li>
                        <?php endforeach; ?>
                        <li class="user-khoa-empty text-muted" style="display:none;padding:8px 15px">Không tìm thấy người dùng</li>
                    </ul>
                </div>
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
            <div class="col-md-4 form-group">
                <label>Năm dữ liệu</label>
                <select id="xem_ck_nam" class="form-control"></select>
            </div>
            <div class="col-md-12">
                <div class="table-responsive"><table class="table table-bordered table-striped">
                    <thead><tr><th>Kỳ</th><th id="xem_ck_label_tuso">Tử số</th><th id="xem_ck_label_mauso">Mẫu số</th><th>Giá trị (%)</th></tr></thead>
                    <tbody id="xem_ck_chuky_body"></tbody>
                </table></div>
            </div>
        </div></div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button></div>
    </div></div>
</div>
