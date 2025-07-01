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
	
	// Database connection variable
	$db = null;
	
	// Check if the host string contains a port number
	$hostStrArray = explode(":",$host);
	
	// Connect to your server 
	if(count($hostStrArray) == 1)
	{
		// default port
		$db = mysqli_connect($host,$user,$pass,$dbname) or die (mysqli_error($db));
	}
	elseif (count($hostStrArray) == 2)
	{
		// user-specified port
		$db = mysqli_connect($hostStrArray[0],$user,$pass,$dbname,$hostStrArray[1]) or die (mysqli_error($db));
	}
	else
	{
		// default port
		$db = mysqli_connect($host,$user,$pass,$dbname) or die (mysqli_error($db));
	}
	
	// Select database
	@mysqli_select_db($db,$dbname) or die (mysqli_error($db));
		
	//////////////////////////////////////////////////
	// Check for the existing table if its not found create it
	// This is really just here to make the life of new users of the script eaiser
	// They won't have to go thru the script and create the table
	/////////////////////////////////////////////////

	if(!mysqli_num_rows( mysqli_query($db,"SHOW TABLES LIKE '".$tname."'")))
	{
		$query = "CREATE TABLE `$tname` (`id` int(11) NOT NULL auto_increment,`gameid` varchar(255) NOT NULL,`playername` varchar(255) NOT NULL,`score` int(255) NOT NULL,`scoredate` varchar(255) NOT NULL,`md5` varchar(255) NOT NULL, PRIMARY KEY  (`id`)) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

		$create_table = mysqli_query($db,$query)or die (mysqli_error($db));
	}
	
	///////////////////////////////////////////////////////
	// Status Checker
	///////////////////////////////////////////////////////
	if (isset($_GET["status"]))
	{
		echo "online";
		exit;
	}
	
	////////////////////////////////////////////////////////
	// Run some checks on our gameid 
	////////////////////////////////////////////////////////
	$gameid_safe = "empty";
	if (isset($_GET["gameid"]))
	{
		$gameid_safe = mysqli_real_escape_string($db,$_GET["gameid"]);
	}
	
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

	if (isset($_GET["playername"]) && isset($_GET["gameid"]) && isset($_GET["score"]) && isset($_GET["code"]))
	{
		
		// Strip out | marks submitted in the name or score
		$playername_safe = str_replace("|","_",$_GET["playername"]);
		$playername_safe = mysqli_real_escape_string($db,$playername_safe);
		$score_safe = mysqli_real_escape_string($db,$_GET["score"]);
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
		$insert_the_data = mysqli_query($db,$query)or die(mysqli_error($db));
	}
		
	///////////////////////////////////////////////////////
	// List high score
	///////////////////////////////////////////////////////
	// Return a list of high scores with "|" as the delimiter
	if ($gameid_safe)
	{
		$query = "select * from $tname where gameid='$gameid_safe' order by score desc limit 10";
		$view_data = mysqli_query($db,$query)or die(mysqli_error($db));
		while($row_data = mysqli_fetch_array($view_data))
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
		$countresults = mysqli_query($db,$query1)or die(mysqli_error($db));
		$countofdeletes = mysqli_num_rows($countresults);
		if (mysqli_num_rows($countresults)>$score_number)
		{
			$query2 ="SELECT * FROM $tname WHERE gameid = '$gameid_safe' ORDER BY score DESC Limit $score_number,$countofdeletes";
			$Get_data = mysqli_query($db,$query2)or die (mysqli_error($db));
			while($row_data = mysqli_fetch_array($Get_data))
			{
				$id_delete = $row_data["id"];
				$query3 = "Delete from $tname where id = $id_delete";
				$Delete_data = mysqli_query($db,$query3)or die (mysqli_error($db));
			}
		}
	}
		
?>