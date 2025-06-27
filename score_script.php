<?php
	///////////////////////////////////////////////////////
	// Online Score Script
	// Jeff Vance 
	// Version 1.3
	// Files and sources can be found at www.flyinvinteractive.com
	//////////////////////////////////////////////////////
	
	//////////////////////////////////////////////////////
	// WARNING AND READ THIS!
	// You don't need to edit this file
	// The only file to edit is config.php
	// Don't edit this unless you know what your doing :)
	// This works out of the box -- your error must be in config.php
	/////////////////////////////////////////////////////
	
	// Get Configuation file
	require("config.php");
	
	// Connect to your server
    $db=mysql_connect($host,$user,$pass) or die (mysql_error());
	@mysql_select_db($dbname) or die (mysql_error());
		
	//////////////////////////////////////////////////
	// Check for the existing table if its not found create it
	// This is really just here to make the life of new users of the script eaiser
	// They won't have to go thru the script and create the table
	/////////////////////////////////////////////////

	if(!mysql_num_rows( mysql_query("SHOW TABLES LIKE '".$tname."'")))
	{
	$query = "CREATE TABLE `$tname` (`id` int(11) NOT NULL auto_increment,`gameid` varchar(255) NOT NULL,`playername` varchar(255) NOT NULL,`score` int(255) NOT NULL,`scoredate` varchar(255) NOT NULL,`md5` varchar(255) NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

	$create_table = mysql_query($query)or die (mysql_error());
	}
	
	///////////////////////////////////////////////////////
	// Status Checker
	///////////////////////////////////////////////////////
	if ($_GET["status"])
	{
	echo "online";
	exit;
	}
	
	////////////////////////////////////////////////////////
	// Run some checks on our gameid 
	////////////////////////////////////////////////////////
	$gameid_safe = mysql_real_escape_string($_GET["gameid"]);
	// Check the gameid is numeric
	// If its not numberic lets exit
	if(!is_numeric($gameid_safe))
    {
     exit; 
    }

	///////////////////////////////////////////////////////
	// Upload new score
	///////////////////////////////////////////////////////
	// Test for the variables submitted by the player
	// If they exist upload into the database

	if ($_GET["playername"] && $_GET["gameid"] && $_GET["score"])
	{
	
	// Strip out | marks submitted in the name or score
	$playername_safe = str_replace("|","_",$_GET["playername"]);
	$playername_safe = mysql_real_escape_string($playername_safe);
	$score_safe = mysql_real_escape_string($_GET["score"]);
	$date = date('M d Y');
		
	// Check the score sent is is numeric
	// If the score is not numberic lets exit
	if(!is_numeric($score_safe))
    {
     exit; 
    }
	
	// this secret key needs to be the same as the secret key in your game.
	$security_md5= md5($_GET["gameid"].$_GET["playername"].$_GET["score"].$secret_key);
	
	// Check for submitted MD5 different then server generated MD5
	if ($security_md5 <>$_GET["code"])
	{
	// Something is wrong -- MD5 security hash is different
	// Could be someone trying to insert bogus score data
	exit;
	}
	// Everything is cool -- Insert the data into the database
	$query = "insert into $tname(gameid,playername,score,scoredate,md5) values ('$gameid_safe','$playername_safe','$score_safe','$date','$security_md5')";
	$insert_the_data = mysql_query($query)or die(mysql_error());
	}
		
	///////////////////////////////////////////////////////
	// List high score
	///////////////////////////////////////////////////////
	// Return a list of high scores with "|" as the delimiter
	if ($gameid_safe)
	{
    $query = "select * from $tname where gameid='$gameid_safe' order by score desc limit 10";
	$view_data = mysql_query($query)or die(mysql_error());
	while($row_data = mysql_fetch_array($view_data))
		{
		print($row_data["playername"]);
		print "|";
		print ($row_data["score"]);
		print ("|");
		print($row_data["scoredate"]);
		print("|");
		}
	
	// We limit the score database to hold the number defined in the config script
	// First check to see how many records we have for this game
  
	$query1 ="select * from $tname where gameid = '$gameid_safe'";
	$countresults = mysql_query($query1)or die(mysql_error());
	$countofdeletes = mysql_num_rows($countresults);
	if (mysql_num_rows($countresults)>$score_number)
		{
		$query2 ="SELECT * FROM $tname WHERE gameid = '$gameid_safe' ORDER BY score DESC Limit $score_number,$countofdeletes";
		$Get_data = mysql_query($query2)or die (mysql_error());
		while($row_data = mysql_fetch_array($Get_data))
		{
		$id_delete = $row_data["id"];
		$query3 = "Delete from $tname where id = $id_delete";
		$Delete_data = mysql_query($query3)or die (mysql_error());
		}
		}
	}
		
?>
