<?php
class User
{
    private $itemsTable = "user";
    public $id;
    public $username;
    public $password;
    public $nama_lengkap;
    public $email;
    public $bidang_pekerjaan;
    public $no_hp;
    public $level;
    public $image_profile;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function read()
    {
        if ($this->id) {
            // Tambahkan klausa WHERE untuk memfilter ID tertentu
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE id = ? AND id NOT IN (1, 2)");
            $stmt->bind_param("i", $this->id);
        } else {
            // Tambahkan klausa WHERE untuk menghindari ID tertentu
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE id NOT IN (1, 2)");
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function update()
    {

        $stmt = $this->conn->prepare("
                UPDATE " . $this->itemsTable . " 
                SET  username= ?,  password= ?, nama_lengkap= ?, email = ?, bidang_pekerjaan = ?, no_hp = ?, level = ?, image_profile = ?
                WHERE id = ?");

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->nama_lengkap = htmlspecialchars(strip_tags($this->nama_lengkap));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->bidang_pekerjaan = htmlspecialchars(strip_tags($this->bidang_pekerjaan));
        $this->no_hp = htmlspecialchars(strip_tags($this->no_hp));
        $this->level = htmlspecialchars(strip_tags($this->level));
        $this->image_profile = htmlspecialchars(strip_tags($this->image_profile));


        $stmt->bind_param("ssssssssi", $this->username, $this->password, $this->nama_lengkap, $this->email, $this->bidang_pekerjaan, $this->no_hp, $this->level, $this->image_profile, $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function create()
    {
        $stmt = $this->conn->prepare("
			INSERT INTO " . $this->itemsTable . "(`username`, `password`, `level`, `nama_lengkap`)
			VALUES(?,?,?,?)");

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->level = htmlspecialchars(strip_tags($this->level));
        $this->nama_lengkap = htmlspecialchars(strip_tags($this->nama_lengkap));

        $stmt->bind_param("ssss", $this->username, $this->password, $this->level, $this->nama_lengkap);


        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function delete()
    {
        if ($this->id == 1 || $this->id == 2) {
            return false; // Jangan izinkan menghapus ID 1 atau 2
        }

        $stmt = $this->conn->prepare("
                DELETE FROM " . $this->itemsTable . " 
                WHERE id = ?");

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bind_param("i", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function isUsernameUnique($username)
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if there are any rows with the given username
        return $result->num_rows === 0;
    }

    public function getUserById($id)
    {
        $query = "SELECT * FROM " . $this->itemsTable . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}
