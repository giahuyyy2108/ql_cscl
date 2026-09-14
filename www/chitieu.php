<?PHP
$thangHienTai = (int) $request->getAttribute('thangHienTai');
$namHienTai = (int) $request->getAttribute('namHienTai');
?>
<div class="x_panel">
    <div class="x_title">
        <h2>Chỉ tiêu năm <?= $namHienTai ?></h2>
        <div class="clearfix"></div>
    </div>
    <div class="x_content">
        <div class="row" style="margin-bottom:15px">
            <div class="col-sm-3">
                <label for="chonThangChiTieu">Chọn tháng nhập liệu</label>
                <select class="form-control" id="chonThangChiTieu">
                    <?php for ($thang = 1; $thang <= $thangHienTai; $thang++): ?>
                        <option value="<?= $thang ?>" <?= $thang === $thangHienTai ? 'selected' : '' ?>>Tháng <?= $thang ?>/<?= $namHienTai ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <!-- <div class="col-sm-9"><div class="alert alert-warning" style="margin-top:25px;margin-bottom:0">Không thể nhập dữ liệu của tháng tương lai.</div></div> -->
        </div>
        <div class="row" style="margin-bottom:15px">
            <div class="col-sm-4"><div class="alert alert-info" style="margin-bottom:0">Tổng chỉ tiêu: <strong id="tongChiTieu">0</strong></div></div>
            <div class="col-sm-4"><div class="alert alert-danger" style="margin-bottom:0">Chưa nhập: <strong id="tongChuaNhap">0</strong></div></div>
            <div class="col-sm-4"><div class="alert alert-success" style="margin-bottom:0">Đã nhập: <strong id="tongDaNhap">0</strong></div></div>
        </div>
        <table id="datatable-chitieu-thang" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
            <thead><tr>
                <th>Mã</th><th>Tên chỉ tiêu</th><th>Phạm vi</th><th>Mục tiêu</th><th>Ngưỡng cảnh báo</th>
                <th>Giá trị tháng</th><th>Trạng thái</th><th>Thao tác</th>
            </tr></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalNhapChiTieuThang" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <form id="formNhapChiTieuThang">
            <div class="modal-header">
                <h4 class="modal-title">Nhập chỉ tiêu tháng</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ct_ma_chi_so">
                <div class="form-group"><label>Chỉ tiêu</label><input class="form-control" id="ct_ten_chi_so" readonly tabindex="-1"></div>
                <div class="row">
                    <div class="col-sm-6 form-group"><label id="ct_label_tu_so">Tử số</label><input type="number" min="0" step="any" class="form-control" id="ct_tu_so" required></div>
                    <div class="col-sm-6 form-group"><label id="ct_label_mau_so">Mẫu số</label><input type="number" min="0.0000000001" step="any" class="form-control" id="ct_mau_so" required></div>
                </div>
                <div class="form-group"><label>Giá trị (%)</label><input class="form-control" id="ct_value" readonly tabindex="-1"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnLuuChiTieuThang"><i class="fa fa-save"></i> Lưu</button>
            </div>
        </form>
    </div></div>
</div>
