<?
$listKCCT = $request->getAttribute("listKCCT");
$listPV = $request->getAttribute("listPV");
$listCky = $request->getAttribute("listCKy");
$listDvt = $request->getAttribute("listDvt");
$listTinhTrang = $request->getAttribute("listTT");
$listKhoi = $request->getAttribute("listKhoi");
$listKhoaPhong = $request->getAttribute("listKhoaPhong");
?>

<!-- Danh mục ẩn dùng để đổi mã sang tên khi hiển thị bảng và popup chi tiết -->
<div hidden aria-hidden="true">
	<select id="ma_khia_canh">
		<?php foreach ($listKCCT as $item): ?>
			<?php if ($item->loai == 'khia_canh'): ?>
				<option value="<?= $item->id ?>"><?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
	<select id="ma_thanh_to">
		<?php foreach ($listKCCT as $item): ?>
			<?php if ($item->loai == 'thanh_to'): ?>
				<option value="<?= $item->id ?>"><?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
	<select id="pham_vi">
		<?php foreach ($listPV as $item): ?>
			<option value="<?= $item->id ?>"><?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?></option>
		<?php endforeach; ?>
	</select>
	<select id="id_chuky">
		<?php foreach ($listCky as $item): ?>
			<option value="<?= $item->id ?>" data-so-ky="<?= (int) $item->chuky ?>"><?= htmlspecialchars($item->ten, ENT_QUOTES, 'UTF-8') ?></option>
		<?php endforeach; ?>
	</select>
</div>

<div class="x_panel">
	<div class="x_title table-title">
		<h2>Danh sách chỉ số đã được phê duyệt theo khoa phòng</h2>
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

		<table id="datatable-chisokhoa" class="table table-striped table-bordered" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Mã chỉ số</th>
					<th>Tên chỉ số</th>
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
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
				<h4 class="modal-title">Xem Chi tieu</h4>
			</div>

			<div class="modal-body">
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li class="nav-item">
						<a class="nav-link active"
						id="thongtin-tab"
						data-toggle="tab"
						href="#thongtin"
						role="tab">
							Thông tin
						</a>
					</li>

					<li class="nav-item">
						<a class="nav-link"
						id="bieudo-tab"
						data-toggle="tab"
						href="#bieudo"
						role="tab">
							Biểu đồ
						</a>
					</li>

					<li class="nav-item">
						<a class="nav-link"
						id="thongke-tab"
						data-toggle="tab"
						href="#thongke"
						role="tab">
							Thống kê
						</a>
					</li>
				</ul>

				<div class="tab-content mt-3" id="myTabContent">
					<div class="tab-pane fade show active" id="thongtin" role="tabpanel">
						<!-- table thong tin -->
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

					<div class="tab-pane fade" id="bieudo" role="tabpanel">
						<div class="indicator-cycle-chart">
							<h4 id="tieu_de_bieu_do_chu_ky">Trung bình toàn bộ phiếu theo chu kỳ</h4>
							<div id="bieu_do_chu_ky_loading" class="text-muted">Đang tải dữ liệu...</div>
							<div id="bieu_do_chu_ky_empty" class="alert alert-info" style="display: none;">Chưa có dữ liệu nhập liệu để hiển thị.</div>
							<div class="indicator-cycle-chart__canvas">
								<canvas id="bieu_do_chu_ky"></canvas>
							</div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
						</div>
					</div>

					<div class="tab-pane fade"
						id="thongke"
						role="tabpanel">
						<div class="table-responsive">
							<table id="datatable-ct-chiso"
								class="table table-striped table-bordered"
								width="100%">
								<thead>
									<tr>
										<th>Khoa/Phòng</th>
										<th>Năm</th>
										<th>Kỳ</th>
										<th>Tổng điểm</th>
										<th>Điểm tối đa</th>
										<th>Tỷ lệ</th>
										<th>Người nhập</th>
										<th>Cập nhật lúc</th>
										<th>Thao tác</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
			</div>
		</div>
	</div>
</div>
