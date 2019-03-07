<?php
	
	/**
	* User class to hold functionalities for entertainment section of the app
	*Author: Abba Bawa
	*Created: 21/06/2017
	*/
	require_once("DBConnection.php");

	class Entertainment	
	{

		private $userId;
		private $institutionId;
		private $dbconnection;
		
		function __construct($userId)
		{
			if (isset($userId)) {
				$conn = new Database();
				$this->dbconnection = $conn->getConnection();
				$stmt = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
				$stmt->execute([$userId]);
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				$this->userId = $res['user_id'];
				$this->institutionId = $res['institution_id'];
			}
		}

		public function getArtists(){

		}
		
		public function isArtist(){
		    $stmt = $this->dbconnection->prepare("SELECT * FROM artist WHERE user_id = ?");
			$stmt->execute([$this->userId]);
			return $stmt->rowCount();
		}

		public function getArtistName($artistId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM artist WHERE artist_id = ?");
			$stmt->execute([$artistId]);
			$artist = $stmt->fetch(PDO::FETCH_ASSOC);
			return $artist['stage_name'];
		}
		
		public function getArtistPic($artistId){
			$stmt = $this->dbconnection->prepare("SELECT a.artist_id, u.profile_pic FROM artist a, user u WHERE a.user_id = u.user_id AND a.artist_id = ?");
			$stmt->execute([$artistId]);
			$artist = $stmt->fetch(PDO::FETCH_ASSOC);
			return $artist['profile_pic'];
		}

		public function becomeAnArtist($stageName){
			$stmt = $this->dbconnection->prepare("INSERT INTO artist (stage_name, user_id) VALUES (?, ?)");
			$status = $stmt->execute([$stageName, $this->userId]);
			return $status;
		}

		public function viewSongs(){
			$stmt = $this->dbconnection->prepare("SELECT s.*, a.*, u.* FROM artist_songs s, artist a, user u WHERE s.artist_id = a.artist_id AND a.user_id = u.user_id AND u.institution_id = ?");
			$stmt->execute([$this->institutionId]);
			return $stmt;
		}

		public function getSong($songId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM artist_songs WHERE song_id = ?");
			$stmt->execute([$songId]);
			return $stmt;
		}

		public function getArtistSongs($artistId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM artist_songs WHERE artist_id = ?");
			$stmt->execute([$artistId]);
			return $stmt;
		}

		public function downloadSong($songId){
			$stmt = $this->dbconnection->prepare("SELECT * FROM artist_songs WHERE song_id = ?");
			$stmt->execute([$this->institutionId]);
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$song = "songs/Asegir.mp3";//$res['song_path'];
			$filename = $res['song_title'];

			if (file_exists($song) && is_readable($song)) {
				$size = filesize($song);
				header('Content-Type: application/octet-stream');
				header('Content-Length: '.$size);
				header('Content-Disposition: attachment; filename='.$filename);
				header('Content-Transfer-Encoding: binary');

				$file = @fopen($song, 'rb');
				if ($file) {
					fpassthru($file);
					exit();
				}
				else{
					echo $err;
				}
			}
			else{
				$err;
			}
		}
		
		public function logDownload($songId){
			$stmt = $this->dbconnection->prepare("INSERT INTO artist_song_download (song_id, user_id) VALUES (?, ?)");
			$res = $stmt->execute([$songId, $this->userId]);

			//log download in songs table **reconsider**
			$stmt = $this->dbconnection->prepare("SELECT downloads FROM artist_songs WHERE song_id = ?");
			$stmt->execute([$songId]);
			$downloads = $stmt->fetch(PDO::FETCH_ASSOC)['downloads'];
			$downloads++;

			$stmt = $this->dbconnection->prepare("UPDATE artist_songs SET downloads = ? WHERE song_id = ? LIMIT 1");
			$res = $stmt->execute([$downloads, $songId]);
			return $res;
		}

		public function getNumberOfDownloads($songId){
			$stmt = $this->dbconnection->prepare("SELECT COUNT(song_id) AS numOfDownloads FROM artist_song_download WHERE song_id = ?");
			$stmt->execute([$songId]);
			return $stmt->fetch(PDO::FETCH_ASSOC)['numOfDownloads'];
		}

		
		public function getTop5(){
			$stmt = $this->dbconnection->prepare("SELECT s.*, a.*, u.* FROM artist_songs s, artist a, user u WHERE s.artist_id = a.artist_id AND a.user_id = u.user_id AND u.institution_id = ? ORDER BY downloads DESC LIMIT 5");
			$stmt->execute([$this->institutionId]);
			return $stmt;
		}

		public function search($searchString){
			$searchString = "%$searchString%";
			$stmt = $this->dbconnection->prepare("SELECT s.*, a.*, u.* FROM artist_songs s, artist a, user u WHERE s.artist_id = a.artist_id AND a.user_id = u.user_id AND u.institution_id = ? AND song_title LIKE ? ORDER BY downloads DESC");
			$stmt->execute([$this->institutionId, $searchString]);
			return $stmt;
		}
		
		public function getArtistOfTheMonth(){
			$stmt = $this->dbconnection->prepare("SELECT m.*, a.*, u.institution_id, u.profile_pic FROM artists_of_the_month m, artist a, user u WHERE u.institution_id = ? AND m.artist_id = a.artist_id AND a.user_id = u.user_id ORDER BY m.win_date DESC LIMIT 1");
			$stmt->execute([$this->institutionId]);
			return $stmt;
		}

		public function commentOnSong(){}

		public function vote(){}
	}

?>