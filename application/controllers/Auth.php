<?php

	class Auth extends CI_Controller
	{

		function __construct()
		{
			parent::__construct();
			$this->load->model('model_user');
			$this->load->model('model_guru');
		}
		
		function index()
		{
			$this->load->view('auth/login');
		}

		function check_login()
		{
			if (isset($_POST['submit'])) {
				
				$username	= $this->input->post('username');
				$password 	= $this->input->post('password');
				// proses pengecekan username dan password di database beradi di model_user dengan memparsing $username dan $password
				// $loginUser untuk mengecek user pada tbl_user sedangkan $loginGuru memerika ke dalam tbl_guru
				$loginUser		= $this->model_user->login($username, $password);

				$loginGuru  	= $this->model_guru->login($username, $password);
				
				// $loginUser-> mengambil nilai dari $user yang ada di function login pada model_user, apabila data salah maka user tidak berisi dan $loginUser menjadi kosong
				// apablia $loginUser tidak kosong (memiliki data) maka akan membuat session dan redirect ke tampilan_utama
				if (!empty($loginUser)) {

					// $this->session->set_userdata($loginUser); -> maksudnya mengset userdata yang mana datanya diambil dari $loginUser
					$this->session->set_userdata($loginUser);

					// Untuk beberapa role (mis. Guru/Wali Kelas) data guru dipakai di modul lain.
					// Jika user login dari tbl_user tapi belum ada id_guru di session, coba hubungkan via username di tbl_guru.
					$idLevel = isset($loginUser['id_level_user']) ? (int) $loginUser['id_level_user'] : 0;
					if (($idLevel === 2 || $idLevel === 3) && empty($this->session->userdata('id_guru')))
					{
						$guru = $this->db->get_where('tbl_guru', array('username' => $loginUser['username']))->row_array();
						if ( ! empty($guru) && ! empty($guru['id_guru']))
						{
							$this->session->set_userdata(array('id_guru' => (int) $guru['id_guru']));
						}
					}
					redirect('tampilan_utama');

				} elseif (!empty($loginGuru)) {

					// $sessionGuru digunakan untuk mengkonversi data agar sesuai dengan data yang ada di tbl_user, sbg contoh di tbl_user ada nama_lengkap sedangkan di tbl_guru hanya ada nama_guru maka dari itu kita menmbuat 'nama_lengkap' yang mana datanya diambil dari $loginGuru['nama_guru']
					$sessionGuru = array(
							'nama_lengkap'   => $loginGuru['nama_guru'],
							'id_level_user'  => 3,
							'id_guru'		 => $loginGuru['id_guru'], 
					);
					// $this->session->set_userdata($sessionGuru); -> maksudnya mengset userdata yang mana datanya diambil dari $sessionGuru
					$this->session->set_userdata($sessionGuru);
					redirect('tampilan_utama');

				} else {
					redirect('auth');
				}
			} else {
				redirect('auth');
			}
		}

		function logout()
		{
			$this->session->sess_destroy();
			redirect('auth');
		}

	}

?>
