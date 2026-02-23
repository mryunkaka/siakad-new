<?php

class Model_pembayaran extends CI_Model
{
	public $table = 'tbl_pembayaran';

	public function ensure_schema()
	{
		// Create table if missing (keeps install simple for local/dev).
		$sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
			`id_pembayaran` INT(11) NOT NULL AUTO_INCREMENT,
			`no_kwitansi` VARCHAR(25) NOT NULL,
			`tanggal` DATE NOT NULL,
			`nim` VARCHAR(11) NOT NULL,
			`id_tahun_akademik` INT(11) NOT NULL,
			`semester` VARCHAR(10) NOT NULL,
			`jenis` VARCHAR(40) NOT NULL,
			`nominal` INT(11) NOT NULL,
			`metode` VARCHAR(20) NOT NULL DEFAULT 'Tunai',
			`status` VARCHAR(12) NOT NULL DEFAULT 'LUNAS',
			`keterangan` TEXT NULL,
			`bukti` TEXT NULL,
			`created_by` INT(11) NULL,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id_pembayaran`),
			UNIQUE KEY `uniq_no_kwitansi` (`no_kwitansi`),
			KEY `idx_nim` (`nim`),
			KEY `idx_tanggal` (`tanggal`),
			KEY `idx_id_tahun_akademik` (`id_tahun_akademik`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1";

		$this->db->query($sql);
	}

	public function get_by_id($id)
	{
		return $this->db->get_where($this->table, array('id_pembayaran' => (int) $id))->row_array();
	}

	public function delete($id)
	{
		$this->db->where('id_pembayaran', (int) $id);
		return $this->db->delete($this->table);
	}

	public function insert($data)
	{
		return $this->db->insert($this->table, $data);
	}

	public function update($id, $data)
	{
		$this->db->where('id_pembayaran', (int) $id);
		return $this->db->update($this->table, $data);
	}

	public function generate_no_kwitansi($tanggal)
	{
		$date = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $tanggal) ? $tanggal : date('Y-m-d');
		$ymd = str_replace('-', '', $date);

		$this->db->select('COUNT(*) AS total', FALSE);
		$this->db->where('tanggal', $date);
		$row = $this->db->get($this->table)->row_array();
		$seq = (int) ($row['total'] ?? 0) + 1;

		return 'BYR-'.$ymd.'-'.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
	}

	public function summary($startDate, $endDate)
	{
		$out = array(
			'total_nominal' => 0,
			'jumlah_transaksi' => 0,
		);

		$this->db->select('COUNT(*) AS jumlah_transaksi, COALESCE(SUM(nominal),0) AS total_nominal', FALSE);
		$this->db->from($this->table);
		$this->db->where('tanggal >=', $startDate);
		$this->db->where('tanggal <=', $endDate);
		$this->db->where('status', 'LUNAS');

		$row = $this->db->get()->row_array();
		if ($row)
		{
			$out['total_nominal'] = (int) $row['total_nominal'];
			$out['jumlah_transaksi'] = (int) $row['jumlah_transaksi'];
		}

		return $out;
	}
}

?>
