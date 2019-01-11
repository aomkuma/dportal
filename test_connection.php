<?php

	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=sakornn1_buddy', 'sakorn_buddy', '1234');
	    foreach($dbh->query('SELECT * from adminnotification_inbox') as $row) {
	        print_r($row);
	    }
	    $dbh = null;
	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage() . "<br/>";
	    die();
	}

?>