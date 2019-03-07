<?php
	/**
	* SUG class to hold functionalities and attributes for SUG section of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/
	require_once("DBConnection.php");
	require_once("Section.php");
	class SUG extends Section
	{
		private $institutionId;
		private $dbconnection;
		private $instName;

		function __construct($institutionId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$stmt = $this->dbconnection->prepare("SELECT * FROM institution WHERE institution_id = ?");
			$stmt->execute([$institutionId]);
			$institution = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->institutionId = $institution['institution_id'];
			$this->instName = $institution['institution_name'];
		}

		public function getId(){
			return $this->institutionId;
		}

		public function getName(){
			return $this->instName;
		}

		public function getOfficials(){
			$query = $this->dbconnection->prepare("SELECT s.official_id, u.f_name, u.l_name, u.profile_pic, a.appointment FROM sug s, user u, sug_appointment a WHERE s.user_id = u.user_id AND s.app_id = a.app_id AND s.institution_id = ?");
			$query->execute([$this->institutionId]);
			
			return $query;
		}

		public function getOfficial($officialId){
			$query = $this->dbconnection->prepare("SELECT s.official_id, u.f_name, u.l_name, u.profile_pic, a.appointment FROM sug s, user u, sug_appointment a WHERE s.user_id = u.user_id AND s.app_id = a.app_id AND s.official_id = ?");
			$query->execute([$officialId]);
			
			return $query;
		}

		public function getEvents(){
			$query = $this->dbconnection->prepare("SELECT * FROM sug_events WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			
			return $query;
		}


		public function getProjects(){
			$query = $this->dbconnection->prepare("SELECT * FROM sug_projects WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			
			return $query;
		}

		public function getProjectImages($projectId){
			$query = $this->dbconnection->prepare("SELECT * FROM  sug_project_images i WHERE project_id = ?");
			$query->execute([$projectId]);
			
			return $query;
		}

		public function getInstitutionParagraphs(){
			$query = $this->dbconnection->prepare("SELECT * FROM institution_paragraphs WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			
			return $query;
		}

		public function getInstitutionImages(){
			$query = $this->dbconnection->prepare("SELECT * FROM institution_images WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			
			return $query;
		}

		public function getPosts(){
			$query = $this->dbconnection->prepare("SELECT * FROM sug_posting WHERE institution_id = ?");
			$query->execute([$this->institutionId]);
			
			return $query;
		}

		public function getPostParagraphs($postingId){
			$query = $this->dbconnection->prepare("SELECT * FROM sug_posting_paragraphs WHERE posting_id = ?");
			$query->execute([$postingId]);
			
			return $query;
		}
	}

	/*$uj = new SUG(1);
	$officials = $uj->getOfficials();
	while($official = $officials->fetch(PDO::FETCH_ASSOC)){
		echo $official['f_name']." ".$official['l_name']."<br>";
	}
	echo $uj->getName()."<br>";

	$events = $uj->getEvents();
	while($event = $events->fetch(PDO::FETCH_ASSOC)){
		echo $event['start_date']." ".$event['event']."<br>";
	}*/

	/*$info = $uj->getInstitutionInfo();
	while($inst = $info->fetch(PDO::FETCH_ASSOC)){
		echo $inst['paragraph']." ".$inst['image']." ".$inst['caption']."<br>";
	}*/

	/*$projects = $uj->getProjectDetails(6);
	while($project = $projects->fetch(PDO::FETCH_ASSOC)){
		echo $project['name']." ".$project['project_details']." ".$project['image']."<br>";
	}*/
?>