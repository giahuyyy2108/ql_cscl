<?
$listKhoi = $request->getAttribute("listKhoi");
?>
<div class="x_panel">
	<div class="x_title table-title">
		<h2>Danh sach khoa/phong</h2>
		<div class="clearfix"></div>
	</div>
	<div class="x_content">
		<div style="display:none">
			<select id="khoi" class="select form-control">
				<?
			if($listKhoi!=false){
				foreach($listKhoi as $khoi){
?>
				<option value="<?=$khoi->get("MaKhoi")?>"><?=$khoi->get("TenKhoi")?></option>
				<?
				}
			}
?>
			</select>
		</div>
		<table id="datatable-khoaphong" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>STT</th>
					<th data-type="String" validate-type="true">Ten khoa/phong <span style="color:red">*</span></th>
					<th data-type="Select" objectid="khoi" validate-type="true">Khoi <span style="color:red">*</span></th>
					<th>Thao tac</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>
