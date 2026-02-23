<?php

class Nilai extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		//checkAksesModule();
	}

	function index()
	{
		$id_guru = (int) $this->session->userdata('id_guru');

		$this->db->select('tj.kd_kelas, tj.id_jadwal, tju.nama_jurusan, ttk.nama_tingkatan, tm.nama_mapel, tj.jam, tr.nama_ruangan, tj.hari, tj.semester');
		$this->db->from('tbl_jadwal AS tj');
		$this->db->join('tbl_jurusan AS tju', 'tj.kd_jurusan = tju.kd_jurusan');
		$this->db->join('tbl_ruangan AS tr', 'tj.kd_ruangan = tr.kd_ruangan');
		$this->db->join('tbl_mapel AS tm', 'tj.kd_mapel = tm.kd_mapel');
		$this->db->join('tbl_tingkatan_kelas AS ttk', 'tj.kd_tingkatan = ttk.kd_tingkatan');
		$this->db->where('tj.id_guru', $id_guru);
		$data['jadwal'] = $this->db->get();

		$this->template->load('template', 'nilai/list_kelas', $data);
	}

	function kelas()
	{
		$id_jadwal = (int) $this->uri->segment(3);
		$jadwal = $this->db->get_where('tbl_jadwal', array('id_jadwal' => $id_jadwal))->row_array();
		$kd_kelas = isset($jadwal['kd_kelas']) ? $jadwal['kd_kelas'] : '';
		$id_tahun_akademik = (int) get_tahun_akademik('id_tahun_akademik');

		$this->db->select('tk.nama_kelas, tju.nama_jurusan, tm.nama_mapel, ttk.nama_tingkatan');
		$this->db->from('tbl_jadwal AS tj');
		$this->db->join('tbl_jurusan AS tju', 'tj.kd_jurusan = tju.kd_jurusan');
		$this->db->join('tbl_kelas AS tk', 'tj.kd_kelas = tk.kd_kelas');
		$this->db->join('tbl_mapel AS tm', 'tj.kd_mapel = tm.kd_mapel');
		$this->db->join('tbl_tingkatan_kelas AS ttk', 'tj.kd_tingkatan = ttk.kd_tingkatan');
		$this->db->where('tj.id_jadwal', $id_jadwal);
		$data['kelas'] = $this->db->get()->row_array();

		$this->db->select('ts.nim, ts.nama');
		$this->db->from('tbl_riwayat_kelas AS trk');
		$this->db->join('tbl_siswa AS ts', 'trk.nim = ts.nim');
		$this->db->where('trk.kd_kelas', $kd_kelas);
		$this->db->where('trk.id_tahun_akademik', $id_tahun_akademik);
		$data['siswa'] = $this->db->get()->result();

		$this->template->load('template', 'nilai/form_nilai', $data);
	}

	function update_nilai()
	{
		$nim = $this->input->get_post('nim', true);
		$idjadwal = (int) $this->input->get_post('id_jadwal', true);
		$nilai = (float) $this->input->get_post('nilai', true);

		if (empty($nim) || $idjadwal <= 0) {
			return;
		}

		$parameter = array(
			'nim' => $nim,
			'id_jadwal' => $idjadwal,
			'nilai' => $nilai
		);

		$validasi = array(
			'nim' => $nim,
			'id_jadwal' => $idjadwal
		);

		$check = $this->db->get_where('tbl_nilai', $validasi);
		if ($check->num_rows() > 0) {
			// Apabila datanya besar dari 0 / ada maka akan melakukan proses update
			$this->db->where('nim', $nim);
			$this->db->where('id_jadwal', $idjadwal);
			$this->db->update('tbl_nilai', array('nilai' => $nilai));
			echo 'data diupdate';
		} else {
			// Jika datanya tidak ada maka akan melakukan proses insert
			$this->db->insert('tbl_nilai', $parameter);
			echo 'data diinsert';
		}
	}
}

?>