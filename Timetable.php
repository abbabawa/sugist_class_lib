<?php
	/**
	* TimeTable class to manage users timetables(school/reading timetable)
	*Author: Abba Bawa
	*Created: 10/07/2017
	*/
	require_once("DBConnection.php");
	class Timetable 
	{
		private $userId;
		
		function __construct($userId)
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
			$stmt = $this->dbconnection->prepare("SELECT * FROM user WHERE user_id = ?");
			$stmt->execute([$userId]);
			$this->userId = $userId;
		}

		public function saveByDay($day, $courses, $time){
			$i = 0;
			$res = 0;
			while ($i < count($courses)) {
				$stmt = $this->dbconnection->prepare("INSERT INTO user_time_table (course, day, course_time, user_id) VALUES (?, ?, ?, ?)");
				$res = $stmt->execute([$courses[$i], $day, $time[$i], $this->userId]);
				$i++;
			}

			return $res;
		}

		public function getCoursesByDay($day){
			$stmt = $this->dbconnection->prepare("SELECT * FROM user_time_table WHERE day = ? AND user_id = ? ORDER BY course_time ASC");
			$stmt->execute([$day, $this->userId]);
			return $stmt;
		}

		public function deleteCourse($id){
			$stmt = $this->dbconnection->prepare("DELETE FROM user_time_table WHERE id = ? AND user_id = ?");
			$res = $stmt->execute([$id, $this->userId]);
			return $res;
		}

		public function getSelectedTime($course){
			$selected = array("", "", "", "", "", "", "", "", "", "");
			
                            switch ($course) {
                                case '8:00 - 9:00':
                                    $selected[0] = "selected";
                                    break;

                                case '9:00 - 10:00':
                                    $selected[1] = "selected";
                                    break;

                                case '10:00 - 11:00':
                                    $selected[2] = "selected";
                                    break;

                                case '11:00 - 12:00':
                                    $selected[3] = "selected";
                                    break;
                                

                                case '12:00 - 1:00':
                                    $selected[4] = "selected";
                                    break;

                                case '1 - 2':
                                    $selected[5] = "selected";
                                    break;


                                case '2:00 - 3:00':
                                    $selected[6] = "selected";
                                    break;


                                case '3:00 - 4:00':
                                    $selected[7] = "selected";
                                    break;

                                case '4:00 - 5:00':
                                    $selected[8] = "selected";
                                    break;


                                case '5:00 - 6:00':
                                    $selected[9] = "selected";
                                    break;
                                default:
                                    # code...
                                    break;
                            }
            

            return $selected;
		}
	}
?>