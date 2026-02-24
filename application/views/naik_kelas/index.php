<section class="content">
	<div class="row">

		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Naik Kelas</h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Jurusan</label>
								<?php echo cmb_dinamis('jurusan', 'tbl_jurusan', 'nama_jurusan', 'kd_jurusan', null, "id='filter_jurusan' onchange='loadKelasAsal()'"); ?>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Kelas Asal</label>
								<div id="kelas_asal"></div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Kelas Tujuan (tingkatan berikutnya)</label>
								<div id="kelas_tujuan"></div>
							</div>
						</div>
					</div>

					<form method="post" action="<?php echo base_url('naik_kelas/proses'); ?>" onsubmit="return confirmSubmit();">
						<input type="hidden" name="kelas_asal" id="kelas_asal_val" value="">
						<input type="hidden" name="kelas_tujuan" id="kelas_tujuan_val" value="">
						<button type="submit" class="btn btn-info">
							<i class="fa fa-arrow-up"></i> Proses Naik Kelas
						</button>
					</form>
				</div>
			</div>
		</div>

		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Preview Siswa (Kelas Asal)</h3>
				</div>
				<div class="box-body">
					<div id="previewSiswa"></div>
				</div>
			</div>
		</div>

	</div>
</section>

<script>
	function loadKelasAsal() {
		var jurusan = $("#filter_jurusan").val();
		$("#kelas_tujuan").html("");
		$("#previewSiswa").html("");
		$.ajax({
			type: 'GET',
			url: '<?php echo base_url('naik_kelas/combobox_kelas_asal'); ?>',
			data: { kd_jurusan: jurusan },
			success: function (html) {
				$("#kelas_asal").html(html);
				var preselectAsal = <?php echo json_encode($asal ?? ''); ?>;
				if (preselectAsal) {
					$("#cbkelas_asal").val(preselectAsal);
				}
				loadTujuan();
			}
		});
	}

	function loadTujuan() {
		var kelasAsal = $("#cbkelas_asal").val();
		$("#kelas_asal_val").val(kelasAsal || '');
		$("#kelas_tujuan_val").val('');
		$("#previewSiswa").html("");
		$.ajax({
			type: 'GET',
			url: '<?php echo base_url('naik_kelas/combobox_kelas_tujuan'); ?>',
			data: { kd_kelas_asal: kelasAsal },
			success: function (html) {
				$("#kelas_tujuan").html(html);
				loadPreviewSiswa();
			}
		});
	}

	function loadPreviewSiswa() {
		var kelasAsal = $("#cbkelas_asal").val();
		var kelasTujuan = $("#cbkelas_tujuan").val();
		$("#kelas_asal_val").val(kelasAsal || '');
		$("#kelas_tujuan_val").val(kelasTujuan || '');
		if (!kelasAsal) {
			$("#previewSiswa").html("");
			return;
		}
		$.ajax({
			type: 'GET',
			url: '<?php echo base_url('naik_kelas/load_siswa'); ?>',
			data: { kd_kelas: kelasAsal },
			success: function (html) {
				$("#previewSiswa").html(html);
			}
		});
	}

	function confirmSubmit() {
		var asal = $("#kelas_asal_val").val();
		var tujuan = $("#kelas_tujuan_val").val();
		if (!asal || !tujuan) {
			alert('Kelas asal dan tujuan wajib dipilih.');
			return false;
		}
		return confirm("Yakin naikkan semua siswa dari " + asal + " ke " + tujuan + "?");
	}

	$(document).ready(function () {
		loadKelasAsal();
	});
</script>

