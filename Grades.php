<?php
	/**
	* Grades class to hold functionalities for grades section of the app
	*Author: Abba Bawa
	*Created: 26/01/2017
	*/
	require_once("DBConnection.php");
	class Grades
	{
		private $A = 4;
		private $B = 3;
		private $C = 2;
		private $D = 1;
		private $E = 0;
		private $F = 0;
		private $totalGrade;
		private $totalCU;
		
		private $totalGradepl;
		private $totalCUpl;

		private $userId;
		private $dbconnection;
		function __construct($userId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$this->userId = $userId;
		}

		public function saveGrade($level, $semester, $course, $cu, $grade){
			if ($semester < 1 || $semester > 2) {
				return 0;
			}
			$stmt = $this->dbconnection->prepare("INSERT INTO grade (level, semester, course, credit_load, grade, user_id) VALUES (?, ?, ?, ?, ?, ?)");
			$status = $stmt->execute([$level, $semester, $course, $cu, $grade, $this->userId]);
			return $status;
		}

		public function getHighestLevel(){
			$stmt = $this->dbconnection->prepare("SELECT MAX(level) FROM grade");
			$stmt->execute();
			return $stmt;
		}

		public function getGrades(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM grade WHERE user_id = ? ORDER BY level, semester ASC");
			$stmt->execute([$this->userId]);
			return $stmt;
		}
		
		public function deleteGrade($gradeId){
		    $stmt = $this->dbconnection->prepare("DELETE FROM grade WHERE grade_id = ?");
			$stmt->execute([$gradeId]);
			return $stmt;
		}

		public function getGradesPerLevel($level){
			$stmt = $this->dbconnection->prepare("SELECT * FROM grade WHERE user_id = ? AND level = ? ORDER BY level, semester ASC");
			$stmt->execute([$this->userId, $level]);
			return $stmt;
		}

		public function calculateGP(){
			
			$savedGrades = $this->getGrades();
			while ($res = $savedGrades->fetch(PDO::FETCH_ASSOC)) {
				switch ($res['grade']) {
					case 'A':
						$hold = $this->A * $res['credit_load'];
						break;

					case 'B':
						$hold = $this->B * $res['credit_load'];
						break;

					case 'C':
						$hold = $this->C * $res['credit_load'];
						break;

					case 'D':
						$hold = $this->D * $res['credit_load'];
						break;

					case 'E':
						$hold = $this->E * $res['credit_load'];
						break;

					case 'F':
						$hold = $this->F * $res['credit_load'];
						break;
					
					default:
						$hold = 0;
						break;
				}

				$this->totalGrade += $hold;
				$this->totalCU += $res['credit_load'];
			}

			$val = 0;
			if ($this->totalCU != 0) {
				$val = $this->totalGrade / $this->totalCU;
			}

			return round($val, 2);
		}

		public function calculateGPPerLevel($level){
			
			$savedGrades = $this->getGradesPerLevel($level);
			while ($res = $savedGrades->fetch(PDO::FETCH_ASSOC)) {
				switch ($res['grade']) {
					case 'A':
						$hold = $this->A * $res['credit_load'];
						break;

					case 'B':
						$hold = $this->B * $res['credit_load'];
						break;

					case 'C':
						$hold = $this->C * $res['credit_load'];
						break;

					case 'D':
						$hold = $this->D * $res['credit_load'];
						break;

					case 'E':
						$hold = $this->E * $res['credit_load'];
						break;

					case 'F':
						$hold = $this->F * $res['credit_load'];
						break;
					
					default:
						$hold = 0;
						break;
				}

				$this->totalGradepl += $hold;
				$this->totalCUpl += $res['credit_load'];
			}
			$result = 0;
			if ($this->totalCUpl != 0) {	
				$result = $this->totalGradepl / $this->totalCUpl;
			}
            
            $this->totalGradepl = 0;
            $this->totalCUpl = 0;
            
			return round($result, 2);
		}

		public function getCurrentCGP(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM grade WHERE user_id = ?");
			$stmt->execute([$this->userId]);
			$cgp = 0;
			while ($res = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$cgp;
				$hold;
				switch ($res['grade']) {
					case 'A':
						$hold = $this->A * $res['credit_load'];
						break;

					case 'B':
						$hold = $this->B * $res['credit_load'];
						break;

					case 'C':
						$hold = $this->C * $res['credit_load'];
						break;

					case 'D':
						$hold = $this->D * $res['credit_load'];
						break;

					case 'E':
						$hold = $this->E * $res['credit_load'];
						break;

					case 'F':
						$hold = $this->F * $res['credit_load'];
						break;
					
					default:
						$hold = 0;
						break;
				}
				$cgp += $hold;  
			}
			return $cgp;
		}

		public function getCurrentTotalCU(){
			$stmt = $this->dbconnection->prepare("SELECT * FROM grade WHERE user_id = ?");
			$stmt->execute([$this->userId]);
			$cu = 0;
			while($res = $stmt->fetch(PDO::FETCH_ASSOC)){
				$cu += $res['credit_load'];
			}

			return $cu;
		}

		public function predictGP($courses, $courseCUs, $grades){
			
			for ($i=0; $i < count($grades); $i++) { 
				switch ($grades[$i]) {
					case 'A':
						$hold = $this->A * $courseCUs[$i];
						break;

					case 'B':
						$hold = $this->B * $courseCUs[$i];
						break;

					case 'C':
						$hold = $this->C * $courseCUs[$i];
						break;

					case 'D':
						$hold = $this->D * $courseCUs[$i];
						break;

					case 'E':
						$hold = $this->E * $courseCUs[$i];
						break;

					case 'F':
						$hold = $this->F * $courseCUs[$i];
						break;
					
					default:
						$hold = 0;
						break;
				}

				$this->totalGrade += $hold;
				$this->totalCU += $courseCUs[$i];
			}

			$val = 0;
			$currentCU = $this->getCurrentTotalCU();
			$currentCGP = $this->getCurrentCGP();
			if ($this->totalCU != 0) {
				$this->totalGrade += $currentCGP;
				$this->totalCU += $currentCU;
				$val = $this->totalGrade / $this->totalCU;
			}

			return round($val, 2);
		}


	}
?>