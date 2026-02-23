<?php

class Pembayaran extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		checkAksesModule();

		$level = (int) $this->session->userdata('id_level_user');
		if ($level !== 1 && $level !== 4)
		{
			show_error('Anda tidak memiliki akses ke menu Pembayaran.', 403);
		}

		$this->load->library('ssp');
		$this->load->model('model_pembayaran');
		$this->model_pembayaran->ensure_schema();
	}

	function index()
	{
		$start = $this->input->get('start_date') ?: date('Y-m-01');
		$end = $this->input->get('end_date') ?: date('Y-m-d');

		$data['start_date'] = $start;
		$data['end_date'] = $end;
		$data['summary'] = $this->model_pembayaran->summary($start, $end);

		$this->template->load('template', 'pembayaran/index', $data);
	}

	function data()
	{
		$table = $this->model_pembayaran->table;
		$primaryKey = 'id_pembayaran';

		$columns = array(
			array('db' => 'tanggal', 'dt' => 'tanggal'),
			array('db' => 'no_kwitansi', 'dt' => 'no_kwitansi'),
			array(
				'db' => 'nim',
				'dt' => 'siswa',
				'formatter' => function($d) {
					$siswa = $this->db->get_where('tbl_siswa', array('nim' => $d))->row_array();
					$nama = $siswa['nama'] ?? '-';
					return $d.'<br><small style="opacity:.85;">'.$nama.'</small>';
				}
			),
			array('db' => 'jenis', 'dt' => 'jenis'),
			array(
				'db' => 'nominal',
				'dt' => 'nominal',
				'formatter' => function($d) {
					return 'Rp '.number_format((int) $d, 0, ',', '.');
				}
			),
			array('db' => 'metode', 'dt' => 'metode'),
			array(
				'db' => 'status',
				'dt' => 'status',
				'formatter' => function($d) {
					$label = ($d === 'LUNAS') ? 'success' : 'default';
					return '<span class="label label-'.$label.'">'.$d.'</span>';
				}
			),
			array(
				'db' => 'id_pembayaran',
				'dt' => 'aksi',
				'formatter' => function($d) {
					return anchor('pembayaran/edit/'.$d, '<i class="fa fa-edit"></i>', 'class="btn btn-xs btn-primary" title="Edit"').' '.
						anchor('pembayaran/kwitansi/'.$d, '<i class="fa fa-print"></i>', 'class="btn btn-xs btn-warning" title="Kwitansi" target="_blank"').' '.
						anchor('pembayaran/delete/'.$d, '<i class="fa fa-times"></i>', 'class="btn btn-xs btn-danger" title="Hapus" onclick="return confirm(\'Hapus transaksi ini?\')"');
				}
			),
		);

		$sql_details = array(
			'user' => $this->db->username,
			'pass' => $this->db->password,
			'db'   => $this->db->database,
			'host' => $this->db->hostname,
		);

		$where = array();
		$start = $this->input->get('start_date');
		$end = $this->input->get('end_date');
		$status = $this->input->get('status');
		$jenis = $this->input->get('jenis');

		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $start))
		{
			$where[] = "tanggal >= ".$this->db->escape($start);
		}
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $end))
		{
			$where[] = "tanggal <= ".$this->db->escape($end);
		}
		if (in_array($status, array('LUNAS', 'BATAL'), TRUE))
		{
			$where[] = "status = ".$this->db->escape($status);
		}
		if (is_string($jenis) && $jenis !== '')
		{
			$where[] = "jenis = ".$this->db->escape($jenis);
		}

		$this->output
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, $where)));
	}

	function add()
	{
		$data['siswa'] = $this->db->select('nim,nama')->order_by('nama')->get('tbl_siswa')->result();
		$data['jenis_options'] = array('SPP', 'Ujian', 'Seragam', 'Buku', 'Lainnya');
		$data['metode_options'] = array('Tunai', 'Transfer');

		if (isset($_POST['submit']))
		{
			$nim = $this->input->post('nim', TRUE);
			$siswa = $this->db->get_where('tbl_siswa', array('nim' => $nim))->row_array();
			if (empty($siswa))
			{
				$this->session->set_flashdata('error', 'NIM tidak ditemukan.');
				redirect('pembayaran/add');
				return;
			}

			$tanggal = $this->input->post('tanggal', TRUE) ?: date('Y-m-d');
			$idTahun = (int) get_tahun_akademik('id_tahun_akademik');
			$semester = (string) get_tahun_akademik('semester');

			$bukti = $this->_upload_bukti('bukti');

			$payload = array(
				'no_kwitansi' => $this->model_pembayaran->generate_no_kwitansi($tanggal),
				'tanggal' => $tanggal,
				'nim' => $nim,
				'id_tahun_akademik' => $idTahun,
				'semester' => $semester,
				'jenis' => $this->input->post('jenis', TRUE),
				'nominal' => (int) $this->input->post('nominal', TRUE),
				'metode' => $this->input->post('metode', TRUE),
				'status' => 'LUNAS',
				'keterangan' => $this->input->post('keterangan', TRUE),
				'bukti' => $bukti,
				'created_by' => (int) $this->session->userdata('id_user'),
			);

			$this->model_pembayaran->insert($payload);
			redirect('pembayaran');
			return;
		}

		$this->template->load('template', 'pembayaran/add', $data);
	}

	function edit()
	{
		$id = (int) $this->uri->segment(3);
		$row = $this->model_pembayaran->get_by_id($id);
		if (empty($row))
		{
			show_404();
		}

		$data['row'] = $row;
		$data['siswa'] = $this->db->select('nim,nama')->order_by('nama')->get('tbl_siswa')->result();
		$data['jenis_options'] = array('SPP', 'Ujian', 'Seragam', 'Buku', 'Lainnya');
		$data['metode_options'] = array('Tunai', 'Transfer');

		if (isset($_POST['submit']))
		{
			$bukti = $row['bukti'];
			$newBukti = $this->_upload_bukti('bukti');
			if ($newBukti !== NULL)
			{
				$bukti = $newBukti;
			}

			$payload = array(
				'tanggal' => $this->input->post('tanggal', TRUE),
				'nim' => $this->input->post('nim', TRUE),
				'jenis' => $this->input->post('jenis', TRUE),
				'nominal' => (int) $this->input->post('nominal', TRUE),
				'metode' => $this->input->post('metode', TRUE),
				'status' => $this->input->post('status', TRUE),
				'keterangan' => $this->input->post('keterangan', TRUE),
				'bukti' => $bukti,
			);

			$this->model_pembayaran->update($id, $payload);
			redirect('pembayaran');
			return;
		}

		$this->template->load('template', 'pembayaran/edit', $data);
	}

	function delete()
	{
		$id = (int) $this->uri->segment(3);
		$this->model_pembayaran->delete($id);
		redirect('pembayaran');
	}

	function kwitansi()
	{
		$id = (int) $this->uri->segment(3);
		$row = $this->model_pembayaran->get_by_id($id);
		if (empty($row))
		{
			show_404();
		}

		$siswa = $this->db->get_where('tbl_siswa', array('nim' => $row['nim']))->row_array();
		$namaSiswa = $siswa['nama'] ?? '-';

		$this->load->library('CFPDF');
		$pdf = new FPDF('P', 'mm', 'A5');
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,7,'KWITANSI PEMBAYARAN',0,1,'C');
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No: '.$row['no_kwitansi'],0,1,'C');
		$pdf->Ln(2);

		$pdf->SetFont('Arial','',9);
		$pdf->Cell(35,5,'Tanggal',0,0);
		$pdf->Cell(0,5,': '.date('d/m/Y', strtotime($row['tanggal'])),0,1);
		$pdf->Cell(35,5,'NIM',0,0);
		$pdf->Cell(0,5,': '.$row['nim'],0,1);
		$pdf->Cell(35,5,'Nama',0,0);
		$pdf->Cell(0,5,': '.$namaSiswa,0,1);
		$pdf->Cell(35,5,'Jenis',0,0);
		$pdf->Cell(0,5,': '.$row['jenis'],0,1);
		$pdf->Cell(35,5,'Nominal',0,0);
		$pdf->Cell(0,5,': Rp '.number_format((int) $row['nominal'], 0, ',', '.'),0,1);
		$pdf->Cell(35,5,'Metode',0,0);
		$pdf->Cell(0,5,': '.$row['metode'],0,1);
		$pdf->Ln(4);

		$pdf->SetFont('Arial','I',8);
		$pdf->MultiCell(0,4,'Keterangan: '.($row['keterangan'] ?: '-'),0,'L');
		$pdf->Ln(6);

		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'Petugas',0,1,'R');
		$pdf->Ln(12);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0,5,(string) $this->session->userdata('nama_lengkap'),0,1,'R');

		$pdf->Output();
	}

	private function _upload_bukti($field)
	{
		if (empty($_FILES[$field]) || empty($_FILES[$field]['name']))
		{
			return NULL;
		}

		$path = './uploads/pembayaran/';
		is_dir($path) OR mkdir($path, 0755, TRUE);

		$config['upload_path'] = $path;
		$config['allowed_types'] = 'gif|jpg|png|jpeg|webp|pdf';
		$config['max_size'] = 4096;
		$config['encrypt_name'] = TRUE;

		$this->load->library('upload', $config);
		if ($this->upload->do_upload($field))
		{
			$upload = $this->upload->data();
			return $upload['file_name'];
		}

		$this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
		return NULL;
	}
}

?>
