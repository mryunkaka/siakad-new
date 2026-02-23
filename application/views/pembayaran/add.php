<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Tambah Pembayaran</h3>
				</div>

				<div class="box-body">
					<?php if($this->session->flashdata('error')): ?>
						<div class="alert alert-danger">
							<i class="fa fa-exclamation-triangle"></i>
							<?php echo $this->session->flashdata('error'); ?>
						</div>
					<?php endif; ?>

					<form method="post" enctype="multipart/form-data" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal</label>
							<div class="col-sm-4">
								<input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
							</div>
							<div class="col-sm-6" style="padding-top:7px;opacity:.85;">
								Periode aktif: <?php echo get_tahun_akademik('tahun_akademik').' ('.get_tahun_akademik('semester').')'; ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Siswa (NIM)</label>
							<div class="col-sm-4">
								<input list="siswaList" name="nim" class="form-control" placeholder="Ketik NIM..." required>
								<datalist id="siswaList">
									<?php foreach ($siswa as $s): ?>
										<option value="<?php echo $s->nim; ?>"><?php echo $s->nim.' - '.$s->nama; ?></option>
									<?php endforeach; ?>
								</datalist>
								<small style="opacity:.8;">Format: NIM - Nama (pilih dari daftar)</small>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Jenis</label>
							<div class="col-sm-4">
								<select name="jenis" class="form-control" required>
									<?php foreach ($jenis_options as $j): ?>
										<option value="<?php echo $j; ?>"><?php echo $j; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Nominal</label>
							<div class="col-sm-4">
								<input type="number" name="nominal" class="form-control" placeholder="contoh: 50000" min="0" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Metode</label>
							<div class="col-sm-4">
								<select name="metode" class="form-control" required>
									<?php foreach ($metode_options as $m): ?>
										<option value="<?php echo $m; ?>"><?php echo $m; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Bukti (opsional)</label>
							<div class="col-sm-4">
								<input type="file" name="bukti" class="form-control">
								<small style="opacity:.8;">jpg/png/webp/pdf (maks 4MB)</small>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Keterangan</label>
							<div class="col-sm-6">
								<textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan transaksi (opsional)"></textarea>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-4">
								<button type="submit" name="submit" class="btn btn-primary btn-flat">Simpan</button>
								<?php echo anchor('pembayaran', 'Kembali', array('class'=>'btn btn-danger btn-flat')); ?>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

