<?php
	$autoOpen = false;
	if (isset($_GET['import']) && $_GET['import'] == '1') {
		$autoOpen = true;
	}
?>

<div class="modal fade" id="modalImportSiswa" tabindex="-1" role="dialog" aria-labelledby="modalImportSiswaLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalImportSiswaLabel">Import Siswa (Excel)</h4>
			</div>
			<div class="modal-body">
				<p>Unduh contoh format lalu upload file Excel (.xlsx/.xls) sesuai kolom yang tersedia.</p>
				<p>
					<a class="btn btn-default btn-sm" href="<?php echo site_url('import_siswa/template'); ?>">
						<i class="fa fa-download"></i> Download Contoh Format
					</a>
				</p>

				<hr>

				<form method="post" action="<?php echo site_url('import_siswa/do_import'); ?>" enctype="multipart/form-data">
					<div class="form-group">
						<label for="importFile">File Excel</label>
						<input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls" required>
						<small class="help-block">Kolom wajib: <strong>NIM</strong>, <strong>NAMA</strong>, <strong>KD_KELAS</strong>.</small>
					</div>
					<div class="text-right">
						<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Import</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php if ($autoOpen): ?>
<script>
	$(function () {
		$('#modalImportSiswa').modal('show');
	});
</script>
<?php endif; ?>

