<?php
	/**
	* News reporter class to hold functionalities and attributes for newspaper reporters section of the app
	*Author: Abba Bawa
	*Created: 09/01/2017
	*/

	require_once("DBConnection.php");
	require_once("News.php");

	class News_reporter extends News
	{
		private $dbconnection;
		private $fName;
		private $lName;
		private $institutionId;
		private $adminId;

		public function __construct($adminId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$query = $this->dbconnection->prepare("SELECT * FROM admin WHERE admin_id = ?");
			$query->execute([$adminId]);
			$result = $query->fetch(PDO::FETCH_ASSOC);
			$this->adminId = $result['admin_id'];
			$this->fName = $result['f_name'];
			$this->lName = $result['l_name'];
			$this->institutionId = $result['institution_id'];
			parent::__construct($this->institutionId);
		}

		public function viewMyArticles(){
			$query = $this->dbconnection->prepare("SELECT n.*, a.f_name, a.l_name FROM newspaper_story n, admin a WHERE n.writer_id = a.admin_id AND writer_id = ?");
			$query->execute([$this->adminId]);
			return $query;
		}

		public function postArticle($headline, $category, $created, $paragraphs, $pic){
			$query = $this->dbconnection->prepare("INSERT INTO newspaper_story (institution_id, writer_id, headline, created, story_category_id, picture) VALUES (?, ?, ?, ?, ?, ?)");
			$status = $query->execute([$this->institutionId, $this->adminId, $headline, $created, $category, $pic]);
			if ($status) {
				$id = $this->dbconnection->query("SELECT LAST_INSERT_ID()");
				$last = $id->fetch(PDO::FETCH_NUM);
				$lastId = $last[0];
				$i = 0;
				while($i < count($paragraphs)){
					$value = $paragraphs[$i];
					$query = $this->dbconnection->prepare("INSERT INTO newspaper_story_paragraphs (story_id, paragraph) VALUES (?, ?)");
					$status = $query->execute([$lastId, $value]);
					$i++;
				}
				return true;
			}
			else{
				return "false";
			}
		}

		public function editMyArticle($story, $headline, $modified, $paragraphs){
			$query = $this->dbconnection->prepare("UPDATE newspaper_story SET headline = ?, modified = ? WHERE story_id = ?");
			$status = $query->execute([$headline, $modified, $story]);
			if ($status) {
				$id = $this->dbconnection->query("SELECT LAST_INSERT_ID()");
				$last = $id->fetch(PDO::FETCH_NUM);
				$lastId = $last[0];
				$i = 0;
				while($i < count($paragraphs)){
					$value = $paragraphs[$i];
					$query = $this->dbconnection->prepare("UPDATE newspaper_story_paragraphs ");
					$status = $query->execute([$lastId, $value]);
					$i++;
				}
				return true;
			}
			else{
				return "false";
			}
		}

		public function viewProfile($adminId){
			$query = $this->dbconnection->prepare("SELECT * FROM admin WHERE admin_id = ?");
			$query->execute([$adminId]);
			return $query;
		}

		public function editProfile($adminId, $fname, $lname, $username){
			$query = $this->dbconnection->prepare("UPDATE admin set username = ?, f_name = ?, l_name = ? WHERE admin_id = ?");
			$status = $query->execute([$username, $fname, $lname, $adminId]);
			return $query;
		}
	}

	/*$reporter = new News_reporter(3);
	$paragraphs = array("This is paragraph1", "This is paragraph2", "This is paragraph3", "This is paragraph4");
	echo $reporter->postArticle("This is a headline", 1, "2017-06-04",$paragraphs );
	//print_r($reporter->viewMyArticles()->fetchAll(PDO::FETCH_ASSOC));*/
?>