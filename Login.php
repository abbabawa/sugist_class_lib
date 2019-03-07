<?php
	/**
	* User class to hold log users in
	*Author: Abba Bawa
	*Created: 10/06/2017
	*/
	require_once("DBConnection.php");
	class Login
	{
		private $userId = 0;
		private $institution = 0;
		private $dbconnection;
		

		function __construct()
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
		}

		public function login($email, $password){
			$query = $this->dbconnection->prepare("SELECT * FROM user WHERE email = ? AND password = ?");
			$query->execute([$email, md5($password)]);
			if($query->rowCount() >= 1){
				$result = $query->fetch(PDO::FETCH_ASSOC);
				$this->userId = $result['user_id'];
				$this->institution = $result['institution_id'];
				return 1;
			}
			else{
				$query = $this->dbconnection->prepare("SELECT * FROM user WHERE email = ?");
				$query->execute([$email]);
				$result = $query->fetch(PDO::FETCH_ASSOC);
				//$hash = password_hash("Abba", PASSWORD_BCRYPT);

				if (password_verify($password, $result['password'])) {
				    $query = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
				    $query->execute([$result['user_id']]);
				    $result = $query->fetch(PDO::FETCH_ASSOC);
				    $this->userId = $result['user_id'];
				    $this->institution = $result['institution_id'];
					return 1;
				}
				else{
					return 0;
				}
				return 0;
			}
		}

		public function getId(){
			return $this->userId;
		}

		public function getInstitution(){
			return $this->institution;
		}

		public function createCookie($token){
			$selector = password_hash($token, PASSWORD_BCRYPT); 
			setcookie("selector", $selector, time() + (86400 * 30), "/");
			$val = $token."".date("Y-m-d");
			$validator = password_hash($val, PASSWORD_BCRYPT);
			setcookie("validator", $validator, time() + (86400 * 30), "/");
			$exDate = $displayDate = date("Y-m-d", time() + (30 * 24 * 60 * 60));
			
			$query = $this->dbconnection->prepare("INSERT INTO auth_tokens (selector, validator, user_id, expires) VALUES (?, ?, ?, ?)");
		    $query->execute([$selector, $validator, $this->userId, $exDate]);
		}
		
		public function cookieLogin($selector, $validator){
		    $query = $this->dbconnection->prepare("SELECT * FROM auth_tokens WHERE selector = ? AND validator = ?");
    		$query->execute([$selector, $validator]);
    		if($query->rowCount() > 0){
    		    $result = $query->fetch(PDO::FETCH_ASSOC);
    		    $qry = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
    		    $qry->execute([$result['user_id']]);
    		    $res = $qry->fetch(PDO::FETCH_ASSOC);
    		    $this->userId = $res['user_id'];
    		    $this->institution = $res['institution_id'];
    		    return 1;
    		}
    		else{
    		    return 0;
    		}
		}
	}
?>