<?
$listKCCT = $request->getAttribute("listKCCT");
$listPV = $request->getAttribute("listPV");
$listCky = $request->getAttribute("listCKy");
$listDvt = $request->getAttribute("listDvt");
$listTinhTrang = $request->getAttribute("listTT");
$listKhoi = $request->getAttribute("listKhoi");
$listKhoaPhong = $request->getAttribute("listKhoaPhong");
?>

<div class="x_panel">
	<div class="x_title table-title">
		<h2>Danh sach chi so da duyet theo khoa/phong</h2>
		<div class="clearfix"></div>
	</div>

	<div class="x_content">
		<div class="row" style="margin-bottom: 12px;">
			<div class="col-md-4">
				<label>Khoi</label>
				<select class="form-control" id="filter_khoi">
					<option value="">Tất cả khối</option>
					<?php foreach ($listKhoi as $khoi): ?>
						<option value="<?= (int) $khoi['id'] ?>"><?= htmlspecialchars($khoi['ten'], ENT_QUOTES, 'UTF-8') ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-md-4">
				<label>Khoa/Phong</label>
				<select class="form-control" id="filter_khoaphong">
					<option value="">Tất cả Khoa/Phòng</option>
					<?php foreach ($listKhoaPhong as $khoaPhong): ?>
						<option value="<?= (int) $khoaPhong['id'] ?>" data-khoi="<?= (int) $khoaPhong['id_khoi'] ?>">
							<?= htmlspecialchars($khoaPhong['ten'], ENT_QUOTES, 'UTF-8') ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<table id="datatable-chisokhoa" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Mã chỉ số</th>
					<th>Tên chỉ số</th>
					<th>Khía cạnh</th>
					<th>Thành tố</th>
					<th>Phạm vi</th>
					<th>Chu kỳ</th>
					<th>Người gửi</th>
					<th>Người duyệt</th>
					<th>Tình trạng</th>
					<th>Thao tác</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

<!-- popup -->
<div class="modal fade" id="modalChiTieu" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form id="formChiTieu">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span>&times;</span>
					</button>
					<h4 class="modal-title">Xem Chi tieu</h4>
				</div>

				<div class="modal-body">
					<input type="hidden" id="ma_chi_so" name="ma_chi_so">

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Ten chi so</label>
								<input type="text" class="form-control" id="ten_chi_so" name="ten_chi_so">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Khia canh</label>
								<select class="form-control" id="ma_khia_canh" name="ma_khia_canh">
									<?php foreach ($listKCCT as $item): ?>
										<?php if ($item->loai == 'khia_canh'): ?>
											<option value="<?= $item->id ?>"><?= $item->ten ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Thanh to</label>
								<select class="form-control" id="ma_thanh_to" name="ma_thanh_to">
									<?php foreach ($listKCCT as $item): ?>
										<?php if ($item->loai == 'thanh_to'): ?>
											<option value="<?= $item->id ?>"><?= $item->ten ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Pham vi</label>
								<select class="form-control" id="pham_vi" name="pham_vi">
									<?php foreach ($listPV as $item): ?>
										<option value="<?= $item->id ?>"><?= $item->ten ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Khoa/Phong</label>
								<select class="form-control" id="id_khoaphong" name="id_khoaphong">
									<?php foreach ($listKhoaPhong as $khoaPhong): ?>
										<option value="<?= (int) $khoaPhong['id'] ?>">
											<?= htmlspecialchars($khoaPhong['ten'], ENT_QUOTES, 'UTF-8') ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Muc tieu</label>
								<input type="text" class="form-control" id="muc_tieu" name="muc_tieu">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Nguong canh bao</label>
								<input type="text" class="form-control" id="nguong_canh_bao" name="nguong_canh_bao">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Don vi tinh</label>
								<select class="form-control" id="don_vi_tinh" name="don_vi_tinh">
									<?php foreach ($listDvt as $item): ?>
										<option value="<?= $item->id ?>"><?= $item->ten ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Chu ky</label>
								<select class="form-control" id="id_chuky" name="id_chuky">
									<?php foreach ($listCky as $item): ?>
										<option value="<?= $item->id ?>"><?= $item->ten ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label>Dinh nghia</label>
						<textarea class="form-control" id="dinh_nghia" name="dinh_nghia" rows="3"></textarea>
					</div>

					<div class="form-group">
						<label>Phuong phap thu thap</label>
						<textarea class="form-control" id="thu_thap" name="thu_thap" rows="3"></textarea>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Ten tu so</label>
								<input type="text" class="form-control" id="ten_tu_so" name="ten_tu_so">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Ten mau so</label>
								<input type="text" class="form-control" id="ten_mau_so" name="ten_mau_so">
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Dong</button>
					<button type="submit" class="btn btn-primary" id="btnLuuChiTieu" style="display:none">
						<i class="fa fa-save"></i>
						Luu
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
