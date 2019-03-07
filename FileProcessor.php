<?php
	/**
	* Image processor class to hold methods for uploading and deleting images
	*Author: Abba Bawa
	*Created: 4/1/2017
	*/
	require_once("DBConnection.php");

	
	class FileProcessor 
	{
		private $dbconnection;

		function __construct()
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
		}

		//funtion to process uploaded file name it uniquely and return file path to caller
		public function saveFile($name, $path, $title){
			if($_FILES[$name]['error'] > 0){
				echo 'Problem: ';
				switch($_FILES[$name]['error']){
					case 1: echo 'File exceeded upload_max_filesize'; break;
					case 2: echo 'File exceeded max_file_size'; break;
					case 3: echo 'File only partially uploaded'; break;
					case 4: echo 'No file uploaded'; break;
				}
				exit;
			}
			$filename = $_FILES[$name]['name'];
			//Check if the file is an image
			$ext = substr($filename, strrpos($filename, '.') + 1);
			if($_FILES[$name]['type'] != 'audio/mpeg3' /*|| $_FILES[$name]['type'] != 'audio/x-wav'*/ && ($ext == ".mp3")){
				echo 'Problem: file is not an audio file';
				exit;
			}
			
			//Save file where you want it to be
			//$newName = time()."".basename($_FILES[$name]['name']);
			$title = str_replace(" ", "_", $title);
			$imagepath = $path."".$title.".mp3";
			$image = basename($_FILES[$name]['name']);
			
			if(is_uploaded_file($_FILES[$name]['tmp_name'])){
				if(!move_uploaded_file($_FILES[$name]['tmp_name'], $imagepath)){
					echo 'Problem: could not move file to destination directory f';
					exit;
				}
			}
			else{
				echo 'Poblem: Possible file upload attack. Filename: 1';
				echo $_FILES[$name]['name'];
				exit;
			}
			return $title.".mp3";
		}

		public function saveMultipleImages($name, $path){
			$i = 0;
			$imagesArray = array();
			foreach ($_FILES[$name]['name'] as $f => $value) {
				if($_FILES[$name]['error'][$f] > 0){
					echo 'Problem: ';
					switch($_FILES[$name]['error'][$f]){
						case 1: echo 'File exceeded upload_max_filesize'; break;
						case 2: echo 'File exceeded max_file_size'; break;
						case 3: echo 'File only partially uploaded'; break;
						case 4: echo 'No file uploaded'; break;
					}
					exit;
				}
				$filename = $_FILES[$name]['name'][$f];
				//Check if the file is an image
				$ext = substr($filename, strrpos($filename, '.') + 1);
				
				if($_FILES[$name]['type'][$f] != 'image/jpeg' && ($ext == "jpg")){
					echo 'Problem: file is not an image';
					exit;
				}
				
				//Save file where you want it to be
				$newName = $i."".time()."".basename($_FILES[$name]['name'][$f]);
				
				$imagepath = $path."".$newName;
				//$image = basename($_FILES[$name]['name'][$f]);
				
				if(is_uploaded_file($_FILES[$name]['tmp_name'][$f])){
					if(!move_uploaded_file($_FILES[$name]['tmp_name'][$f], $imagepath)){
						echo 'Problem: could not move file to destination directory';
						exit;
					}
				}
				else{
					echo 'Poblem: Possible file upload attack. Filename: ';
					echo $_FILES[$name]['name'][$f];
					exit;
				}
				$imagesArray[$i] = $newName;
				$i++;
			}
			return $imagesArray;
		}

		public function deleteImage(){

		}
	}
?>