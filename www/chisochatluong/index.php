<?
$listNQ = $request->getAttribute("listNQ");
$listKCCT = $request->getAttribute("listKCCT");
$listPV = $request->getAttribute("listPV");
$listCky = $request->getAttribute("listCKy");
$listDvt = $request->getAttribute("listDvt");
$listTinhTrang = $request->getAttribute("listTT");
$listKhoi = $request->getAttribute("listKhoi");
$listKhoaPhong = $request->getAttribute("listKhoaPhong");
$currentKhoaPhongId = (int) $request->getAttribute("currentKhoaPhongId");



?>


<div class="x_panel">
	<div class="x_title table-title">
		<h2>Danh sách các chỉ tiêu</h2>
		<div class="clearfix"></div>
	</div>
	<!-- Form tim kiem -->
	<!-- <form class="form-horizontal form-label-left">
		<div class="x_content">
			<div class="panel panel-default tablemodify" style="padding:0 10px 10px 10px;margin-bottom: 0;">
				<div class="x_title" style="margin-left: -10px;">
						<h4 style="margin-bottom: 0px;">Tìm kiếm người dùng</h4>
						<div class="clearfix"></div>
				</div>
				<div class="row">
					<div class="form-group" style="margin-left: 10px;">
						<label class="control-label" for="fullName">Nhập họ và tên</label>
						<input type="text" class="form-control" name="fullName" id="fullName">
					</div>	
					<div class="form-group"  style="margin-left: 10px;">	
						<label class="control-label">Nhập tên đăng nhập</label>
						<input type="text" class="form-control" name="userName" id="userName">
					</div>	
							
				</div>
			</div>
		</div>
	</form> -->
	<!-- Ket thuc Form tim kiem -->
	<div class="x_content">
		<?php
			// print_r($listKCCT); 	

			// foreach($listKCCT as $item ){
			// 	echo $item->ten;
			// }
		?>
		<table id="datatable-chiso" class="table table-striped table-bordered dt-responsive " cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>STT</th>
					<th data-type="String" validate-type="true">Tên chỉ số<span style="color:red"></span></th>
					<th data-type="String" validate-type="true">Phạm vi<span style="color:red"></span></th>
					<th data-type="Number" validate-type="true">Chu kỳ<span style="color:red"></span></th>
					<th data-type="Number" validate-type="true">Người gửi<span style="color:red"></span></th>
					<th data-type="Number" validate-type="true">Người duyệt<span style="color:red"></span></th>
					<th data-type="String" validate-type="true">Tình trạng<span style="color:red"></span></th>
					<th>Thao tác</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<!-- popup -->
