<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Portal Wali Kelas</h3>
				</div>
				<div class="box-body">
					<table class="table table-bordered">
						<tr>
							<td width="200">Tahun Akademik</td>
							<td>: <?php echo get_tahun_akademik('tahun_akademik'); ?></td>
						</tr>
						<tr>
							<td>Semester</td>
							<td>: <?php echo get_tahun_akademik('semester'); ?></td>
						</tr>
						<tr>
							<td>Kelas</td>
							<td>: <?php echo ($kelas['nama_kelas'] ?? '-'); ?></td>
						</tr>
						<tr>
							<td>Jurusan &amp; Tingkatan</td>
							<td>: <?php echo 'Jurusan '.($kelas['nama_jurusan'] ?? '-').' '.($kelas['nama_tingkatan'] ?? '-'); ?></td>
						</tr>
						<tr>
							<td>Jumlah Siswa</td>
							<td>: <?php echo (int) $jumlah_siswa; ?></td>
						</tr>
					</table>
				</div>
				<div class="box-footer">
					<?php echo anchor('wk_siswa', '<i class="fa fa-users"></i> Siswa Kelas', 'class="btn btn-primary"'); ?>
					<?php echo anchor('laporan_nilai', '<i class="fa fa-file-pdf-o"></i> Cetak Raport', 'class="btn btn-danger"'); ?>
				</div>
			</div>
		</div>
	</div>
</section>

