<?php

class Jadwal extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		//checkAksesModule();
		// $this->load->library('ssp');
		$this->load->model('model_jadwal');
	}

	function index()
	{
		// Apabila yang login = guru (id_level_user 3 = guru) maka hanya akan menampilkan jadwal yang hanya diajar oleh guru tersebut
		if ((int) $this->session->userdata('id_level_user') === 3) {
			$id_guru = (int) $this->session->userdata('id_guru');

			$this->db->select('tj.id_jadwal, tju.nama_jurusan, ttk.nama_tingkatan, tm.nama_mapel, tj.jam, tr.nama_ruangan, tj.hari, tj.semester');
			$this->db->from('tbl_jadwal AS tj');
			$this->db->join('tbl_jurusan AS tju', 'tj.kd_jurusan = tju.kd_jurusan');
			$this->db->join('tbl_ruangan AS tr', 'tj.kd_ruangan = tr.kd_ruangan');
			$this->db->join('tbl_mapel AS tm', 'tj.kd_mapel = tm.kd_mapel');
			$this->db->join('tbl_tingkatan_kelas AS ttk', 'tj.kd_tingkatan = ttk.kd_tingkatan');
			$this->db->where('tj.id_guru', $id_guru);

			$data['jadwal'] = $this->db->get();
			// load daftar ngajar guru
			$this->template->load('template', 'jadwal/jadwal_ajar_guru', $data);
		} else {
			$this->template->load('template', 'jadwal/view');
		}
	}

	function generate_jadwal()
	{
		if ($this->input->post('submit', true)) {
			$this->model_jadwal->generateJadwal();
		}
		redirect('jadwal');
	}

	function dataJadwal()
	{
		$kode_jurusan = $this->input->get_post('kd_jurusan', true);
		$kode_tingkatan = $this->input->get_post('kd_tingkatan', true);
		$kelas = $this->input->get_post('kelas', true);

		echo "<table class='table table-striped table-bordered table-hover table-full-width dataTable'>
                    <thead>
                        <tr>
                            <th class='text-center'>NO</th>
                            <th class='text-center'>MATA PELAJARAN</th>
                            <th class='text-center'>GURU</th>
                            <th class='text-center'>RUANGAN</th>
                            <th class='text-center'>HARI</th>
                            <th class='text-center'>JAM</th>
                            <th></th>
                        </tr>
                    </thead>";

		$this->db->select('tj.id_jadwal, tm.nama_mapel, tg.id_guru, tg.nama_guru, tr.kd_ruangan, tj.hari, tj.jam');
		$this->db->from('tbl_jadwal AS tj');
		$this->db->join('tbl_mapel AS tm', 'tj.kd_mapel = tm.kd_mapel');
		$this->db->join('tbl_guru AS tg', 'tj.id_guru = tg.id_guru');
		$this->db->join('tbl_ruangan AS tr', 'tj.kd_ruangan = tr.kd_ruangan');
		$this->db->where('tj.kd_jurusan', $kode_jurusan);
		$this->db->where('tj.kd_kelas', $kelas);
		if (!empty($kode_tingkatan)) {
			$this->db->where('tj.kd_tingkatan', $kode_tingkatan);
		}
		$data_jadwal = $this->db->get()->result();

		$no = 1;
		$jam_pelajaran = $this->model_jadwal->jamPelajaran();
		$hari = array(
			'Senin'  => 'Senin',
			'Selasa' => 'Selasa',
			'Rabu'   => 'Rabu',
			'Kamis'  => 'Kamis',
			'Jumat'  => 'Jumat',
			'Sabtu'  => 'Sabtu'
		);

		// cmb_dinamis(nama, tabelnya, fieldnya, pknya, selected, extra)
		// untuk selected $row->id, harus memasukan field id terlebih dahulu di query.
		foreach ($data_jadwal as $row) {
			echo "<tr>
					<td class='text-center'>$no</td>
					<td>$row->nama_mapel</td>
					
					<td>" . cmb_dinamis('guru', 'tbl_guru', 'nama_guru', 'id_guru', $row->id_guru, "id='guru" . $row->id_jadwal . "' onChange='updateGuru(" . $row->id_jadwal . ")'") . "</td>

					<td>" . cmb_dinamis('ruangan', 'tbl_ruangan', 'nama_ruangan', 'kd_ruangan', $row->kd_ruangan, "id='ruangan" . $row->id_jadwal . "' onChange='updateRuangan(" . $row->id_jadwal . ")'") . "</td>
					
					<td>" . form_dropdown('hari', $hari, $row->hari, "class='form-control' id='hari" . $row->id_jadwal . "' onChange='updateHari(" . $row->id_jadwal . ")'") . "</td>

					<td>" . form_dropdown('jam', $jam_pelajaran, $row->jam, "class='form-control' id='jam" . $row->id_jadwal . "' onChange='updateJam(" . $row->id_jadwal . ")'") . "</td>

					<td  class='text-center'>" . anchor('jadwal/delete_dataJadwal/' . $row->id_jadwal, '<i class="fa fa-times fa fa-white"></i>', 'class="btn btn-xs btn-danger" data-placement="top" title="Delete"') . "</td>
				 </tr>";
			$no++;
		}

		echo "</table>";
	}

	function update_guru()
	{
		$idguru = (int) $this->input->get_post('id_guru', true);
		$idjadwal = (int) $this->input->get_post('id_jadwal', true);
		if ($idjadwal <= 0) {
			return;
		}

		$this->db->where('id_jadwal', $idjadwal);
		$this->db->update('tbl_jadwal', array('id_guru' => $idguru));
	}

	function update_ruangan()
	{
		$kdruangan = $this->input->get_post('kd_ruangan', true);
		$idjadwal = (int) $this->input->get_post('id_jadwal', true);
		if ($idjadwal <= 0) {
			return;
		}

		$this->db->where('id_jadwal', $idjadwal);
		$this->db->update('tbl_jadwal', array('kd_ruangan' => $kdruangan));
	}

	function update_hari()
	{
		$harinya = $this->input->get_post('hari', true);
		$idjadwal = (int) $this->input->get_post('id_jadwal', true);
		if ($idjadwal <= 0) {
			return;
		}

		$this->db->where('id_jadwal', $idjadwal);
		$this->db->update('tbl_jadwal', array('hari' => $harinya));
	}

	function update_jam()
	{
		$jamnya = $this->input->get_post('jam', true);
		$idjadwal = (int) $this->input->get_post('id_jadwal', true);
		if ($idjadwal <= 0) {
			return;
		}

		$this->db->where('id_jadwal', $idjadwal);
		$this->db->update('tbl_jadwal', array('jam' => $jamnya));
	}

	function tampil_kelas()
	{
		$kd_jurusan = $this->input->get_post('kd_jurusan', true);
		$kd_tingkatan = $this->input->get_post('kd_tingkatan', true);

		echo "<select id='kelas' name='kelas' class='form-control' onChange='loadPelajaran()'>";

		$this->db->where('kd_jurusan', $kd_jurusan);
		$this->db->where('kd_tingkatan', $kd_tingkatan);
		$kelas = $this->db->get('tbl_kelas');

		foreach ($kelas->result() as $row) {
			echo "<option value='$row->kd_kelas'>$row->nama_kelas</option>";
		}

		echo "</select>";
	}

	function cetak_jadwal()
	{
		$kelas = $this->input->post('kelas', true);
		$this->load->library('CFPDF');

		$days = array(
			'SENIN'  => 'SENIN',
			'SELASA' => 'SELASA',
			'RABU'   => 'RABU',
			'KAMIS'  => 'KAMIS',
			'JUMAT'  => 'JUMAT',
			'SABTU'  => 'SABTU'
		);

		$pdf = new FPDF('L', 'mm', 'A4');
		$pdf->AddPage();
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(10, 10, 'NO', 1, 0, 'L');
		$pdf->Cell(30, 10, 'WAKTU', 1, 0, 'L');

		foreach ($days as $day) {
			$pdf->Cell(40, 10, $day, 1, 0, 'L');
		}
		$pdf->Cell(30, 10, '', 0, 1, 'L');

		$jam_ajar = $this->model_jadwal->jamPelajaran();
		$no = 1;

		foreach ($jam_ajar as $jam) {
			$pdf->Cell(10, 10, $no, 1, 0, 'L');
			$pdf->Cell(30, 10, $jam, 1, 0, 'L');

			foreach ($days as $day) {
				$pdf->Cell(40, 10, $this->getPelajaran($jam, $day, $kelas), 1, 0, 'L');
			}
			$pdf->Cell(30, 10, '', 0, 1, 'L');
			$no++;
		}

		$pdf->Output();
	}

	function getPelajaran($jam, $hari, $kelas)
	{
		$this->db->select('tm.nama_mapel');
		$this->db->from('tbl_jadwal AS tj');
		$this->db->join('tbl_mapel AS tm', 'tj.kd_mapel = tm.kd_mapel');
		$this->db->where('tj.kd_kelas', $kelas);
		$this->db->where('tj.hari', $hari);
		$this->db->where('tj.jam', $jam);
		$pelajaran = $this->db->get();

		if ($pelajaran->num_rows() > 0) {
			$row = $pelajaran->row_array();
			return $row['nama_mapel'];
		}

		return '-';
	}
}

?>