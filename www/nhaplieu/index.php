<?php
$listKCCT = $request->getAttribute('listKCCT');
$listPV = $request->getAttribute('listPV');
$listCky = $request->getAttribute('listCKy');
$listKhoi = $request->getAttribute('listKhoi');
$listKhoaPhong = $request->getAttribute('listKhoaPhong');
?>

<!-- Danh mục ẩn dùng để đổi mã sang tên trên DataTable và modal. -->
<div hidden aria-hidden="true">
    <select id="dm_khia_canh">
        <?php foreach ($listKCCT as $item): ?>
            <?php if ($item->loai === 'khia_canh'): ?>
                <option value="<?= (int) $item->id ?>">
                    <?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <select id="dm_thanh_to">
        <?php foreach ($listKCCT as $item): ?>
            <?php if ($item->loai === 'thanh_to'): ?>
                <option value="<?= (int) $item->id ?>">
                    <?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>

    <select id="dm_pham_vi">
        <?php foreach ($listPV as $item): ?>
            <option value="<?= (int) $item->id ?>">
                <?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select id="dm_chu_ky">
        <?php foreach ($listCky as $item): ?>
            <option
                value="<?= (int) $item->id ?>"
                data-so-ky="<?= (int) $item->chuky ?>">
                <?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select id="dm_khoi">
        <?php foreach ($listKhoi as $item): ?>
            <option value="<?= (int) $item['id'] ?>">
                <?= htmlspecialchars($item['ten'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select id="dm_khoa_phong">
        <?php foreach ($listKhoaPhong as $item): ?>
            <option
                value="<?= (int) $item['id'] ?>"
                data-khoi="<?= (int) $item['id_khoi'] ?>">
                <?= htmlspecialchars($item['ten'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<div class="x_panel">
    <div class="x_title table-title">
        <h2>Danh sách chỉ tiêu đã duyệt</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        <div class="row" style="margin-bottom: 15px;">
            <div class="col-sm-4">
                <div class="alert alert-info" style="margin-bottom: 0;">
                    <strong>Tổng chỉ tiêu:</strong>
                    <span id="tk_tong">0</span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="alert alert-success" style="margin-bottom: 0;">
                    <strong>Đã nhập kỳ hiện tại:</strong>
                    <span id="tk_da_nhap">0</span>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="alert alert-warning" style="margin-bottom: 0;">
                    <strong>Chưa nhập kỳ hiện tại:</strong>
                    <span id="tk_chua_nhap">0</span>
                </div>
            </div>
        </div>

        <table
            id="datatable-nhaplieu"
            class="table table-striped table-bordered dt-responsive"
            width="100%">
            <thead>
                <tr>
                    <th>Mã chỉ số</th>
                    <th>Tên chỉ số</th>
                    <th>Phạm vi</th>
                    <th>Chu kỳ</th>
                    <th>Nhập liệu</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal xem chi tiết chỉ tiêu. -->
<div class="modal fade" id="modalXemChiTieu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Chi tiết chỉ tiêu</h4>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                    <tbody>
                        <tr>
                            <th style="width: 20%;">Mã chỉ số</th>
                            <td id="xem_ma_chi_so"></td>
                            <th>Người duyệt</th>
                            <td id="xem_nguoi_duyet"></td>
                        </tr>
                        <tr>
                            <th>Tên chỉ số</th>
                            <td id="xem_ten_chi_so" colspan="3"></td>
                        </tr>
                        <tr>
                            <th>Khía cạnh</th>
                            <td id="xem_khia_canh"></td>
                            <th>Thành tố</th>
                            <td id="xem_thanh_to"></td>
                        </tr>
                        <tr>
                            <th>Phạm vi</th>
                            <td id="xem_pham_vi"></td>
                            <th>Chu kỳ</th>
                            <td id="xem_chu_ky"></td>
                        </tr>
                        <tr>
                            <th>Khoa/Phòng áp dụng</th>
                            <td id="xem_khoa_phong" colspan="3"></td>
                        </tr>
                        <tr>
                            <th>Mục tiêu</th>
                            <td id="xem_muc_tieu"></td>
                            <th>Ngưỡng cảnh báo</th>
                            <td id="xem_nguong_canh_bao"></td>
                        </tr>
                        <tr>
                            <th>Người gửi</th>
                            <td id="xem_nguoi_gui"></td>
                            <th>Khoa/Phòng</th>
                            <td id="khoa_user"></td>
                        </tr>
                        <tr>
                            <th>Định nghĩa</th>
                            <td id="xem_dinh_nghia" colspan="3" style="white-space: pre-wrap;"></td>
                        </tr>
                        <tr>
                            <th>Phương pháp thu thập</th>
                            <td id="xem_thu_thap" colspan="3" style="white-space: pre-wrap;"></td>
                        </tr>
                        <tr>
                            <th>Tên tử số</th>
                            <td id="xem_tu_so"></td>
                            <th>Tên mẫu số</th>
                            <td id="xem_mau_so"></td>
                        </tr>
                    </tbody>
                </table>
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed" id="bang_du_lieu_chu_ky" >
                        <thead>
                            <tr id="xem_tieu_de_chu_ky"></tr>
                        </thead>
                        <tbody>
                            <tr id="xem_du_lieu_chu_ky"></tr>
                        </tbody>
                    </table>
                </div>
                <div id="xem_loi_du_lieu_chu_ky" class="alert alert-danger" style="display: none;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal nhập liệu chỉ tiêu. -->
<div class="modal fade" id="modalNhapLieu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="formNhapLieu">
                <div class="modal-header">
                    <h4 class="modal-title">Nhập liệu chỉ tiêu</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="nhap_ma_chi_so">
                    <input type="hidden" id="nhap_so_chu_ky">
                    <input type="hidden" id="nhap_nam" value="<?= (int) date('Y') ?>">

                    <table class="table table-bordered table-condensed">
                        <tbody>
                            <tr>
                                <th style="width: 25%;">Mã chỉ số</th>
                                <td id="nhap_hien_thi_ma_chi_so"></td>
                            </tr>
                            <tr>
                                <th>Tên chỉ số</th>
                                <td id="nhap_ten_chi_so"></td>
                            </tr>
                            <tr>
                                <th>Chu kỳ</th>
                                <td id="nhap_chu_ky"></td>
                            </tr>
                            <tr>
                                <th>Mục tiêu</th>
                                <td id="nhap_muc_tieu"></td>
                            </tr>
                            <tr>
                                <th>Ngưỡng cảnh báo</th>
                                <td id="nhap_nguong_canh_bao"></td>
                            </tr>
                        </tbody>
                    </table>

                    <p id="nhap_ky_hien_tai" class="text-muted"></p>

                    <div class="form-group">
                        <label for="nhap_ky">Chọn chu kỳ nhập</label>
                        <select id="nhap_ky" class="form-control" required></select>
                    </div>

                    <div id="tong_quan_chu_ky" class="alert alert-info"></div>
                    <div id="trang_thai_ky" class="alert" style="display: none;"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nhap_tu_so">Tử số</label>
                                <input
                                    type="number"
                                    id="nhap_tu_so"
                                    class="form-control"
                                    min="0"
                                    step="any"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nhap_mau_so">Mẫu số</label>
                                <input
                                    type="number"
                                    id="nhap_mau_so"
                                    class="form-control"
                                    min="0"
                                    step="any"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nhap_value">Kết quả (%)</label>
                        <input type="text" id="nhap_value" class="form-control" readonly>
                        <p class="help-block">Công thức: mẫu số / tử số × 100</p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" id="btnLuuNhapLieu" class="btn btn-primary">
                        <i class="fa fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
