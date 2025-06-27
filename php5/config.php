<?php
	///////////////////////////////////////////////////////
	// Online Score Script
	// Jeff Vance 
	// Version 1.3
	// Files and sources can be found at www.flyinvinteractive.com
	//////////////////////////////////////////////////////

	// You need to fill in this data from your own mySQL server
	
	// Your host -- for example localhost or mysql.server.com
	$host = '';
	
	// Your user name for mySQL
	$user = '';
	
	// Your password for mySQL
    $pass = '';
	
	// Your database name for mySQL
	$dbname= '';

	// ATTENTION
	// This is your secret key - Needs to be the same as the secret key in your game
	// You can change this but remember to change it in your game.
	// This is used to help secure the score and produce MD5 hashes
    $secret_key = "this is secret";
	
	// Your table name for mySQL
	// You can change this is you wish
	$tname= 'scores';
	
	// Number of scores to save for each gameid
	// Feel free to change this but the example file only lists 10 scores
	// You would need to code this
	$score_number = '10';
	
?>