<?php

class Import_siswa extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		checkAksesModule();
		$this->load->model('model_siswa');
	}

	private function suppressDeprecated()
	{
		ini_set('display_errors', '0');
		error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);
	}

	private function normalizeHeader($value)
	{
		$value = strtolower(trim((string) $value));
		$value = preg_replace('/\s+/', '_', $value);
		$value = preg_replace('/[^a-z0-9_]/', '', $value);
		return $value;
	}

	private function normalizeGender($value)
	{
		$value = strtolower(trim((string) $value));
		if ($value === 'l' || $value === 'laki' || $value === 'laki-laki' || $value === 'lakilaki') {
			return 'L';
		}
		if ($value === 'p' || $value === 'perempuan' || $value === 'wanita') {
			return 'P';
		}
		return '';
	}

	private function parseTanggalLahir($cell, $rawValue)
	{
		$raw = is_string($rawValue) ? trim($rawValue) : $rawValue;

		if ($raw === '' || $raw === null) {
			return '';
		}

		if (is_numeric($rawValue)) {
			try {
				if (class_exists('PHPExcel_Shared_Date') && PHPExcel_Shared_Date::isDateTime($cell)) {
					$dt = PHPExcel_Shared_Date::ExcelToPHPObject($rawValue);
					return $dt ? $dt->format('Y-m-d') : '';
				}
			} catch (Throwable $e) {
				return '';
			}
		}

		$ts = strtotime((string) $raw);
		if ($ts === false) {
			return '';
		}
		return date('Y-m-d', $ts);
	}

	public function template()
	{
		$this->suppressDeprecated();

		$this->load->library('CPHP_excel');

		$objPHPExcel = new PHPExcel();
		$sheet = $objPHPExcel->getActiveSheet();

		$headers = [
			'NIM',
			'NAMA',
			'TANGGAL_LAHIR',
			'TEMPAT_LAHIR',
			'GENDER',
			'KD_AGAMA',
			'KD_KELAS',
		];

		$col = 0;
		foreach ($headers as $header) {
			$sheet->setCellValueByColumnAndRow($col, 1, $header);
			$col++;
		}

		$sheet->setCellValueByColumnAndRow(0, 2, 'S001');
		$sheet->setCellValueByColumnAndRow(1, 2, 'Contoh Siswa');
		$sheet->setCellValueByColumnAndRow(2, 2, '2015-01-31');
		$sheet->setCellValueByColumnAndRow(3, 2, 'Rantau');
		$sheet->setCellValueByColumnAndRow(4, 2, 'L');
		$sheet->setCellValueByColumnAndRow(5, 2, '1');
		$sheet->setCellValueByColumnAndRow(6, 2, '7-A1');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

		while (ob_get_level() > 0) {
			@ob_end_clean();
		}
		ob_start();
		$objWriter->save('php://output');
		$excelData = ob_get_clean();

		$this->load->helper('download');
		force_download('format-import-siswa.xlsx', $excelData);
	}

	public function do_import()
	{
		$this->suppressDeprecated();

		$uploadDir = FCPATH . 'uploads/import/';
		if (!is_dir($uploadDir)) {
			@mkdir($uploadDir, 0777, true);
		}

		$config = [
			'upload_path' => $uploadDir,
			'allowed_types' => 'xlsx|xls',
			'max_size' => 8192,
			'overwrite' => true,
			'file_name' => 'import_siswa_' . date('Ymd_His'),
		];

		$this->load->library('upload', $config);
		if (!$this->upload->do_upload('file')) {
			$this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
			redirect('siswa?import=1');
			return;
		}

		$uploadData = $this->upload->data();
		$filePath = $uploadData['full_path'];

		$this->load->library('CPHP_excel');

		try {
			$inputFileType = PHPExcel_IOFactory::identify($filePath);
			$reader = PHPExcel_IOFactory::createReader($inputFileType);
			$reader->setReadDataOnly(true);
			$objPHPExcel = $reader->load($filePath);
		} catch (Throwable $e) {
			@unlink($filePath);
			$this->session->set_flashdata('error', 'File Excel tidak bisa dibaca. Pastikan format .xlsx/.xls benar.');
			redirect('siswa?import=1');
			return;
		}

		$sheet = $objPHPExcel->getActiveSheet();
		$highestRow = (int) $sheet->getHighestRow();
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

		$headerMap = [];
		for ($c = 0; $c < $highestColumnIndex; $c++) {
			$headerValue = $sheet->getCellByColumnAndRow($c, 1)->getValue();
			$key = $this->normalizeHeader($headerValue);
			if ($key !== '' && !isset($headerMap[$key])) {
				$headerMap[$key] = $c;
			}
		}

		$required = ['nim', 'nama', 'kd_kelas'];
		$missing = [];
		foreach ($required as $key) {
			if (!isset($headerMap[$key])) {
				$missing[] = strtoupper($key);
			}
		}

		if (!empty($missing)) {
			@unlink($filePath);
			$this->session->set_flashdata('error', 'Kolom wajib belum ada: ' . implode(', ', $missing) . '. Silakan download contoh format.');
			redirect('siswa?import=1');
			return;
		}

		$tahunAkademik = $this->db->get_where('tbl_tahun_akademik', ['is_aktif' => 'Y'])->row_array();
		if (empty($tahunAkademik) || empty($tahunAkademik['id_tahun_akademik'])) {
			@unlink($filePath);
			$this->session->set_flashdata('error', 'Tahun akademik aktif belum tersedia. Set dulu tahun akademik aktif.');
			redirect('siswa?import=1');
			return;
		}

		$rows = [];
		$nimList = [];
		$skippedInvalid = 0;

		for ($r = 2; $r <= $highestRow; $r++) {
			$nim = trim((string) $sheet->getCellByColumnAndRow($headerMap['nim'], $r)->getValue());
			$nama = trim((string) $sheet->getCellByColumnAndRow($headerMap['nama'], $r)->getValue());
			$kdKelas = trim((string) $sheet->getCellByColumnAndRow($headerMap['kd_kelas'], $r)->getValue());

			if ($nim === '' && $nama === '' && $kdKelas === '') {
				continue;
			}

			if ($nim === '' || $nama === '' || $kdKelas === '') {
				$skippedInvalid++;
				continue;
			}

			$tanggalLahir = '';
			if (isset($headerMap['tanggal_lahir'])) {
				$cell = $sheet->getCellByColumnAndRow($headerMap['tanggal_lahir'], $r);
				$tanggalLahir = $this->parseTanggalLahir($cell, $cell->getValue());
			}

			$tempatLahir = isset($headerMap['tempat_lahir'])
				? trim((string) $sheet->getCellByColumnAndRow($headerMap['tempat_lahir'], $r)->getValue())
				: '';

			$gender = isset($headerMap['gender'])
				? $this->normalizeGender($sheet->getCellByColumnAndRow($headerMap['gender'], $r)->getValue())
				: '';

			$kdAgama = isset($headerMap['kd_agama'])
				? trim((string) $sheet->getCellByColumnAndRow($headerMap['kd_agama'], $r)->getValue())
				: '';

			$rows[$nim] = [
				'nim' => $nim,
				'nama' => $nama,
				'tanggal_lahir' => ($tanggalLahir !== '' ? $tanggalLahir : null),
				'tempat_lahir' => ($tempatLahir !== '' ? $tempatLahir : null),
				'gender' => ($gender !== '' ? $gender : null),
				'kd_agama' => ($kdAgama !== '' ? $kdAgama : null),
				'foto' => 'user-siluet.jpg',
				'kd_kelas' => $kdKelas,
			];
			$nimList[] = $nim;
		}

		@unlink($filePath);

		if (empty($rows)) {
			$this->session->set_flashdata('error', 'Tidak ada data yang valid untuk diimport.');
			redirect('siswa?import=1');
			return;
		}

		$nimUnique = array_values(array_unique($nimList));
		$existing = $this->db->select('nim')->where_in('nim', $nimUnique)->get('tbl_siswa')->result_array();
		$existingNim = [];
		foreach ($existing as $row) {
			$existingNim[$row['nim']] = true;
		}

		$insertSiswa = [];
		$insertRiwayat = [];
		$skippedDuplicate = 0;

		foreach ($rows as $nim => $data) {
			if (isset($existingNim[$nim])) {
				$skippedDuplicate++;
				continue;
			}

			$insertSiswa[] = $data;
			$insertRiwayat[] = [
				'nim' => $nim,
				'kd_kelas' => $data['kd_kelas'],
				'id_tahun_akademik' => $tahunAkademik['id_tahun_akademik'],
			];
		}

		if (empty($insertSiswa)) {
			$this->session->set_flashdata('error', 'Semua data dilewati karena NIM sudah ada.');
			redirect('siswa?import=1');
			return;
		}

		$this->db->trans_start();
		$this->db->insert_batch('tbl_siswa', $insertSiswa);
		$this->db->insert_batch('tbl_riwayat_kelas', $insertRiwayat);
		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			$this->session->set_flashdata('error', 'Import gagal. Silakan coba lagi.');
			redirect('siswa?import=1');
			return;
		}

		$successCount = count($insertSiswa);
		$msg = "Import berhasil: {$successCount} data.";
		if ($skippedDuplicate > 0) {
			$msg .= " Dilewati (duplikat): {$skippedDuplicate}.";
		}
		if ($skippedInvalid > 0) {
			$msg .= " Dilewati (tidak lengkap): {$skippedInvalid}.";
		}

		$this->session->set_flashdata('success', $msg);
		redirect('siswa');
	}
}
