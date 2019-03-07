<?php
	/**
	* User class to hold functionalities for JOBS section of the app
	*Author: Abba Bawa
	*Created: 02/07/2017
	*/
	require_once("DBConnection.php");

	class Jobs
	{
		private $userId;
		private $dbconnection;

		function __construct($userId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$stmt = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
			$stmt->execute([$userId]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->userId = $user['user_id'];
		}

		public function getCategoryName($categoryId){
			$stmt = $this->dbconnection->prepare("SELECT category FROM entrepreneur_categories WHERE category_id = ?");
			$stmt->execute([$categoryId]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['category'];
		}

		public function getCategories(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM entrepreneur_categories");
			$stmt->execute();
			return $stmt;
		}

		public function getSamples($categoryId){
			$stmt = $this->dbconnection->prepare("SELECT s.*, e.category, e.business_name, e.entrepreneur_id FROM entrepreneur_samples s, entrepreneur e WHERE s.entrepreneur_id = e.entrepreneur_id AND category = ?");
			$stmt->execute([$categoryId]);
			return $stmt;
		}

		public function getSampleOwner($entrepreneurId){
			$stmt = $this->dbconnection->prepare("SELECT e.*, u.profile_pic, u.f_name, u.l_name FROM entrepreneur e, user u WHERE e.user_id = u.user_id AND e.entrepreneur_id = ?");
			$stmt->execute([$entrepreneurId]);
			return $stmt;
		}

		public function getSamplesByOwner($entrepreneurId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM entrepreneur_samples WHERE entrepreneur_id = ?");
			$stmt->execute([$entrepreneurId]);
			return $stmt;
		}
		
		public function sendMessage($entrepreneurId, $message){
			$owner = $this->getSampleOwner($entrepreneurId)->fetch(PDO::FETCH_ASSOC)['user_id'];
			require_once("User.php");
			$user = new User($this->userId);
			if ($user->sendMessage($owner, $message) == 1) {
				$stmt = $this->dbconnection->prepare("INSERT INTO job_messages (entrepreneur_id, user_id) VALUES (?, ?)");
				$res = $stmt->execute([$entrepreneurId, $this->userId]);
				return $res;
			}
			return 0;
		}

		public function getMessages($entrepreneurId){
		    $stmt = $this->dbconnection->prepare("SELECT user_id FROM entrepreneur WHERE entrepreneur_id = ?");
			$stmt->execute([$entrepreneurId]);
			$entrepreneur = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];
		    
			$stmt = $this->dbconnection->prepare("SELECT * FROM chat WHERE sender = ? AND recipient = ? OR sender = ? AND recipient = ?");
			$stmt->execute([$entrepreneur, $this->userId, $this->userId, $entrepreneur]);
			return $stmt;
		}
	}

	//$jobs = new Jobs(10);
	//print_r($jobs->getSamples(3)->fetchAll());
?>