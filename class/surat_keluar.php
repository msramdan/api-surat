<?php
class Surat_Keluar
{
	private $itemsTable = "surat_keluar";
	public $id;
	public $tgl_catat;
	public $tgl_surat;
	public $no_surat;
	public $lampiran;
	public $kategori;
	public $dikirim_kepada;
	public $perihal;
	public $keterangan;
	public $image_surat;
	private $conn;

	public function __construct($db)
	{
		$this->conn = $db;
	}


	function read()
	{
		if ($this->id) {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE id = ?");
			$stmt->bind_param("i", $this->id);
		} else {
			$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable);
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
			INSERT INTO " . $this->itemsTable . "(`tgl_catat`, `tgl_surat`, `no_surat`, `kategori`, `lampiran`, `dikirim_kepada`, `perihal`, `keterangan`, `image_surat`)
			VALUES(?,?,?,?,?,?,?,?,?)");

		$this->tgl_catat = htmlspecialchars(strip_tags($this->tgl_catat));
		$this->tgl_surat = htmlspecialchars(strip_tags($this->tgl_surat));
		$this->no_surat = htmlspecialchars(strip_tags($this->no_surat));
		$this->kategori = htmlspecialchars(strip_tags($this->kategori));
		$this->lampiran = htmlspecialchars(strip_tags($this->lampiran));
		$this->dikirim_kepada = htmlspecialchars(strip_tags($this->dikirim_kepada));
		$this->perihal = htmlspecialchars(strip_tags($this->perihal));
		$this->keterangan = htmlspecialchars(strip_tags($this->keterangan));
		$this->image_surat = htmlspecialchars(strip_tags($this->image_surat));

		$stmt->bind_param("sssssssss", $this->tgl_catat, $this->tgl_surat, $this->no_surat, $this->kategori, $this->lampiran, $this->dikirim_kepada, $this->perihal, $this->keterangan, $this->image_surat);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	function update()
	{

		$stmt = $this->conn->prepare("
			UPDATE " . $this->itemsTable . " 
			SET tgl_catat = ?, tgl_surat = ?, no_surat = ?, kategori = ?, lampiran = ?, dikirim_kepada = ?,  perihal = ?,  keterangan = ?, image_surat = ?
			WHERE id = ?");

		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->tgl_catat = htmlspecialchars(strip_tags($this->tgl_catat));
		$this->tgl_surat = htmlspecialchars(strip_tags($this->tgl_surat));
		$this->no_surat = htmlspecialchars(strip_tags($this->no_surat));
		$this->kategori = htmlspecialchars(strip_tags($this->kategori));
		$this->lampiran = htmlspecialchars(strip_tags($this->lampiran));
		$this->dikirim_kepada = htmlspecialchars(strip_tags($this->dikirim_kepada));
		$this->perihal = htmlspecialchars(strip_tags($this->perihal));
		$this->keterangan = htmlspecialchars(strip_tags($this->keterangan));
		$this->image_surat = htmlspecialchars(strip_tags($this->image_surat));

		$stmt->bind_param("sssssssssi", $this->tgl_catat, $this->tgl_surat, $this->no_surat, $this->kategori, $this->lampiran, $this->dikirim_kepada, $this->perihal, $this->keterangan, $this->image_surat, $this->id);

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
				$imagePath = '../assets/surat_keluar/' . $image_surat;
				if (file_exists($imagePath)) {
					unlink($imagePath);
				}
			}

			if ($lampiran) {
				$lampiranPath = '../assets/surat_keluar/' . $lampiran;
				if (file_exists($lampiranPath)) {
					unlink($lampiranPath);
				}
			}

			return true;
		}

		return false;
	}

	public function getOldFileNames($id)
	{
		$stmtFetch = $this->conn->prepare("SELECT image_surat, lampiran FROM " . $this->itemsTable . " WHERE id = ?");
		$id = htmlspecialchars(strip_tags($id));
		$stmtFetch->bind_param("i", $id);
		$stmtFetch->execute();
		$stmtFetch->bind_result($oldImage_surat, $oldLampiran);
		$stmtFetch->fetch();
		$stmtFetch->close();

		return array('image_surat' => $oldImage_surat, 'lampiran' => $oldLampiran);
	}
}
