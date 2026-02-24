<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Import Data Siswa</h3>
				</div>
				<div class="box-body">
					<button type="button" class="btn btn-warning btn-flat" data-toggle="modal" data-target="#modalImportSiswa">
						<i class="fa fa-upload"></i> Import Data (Excel)
					</button>
					<?php $this->load->view('siswa/_import_excel_modal'); ?>
					<script>
						$(function () {
							$('#modalImportSiswa').modal('show');
						});
					</script>
				</div>
			</div>
		</div>
	</div>
</section>

