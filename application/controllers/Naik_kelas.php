<?php

class Naik_kelas extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		checkAksesModule();
	}

	private function require_admin()
	{
		$idLevel = (int) $this->session->userdata('id_level_user');
		if ($idLevel !== 1)
		{
			$this->session->set_flashdata('error', 'Fitur ini hanya untuk Administrator.');
			redirect('tampilan_utama');
			return false;
		}
		return true;
	}

	function index()
	{
		if ( ! $this->require_admin())
		{
			return;
		}

		$data['asal'] = $this->input->get('asal', TRUE);
		$this->template->load('template', 'naik_kelas/index', $data);
	}

	function combobox_kelas_asal()
	{
		if ( ! $this->require_admin())
		{
			return;
		}

		$jurusan = $this->input->get('kd_jurusan', TRUE);
		$html = "<select id='cbkelas_asal' class='form-control' onchange='loadTujuan()'>";

		if (empty($jurusan))
		{
			$html .= "<option value=''>-- Pilih Jurusan --</option>";
			$html .= "</select>";
			echo $html;
			return;
		}

		$this->db->where('kd_jurusan', $jurusan);
		$this->db->order_by('kd_tingkatan', 'ASC');
		$this->db->order_by('kd_kelas', 'ASC');
		$kelas = $this->db->get('tbl_kelas')->result();

		$html .= "<option value=''>-- Pilih Kelas Asal --</option>";
		foreach ($kelas as $k)
		{
			$html .= "<option value='".htmlspecialchars($k->kd_kelas, ENT_QUOTES, 'UTF-8')."'>".htmlspecialchars($k->nama_kelas, ENT_QUOTES, 'UTF-8')."</option>";
		}
		$html .= "</select>";
		echo $html;
	}

	function combobox_kelas_tujuan()
	{
		if ( ! $this->require_admin())
		{
			return;
		}

		$kdKelasAsal = $this->input->get('kd_kelas_asal', TRUE);
		$html = "<select id='cbkelas_tujuan' class='form-control' onchange='loadPreviewSiswa()'>";

		if (empty($kdKelasAsal))
		{
			$html .= "<option value=''>-- Pilih Kelas Asal dulu --</option>";
			$html .= "</select>";
			echo $html;
			return;
		}

		$asal = $this->db->get_where('tbl_kelas', array('kd_kelas' => $kdKelasAsal))->row_array();
		if (empty($asal))
		{
			$html .= "<option value=''>Kelas asal tidak ditemukan</option>";
			$html .= "</select>";
			echo $html;
			return;
		}

		$tingkatanAsal = (int) ($asal['kd_tingkatan'] ?? 0);
		$tingkatanTujuan = (string) ($tingkatanAsal + 1);
		$kdJurusan = $asal['kd_jurusan'] ?? '';

		$this->db->where('kd_jurusan', $kdJurusan);
		$this->db->where('kd_tingkatan', $tingkatanTujuan);
		$this->db->order_by('kd_kelas', 'ASC');
		$tujuan = $this->db->get('tbl_kelas')->result();

		if (empty($tujuan))
		{
			$html .= "<option value=''>Tidak ada kelas tujuan (tingkatan berikutnya) untuk jurusan ini</option>";
			$html .= "</select>";
			echo $html;
			return;
		}

		$html .= "<option value=''>-- Pilih Kelas Tujuan --</option>";
		foreach ($tujuan as $k)
		{
			$html .= "<option value='".htmlspecialchars($k->kd_kelas, ENT_QUOTES, 'UTF-8')."'>".htmlspecialchars($k->nama_kelas, ENT_QUOTES, 'UTF-8')."</option>";
		}
		$html .= "</select>";
		echo $html;
	}

	function load_siswa()
	{
		if ( ! $this->require_admin())
		{
			return;
		}

		$kdKelas = $this->input->get('kd_kelas', TRUE);
		if (empty($kdKelas))
		{
			echo "<div class='alert alert-danger'>Kelas belum dipilih.</div>";
			return;
		}

		$this->db->where('kd_kelas', $kdKelas);
		$this->db->order_by('nama', 'ASC');
		$siswa = $this->db->get('tbl_siswa')->result();

		echo "<table class='table table-striped table-bordered'>
				<tr>
					<th class='text-center' width='60'>No</th>
					<th class='text-center' width='140'>NIM</th>
					<th>Nama</th>
				</tr>";

		$no = 1;
		foreach ($siswa as $row)
		{
			echo "<tr>
					<td class='text-center'>".$no."</td>
					<td class='text-center'>".htmlspecialchars($row->nim, ENT_QUOTES, 'UTF-8')."</td>
					<td>".htmlspecialchars($row->nama, ENT_QUOTES, 'UTF-8')."</td>
				  </tr>";
			$no++;
		}
		echo "</table>";
	}

	function proses()
	{
		if ( ! $this->require_admin())
		{
			return;
		}

		$asal = $this->input->post('kelas_asal', TRUE);
		$tujuan = $this->input->post('kelas_tujuan', TRUE);

		if (empty($asal) || empty($tujuan))
		{
			$this->session->set_flashdata('error', 'Kelas asal dan kelas tujuan wajib dipilih.');
			redirect('naik_kelas');
			return;
		}

		$this->db->where('kd_kelas', $asal);
		$siswa = $this->db->get('tbl_siswa')->result_array();
		$jumlah = count($siswa);

		if ($jumlah < 1)
		{
			$this->session->set_flashdata('error', 'Tidak ada siswa pada kelas asal.');
			redirect('naik_kelas?asal='.rawurlencode($asal));
			return;
		}

		$idTahunAkademik = (int) get_tahun_akademik('id_tahun_akademik');

		$this->db->trans_start();

		// Update kelas pada master siswa
		$this->db->where('kd_kelas', $asal);
		$this->db->update('tbl_siswa', array('kd_kelas' => $tujuan));

		// Update/insert riwayat kelas untuk tahun akademik aktif
		foreach ($siswa as $row)
		{
			$nim = $row['nim'];
			if (empty($nim))
			{
				continue;
			}

			$exists = $this->db->get_where('tbl_riwayat_kelas', array(
				'nim' => $nim,
				'id_tahun_akademik' => $idTahunAkademik,
			))->row_array();

			if ( ! empty($exists) && ! empty($exists['id_riwayat']))
			{
				$this->db->where('id_riwayat', (int) $exists['id_riwayat']);
				$this->db->update('tbl_riwayat_kelas', array('kd_kelas' => $tujuan));
			}
			else
			{
				$this->db->insert('tbl_riwayat_kelas', array(
					'kd_kelas' => $tujuan,
					'nim' => $nim,
					'id_tahun_akademik' => $idTahunAkademik,
				));
			}
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			$this->session->set_flashdata('error', 'Proses naik kelas gagal. Silakan coba lagi.');
			redirect('naik_kelas?asal='.rawurlencode($asal));
			return;
		}

		$this->session->set_flashdata('success', "Berhasil menaikkan {$jumlah} siswa dari {$asal} ke {$tujuan}.");
		redirect('naik_kelas?asal='.rawurlencode($tujuan));
	}
}

?>

