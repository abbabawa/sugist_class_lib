<?php
	/**
	* SUG Admin class to hold functionalities and attributes for SUG admin section of the app
	*Author: Abba Bawa
	*Created: 2/1/2017
	*/

	require_once("DBConnection.php");
	require_once("Section.php");
	require_once("SUG.php");

	class SUG_admin extends SUG
	{
		private $adminId;
		private $institutionId;
		private $dbconnection;

		function __construct($adminId)
		{

			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$stmt = $this->dbconnection->prepare("SELECT * FROM admin WHERE admin_id = ? AND section_id = 2");
			$stmt->execute([$adminId]);
			$admin = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->institutionId = $admin['institution_id'];
			$this->adminId = $admin['admin_id'];
			parent::__construct($admin['institution_id']);
		}

		public function getInstitutionId(){
			return $this->institutionId;
		}

		public function addOfficial($userId, $post){
			$check = $this->dbconnection->prepare("SELECT * FROM sug WHERE app_id = ? AND institution_id = ?");
			$check->execute([$post, $this->institutionId]);
			
			if($check->rowCount() > 0){
				return "error: The post you tried to enter has already been filled";
			}
			$query = $this->dbconnection->prepare("INSERT INTO sug (app_id, user_id, institution_id) VALUES (?, ?, ?)");
			$status = $query->execute([$post, $userId, $this->institutionId]);
			if($status){
				return "official inserted successfully";
			}
			else{
				return "Insertion was unsuccessful";
			}
			exit();
		}

		public function deleteOfficial($officialId){
			//TO-DO: check if ID exists in table before trying to delete
			$query = $this->dbconnection->prepare("DELETE FROM sug WHERE official_id = ?");
			$status = $query->execute([$officialId]);
			if($status){
				echo "Deleted";
			}
			else{
				echo "unable to delete";
			}
		}

		public function isPositionFilled($position){
			$check = $this->dbconnection->prepare("SELECT * FROM sug WHERE institution_id = ? AND app_id = ?");
			$check->execute([$this->institutionId, $position]);
			
			if(count($check->rowCount()) != 0){
				return "true";
			}
			else{
				return "false";
			}
		}

		public function savePost($post, $title, $author){
			$datePosted = "2017-04-19"; //Get server date
			$query = $this->dbconnection->prepare("INSERT INTO sug_posting (admin_id, institution_id, title, date_posted, author) VALUES (?, ?, ?, ?, ?)");
			$status = $query->execute([$this->adminId, $this->institutionId, $title, $datePosted, $author]);
			if($status){
				$i=0;
				$id = $this->dbconnection->query("SELECT LAST_INSERT_ID()");
				$last = $id->fetch(PDO::FETCH_NUM);
				$lastId = $last[0];
				while($i < count($post)){
					$value = $post[$i];
					$query = $this->dbconnection->prepare("INSERT INTO sug_posting_paragraphs (posting_id, paragraph) VALUES (?, ?)");
					$status = $query->execute([$lastId, $value]);
					$i++;
				}
				echo "Your post was saved successfully";
			}
			else{
				echo "Sorry Your post could not be saved, please try again later";
			}
		}

		public function deletePost($postId){
			$query = $this->dbconnection->prepare("DELETE FROM sug_posting WHERE posting_id = ?");
			$status = $query->execute([$postId]);
			if($status){
				echo "The post has been deleted";
			}
			else{
				echo "Sorry the post could not be deleted, please try  again later";
			}
		}

		public function saveEvent($event, $startDate, $institutionId){
			$query = $this->dbconnection->prepare("INSERT INTO sug_events(event, start_date, institution_id) VALUES (?, ?, ?)");
			$status = $query->execute([$event, $startDate, $institutionId]);
			if($status){
				echo "The event was saved successfully";
			}
			else{
				echo "Sorry the event could not be saved, please try again later";
			}
		}

		public function deleteEvent($eventId){
			$query = $this->dbconnection->prepare("DELETE FROM sug_events WHERE event_id = ?");
			$status = $query->execute([$eventId]);
			if($status){
				echo "The event was deleted successfully";
			}
			else{
				echo "Sorry the event could not be deleted, please try again later";
			}
		}

		//saveProject function to save information about a project executed by the SUG. It accepts project name, date project was completed, details about the project and an array of image
		public function saveProject($projectName, $dateCompleted, $details, $images, $institutionId, $path){
			$query = $this->dbconnection->prepare("INSERT INTO sug_projects(name, date_completed, project_details, institution_id) VALUES (?, ?, ?, ?)");
			$status = $query->execute([$projectName, $dateCompleted, $details, $institutionId]);
			if($status){
				$i = 0;
				$id = $this->dbconnection->query("SELECT LAST_INSERT_ID()");
				$last = $id->fetch(PDO::FETCH_NUM);
				$lastId = $last[0];
				while ($i < count($images)) {
					$query = $this->dbconnection->prepare("INSERT INTO sug_project_images(project_id, image) VALUES (?, ?)");
					$imageValue = $path."".$images[$i];
					$status = $query->execute([$lastId, $imageValue]);
					//Consider roll back options
					$i++;
				}
				echo "The project was saved successfully";
			}
			else{
				echo "Sorry the project could not be saved, please try again later";
			}
		}

		//saveInstitutionInfo function to store information about an institution. It accepts an array of paragraphs, and array of images and an array of captions for each image and stores them individually in the database.
		public function saveInstParagraphs($paragraphs){
			$i = 0;
			while ($i < count($paragraphs)) {
				$paragraph = $paragraphs[$i];
				$query = $this->dbconnection->prepare("INSERT INTO institution_paragraphs (institution_id, paragraph) VALUES (?, ?)");
				$status = $query->execute([$this->institutionId, $paragraph]);
				$i++;

			}	
		}

		public function saveInstImages($images, $captions, $path){
			$i = 0;
			while ($i < count($images)) {
				$image = $path."".$images[$i];
				$caption = $captions[$i];
				$query = $this->dbconnection->prepare("INSERT INTO institution_images (institution_id, image, caption) VALUES (?, ?, ?)");
				$status = $query->execute([$this->institutionId, $image, $caption]);
				$i++;
			}		
		}

		public function search($searchString){
			$search = "%$searchString%";

			$stmt = $this->dbconnection->prepare("SELECT * FROM user WHERE f_name LIKE ? OR l_name LIKE ?");
			$stmt->execute([$search, $search]);
			return $stmt;
		}
	}

	$admin = new SUG_admin(2);
	//echo $admin->addOfficial(3, 0);
	//echo $admin->deleteOfficial(3);
	//echo $admin->savePost("SUG welcomes its students back from the christmas break", "Welcome back", "2017-01-02", "SUG President");
	//echo $admin->saveEvent("Wlecome Special", "2017-02-18");
	//echo $admin->deletePost(0);

	//$var = array("abba", "bawa", "solomon", "joshua");
	
	//$admin->saveProject("Bus project", "2017-04-18", "Bus prject executed to alleviate the transport problem of students", $var);

	/*$paragraphs = array("Paragraph1", "Paragraph2", "Paragraph3", "Paragraph4");
	$images = array("image1", "image2", "image3", "image4", "image5");
	$captions = array("caption1", "caption2", "caption3", "caption4", "caption5");

	$admin->saveInstitutionInfo($paragraphs, $images, $captions);*/

	//print_r($admin->getEvents()->fetch(PDO::FETCH_ASSOC));
?>