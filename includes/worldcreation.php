<?php

if (isset($_POST['button_createworld'], $_POST['cw_worldname']))
{
	$worldname = tools_sanitize_data($_POST['cw_worldname']);
	$status = 0;
	$date = date("Y-m-d");

	$httpReferer = tools_get_referer("index.php");


	//check database to make sure this world name doesn't already exist
	if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Worlds WHERE Name = ? LIMIT 1"))
	{
		$stmt->bind_param('s', $worldname);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($count);
		$stmt->fetch();

		if ($count > 0) { throw_msg(402, $errorHttpReferer); }
	}
	else { throw_msg(300, $httpReferer, "admin.php", 23); }

	$webName = tools_iterative_web_safe($worldname, "Worlds", $httpReferer);
		
	// insert world into db
	if ($stmt = $mysqli->prepare("INSERT INTO Worlds(Name, Status, Created, NameSafe) VALUES (?, ?, ?, ?)"))
	{
		//set variables
		$stmt->bind_param("ssss", $worldname, $status, $date, $webName);
		
		$result = $stmt->execute();
		$errorMSG = $stmt->error;
	}
	else { throw_msg(300, $httpReferer, "worldcreation.php", 86); }

	// world creation stuff
	
	$worldid = $mysqli->insert_id;
	$userid = 0;
	$name = "Raiders";
	$webName = "raiders";
	$regDate = date("Y-m-d");
	
	// create raider faction
	if ($stmt = $mysqli->prepare("INSERT INTO Factions(UserID, WorldID, Name, NameSafe, Joined) VALUES (?, ?, ?, ?, ?)"))
	{
		$userid = LOGGED_USER_ID;
		//set variables
		$stmt->bind_param("sssss", $userid, $worldid, $name, $webName, $regDate);
		
		$result = $stmt->execute();
		$errorMSG = $stmt->error;
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 86); }


	createBunkers($worldid, 20, $httpReferer);


	
	 

	throw_msg(100, $httpReferer);
}

function createBunkers($worldid, $count, $httpReferer)
{
	global $mysqli;
		
	$min = 0;
	$max = 100;
	
	for ($i = 0; $i < $count; $i++)
	{
		$x = rand($min, $max);
		$y = rand($min, $max);

		$factionid = 0;

		// insert into database
		if ($stmt = $mysqli->prepare("INSERT INTO Bunkers(WorldID, FactionID, WorldX, WorldY) VALUES (?, ?, ?, ?)"))
		{
			//set variables
			$stmt->bind_param("ssss", $worldid, $factionid, $x, $y);
			
			$result = $stmt->execute();
			$errorMSG = $stmt->error;
		}
		else { throw_msg(300, $httpReferer, "admin.php", 86); }
	}	
}

?>
