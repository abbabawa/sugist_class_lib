<?php
	/**
	* User class to hold functionalities for users of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/
	require_once("DBConnection.php");
	class User
	{
		private $userId;
		private $fName;
		private $lName;
		private $noOfFriends;
		private $institution;
		private $dbconnection;
		private $profPic;

		function __construct($userId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$stmt = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
			$stmt->execute([$userId]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->userId = $user['user_id'];
			$this->fName = $user['f_name'];
			$this->lName = $user['l_name'];
			$this->institution = $user['institution_id'];
			$this->profPic = $user['profile_pic'];
			
		}

		public function getId(){
			return $this->userId;
		}

		public function getName(){
			return $this->fName." ".$this->lName;
		}

		public function getProfilePic(){
			return $this->profPic;
		}

		/*public function getName($id){
			$stmt = $this->dbconnection->prepare("SELECT f_name, l_name, profile_pic FROM user WHERE user_id = ?");
			$stmt->execute([$id]);
			return $stmt;
		}*/

		public function getInstitution(){
			return $this->institution;

		}
		
		public function getInstitutionName(){
		    $stmt = $this->dbconnection->prepare("SELECT institution_name FROM institution WHERE institution_id = ?");
			$stmt->execute([$this->institution]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['institution_name'];
		}

		public function getProfile($id){
			$stmt = $this->dbconnection->prepare("SELECT u.* FROM user u WHERE u.user_id = ?");
			$stmt->execute([$id]);
			return $stmt;
		}
		
		public function changePassword($oldPassword, $newPassword){
		    $query = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
			$query->execute([$this->userId]);
			
			if($query->rowCount() > 0){
			    $hash = md5($newPassword);
			     $query = $this->dbconnection->prepare("UPDATE user SET password = ? WHERE user_id = ?");
			    $query->execute([$hash, $this->userId]);
			    
			    return 1;
			}
			else{
			    return 0;
			}
		}

		public function getNumberOfFriends($id){
			$stmt = $this->dbconnection->prepare("SELECT COUNT(friend_id) AS total FROM  friendship WHERE friend_1 = ? OR friend_2 = ?");
			$stmt->execute([$id, $id]);
			$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

			return $total;
		}
		
		public function getNotification(){
		    $stmt = $this->dbconnection->prepare("SELECT * FROM  notification WHERE user_id = ?");
			$stmt->execute([$this->userId]);

			return $stmt;
		}
		
		public function updateReadNotifications(){
		    $stmt = $this->dbconnection->prepare("UPDATE notification SET viewed = ? WHERE user_id = ?");
			$stmt->execute([1, $this->userId]);

			return $stmt;
		}
		
		public function countUnreadNotifications(){
		    $stmt = $this->dbconnection->prepare("SELECT COUNT(notification_id) AS number FROM notification WHERE viewed = ? AND user_id = ?");
			$stmt->execute([0, $this->userId]);

			return $stmt->fetch(PDO::FETCH_ASSOC)['number'];
		}
		
		public function countReadNotifications(){
		     $stmt = $this->dbconnection->prepare("SELECT COUNT(notification_id) AS number FROM notification WHERE viewed = ? AND user_id = ?");
			$stmt->execute([1, $this->userId]);

			return $stmt->fetch(PDO::FETCH_ASSOC)['number'];
		}
		
		public function getAllNotifications(){
		    $stmt = $this->dbconnection->prepare("SELECT n.*, s.name FROM  notification n, sections s WHERE n.section = s.section_id AND n.user_id = ? ORDER BY date_sent ASC");
			$stmt->execute([$this->userId]);

			return $stmt;
		}

		public function editProfile($fName, $lName, $gender, $dob, $phone, $state, $level){
			$stmt = $this->dbconnection->prepare("UPDATE user SET f_name = ?, l_name = ?, gender = ?, date_of_birth = ?, phone = ?, state = ?, level = ? WHERE user_id = ?");
			$status = $stmt->execute([$fName, $lName, $gender, $dob, $phone, $state, $level, $this->userId]);
			return $status;
		}
		
		public function getStates(){
		    $stmt = $this->dbconnection->prepare("SELECT * FROM state");
			$stmt->execute();
			return $stmt;
		}
		
		public function getState($id){
		    $stmt = $this->dbconnection->prepare("SELECT * FROM state WHERE state_id = ?");
			$stmt->execute([$id]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['state'];
		}

		public function updateProfilePic($path){
			$stmt = $this->dbconnection->prepare("UPDATE user SET profile_pic = ? WHERE user_id = ?");
			$status = $stmt->execute([$path, $this->userId]);
			return $status;
		}

		public function getFriends($startId){
			$friends = array();
			$stmt = $this->dbconnection->prepare("SELECT * FROM  friendship WHERE (friend_1 = ? OR friend_2 = ?) AND friend_id > ? LIMIT 10");
			$stmt->execute([$this->userId, $this->userId, $startId]);
            
			$i = 0;
			while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
				if ($result['friend_1'] != $this->userId) {
					$friends[$i] = $result['friend_1'];
				}
				else{
					$friends[$i] = $result['friend_2'];
				}
				$i++;
			}
			//return $friends;
            
			$in  = str_repeat('?,', count($friends) - 1) . '?';

			$query = $this->dbconnection->prepare("SELECT user_id, f_name, l_name, profile_pic FROM user WHERE user_id IN ($in)");
			$query->execute($friends);
			return $query;
		}
		
		public function getFriendshipId($friendId){
		    $query = $this->dbconnection->prepare("SELECT friend_id FROM friendship WHERE (friend_1 = ? AND friend_2 = ?) OR (friend_1 = ? AND friend_2 = ?)");
			$query->execute([$friendId, $this->userId, $this->userId, $friendId]);
			return $query->fetch(PDO::FETCH_ASSOC)['friend_id'];
		}

		public function getFriendshipStatus($id){
			$query = $this->dbconnection->prepare("SELECT * FROM friend_request WHERE (sender = ? AND recipient = ?) OR (sender = ? AND recipient = ?)");
			$query->execute([$this->userId, $id, $id, $this->userId]);
			if($query->rowCount() > 0){
				$result = $query->fetch(PDO::FETCH_ASSOC);
				if($result['sender'] == $this->userId){
					return 0;
				}elseif ($result['sender'] == $id) {
					return 1;
				}
			}
			else{
				$query = $this->dbconnection->prepare("SELECT * FROM friendship WHERE (friend_1 = ? AND friend_2 = ?) OR (friend_1 = ? AND friend_2 = ?)");
				$query->execute([$this->userId, $id, $id, $this->userId]);

				if ($query->rowCount() > 0) {
					return 2;
				}
				else{
					return -1;
				}
				
			}
		}

		public function getFriendDetails($friendId){
			$query = $this->dbconnection->prepare("SELECT user_id, f_name, l_name, profile_pic, email, phone, department, level, state, date_of_birth FROM user WHERE user_id = ?");
			$query->execute([$friendId]);
			return $query;
		}

		public function sendFriendRequest( $recipient){
			$query = $this->dbconnection->prepare("SELECT * FROM friend_request WHERE sender = ? AND recipient = ? OR sender = ? AND recipient = ?");
			$results = $query->execute([$this->userId, $recipient, $recipient, $this->userId]);
			
			if (!$query->rowCount()) {
				$stmt = $this->dbconnection->prepare("INSERT INTO friend_request (sender, recipient) VALUES (?, ?)");
				$check = $stmt->execute([$this->userId, $recipient]);
				    //Send notification
				    $date = date("y-m-d", strtotime("today"));
        			date_default_timezone_set("Africa/Lagos");
        			$time = date("H:i:s");
					$message = "You have a connection request";
					$stmt = $this->dbconnection->prepare("INSERT INTO notification (message, user_id, section, reference, date_sent, time_sent) VALUES (?, ?, ?, ?, ?, ?)");
					$friends = $stmt->execute([$message, $recipient, 3, $recipient, $date, $time]);
				return $check;
			}

			return "false";
		}

		public function getNumOfFriendRequests(){
			$stmt = $this->dbconnection->prepare("SELECT COUNT(sender) AS total FROM  friend_request WHERE recipient = ?");
			$stmt->execute([$this->userId]);
			$total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

			return $total;
		}

		public function getFriendRequests(){
			$stmt = $this->dbconnection->prepare("SELECT r.sender, u.user_id, u.l_name, u.f_name, u.profile_pic FROM friend_request r, user u WHERE r.sender = u.user_id AND r.recipient = ?");
			$stmt->execute([$this->userId]);
			return $stmt;
		}

		public function getFriendRequest($senderId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM friend_request WHERE recipient = ? AND sender = ?");
			$stmt->execute([$this->userId, $senderId]);
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}

		public function processRequest($requestId, $action, $fdate){
			if($action == 0){
				$stmt = $this->dbconnection->prepare("DELETE FROM friend_request WHERE request_id = ?");
				$status = $stmt->execute([$requestId]);
				return $status;
			}
			else{
				//Get request from friend request table
				$stmt = $this->dbconnection->prepare("SELECT * FROM friend_request WHERE request_id = ?");
				$stmt->execute([$requestId]);

				if($a = $stmt->fetch(PDO::FETCH_ASSOC)){
					$stmt = $this->dbconnection->prepare("INSERT INTO friendship (friend_1, friend_2, date) VALUES (?, ?, ?)");
					$friends = $stmt->execute([$a['sender'], $a['recipient'], $fdate]);	
					
					//Send notification
					$date = date("y-m-d", strtotime("today"));
        			date_default_timezone_set("Africa/Lagos");
        			$time = date("H:i:s");
					$message = "Your connection request has been accepted";
					$stmt = $this->dbconnection->prepare("INSERT INTO notification (message, user_id, section, reference, date_sent, time_sent) VALUES (?, ?, ?, ?, ?, ?)");
					$friends = $stmt->execute([$message, $a['sender'], 3, $a['recipient'], $date, $time]);

					if($friends){
						$stmt = $this->dbconnection->prepare("DELETE FROM friend_request WHERE request_id = ?");
						$status = $stmt->execute([$requestId]);
						return $status;		
					}
					return $friends;
				}	
			}
			return "false";
		}
		
		public function getSuggestions(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM user WHERE institution_id = ? AND user_id != ? ORDER BY user_id ASC LIMIT 10");
			$stmt->execute([$this->institution, $this->userId]);
			return $stmt;
		}
		
		public function getForumMembers($lastId){
		    $stmt = $this->dbconnection->prepare("SELECT * FROM user WHERE institution_id = ? AND user_id != ? AND user_id > ?  ORDER BY user_id ASC LIMIT 10");
			$stmt->execute([$this->institution, $this->userId, $lastId]);
			return $stmt;
		}

		public function getMessages($chatIden){
			$stmt = $this->dbconnection->prepare("SELECT * FROM chat WHERE chat_iden = ? ORDER BY chat_date ASC, chat_time ASC");
			$stmt->execute([$chatIden]);
			return $stmt;
		}

		public function getChatDetails($chatIden){
			$start = 0;
			$stop = 1;
			$stmt = $this->dbconnection->prepare("SELECT sender, recipient FROM chat WHERE chat_iden = ? LIMIT ?, ?");
			$stmt->execute([$chatIden, $start, $stop]);
			return $stmt;
		}
		
		public function getChatIden($id){
			$stmt = $this->dbconnection->prepare("SELECT chat_iden FROM chat WHERE (sender = ? AND recipient = ?) OR (recipient = ? AND sender = ?)");
			$stmt->execute([$this->userId, $id, $this->userId, $id]);
			if($stmt->rowCount() > 0){
			    return $stmt->fetch(PDO::FETCH_ASSOC)['chat_iden'];
			}
			else{
			    return 0;
			}
		}

		public function getMessageHeads(){
			$stmt = $this->dbconnection->prepare("SELECT sender, chat_iden FROM chat WHERE recipient = ? UNION SELECT recipient, chat_iden FROM chat WHERE sender = ?");
			$stmt->execute([$this->userId, $this->userId]);
			return $stmt;
		}

		public function getUserName($userId){
			$stmt = $this->dbconnection->prepare("SELECT f_name, l_name FROM user WHERE user_id = ?");
			$stmt->execute([$userId]);
			return $stmt;
		}

		public function getLastMessage($chatId){
			$start = 0;
			$stop = 1;
			$stmt = $this->dbconnection->prepare("SELECT message, chat_date FROM chat WHERE chat_iden = ? ORDER BY chat_date DESC LIMIT ?, ?");
			$stmt->execute([$chatId, $start, $stop]);
			return $stmt;
		}

		public function sendMessage($recipient, $message){
			$stmt = $this->dbconnection->prepare("SELECT chat_iden FROM chat WHERE (recipient = ? AND sender = ?) OR (sender = ? AND recipient = ?)");
			$stmt->execute([$this->userId, $recipient, $this->userId, $recipient]);

			$chatIden = 0;
		
			if ($stmt->rowCount() > 0) {
				$value = $stmt->fetch(PDO::FETCH_ASSOC);
				$chatIden = $value['chat_iden'];
					
			}
			else{
				$query = $this->dbconnection->prepare("SELECT MAX(chat_iden) AS maxId FROM chat");
				$query->execute();
				
				$chatIden = $query->fetch(PDO::FETCH_ASSOC)['maxId'] + 1;
			}
			$date = date("y-m-d", strtotime("today"));
			date_default_timezone_set("Africa/Lagos");
			$time = date("H:i:s");

			$query = $this->dbconnection->prepare("INSERT INTO chat (chat_iden, sender, recipient, message, chat_date, chat_time) values (? , ?, ?, ?, ?, ?)");
			$status = $query->execute([$chatIden, $this->userId, $recipient, $message, $date, $time]);

			if($status){
				return 1;
			}
			else{
				return 0;
			}
			return 0;
		}

		public function search($searchKey){
			$searchString = "%$searchKey%";
			$query = $this->dbconnection->prepare("SELECT user_id, f_name, l_name, profile_pic FROM user WHERE f_name LIKE ? OR l_name LIKE ?");
			$status = $query->execute([$searchString, $searchString]);
			return $query;
		}
		
		public function sendFeedback($feedback){
		    $query = $this->dbconnection->prepare("INSERT INTO feedback (user_id, feedback) VALUES (?, ?)");
			$status = $query->execute([$this->userId, $feedback]);
			return $status;
		}
		
		public function logView(){
		    $date = date("y-m-d", strtotime("today"));
            $time = date("H:i:s");
            $query = $this->dbconnection->prepare("INSERT INTO home_track (view_date, view_time, user_id) VALUES (?, ?, ?)");
        	$query->execute([$date, $time, $this->userId]);
		}


	}
?>