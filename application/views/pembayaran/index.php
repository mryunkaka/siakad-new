<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<?php if($this->session->flashdata('error')): ?>
				<div class="alert alert-danger">
					<i class="fa fa-exclamation-triangle"></i>
					<?php echo $this->session->flashdata('error'); ?>
				</div>
			<?php endif; ?>

			<div class="box box-info">
				<div class="box-header with-border">
					<h3 class="box-title">Pembayaran</h3>
					<div class="box-tools pull-right">
						<?php echo anchor('pembayaran/add', '<i class="fa fa-plus"></i> Tambah Pembayaran', 'class="btn btn-success btn-sm"'); ?>
					</div>
				</div>

				<div class="box-body">
					<div class="row" style="margin-bottom:10px;">
						<div class="col-sm-3">
							<label>Periode</label>
							<div style="font-size:13px;opacity:.85;">
								<?php echo get_tahun_akademik('tahun_akademik').' ('.get_tahun_akademik('semester').')'; ?>
							</div>
						</div>
						<div class="col-sm-3">
							<label>Ringkasan (Lunas)</label>
							<div style="font-size:13px;opacity:.85;">
								<?php echo (int) ($summary['jumlah_transaksi'] ?? 0); ?> transaksi
							</div>
							<div style="font-weight:600;">
								Rp <?php echo number_format((int) ($summary['total_nominal'] ?? 0), 0, ',', '.'); ?>
							</div>
						</div>
						<div class="col-sm-6">
							<form class="form-inline pull-right" onsubmit="return false;">
								<label style="margin-right:6px;">Filter</label>
								<input type="date" id="start_date" class="form-control input-sm" value="<?php echo $start_date ?? date('Y-m-01'); ?>">
								<input type="date" id="end_date" class="form-control input-sm" value="<?php echo $end_date ?? date('Y-m-d'); ?>">
								<select id="status" class="form-control input-sm">
									<option value="">Semua Status</option>
									<option value="LUNAS">LUNAS</option>
									<option value="BATAL">BATAL</option>
								</select>
								<select id="jenis" class="form-control input-sm">
									<option value="">Semua Jenis</option>
									<option value="SPP">SPP</option>
									<option value="Ujian">Ujian</option>
									<option value="Seragam">Seragam</option>
									<option value="Buku">Buku</option>
									<option value="Lainnya">Lainnya</option>
								</select>
								<button id="btnFilter" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i></button>
								<button id="btnReset" class="btn btn-default btn-sm">Reset</button>
							</form>
						</div>
					</div>

					<table id="mytable" class="table table-striped table-bordered table-hover table-full-width" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th width="45" class="text-center">No</th>
								<th width="95">Tanggal</th>
								<th width="150">Kwitansi</th>
								<th>Siswa</th>
								<th width="90">Jenis</th>
								<th width="120">Nominal</th>
								<th width="80">Metode</th>
								<th width="70">Status</th>
								<th width="120" class="text-center">Aksi</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<script src="<?php echo base_url(); ?>assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

<script>
	$(document).ready(function() {
		var t = $('#mytable').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: '<?php echo site_url('pembayaran/data'); ?>',
				data: function(d){
					d.start_date = $('#start_date').val();
					d.end_date = $('#end_date').val();
					d.status = $('#status').val();
					d.jenis = $('#jenis').val();
				}
			},
			order: [[1, 'desc']],
			columns: [
				{ data: null, width: "45px", className: "text-center", orderable: false },
				{ data: "tanggal" },
				{ data: "no_kwitansi" },
				{ data: "siswa" },
				{ data: "jenis" },
				{ data: "nominal" },
				{ data: "metode" },
				{ data: "status", className: "text-center" },
				{ data: "aksi", className: "text-center", orderable: false, searchable: false }
			]
		});

		t.on('order.dt search.dt draw.dt', function () {
			t.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
				cell.innerHTML = i+1;
			});
		});

		$('#btnFilter').on('click', function(){
			t.ajax.reload();
		});
		$('#btnReset').on('click', function(){
			$('#start_date').val('');
			$('#end_date').val('');
			$('#status').val('');
			$('#jenis').val('');
			t.ajax.reload();
		});
	});
</script>
