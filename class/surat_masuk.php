<?php
class Surat_Masuk
{
	private $itemsTable = "surat_masuk";
	public $id;
	public $tgl_penerimaan;
	public $tgl_surat;
	public $no_surat;
	public $kategori;
	public $lampiran;
	public $dari_mana;
	public $perihal;
	public $keterangan;
	public $image_surat;
	public $klasifikasi;
	public $derajat;
	public $nomor_agenda;
	public $isi_disposisi;
	public $diteruskan_kepada;
	private $conn;

	public function __construct($db)
	{
		$this->conn = $db;
	}

	function read()
	{
		if ($this->id) {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE id = ?)");
			$stmt->bind_param("i", $this->id);
			$stmt->execute();
		} else {
			$level = $_SESSION['level'];
			if ($level == 'Admin' || $level == 'Pimpinan') {
				$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable);
			} else {
				$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE diteruskan_kepada LIKE ?");
				$searchTerm = '%' . $level . '%';
				$stmt->bind_param("s", $searchTerm);
			}
		}
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	function search()
	{
		if ($this->perihal) {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE perihal LIKE ?");
			$searchTerm = '%' . $this->perihal . '%';
			$stmt->bind_param("s", $searchTerm);
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable);
		}
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	function create()
	{

		$stmt = $this->conn->prepare("
			INSERT INTO " . $this->itemsTable . "(`tgl_penerimaan`, `tgl_surat`, `no_surat`, `kategori`, `lampiran`, `dari_mana`, `perihal`, `keterangan`, `image_surat`)
			VALUES(?,?,?,?,?,?,?,?,?)");

		$this->tgl_penerimaan = htmlspecialchars(strip_tags($this->tgl_penerimaan));
		$this->tgl_surat = htmlspecialchars(strip_tags($this->tgl_surat));
		$this->no_surat = htmlspecialchars(strip_tags($this->no_surat));
		$this->kategori = htmlspecialchars(strip_tags($this->kategori));
		$this->lampiran = htmlspecialchars(strip_tags($this->lampiran));
		$this->dari_mana = htmlspecialchars(strip_tags($this->dari_mana));
		$this->perihal = htmlspecialchars(strip_tags($this->perihal));
		$this->keterangan = htmlspecialchars(strip_tags($this->keterangan));
		$this->image_surat = htmlspecialchars(strip_tags($this->image_surat));

		$stmt->bind_param("sssssssss", $this->tgl_penerimaan, $this->tgl_surat, $this->no_surat, $this->kategori, $this->lampiran, $this->dari_mana, $this->perihal, $this->keterangan, $this->image_surat);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	function update()
	{

		$stmt = $this->conn->prepare("
			UPDATE " . $this->itemsTable . " 
			SET tgl_penerimaan= ?, tgl_surat = ?, no_surat = ?, kategori = ?, lampiran = ?, dari_mana = ?,  perihal = ?,  keterangan = ?, image_surat = ?
			WHERE id = ?");

		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->tgl_penerimaan = htmlspecialchars(strip_tags($this->tgl_penerimaan));
		$this->tgl_surat = htmlspecialchars(strip_tags($this->tgl_surat));
		$this->no_surat = htmlspecialchars(strip_tags($this->no_surat));
		$this->kategori = htmlspecialchars(strip_tags($this->kategori));
		$this->lampiran = htmlspecialchars(strip_tags($this->lampiran));
		$this->dari_mana = htmlspecialchars(strip_tags($this->dari_mana));
		$this->perihal = htmlspecialchars(strip_tags($this->perihal));
		$this->keterangan = htmlspecialchars(strip_tags($this->keterangan));
		$this->image_surat = htmlspecialchars(strip_tags($this->image_surat));


		$stmt->bind_param("sssssssssi", $this->tgl_penerimaan, $this->tgl_surat, $this->no_surat, $this->kategori, $this->lampiran, $this->dari_mana, $this->perihal, $this->keterangan, $this->image_surat, $this->id);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	function updateDisposisi()
	{
		$stmt = $this->conn->prepare("
			UPDATE " . $this->itemsTable . " 
			SET klasifikasi= ?, derajat = ?, nomor_agenda = ?, isi_disposisi = ?,  diteruskan_kepada = ?
			WHERE id = ?");

		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->klasifikasi = htmlspecialchars(strip_tags($this->klasifikasi));
		$this->derajat = htmlspecialchars(strip_tags($this->derajat));
		$this->nomor_agenda = htmlspecialchars(strip_tags($this->nomor_agenda));
		$this->isi_disposisi = htmlspecialchars(strip_tags($this->isi_disposisi));
		$this->diteruskan_kepada = htmlspecialchars(strip_tags($this->diteruskan_kepada));


		$stmt->bind_param("sssssi", $this->klasifikasi, $this->derajat, $this->nomor_agenda, $this->isi_disposisi, $this->diteruskan_kepada, $this->id);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}



	function delete()
	{
		// Fetch file names from the database
		$stmtFetch = $this->conn->prepare("SELECT image_surat, lampiran FROM " . $this->itemsTable . " WHERE id = ?");
		$this->id = htmlspecialchars(strip_tags($this->id));
		$stmtFetch->bind_param("i", $this->id);
		$stmtFetch->execute();
		$stmtFetch->bind_result($image_surat, $lampiran);
		$stmtFetch->fetch();
		$stmtFetch->close();

		// Delete record from the database
		$stmtDelete = $this->conn->prepare("DELETE FROM " . $this->itemsTable . " WHERE id = ?");
		$stmtDelete->bind_param("i", $this->id);
		$resultDelete = $stmtDelete->execute();
		$stmtDelete->close();

		if ($resultDelete) {
			// Unlink associated files
			if ($image_surat) {
				$imagePath = '../assets/surat_masuk/' . $image_surat;
				if (file_exists($imagePath)) {
					unlink($imagePath);
				}
			}

			if ($lampiran) {
				$lampiranPath = '../assets/surat_masuk/' . $lampiran;
				if (file_exists($lampiranPath)) {
					unlink($lampiranPath);
				}
			}

			return true;
		}

		return false;
	}

}
