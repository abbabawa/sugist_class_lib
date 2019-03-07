<?php
	/**
	* News class to hold functionalities and attributes for Online newspaper section of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/

	require_once("DBConnection.php");
	require_once("Section.php");

	class News extends Section
	{
		private $dbconnection;
		private $name;
		private $institutionId;
		private $published = 1;

		public function __construct($institutionId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$query = $this->dbconnection->prepare("SELECT institution_id, newspaper FROM institution WHERE institution_id = ?");
			$query->execute([$institutionId]);
			$result = $query->fetch(PDO::FETCH_ASSOC);
			$this->name = $result['newspaper'];
			$this->institutionId = $result['institution_id'];
		}

		public function getId(){

		}

		public function getName(){
			return $this->name;
		}

		public function getNewsArticle($storyId){
			$query = $this->dbconnection->prepare("SELECT n.*, a.f_name, a.l_name FROM newspaper_story n, admin a WHERE writer_id = admin_id AND story_id = ?");
			$query->execute([$storyId]);
			return $query;
		} 

		public function getNewsArticleParagraphs($storyId){
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story_paragraphs WHERE story_id = ?");
			$query->execute([$storyId]);
			return $query;
		}

		public function getNewsArticlesByDate($pDate){

			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE publish_date = ? AND approval = ?");
			$query->execute([$pDate, 2]);
			return $query;
		}
		
		public function getTopStories(){
		    date_default_timezone_set("Africa/Lagos");
		    $publishDate = date("Y-m-d", time());
		    $query = $this->dbconnection->prepare("SELECT i.story_id, h.headline FROM newspaper_headlines i, newspaper_story h WHERE i.story_id = h.story_id AND h.publish_date = ? LIMIT 9");
			$query->execute([$publishDate]);
			return $query;
		}
		
		

		public function getNewsArticlesByCategory($category){
			$published = 2;
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE story_category_id = ? AND approval = ?");
			$query->execute([$category, 2]);
			return $query;
		}

		public function searchArticle($storyId, $searchKey){
			$keyword = "%$searchKey%";
			$published = 1;
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story_keywords WHERE story_id = ? AND keyword LIKE ? AND approval = ?");
			$query->execute([$storyId, $keyword, $this->published]);
			return $query;
		}

		public function getCategories(){
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story_categories");
			$query->execute();
			return $query;
		}

		public function slideShowPics(){
			$value = 2;
			$query = $this->dbconnection->prepare("SELECT picture FROM newspaper_story WHERE approval = ?");
			$query->execute([$value]);
			return $query;
		}
	}

	/*$news = new News(1);
	//echo $news->getName();
	print_r($news->searchArticle(1000, "SUG")->fetchAll(PDO::FETCH_ASSOC));*/
?>