<?php
	class Database{
		private $host = "localhost";
		private $db_name = "sugistco_sugist";
		private $username = "";
		private $password = "";
		private $table_name = "user";
		public $conn;
		private $opt = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
						PDO::ATTR_EMULATE_PREPARES => false,
						];

		public function getConnection(){

			$this->conn = null;
			try{
				return $this->conn = new PDO("mysql:host=" .$this->host . ";dbname=" . $this->db_name, $this->username, $this->password, $this->opt);
			}catch(Exception $exception){
				//echo "<script>alert('Connection error:');</script> ";
				echo "Error: ".$exception->getMessage();
				//header("location:../register/register.php");
				exit();
			}
		}
	}
	
?>
