<?php
	/**
	* People class to hold functionalities and attributes for People section of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/

	require_once("DBConnection.php");
	require_once("Section.php");

	class People extends Section
	{
		private $dbconnection;

		function __construct()
		{
			$conn = new Database();
			$this->dbconnection = $conn->getConnection();
		}

		public function getId(){

		}

		public function getName(){

		}

		public function setName($name){

		}
	}
?>