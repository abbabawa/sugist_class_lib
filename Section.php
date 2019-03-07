<?php
	/**
	* Section class to hold functionalities and attributes common to all sections of the app
	*Author: Abba Bawa
	*Created: 29/12/2016
	*/

	require_once("DBConnection.php");

	abstract class Section
	{
		private $sectionId;
		private $sectionName;
		private $dbconnection;

		public abstract function getId();

		public abstract function getName();

		
	}
?>