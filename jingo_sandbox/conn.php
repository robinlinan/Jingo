<?php

	//Create a database connection
	$conn = mysql_connect("localhost", "root", "root");
	if(!$conn){
		die("Database connection failed: " . mysql_error());
	}
	//Select a database to use
	$db_select = mysql_select_db("jingodb", $conn);
	if(!$db_select){
		die("Database selection failed: " . mysql_error());
	}
?>

