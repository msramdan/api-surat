<?php
	date_default_timezone_set("Asia/Jakarta");
class Database{

	private $host  = 'localhost';
    private $user  = 'root';
    private $password   = "";
    private $database  = "surat"; 
    
    public function getConnection(){		
		$conn = new mysqli($this->host, $this->user, $this->password, $this->database);
		if($conn->connect_error){
			die("Error failed to connect to MySQL: " . $conn->connect_error);
		} else {
			return $conn;
		}
    }
}
?>