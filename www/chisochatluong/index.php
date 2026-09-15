<?
$listNQ = $request->getAttribute("listNQ");
$listKCCT = $request->getAttribute("listKCCT");
$listPV = $request->getAttribute("listPV");
$listCky = $request->getAttribute("listCKy");
$listDvt = $request->getAttribute("listDvt");
$listTinhTrang = $request->getAttribute("listTT");
$canChoosePhamVi = (bool) $request->getAttribute("canChoosePhamVi");
$canApprove = (bool) $request->getAttribute("canApprove");



?>


<div class="x_panel">
	<style>
		#datatable-chiso th:last-child,
		#datatable-chiso td.dt-chiso-actions {
			white-space: nowrap;
			min-width: 190px;
		}
		#datatable-chiso td.dt-chiso-actions .btn {
			margin: 1px 2px 1px 0;
		}
		#datatable-chiso th:nth-child(2),
		#datatable-chiso td.dt-chiso-name {
			width: 12%;
			max-width: 180px;
			white-space: normal;
			word-break: break-word;
		}
	</style>
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
		<table id="datatable-chiso"
			data-can-approve="<?= $canApprove ? '1' : '0' ?>"
			class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>STT</th>
					<th data-type="String" validate-type="true">Tên chỉ số<span style="color:red"></span></th>
					<th data-type="Number" validate-type="true">Mã khía cạnh<span style="color:red"></span></th>
					<th data-type="Number" validate-type="true">Mã thành tố<span style="color:red"></span></th>
					<th data-type="String" validate-type="true">Phạm vi<span style="color:red"></span></th>
					<th data-type="String" validate-type="true">Đơn vị tính<span style="color:red"></span></th>
					<th data-type="Number" validate-type="true">Chu kỳ<span style="color:red"></span></th>
					<th data-type="Number" validate-type="true">Khoa/Phòng<span style="color:red"></span></th>
					<th data-type="String" validate-type="true">Tình trạng<span style="color:red"></span></th>
					<th>Thao tác</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>

<!-- Popup nhập dữ liệu chỉ số theo chu kỳ -->
<style>
    #modalNhapDuLieu .nhap-value-xanh { background:#dff0d8; color:#3c763d; border-color:#3c763d; font-weight:600; }
    #modalNhapDuLieu .nhap-value-do { background:#f2dede; color:#a94442; border-color:#a94442; font-weight:600; }
    #modalNhapDuLieu .nhap-value-vang { background:#fcf8e3; color:#8a6d3b; border-color:#8a6d3b; font-weight:600; }
</style>
<!-- popup nhập liệu -->
<div class="modal fade" id="modalNhapDuLieu" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <form id="formNhapDuLieu">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title">Nhập dữ liệu chỉ số</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="nhap_ma_chi_so"><input type="hidden" id="nhap_id_chuky">
                <div class="row">
                    <div class="col-md-8 form-group">
                        <label>Chỉ số: </label>
                        <input type="text" class="form-control" id="nhap_ten_chi_so" readonly>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Năm</label>
                        <input type="number" class="form-control" id="nhap_nam" min="2000" max="2100" required readonly>
                    </div>
                </div>
                <div id="xem_thong_tin_chi_so" class="well well-sm" style="margin-bottom:15px">
                    <div class="row">
                        <div class="col-md-6"><strong>Khía cạnh:</strong> <span id="xem_khia_canh"></span></div>
                        <div class="col-md-6"><strong>Thành tố:</strong> <span id="xem_thanh_to"></span></div>
                        <div class="col-md-6"><strong>Phạm vi:</strong> <span id="xem_pham_vi"></span></div>
                        <div class="col-md-6"><strong>Đơn vị tính:</strong> <span id="xem_don_vi_tinh"></span></div>
                        <div class="col-md-6"><strong>Mục tiêu:</strong> <span id="xem_muc_tieu"></span></div>
                        <div class="col-md-6"><strong>Ngưỡng cảnh báo:</strong> <span id="xem_nguong_canh_bao"></span></div>
                        <div class="col-md-12"><strong>Định nghĩa:</strong> <span id="xem_dinh_nghia"></span></div>
                        <div class="col-md-12"><strong>Phương pháp thu thập:</strong> <span id="xem_thu_thap"></span></div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                    <thead><tr><th style="width:18%">Kỳ</th><th id="nhap_label_tuso">Tử số</th><th id="nhap_label_mauso">Mẫu số</th><th style="width:18%">Giá trị (%)</th></tr></thead>
                    <tbody id="nhap_dulieu_body"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary" id="btnLuuNhapDuLieu"><i class="fa fa-save"></i> Lưu dữ liệu</button>
            </div>
        </form>
    </div></div>
</div>

<!-- popup thêm chỉ tiêu -->
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
                                       autofocus
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
										name="pham_vi"
										data-can-choose="<?= $canChoosePhamVi ? '1' : '0' ?>"
										<?= $canChoosePhamVi ? '' : 'disabled' ?>>

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
