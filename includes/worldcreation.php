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
		
	//get an unused websafe name
	$foundFreeSafeName = false;
	$nameIndex = 1; //the number added onto the end
	$webName = tools_web_safe($worldname);
	$analysisName = $webName;
	while(!$foundFreeSafeName)
	{
		//check current iteration
		if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Worlds WHERE NameSafe = ? LIMIT 1"))
		{
			$stmt->bind_param('s', $analysisName);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($WorldSafeCount);
			$stmt->fetch();

			if ($WorldSafeCount > 0) { $nameIndex++; $analysisName = $webName . $nameIndex; continue; } //haven't found one yet
		}
		else { throw_msg(300, $httpReferer, "admin.php", 50); }
		$foundFreeSafeName = true;
	}

	//assign safe websafe name to official websafe name variable
	$webName = $analysisName;

	// insert world into db
	if ($stmt = $mysqli->prepare("INSERT INTO Worlds(Name, Status, Created, NameSafe) VALUES (?, ?, ?, ?)"))
	{
		//set variables
		$stmt->bind_param("ssss", $worldname, $status, $date, $webName);
		
		$result = $stmt->execute();
		$errorMSG = $stmt->error;
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 86); }

	// world creation stuff
	
	




	
	 

	throw_msg(100, $httpReferer);
}

?>
