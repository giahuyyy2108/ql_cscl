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

<style>
    #modalXemChiSoKhoa .xem-value-xanh { background:#dff0d8; color:#3c763d; border-color:#3c763d; font-weight:600; }
    #modalXemChiSoKhoa .xem-value-do { background:#f2dede; color:#a94442; border-color:#a94442; font-weight:600; }
    #modalXemChiSoKhoa .xem-value-vang { background:#fcf8e3; color:#8a6d3b; border-color:#8a6d3b; font-weight:600; }
</style>
<div class="modal fade" id="modalXemChiSoKhoa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            <h4 class="modal-title">Xem dữ liệu chỉ số</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-8 form-group">
                    <label>Chỉ số:</label>
                    <input type="text" class="form-control" id="xem_ck_ten" readonly>
                </div>
                <div class="col-md-4 form-group">
                    <label>Năm</label>
                    <input type="number" class="form-control" id="xem_ck_nam" readonly>
                </div>
            </div>
            <div class="well well-sm" style="margin-bottom:15px">
                <div class="row">
                    <div class="col-md-6"><strong>Khía cạnh:</strong> <span id="xem_ck_khia_canh"></span></div>
                    <div class="col-md-6"><strong>Thành tố:</strong> <span id="xem_ck_thanh_to"></span></div>
                    <div class="col-md-6"><strong>Phạm vi:</strong> <span id="xem_ck_pham_vi"></span></div>
                    <div class="col-md-6"><strong>Đơn vị tính:</strong> <span id="xem_ck_don_vi_tinh"></span></div>
                    <div class="col-md-6"><strong>Mục tiêu:</strong> <span id="xem_ck_muc_tieu"></span></div>
                    <div class="col-md-6"><strong>Ngưỡng cảnh báo:</strong> <span id="xem_ck_nguong_canh_bao"></span></div>
                    <div class="col-md-12"><strong>Định nghĩa:</strong> <span id="xem_ck_dinh_nghia"></span></div>
                    <div class="col-md-12"><strong>Phương pháp thu thập:</strong> <span id="xem_ck_thu_thap"></span></div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th style="width:18%">Kỳ</th><th id="xem_ck_label_tuso">Tử số</th><th id="xem_ck_label_mauso">Mẫu số</th><th style="width:18%">Giá trị (%)</th></tr></thead>
                    <tbody id="xem_ck_chuky_body"></tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button></div>
    </div></div>
</div>
