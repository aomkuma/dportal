<?php

	header('Content-Type: text/html; charset=utf-8');

	$DB_NAME = 'dpo';
	$DB_HOST = 'localhost';
	$DB_USER = 'root';
	$DB_PASS = '';


	$con = mysql_pconnect($DB_HOST, $DB_USER, $DB_PASS);
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}

	mysql_select_db($DB_NAME, $con) or die ("Database not found.");
	mysql_query('SET NAMES utf8');
	mysql_query("SET character_set_results=utf8", $con);
	mysql_query("SET character_set_client=utf8", $con);
	mysql_query("SET character_set_connection=utf8", $con);

	$SQL = "SELECT UserID, RegionID FROM tbl_account";
	$result = mysql_query($SQL);
	while ($thisrow=mysql_fetch_row($result))  //get one row at a time
	{
		mysql_query("INSERT INTO tbl_person_region (UserID, RegionID) VALUES('".$thisrow[0]."','".$thisrow[1]."');");
	}

?>