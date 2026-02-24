<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Siswa Kelas Wali Kelas</h3>
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
					</table>
					<br>
					<table class="table table-striped table-bordered">
						<tr>
							<th class="text-center" width="60">No</th>
							<th class="text-center" width="120">NIM</th>
							<th>Nama</th>
							<th class="text-center" width="160">Aksi</th>
						</tr>
						<?php
							$no = 1;
							foreach ($siswa as $row) {
								echo "<tr>
										<td class='text-center'>".$no."</td>
										<td class='text-center'>".$row->nim."</td>
										<td>".$row->nama."</td>
										<td class='text-center'>".anchor('laporan_nilai/nilai_semester/'.$row->nim, 'Cetak Raport', 'class="btn btn-xs btn-danger"')."</td>
									  </tr>";
								$no++;
							}
						?>
					</table>
				</div>
				<div class="box-footer">
					<?php echo anchor('portal_walikelas', '<i class="fa fa-arrow-left"></i> Kembali', 'class="btn btn-default"'); ?>
				</div>
			</div>
		</div>
	</div>
</section>
