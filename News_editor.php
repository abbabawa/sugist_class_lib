<?php
	/**
	* News reporter class to hold functionalities and attributes for newspaper reporters section of the app
	*Author: Abba Bawa
	*Created: 09/01/2017
	*/

	require_once("DBConnection.php");
	require_once("News.php");

	class News_editor extends News
	{
		private $dbconnection;
		private $fName;
		private $lName;
		private $institutionId;
		private $adminId;

		function __construct($adminId)
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

		public function getNewsArticle($storyId){
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE story_id = ?");
			$query->execute([$storyId]);
			return $query;
		} 

		public function getPendingArticles(){
			$status = 0;
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE approval = ? AND institution_id = ?");
			$query->execute([$status, $this->institutionId]);
			return $query;
		}

		public function approveArticle($articleId){
			$status = 1;
			$query = $this->dbconnection->prepare("UPDATE newspaper_story SET approval = ?, approved_by = ? WHERE story_id = ?");
			$result = $query->execute([$status, $this->adminId, $articleId]);
			if ($result) {
				return true;
			}
			else{
				return false;
			}
		}

		public function getArticlesApprovedByMe(){
			$status = 1;
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE approval = ? AND approved_by = ?");
			$query->execute([$status, $this->adminId]);
			return $query;
		}

		public function publishArticle($storyId){
		    date_default_timezone_set("Africa/Lagos");
			$displayDate = date("Y-m-d", time() + (1 * 24 * 60 * 60));
			$status = 2;
			$query = $this->dbconnection->prepare("UPDATE newspaper_story SET approval = ?, publish_date = ? WHERE story_id = ?");
			$result = $query->execute([$status, $displayDate, $storyId]);
			if ($result) {
				return true;
			}
			else{
				return false;
			}
		}

		public function getApprovedArticles(){
			$status = 1;
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE approval = ?");
			$query->execute([$status]);
			return $query;
		}

		public function getTomorrowsArticles(){
		    date_default_timezone_set("Africa/Lagos");
			$displayDate = date("Y-m-d", time() + (1 * 24 * 60 * 60));
			$status = 2;
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE approval = ? AND publish_date = ?");
			$query->execute([$status, $displayDate]);
			return $query;
		}

		public function getNonHeadlineArticles(){
		    date_default_timezone_set("Africa/Lagos");
			$displayDate = date("Y-m-d", time() + (1 * 24 * 60 * 60));
			$status = 2;
			$query = $this->dbconnection->prepare("SELECT * FROM newspaper_story WHERE story_id NOT IN(SELECT story_id FROM newspaper_headlines) AND approval = ? AND publish_date = ?");
			$query->execute([$status, $displayDate]);
			return $query;
		}
		
		public function getTomorrowTopStories(){
		    date_default_timezone_set("Africa/Lagos");
		    $publishDate = date("Y-m-d", time() + (1 * 24 * 60 * 60));
		    $query = $this->dbconnection->prepare("SELECT i.story_id, h.headline FROM newspaper_headlines i, newspaper_story h WHERE i.story_id = h.story_id AND h.publish_date = ? LIMIT 9");
			$query->execute([$publishDate]);
			return $query;
		}

		public function setHeadline($stories){
			foreach ($stories as $key => $value) {
			    $headlineCount = $this->dbconnection->prepare("SELECT COUNT(story_id) AS total FROM newspaper_headlines");
			    $headlineCount->execute();
			    if($headlineCount->fetch(PDO::FETCH_ASSOC)['total'] > 9){
			        break;
			    }
				$query = $this->dbconnection->prepare("INSERT INTO newspaper_headlines (story_id) VALUES (?)");
				$query->execute([$value]);
			}
		}
		
		public function deleteHeadline($headlineId){
		    $query = $this->dbconnection->prepare("DELETE FROM newspaper_headlines WHERE story_id = ?");
			$status = $query->execute([$headlineId]);
			return $status;
		}
		
		public function addCategory($category){
		    $query = $this->dbconnection->prepare("INSERT INTO newspaper_story_categories (name) VALUES (?)");
			$query->execute([$category]);
			return $query;
		}
	}

	/*$editor = new News_editor(5);
	echo $editor->publishArticle(1003);*/
?>