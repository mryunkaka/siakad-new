<?php

class Seeder extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	private function guard()
	{
		$isCli = is_cli();
		$idLevel = (int) $this->session->userdata('id_level_user');

		if ($isCli)
		{
			return;
		}

		if ($idLevel !== 1)
		{
			show_404();
			return;
		}
	}

	private function repair_data_master_if_overwritten()
	{
		$row = $this->db->get_where('tabel_menu', array('id' => 3))->row_array();
		if (empty($row))
		{
			return;
		}

		// Menu bawaan: id=3 adalah "Data Master" dengan link '#'.
		// Jika terlanjur ter-overwrite oleh seeder lama (Wali Kelas memakai link '#'),
		// kembalikan seperti semula agar submenu master (id 4-10) tetap benar.
		if (($row['link'] ?? '') === '#' && ($row['nama_menu'] ?? '') === 'Wali Kelas')
		{
			$this->db->where('id', 3);
			$this->db->update('tabel_menu', array(
				'nama_menu' => 'Data Master',
				'link' => '#',
				'icon' => 'fa fa-bars',
				'is_main_menu' => 0,
			));

			// Pastikan level 2 tidak ikut punya akses ke Data Master.
			$this->db->where('id_level_user', 2);
			$this->db->where('id_menu', 3);
			$this->db->delete('tbl_user_rule');
		}
	}

	private function upsert_menu($nama_menu, $link, $icon, $is_main_menu)
	{
		$row = $this->db->get_where('tabel_menu', array('link' => $link))->row_array();
		$data = array(
			'nama_menu' => $nama_menu,
			'link' => $link,
			'icon' => $icon,
			'is_main_menu' => (int) $is_main_menu,
		);

		if ( ! empty($row) && ! empty($row['id']))
		{
			$this->db->where('id', (int) $row['id']);
			$this->db->update('tabel_menu', $data);
			return (int) $row['id'];
		}

		$this->db->insert('tabel_menu', $data);
		return (int) $this->db->insert_id();
	}

	private function ensure_rule($id_level_user, $id_menu)
	{
		$exists = $this->db->get_where('tbl_user_rule', array(
			'id_level_user' => (int) $id_level_user,
			'id_menu' => (int) $id_menu,
		));
		if ($exists->num_rows() > 0)
		{
			return;
		}

		$this->db->insert('tbl_user_rule', array(
			'id_level_user' => (int) $id_level_user,
			'id_menu' => (int) $id_menu,
		));
	}

	private function ensure_guru_for_user($userRow)
	{
		$username = $userRow['username'] ?? '';
		if ($username === '')
		{
			return 0;
		}

		$guru = $this->db->get_where('tbl_guru', array('username' => $username))->row_array();
		if ( ! empty($guru) && ! empty($guru['id_guru']))
		{
			return (int) $guru['id_guru'];
		}

		$idUser = (int) ($userRow['id_user'] ?? 0);
		$nuptk = str_pad((string) $idUser, 11, '0', STR_PAD_LEFT);

		$data = array(
			'nuptk' => $nuptk,
			'nama_guru' => $userRow['nama_lengkap'] ?? $username,
			'gender' => 'P',
			'username' => $username,
			'password' => $userRow['password'] ?? md5('123456'),
		);
		$this->db->insert('tbl_guru', $data);
		return (int) $this->db->insert_id();
	}

	private function ensure_walikelas_assignment($idGuru, $idTahunAkademik)
	{
		if ($idGuru <= 0 || $idTahunAkademik <= 0)
		{
			return false;
		}

		$exists = $this->db->get_where('tbl_walikelas', array(
			'id_guru' => (int) $idGuru,
			'id_tahun_akademik' => (int) $idTahunAkademik,
		));
		if ($exists->num_rows() > 0)
		{
			return true;
		}

		$this->db->where('id_tahun_akademik', (int) $idTahunAkademik);
		$this->db->where('id_guru', 0);
		$row = $this->db->get('tbl_walikelas')->row_array();
		if (empty($row) || empty($row['id_walikelas']))
		{
			return false;
		}

		$this->db->where('id_walikelas', (int) $row['id_walikelas']);
		$this->db->update('tbl_walikelas', array('id_guru' => (int) $idGuru));
		return true;
	}

	// Jalankan semua seed penting: menu+rule level 2, dan linking user->guru.
	function run()
	{
		$this->guard();

		$this->repair_data_master_if_overwritten();

		$idTahunAkademik = (int) get_tahun_akademik('id_tahun_akademik');

		// --- Menu Wali Kelas (Level 2) ---
		// Jangan gunakan link '#' karena sudah dipakai menu bawaan "Data Master".
		$wkMainId = $this->upsert_menu('Wali Kelas', 'wali_kelas_menu', 'fa fa-graduation-cap', 0);
		$wkPortalId = $this->upsert_menu('Portal Wali Kelas', 'portal_walikelas', 'fa fa-dashboard', $wkMainId);
		$wkSiswaId = $this->upsert_menu('Siswa Kelas', 'wk_siswa', 'fa fa-users', $wkMainId);

		// Pastikan menu laporan_nilai sudah ada (dipakai wali kelas untuk cetak raport).
		$laporanId = $this->upsert_menu('Laporan Nilai', 'laporan_nilai', 'fa fa-file-pdf-o', 0);

		foreach (array($wkMainId, $wkPortalId, $wkSiswaId, $laporanId) as $menuId)
		{
			$this->ensure_rule(2, $menuId);
		}

		// --- Link tbl_user(level 2/3) ke tbl_guru via username ---
		$this->db->where_in('id_level_user', array(2, 3));
		$users = $this->db->get('tbl_user')->result_array();
		foreach ($users as $u)
		{
			$idGuru = $this->ensure_guru_for_user($u);

			// Untuk wali kelas (level 2), sekalian set jadi walikelas bila ada slot kosong.
			if ((int) ($u['id_level_user'] ?? 0) === 2)
			{
				$this->ensure_walikelas_assignment($idGuru, $idTahunAkademik);
			}
		}

		if (is_cli())
		{
			echo "Seeder run OK\n";
			return;
		}

		$this->session->set_flashdata('success', 'Seeder berhasil dijalankan. Silakan logout/login ulang untuk refresh session.');
		redirect('tampilan_utama');
	}
}

?>
