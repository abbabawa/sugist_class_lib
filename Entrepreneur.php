<?php
	
	/**
	* User class to hold functionalities for Entrepreneur(JOBS) section of the app
	*Author: Abba Bawa
	*Created: 24/06/2017
	*/
	require_once("DBConnection.php");
	require_once("Jobs.php");

	class Entrepreneur extends Jobs
	{

		private $userId;
		private $entrepreneurId;
		private $dbconnection;
		
		function __construct($userId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			if ($userId > 0) {
				$stmt = $this->dbconnection->prepare("SELECT * FROM entrepreneur WHERE user_id = ?");
				$stmt->execute([$userId]);
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->userId = $res['user_id'];
				$this->entrepreneurId = $res['entrepreneur_id'];
			}
		}

		public function getBusinessProfile($userId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM entrepreneur WHERE user_id = ?");
			$stmt->execute([$userId]);
			return $stmt;
		}

		public function addABusiness($userId, $category){
		    $stmt = $this->dbconnection->prepare("SELECT * FROM entrepreneur WHERE user_id = ?");
			$stmt->execute([$userId]);
		    if($stmt->fetch(PDO::FETCH_ASSOC)){
    			$stmt = $this->dbconnection->prepare("INSERT INTO entrepreneur (user_id, category) VALUES (?, ?)");
    			$res = $stmt->execute([$userId, $category]);
    			$id = $this->dbconnection->query("SELECT LAST_INSERT_ID()");
    			$last = $id->fetch(PDO::FETCH_NUM);
    			$this->entrepreneurId = $last[0];
    			return $res;
		    }
		}

		public function getCategories(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM entrepreneur_categories");
			$stmt->execute();
			return $stmt;
		}

		public function postSample($name, $description, $price, $pic){
			$uploadDate = date("y-m-d", strtotime("today"));
			$stmt = $this->dbconnection->prepare("INSERT INTO entrepreneur_samples (entrepreneur_id, name, description, price, pic, upload_date) VALUES (?, ?, ?, ?, ?, ?)");
			$res = $stmt->execute([$this->entrepreneurId, $name, $description, $price, $pic, $uploadDate]);
			return $res;
		}

		public function getSamples($entrepreneurId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM entrepreneur_samples WHERE entrepreneur_id = ?");
			$stmt->execute([$entrepreneurId]);
			return $stmt;
		}

		public function deleteSample($sampleId){
			$stmt = $this->dbconnection->prepare("DELETE FROM entrepreneur_samples WHERE sample_id = ?");
			$res = $stmt->execute([$sampleId]);
			return $res;
		}
		
		public function sendMessage($userId, $message){
			require_once("User.php");
			$user = new User($this->userId);
			if ($user->sendMessage($userId, $message) == 1) {
				return 1;
			}
			return 0;
		}
		
		public function getMessageHeads(){
			$stmt = $this->dbconnection->prepare("SELECT DISTINCT j.user_id, u.f_name, l_name FROM job_messages j, user u WHERE j.user_id = u.user_id AND j.entrepreneur_id = ?");
			$stmt->execute([$this->entrepreneurId]);
			return $stmt;  
		}
		
		public function getLastMessage($userId){
		    $stmt = $this->dbconnection->prepare("SELECT  message from chat WHERE sender = ? AND recipient = ? OR sender = ? AND recipient = ? ORDER BY chat_id DESC LIMIT 1");
			$stmt->execute([$this->userId, $userId, $userId, $this->userId]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['message'];
		}

		public function getMessages($userId){
			$stmt = $this->dbconnection->prepare("SELECT j.*, c.* FROM job_messages j, chat c WHERE j.chat_id = c.chat_id AND j.entrepreneur_id = ? AND j.user_id = ?");
			$stmt->execute([$this->entrepreneurId, $userId]);
			return $stmt;
		}
		
		public function deleteProfile(){
		    $stmt = $this->dbconnection->prepare("DELETE FROM entrepreneur WHERE entrepreneur_id = ?");
			$status = $stmt->execute([$this->entrepreneurId]);
			return $status;
		}
	}
?>