<?php
if (isset($_POST['button_nf'], $_POST['nf_name'], $_POST['nf_wid']))
{
	$facname = tools_sanitize_data($_POST['nf_name']);
	$worldid = tools_sanitize_data($_POST['nf_wid']);
	

	$httpReferer = tools_get_referer("index.php");
	
	// check that we can actually make a faction (no faction yet associating this
	// user and world)
	if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Factions WHERE UserID = ? AND WorldID = ? LIMIT 1"))
	{
		$stmt->bind_param('ss', $userid, $worldid);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($UserCount);
		$stmt->fetch();

		if ($UserCount > 0) { throw_msg(402, $httpReferer); }
	}
	else { throw_msg(300, $httpReferer, "create_faction.php", 30); }


	//get an unused websafe name
	$foundFreeSafeName = false;
	$nameIndex = 1; //the number added onto the end
	$webName = tools_web_safe($username);
	$analysisName = $webName;
	while(!$foundFreeSafeName)
	{
		//check current iteration
		if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Factions WHERE NameSafe = ? AND WorldID = ? LIMIT 1"))
		{
			$stmt->bind_param('ss', $analysisName, $worldid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($UserSafeCount);
			$stmt->fetch();

			if ($UserSafeCount > 0) { $nameIndex++; $analysisName = $webName . $nameIndex; continue; } //haven't found one yet
		}
		else { throw_msg(300, $httpReferer, "register.php", 50); }
		$foundFreeSafeName = true;
	}

	//assign safe websafe name to official websafe name variable
	$webName = $analysisName;

	$regDate = date("Y-m-d");
	$level = 0;

	// insert faction
	if ($stmt = $mysqli->prepare("INSERT INTO Factions(UserID, WorldID, Name, NameSafe, Joined) VALUES (?, ?, ?, ?, ?)"))
	{
		$userid = LOGGED_USER_ID;
		//set variables
		$stmt->bind_param("sssss", $userid, $worldid, $facname, $webName, $regDate);
		
		$result = $stmt->execute();
		$errorMSG = $stmt->error;
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 86); }
	
	throw_msg(100, "index.php");
}
?>
