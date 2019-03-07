<?php

	/**
	* User class to hold functionalities for artists of the app
	*Author: Abba Bawa
	*Created: 21/06/2017
	*/
	require_once("DBConnection.php");

	class Artist
	{
		private $userId;
		private $artistId;
		
		function __construct($userId)
		{
			if (isset($userId)) {
				$conn = new Database();
				$this->dbconnection = $conn->getConnection();
				$stmt = $this->dbconnection->prepare("SELECT * FROM artist WHERE user_id = ?");
				$stmt->execute([$userId]);
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->userId = $res['user_id'];
				$this->artistId = $res['artist_id'];
			}
		}



		public function uploadSong($path, $title, $pic){
			$upload_date = date("y-m-d", strtotime("today"));
			$stmt = $this->dbconnection->prepare("INSERT INTO artist_songs (artist_id, song_title, song_path, album_art, upload_date) VALUES (?, ?, ?, ?, ?)");
			$status = $stmt->execute([$this->artistId, $title, $path, $pic, $upload_date]);
			return $status;
		}

		public function deleteSong($songId){
			$stmt = $this->dbconnection->prepare("DELETE FROM artist_songs WHERE song_id = ?");
			$stmt->execute([$songId]);
			return $stmt;
		}

		public function viewMySongs(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM artist_songs WHERE artist_id = ?");
			$stmt->execute([$this->artistId]);
			return $stmt;
		}

		
	}

?>