<div class="modal fade" id="modalChiTieu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <form id="formChiTieu">
                <div class="modal-header">
                    <button type="button"
                            class="close"
                            data-dismiss="modal">
                        <span>&times;</span>
                    </button>

                    <h4 class="modal-title">
                        Thêm Chỉ tiêu
                    </h4>
                </div>

                <div class="modal-body">

                    <!-- dùng để phân biệt thêm / sửa -->
                    <input type="hidden" id="action" name="action" value="add">
                    <input type="hidden" id="fullname" name="fullname" value="<?= $_SESSION["FullName"]? $_SESSION["FullName"] : '' ?>">
                    <div class="row">

						<div class="form-group">
							<input type="text"
									class="form-control"
									id="ma_chi_so"
									name="ma_chi_so"
									hidden>
						</div>

                        <!-- Tên chỉ số -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Tên chỉ số <span class="text-danger">*</span></label>

                                <input type="text"
                                       class="form-control"
                                       id="ten_chi_so"
                                       name="ten_chi_so"
                                       required>
                            </div>
                        </div>

                    </div>

                    <div class="row">
						<!-- Khía cạnh -->
						<div class="col-md-6">
							<div class="form-group">
								<label>
									Khía cạnh <span class="text-danger">*</span>
								</label>
								<select class="form-control"
										name="ma_khia_canh"
										id="ma_khia_canh"
										>
										<?php foreach($listKCCT as $item): ?>
											<?php if($item->loai == 'khia_canh'): ?>
										<option value="<?= $item->id ?>"><?php echo $item->ten ?></option>
											<?php endif ?>
										<?endforeach ?>
								</select>
							</div>
						</div>

						<!-- Thành tố -->
						<div class="col-md-6">
							<div class="form-group">
								<label>
									Thành tố <span class="text-danger">*</span>
								</label>

								<select class="form-control"
										id="ma_thanh_to"
										name="ma_thanh_to"
										>
										<?php foreach($listKCCT as $item): ?>
											<?php if($item->loai == 'thanh_to'): ?>
										<option id="<?= $item->id ?>" value="<?= $item->id ?>"><?php echo $item->ten ?></option>
											<?php endif ?>
										<?endforeach ?>
								</select>
							</div>
						</div>

					</div>


                    <div class="row">

                        <!-- Nhóm chỉ số -->
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label>Nhóm chỉ số</label>

                                <input type="text"
                                       class="form-control"
                                       id="nhom_chi_so"
                                       name="nhom_chi_so">
                            </div>
                        </div> -->

                        <!-- Phạm vi -->
						<div class="col-md-6">
							<div class="form-group">
								<label>Phạm vi</label>

								<select class="form-control"
										id="pham_vi"
										name="pham_vi">

									<?php foreach ($listPV as $item): ?>
										<option value="<?= $item->id ?>">
											<?= $item->ten ?>
										</option>
									<?php endforeach; ?>

								</select>

								<select class="form-control"
										id="tinhtrang"
										name="tinhtrang"
										hidden>

									<?php foreach ($listTinhTrang as $item): ?>
										<option value="<?= $item->maTrangThai ?>" url="<?= $item->tag ?>">
											<?= $item->tenTrangThai ?>
										</option>
									<?php endforeach; ?>

								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<select id="id_khoaphong"
										name="id_khoaphong"
										multiple
										hidden
										aria-hidden="true"
										style="display: none;">
									<option value="">-- Chọn khoa/phòng --</option>
									<?php foreach ($listKhoaPhong as $khoaPhong): ?>
										<option value="<?= (int) $khoaPhong['id'] ?>"
											<?= (int) $khoaPhong['id'] === $currentKhoaPhongId ? 'selected' : '' ?>>
											<?= htmlspecialchars($khoaPhong['ten'], ENT_QUOTES, 'UTF-8') ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="form-group">
								<button type="button"
										class="btn btn-info"
										id="btnMoPopupCon">
									Chọn Khoa/Phòng
								</button>
							</div>
						</div>

                    </div>


                    <div class="row">

                        <!-- Mục tiêu -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mục tiêu</label>

                                <input type="text"
                                       class="form-control"
                                       id="muc_tieu"
                                       name="muc_tieu"
									   placeholder="VD >= 90">
                            </div>
                        </div>

                        <!-- Ngưỡng cảnh báo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Ngưỡng cảnh báo</label>

                                <input type="text"
                                       class="form-control"
                                       id="nguong_canh_bao"
                                       name="nguong_canh_bao"
									   placeholder="VD <=85%">
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <!-- Đơn vị tính listDvt -->
						<div class="col-md-6">
							<div class="form-group">
								<label>Đơn vị tính</label>

								<select class="form-control"
										id="don_vi_tinh"
										name="don_vi_tinh">

									<?php foreach ($listDvt as $item): ?>
										<option value="<?= $item->id ?>">
											<?= $item->ten ?>
										</option>
									<?php endforeach; ?>

								</select>
							</div>
						</div>
                        <!-- Chu kỳ -->
						<div class="col-md-6">
                            <label>Chu Kỳ</label>
							<select class="form-control"
										id="id_chuky"
										name="id_chuky">

								<?php foreach ($listCky as $item): ?>
									<option value="<?= $item->id ?>">
										<?= $item->ten ?>
									</option>
								<?php endforeach; ?>
							</select>
                        </div>
                    </div>

                    <!-- Định nghĩa -->
                    <div class="form-group">
                        <label>Định nghĩa</label>

                        <textarea class="form-control"
                                  id="dinh_nghia"
                                  name="dinh_nghia"
                                  rows="3"></textarea>
                    </div>


                    <!-- Thu thập -->
                    <div class="form-group">
                        <label>Phương pháp thu thập</label>

                        <textarea class="form-control"
                                  id="thu_thap"
                                  name="thu_thap"
                                  rows="3"></textarea>
                    </div>


                    <div class="row">

                        <!-- Tử số -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tên tử số</label>

                                <input type="text"
                                       class="form-control"
                                       id="ten_tu_so"
                                       name="ten_tu_so">
                            </div>
                        </div>

                        <!-- Mẫu số -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tên mẫu số</label>

                                <input type="text"
                                       class="form-control"
                                       id="ten_mau_so"
                                       name="ten_mau_so">
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        Hủy
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            id="btnLuuChiTieu">

                        <i class="fa fa-save"></i>
                        Lưu

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<!-- Popup xem chi tiết -->
<div class="modal fade" id="modalXemChiTieu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"></i> Chi tiết chỉ tiêu</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped" style="margin-bottom: 0;">
                    <tbody>
                        <tr>
                            <th style="width: 20%;">Mã chỉ số</th>
                            <td id="xem_ma_chi_so"></td>
                            <th style="width: 20%;">Trạng thái</th>
                            <td id="xem_trang_thai"></td>
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
                            <td id="xem_khoa_phong" colspan="3" style="white-space: pre-wrap;"></td>
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
                            <th>Khoa</th>
                            <td id="xem_khoa"></td>
                        </tr>
                        <tr>
                            <th>Người duyệt</th>
                            <td id="xem_nguoi_duyet" colspan="3"></td>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- popup con -->
 <div class="modal fade" id="modalCon" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <h4 class="modal-title">Chọn Khoa/Phòng</h4>
            </div>

            <div class="modal-body">
                <table class="table table-hover table-condensed" id="tableKhoaPhong">
                    <tbody>
                        <?php foreach ($listKhoi as $khoi): ?>
                            <tr>
                                <td style="padding-left: 12px !important; font-weight: bold;">
                                    <label style="display: block; margin: 0; cursor: pointer;">
                                        <input type="checkbox"
                                               class="check-khoi"
                                               data-khoi="<?= (int) $khoi['id'] ?>">
                                        <?= htmlspecialchars($khoi['ten'], ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                </td>
                            </tr>

                            <?php foreach ($listKhoaPhong as $khoaPhong): ?>
                                <?php if ((int) $khoaPhong['id_khoi'] === (int) $khoi['id']): ?>
                                    <tr>
                                        <td style="padding-left: 36px !important;">
                                            <label style="display: block; margin: 0; font-weight: normal; cursor: pointer;">
                                                <input type="checkbox"
                                                       class="check-khoa-phong"
                                                       data-khoi="<?= (int) $khoi['id'] ?>"
                                                       value="<?= (int) $khoaPhong['id'] ?>">
                                                <?= htmlspecialchars($khoaPhong['ten'], ENT_QUOTES, 'UTF-8') ?>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Quay lại
                </button>

                <button type="button"
                        class="btn btn-primary"
                        id="btnChonPopupCon">
                    Chọn
                </button>
            </div>

        </div>
    </div>
</div>
