<?php

	
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

	$SQL = "SELECT * FROM tbl_repaired_type";
	$result = mysql_query($SQL);

	while ($thisrow=mysql_fetch_row($result))  //get one row at a time
	{
		foreach($thisrow as $k1 => $v1){
			echo $v1.' ';
		}
		echo "<br>";
		$SQL1 = "SELECT * FROM tbl_repaired_title WHERE RepairedTypeID = '" . $thisrow[0] . "'";
		$result1 = mysql_query($SQL1);
		while ($thisrow1=mysql_fetch_row($result1))  //get one row at a time
		{
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			foreach($thisrow1 as $k2 => $v2){
				echo $v2.' ';
			}
			echo '<br>';
			$SQL2 = "SELECT * FROM tbl_repaired_issue WHERE RepairedTitleID = '" . $thisrow1[0] . "'";
			$result2 = mysql_query($SQL2);
			while ($thisrow2=mysql_fetch_row($result2))  //get one row at a time
			{
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				foreach($thisrow2 as $k3 => $v3){
					echo $v3.' ';
				}
				echo '<br>';
				$SQL3 = "SELECT * FROM tbl_repaired_sub_issue WHERE RepairedIssueID = '" . $thisrow2[0] . "'";
				$result3 = mysql_query($SQL3);
				while ($thisrow3=mysql_fetch_row($result3))  //get one row at a time
				{
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					foreach($thisrow3 as $k4 => $v4){
						echo $v4.' ';
					}
					echo '<br>';
				}
				echo '<br>';
			}
			echo '<br>';
		}

		echo '---------------------------------<br>';
	}

?>