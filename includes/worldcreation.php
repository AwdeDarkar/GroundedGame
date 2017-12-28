<?php

if (isset($_POST['button_createworld'], $_POST['cw_worldname']))
{
	$worldname = tools_sanitize_data($_POST['cw_worldname']);
	$status = 0;
	$date = date("Y-m-d");
	
	$numBunkers = 20;
	$numDeposits = $numBunkers * 10;

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
		//set variables
		$stmt->bind_param("sssss", $userid, $worldid, $name, $webName, $regDate);
		
		$result = $stmt->execute();
		$errorMSG = $stmt->error;
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 86); }


	createBunkers($worldid, $numBunkers, $httpReferer);
	generateDeposits($worldid, $numDeposits, $numBunkers, $httpReferer);


	
	 

	throw_msg(100, $httpReferer);
}


function startingResourceCollections($worldid, $httpReferer)
{

	// get list of bunker ids
	if ($stmt = $mysqli->prepare("SELECT ID FROM Bunkers WHERE WorldID = ?"))
	{
		$stmt->bind_param('s', $worldid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($bunkerid);
		while ($stmt->fetch())
		{
			
			//miner and smelter
			$minerid = 6;
			$smelterid = 5;
			$facid = 0;
			$amount = 1;

			if ($stmt = $mysqli->prepare("INSERT INTO ResourceCollections (ResourceID,BunkerID,FactionID,Amount) VALUES (?, ?, ?, ?)"))
			{
				//set variables
				$stmt->bind_param("ssss", $minerid, $factionid, $facid, $amount);
				$stmt->execute();
			}
			else { throw_msg(300, $httpReferer, "admin.php", 86); }
			if ($stmt = $mysqli->prepare("INSERT INTO ResourceCollections (ResourceID,BunkerID,FactionID,Amount) VALUES (?, ?, ?, ?)"))
			{
				//set variables
				$stmt->bind_param("ssss", $smelterid, $factionid, $facid, $amount);
				$stmt->execute();
			}
			else { throw_msg(300, $httpReferer, "admin.php", 86); }
			
		}
	}
	else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


	
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

function generateDeposits($worldid, $numDeposits, $numBunkers, $httpReferer)
{
	global $mysqli;
	
	$depositTypes = array();
	$depositType;
	$depositFreq;
	
	if ($stmt = $mysqli->prepare("
	SELECT Resources.ID, Resources.Frequency FROM Resources
	WHERE Resources.Type = 1 " /* 1 means depositable */ . "
	"))
	{
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($depositType, $depositFreq);
		
		while($stmt->fetch())
		{
			for($i = 0; $i < $depositFreq; $i++)
			{
				array_push($depositTypes, $depositType);
			}
		}
	}

	$min = 1;
	$max = 1000;
	
	for($i = 0; $i < $numDeposits; $i++)
	{
		//$bunker = rand(0, $numBunkers);
		$bunker = $i % $numBunkers;
		$index = array_rand($depositTypes);
		$type = $depositTypes[$index];
		$amount = rand($min, $max);
		$rate = 0;
		
		// insert into database
		if ($stmt = $mysqli->prepare("INSERT INTO ResourceDeposits(BunkerID, ResourceID, Amount, ReplenishRate, Maximum) VALUES (?, ?, ?, ?, ?)"))
		{
			//set variables
			$stmt->bind_param("sssss", $bunker, $type, $amount, $rate, $amount);
			
			$result = $stmt->execute();
			$errorMSG = $stmt->error;
		}
		else { throw_msg(300, $httpReferer, "admin.php", 86); }
	}
}

?>
