<?php
	set_error_handler("error_handler");

	function error_handler($errno, $errstr){
		echo "An error occured";
	}
?>