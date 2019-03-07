<?php
	/**
	* News class to hold functionalities and attributes for Online newspaper section of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/

	require_once("DBConnection.php");
	require_once("Market.php");
	require_once("News.php");
	require_once("People.php");
	require_once("User.php");
	require_once("Jobs.php");
	require_once("Entertainment.php");
	require_once("Timetable.php");
	//require_once("Section.php");

	class Home 
	{
		private $dbconnection;
		private $name;
		private $institutionId;
		private $userId;
		private $market;
		private $news;
		private $user;
		private $jobs;

		private $instInfo = array( array('title'=>"Connecting students with the SUG",
									'author'=>"SUGist admin",
									'date_posted'=>"Today"),
								array('title'=>"Encouraging Accountability",
									'author'=>"SUGist admin",
									'date_posted'=>"Today"),
								array('title'=>"Keeping students informed",
									'author'=>"SUGist admin",
									'date_posted'=>"Today"),
								array('title'=>"Encouraging Unionism among students",
									'author'=>"SUGist admin",
									'date_posted'=>"Today")
							);
		private $topStories = array( array('headline'=> "SUGist comes to University of Jos"),
									 array('headline'=> "Stay up to date with SUGist"),
									 array('headline'=> "Get the latest news on SUGist"),
									 array('headline'=> "Entertainment news"),
									 array('headline'=> "Sports news"),
									 array('headline'=> "Politics"),
									 array('headline'=> "Educational news"),
									 array('headline'=> "Tech news"),
									 array('headline'=> "Fashion"));

		public function __construct($userId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$query = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
			$query->execute([$userId]);
			$result = $query->fetch(PDO::FETCH_ASSOC);
			$this->institutionId = $result['institution_id'];
			$this->userId = $result['user_id'];
		}

		public function getSUGStatus(){
			$query = $this->dbconnection->prepare("SELECT * FROM institution WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			return $query->fetch(PDO::FETCH_ASSOC)['sug'];
		}

		public function getSUGPosts(){
			$query = $this->dbconnection->prepare("SELECT * FROM sug_posting WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			return $query;
		}

		public function getDefaultSUGPosts(){
			return $this->instInfo;
		}

		public function getMarketAds(){
			$market = new Market();
			$result = $market->getHomeAds();
			return $result;
		}

		public function getNewsStatus(){
			$query = $this->dbconnection->prepare("SELECT * FROM institution WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			return $query->fetch(PDO::FETCH_ASSOC)['newspaper'];
		}

		public function getTopStories(){
			$news = new News($this->institutionId);
			return $news->getTopStories();
		}

		public function getDefaultTopStories(){
			return $this->topStories;
		}

		public function suggestedPeople(){
			$this->user = new User($this->userId);
			return $this->user->getSuggestions(); 
		}
		
		public function getMessageHeads(){
		    $this->user = new User($this->userId);
		    return $this->user->getMessageHeads();
		}
		
		public function getLastMessage($chatIden){
		    $this->user = new User($this->userId);
		    return $this->user->getLastMessage($chatIden);
		}
		
		public function getUserName($userId){
		    $this->user = new User($this->userId);
		    return $this->user->getUserName($userId);
		}
		
		public function getInstitutionName(){
		    $this->user = new User($this->userId);
		    return $this->user->getInstitutionName();
		}
		
		public function getJobCategories(){
			$this->jobs = new Jobs($this->userId);
			return $this->jobs->getCategories();
		}
		
		public function getArtistOfTheMonth(){
			$this->entertainment = new Entertainment($this->userId);
			return  $this->entertainment->getArtistOfTheMonth();
		}

		public function checkArtistOfTheMonth(){
			$status = $this->getArtistOfTheMonth();
			return $status->fetch(PDO::FETCH_ASSOC)['artist_id'];
		}
		
		public function getSongs(){
			$this->entertainment = new Entertainment($this->userId);
			return  $this->entertainment->getTop5();
		}

		public function checkSongs(){
			$status = $this->getSongs();
			return $status->rowCount();
		}
		
		public function getCourses(){
			$timetable = new Timetable($this->userId);
			return $timetable->getCoursesByDay(date('l'));
		}

		public function checkCourses(){
			$status = $this->getCourses($this->userId);
			return $status->rowCount();
		}
	}

	
?>