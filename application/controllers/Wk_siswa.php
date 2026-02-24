<?php

class Wk_siswa extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		checkAksesModule();
	}

	private function get_walikelas_context()
	{
		$idGuru = (int) $this->session->userdata('id_guru');
		if ($idGuru <= 0)
		{
			$this->session->set_flashdata('error', 'Akun Anda belum terhubung ke data guru. Pastikan username akun Wali Kelas sama dengan username di Data Guru.');
			redirect('tampilan_utama');
			return null;
		}

		$idTahunAkademik = (int) get_tahun_akademik('id_tahun_akademik');
		$walikelas = $this->db->get_where('tbl_walikelas', array(
			'id_guru' => $idGuru,
			'id_tahun_akademik' => $idTahunAkademik,
		))->row_array();

		if (empty($walikelas) || empty($walikelas['kd_kelas']))
		{
			$this->session->set_flashdata('error', 'Data wali kelas belum ditemukan atau belum ditentukan untuk tahun akademik aktif.');
			redirect('tampilan_utama');
			return null;
		}

		$kelas = $this->db->get_where('view_kelas', array('kd_kelas' => $walikelas['kd_kelas']))->row_array();

		$sqlSiswa = "SELECT ts.nim, ts.nama
			FROM tbl_riwayat_kelas AS trk
			JOIN tbl_siswa AS ts ON trk.nim = ts.nim
			WHERE trk.kd_kelas = ? AND trk.id_tahun_akademik = ?
			ORDER BY ts.nama ASC";
		$siswa = $this->db->query($sqlSiswa, array($walikelas['kd_kelas'], $idTahunAkademik))->result();

		return array(
			'id_guru' => $idGuru,
			'id_tahun_akademik' => $idTahunAkademik,
			'walikelas' => $walikelas,
			'kelas' => $kelas,
			'siswa' => $siswa,
		);
	}

	function index()
	{
		$ctx = $this->get_walikelas_context();
		if ($ctx === null)
		{
			return;
		}

		$this->template->load('template', 'wk_siswa/index', $ctx);
	}
}

?>

