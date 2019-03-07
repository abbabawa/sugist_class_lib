<?php
	/**
	* Market admin class to hold functionalities and attributes for Market admin of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/

	require_once("DBConnection.php");
	require_once("Market.php");

	class Market_admin extends Market
	{
		private $adminId;
		private $fname;
		private $lname;
		private $dbconnection;


		function __construct($adminId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			parent::__construct();
		}

		public function getId(){

		}

		public function getName(){

		}

		public function addCategory($category){
			$stmt = $this->dbconnection->prepare("INSERT INTO category (category_type) VALUES (?)");
			$check = $stmt->execute([$category]);
		}

		public function deleteCategory($categoryId){
			$stmt = $this->dbconnection->prepare("DELETE FROM category WHERE category_id = ?");
			$check = $stmt->execute([$categoryId]);
		}

		public function processAd($adId, $status){
			$stmt = $this->dbconnection->prepare("UPDATE item SET approval = ? WHERE item_id = ?");
			$check = $stmt->execute([$status, $adId]);
			$user = $this->getAdOwner($adId);
             $approvalDate = date('Y-m-d');
			if($status){
			    $approvalDate = date('Y-m-d');
			    $notification = "Congratulations!! Your Ad has been approved.<br>Please update your ad on a regular basis. If item has been sold please endeavour to take the ad down.<br> Thank you"; 
			    $stmt = $this->dbconnection->prepare("INSERT INTO notification (message, user_id, section, reference, date_sent) VALUES (?, ?, ?, ?, ?)");
		    	$check = $stmt->execute([$notification, $user, 2, $adId, $approvalDate]);
				return true;
			}
			else{
			   $notification = "Your ad didn't meet the requirements and so it has been rejected. Please make changes to your ad and try again. "; 
			    $stmt = $this->dbconnection->prepare("INSERT INTO notification (message, user_id, section, reference, date_sent) VALUES (?, ?, ?, ?, ?)");
			    $check = $stmt->execute([$notification, $user, 2, $adId, $approvalDate]);
				return false;
			}
		}

		public function sendMessage($item, $status){
			if ($status == 1) {
				$message = "Congratulations!! Your Ad has been approved.<br>Please update your ad on a regular basis. If item has been sold please endeavour to take the ad down.<br> Thank you";
				$stmt = $this->dbconnection->prepare("INSERT INTO market_messages (item_id, message, date_sent) VALUES (?, ?, ?)");
				$stmt->execute([$item, $message]);
			}
		}

		public function getAdsByDate($itemDate){
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE date_posted = ?");
			$stmt->execute([$itemDate]);
			return $stmt;
		}

		public function getPendingAds(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE approval = ?");
			$stmt->execute([0]);
			return $stmt;
		}

		public function getPendingAd($itemId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE item_id = ?");
			$stmt->execute([$itemId]);
			return $stmt;
		}

		public function messageAdOwner(){

		}

		/*public function deleteAd($adId){
			$stmt = $this->dbconnection->prepare("DELETE FROM item WHERE item_id = ?");
			$check = $stmt->execute([$adId]);
		}*/
	}

	
?>