<?php
	/**
	* Market class to hold functionalities and attributes for Market section of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/
	require_once("DBConnection.php");
	require_once("Section.php");

	class Market extends Section
	{
		private $dbconnection;

		function __construct()
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();

		}

		public function getId(){

		}

		public function getName(){

		}

		public function setName($name){

		}

		public function getCategories(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM category");
			$stmt->execute();
			return $stmt;
		}

		public function getCategory($categoryId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM category WHERE category_id = ?");
			$stmt->execute([$categoryId]);
			return $stmt;
		}

		public function getAds(){
			$approval = 1;
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE approval = ?");
			$stmt->execute([$approval]);
			return $stmt;
		}
		
		public function getHomeAds(){
			$approval = 1;
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE approval = ? LIMIT 6");
			$stmt->execute([$approval]);
			return $stmt;
		}

		public function getMyAds($userId){
			$approval = 1;
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE user_id = ? AND approval = ?");
			$stmt->execute([$userId, $approval]);
			return $stmt;
		}

		public function getAd($adId){
			$approval = 1;
			$stmt = $this->dbconnection->prepare("SELECT i.price, i.picture, i.phone, i.item_name, i.description, u.f_name, u.l_name, u.user_id FROM item i, user u WHERE i.user_id = u.user_id AND i.item_id = ? AND i.approval = ?");
			$stmt->execute([$adId, $approval]);
			return $stmt;
		}

		public function getAdOwner($adId){
			$stmt = $this->dbconnection->prepare("SELECT user_id FROM item WHERE item_id = ?");
			$stmt->execute([$adId]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];
		}

		public function getAdsByCategory($categoryId){
			$approval = 1;
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE category_id = ? AND approval = ?");
			$stmt->execute([$categoryId, $approval]);
			return $stmt;
		}

		public function postAd($categoryId, $userId, $price, $picture, $phone, $itemName, $date, $description){
			$stmt = $this->dbconnection->prepare("INSERT INTO item (category_id, user_id, price, picture, phone, item_name, date_posted, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			$status = $stmt->execute([$categoryId, $userId, $price, $picture, $phone, $itemName, $date, $description]);
			return $status;
		}

		public function editAd($itemId, $price, $phone, $itemName, $description){
			$stmt = $this->dbconnection->prepare("UPDATE item SET price = ?, phone = ?, item_name = ?, description = ? WHERE item_id = ?");
			$stmt->execute([$price, $phone, $itemName, $description, $itemId]);
		}

		public function deleteAd($itemId, $userId){
			$stmt = $this->dbconnection->prepare("DELETE FROM item WHERE item_id = ? AND user_id = ?");
			$status = $stmt->execute([$itemId, $userId]);
			return $status;
		}

		public function searchAds($searchString){
			$search = "%$searchString%";
			$index = 1;
			$stmt = $this->dbconnection->prepare("SELECT * FROM item WHERE item_name LIKE ? AND approval = ?");
			$stmt->execute([$search, $index]);

			return $stmt;
		}

	}

?>