<?php
	/**
	* General Admin class to hold functionalities and attributes for General admin of the app
	*Author: Abba Bawa
	*Created: 3/1/2017
	*/

	require_once("DBConnection.php");
	require_once("Section.php");

	class General_admin extends Section
	{

		private $adminId;
		private $fName;
		private $lName;
		private $dbconnection;
		
		function __construct($adminId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$stmt = $this->dbconnection->prepare("SELECT * FROM admin WHERE admin_id = ? AND section_id = 1");
			$stmt->execute([$adminId]);
			$admin = $stmt->fetch(PDO::FETCH_ASSOC);
			$this->fName = $admin['f_name'];
			$this->lName = $admin['l_name'];
			$this->adminId = $admin['admin_id'];
		}

		public function getId(){
			return $this->institutionId;
		}

		public function getName(){
			return $this->instName;
		}

		public function getInstitutions(){
			$query = $this->dbconnection->prepare("SELECT i.institution_id, i.institution_name, t.institution_type FROM institution i, institution_type t WHERE t.type_id = i.type_id");
			$query->execute();
			return $query;
		}

		public function getUsers($institutionId){
			$query = $this->dbconnection->prepare("SELECT * FROM user WHERE institution_id = ?");
			$query->execute([$institutionId]);
			return $query;
		}

		public function getUser($userId){
			$query = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
			$query->execute([$userId]);
			return $query;
		}

		public function deleteUser($userId){
			$query = $this->dbconnection->prepare("DELETE FROM user WHERE user_id = ?");
			$query->execute([$userId]);
			return $query;
		}

		public function getAdmins($institutionId, $sectionId){
			$query = $this->dbconnection->prepare("SELECT * FROM admin WHERE institution_id = ? and section_id = ?");
			$query->execute([$institutionId, $sectionId]);
			return $query;
		}

		public function addAdmin($institutionId, $sectionId, $username){
			$query = $this->dbconnection->prepare("INSERT INTO admin (institution_id, section_id, username) VALUES (?, ?, ?)");
			$status = $query->execute([$institutionId, $sectionId, $username]);
			if($status){
				return true;
			}
			else{
				return false;
			}
		}

		public function deleteAdmin($adminId){
			$query = $this->dbconnection->prepare("DELETE FROM admin WHERE admin_id = ?");
			$status = $query->execute([$adminId]);
			if($status){
				return true;
			}
			else{
				return false;
			}
		}

	}

	/*$admin = new General_admin(1);
	echo $admin->deleteAdmin(7);
	exit();

	while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
		echo $user['f_name'];
	}*/
?>