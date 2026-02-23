<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Edit Pembayaran</h3>
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
							<label class="col-sm-2 control-label">No Kwitansi</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" value="<?php echo $row['no_kwitansi']; ?>" readonly>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal</label>
							<div class="col-sm-4">
								<input type="date" name="tanggal" class="form-control" value="<?php echo $row['tanggal']; ?>" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Siswa (NIM)</label>
							<div class="col-sm-4">
								<input list="siswaList" name="nim" class="form-control" value="<?php echo $row['nim']; ?>" required>
								<datalist id="siswaList">
									<?php foreach ($siswa as $s): ?>
										<option value="<?php echo $s->nim; ?>"><?php echo $s->nim.' - '.$s->nama; ?></option>
									<?php endforeach; ?>
								</datalist>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Jenis</label>
							<div class="col-sm-4">
								<select name="jenis" class="form-control" required>
									<?php foreach ($jenis_options as $j): ?>
										<option value="<?php echo $j; ?>" <?php echo ($row['jenis'] === $j) ? 'selected' : ''; ?>><?php echo $j; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Nominal</label>
							<div class="col-sm-4">
								<input type="number" name="nominal" class="form-control" min="0" value="<?php echo (int) $row['nominal']; ?>" required>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Metode</label>
							<div class="col-sm-4">
								<select name="metode" class="form-control" required>
									<?php foreach ($metode_options as $m): ?>
										<option value="<?php echo $m; ?>" <?php echo ($row['metode'] === $m) ? 'selected' : ''; ?>><?php echo $m; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Status</label>
							<div class="col-sm-4">
								<select name="status" class="form-control" required>
									<option value="LUNAS" <?php echo ($row['status'] === 'LUNAS') ? 'selected' : ''; ?>>LUNAS</option>
									<option value="BATAL" <?php echo ($row['status'] === 'BATAL') ? 'selected' : ''; ?>>BATAL</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Bukti</label>
							<div class="col-sm-6">
								<input type="file" name="bukti" class="form-control">
								<?php if(!empty($row['bukti'])): ?>
									<div style="margin-top:6px;">
										<a class="btn btn-xs btn-default" href="<?php echo base_url('uploads/pembayaran/'.$row['bukti']); ?>" target="_blank">
											<i class="fa fa-paperclip"></i> Lihat bukti saat ini
										</a>
									</div>
								<?php endif; ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Keterangan</label>
							<div class="col-sm-6">
								<textarea name="keterangan" class="form-control" rows="3"><?php echo $row['keterangan']; ?></textarea>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-6">
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

