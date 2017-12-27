<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");

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

	// insert world into db
	if ($stmt = $mysqli->prepare("INSERT INTO Worlds(Name, Status, Created) VALUES (?, ?, ?)"))
	{
		//set variables
		$stmt->bind_param("sss", $worldname, $status, $date);
		
		$result = $stmt->execute();
		$errorMSG = $stmt->error;
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 86); }


	throw_msg(100, $httpReferer);
}

?>
