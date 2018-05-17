<?php

function getCurrentWorld()
{
	$world = -1;
	if ($_GET['w']) 
	{ 
		$world = tools_sanitize_data($_GET['w']); 
		$_SESSION['world'] = $world;
	}
	elseif ($_SESSION['world']) { $world = $_SESSION['world']; }

	return $world;
}

function getCurrentBunker()
{
	$bunkerID = -1;
	if ($_get['b'])
	{
		$bunkerID = tools_sanitize_data($_GET['b']);
		$_SESSION['bunker'] = $bunkerID;
	}
	elseif ($_SESSION['bunker']) { $bunkerID = $_SESSION['bunker']; }
	
	return $bunkerID;
}

function getWorldName($worldNameSafe)
{
	global $mysqli;
	
	$httpReferer = tools_get_referer("index.php");
	
	// get world name
	$worldName = "";
	if ($stmt = $mysqli->prepare("SELECT Name FROM Worlds WHERE NameSafe = ? LIMIT 1"))
	{
		$stmt->bind_param('s', $worldNameSafe);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($worldName);
		$stmt->fetch();
	}
	else { throw_msg(300, $httpReferer); }

	return $worldName;
}

function getWorldID($worldNameSafe)
{
	global $mysqli;
	
	$httpReferer = tools_get_referer("index.php");

	$worldID = -1;

	if ($stmt = $mysqli->prepare("SELECT Worlds.ID FROM Worlds WHERE Worlds.NameSafe = ?"))
	{
		$stmt->bind_param('s', $worldNameSafe);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($worldID);
		$stmt->fetch();
	}
	else { throw_msg(300, $httpReferer); }
	
	return $worldID;
}

function getFactionID($userID, $worldNameSafe)
{
	global $mysqli;
	
	$httpReferer = tools_get_referer("index.php");

	$factionID = -1;

	if ($stmt = $mysqli->prepare("SELECT Factions.ID FROM Users, Factions, Worlds WHERE Factions.WorldID = Worlds.ID AND Factions.UserID = Users.ID AND Worlds.NameSafe = ? AND Users.ID = ?"))
	{
		$stmt->bind_param('ss', $worldNameSafe, $userID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($factionID);
		$stmt->fetch();
	}
	else { throw_msg(300, $httpReferer); }

	return $factionID;
}

function getFactionName($facID)
{
	global $mysqli;
	
	$httpReferer = tools_get_referer("index.php");

	$factionName = "";

	if ($stmt = $mysqli->prepare("SELECT Factions.Name FROM Factions WHERE Factions.ID = ?"))
	{
		$stmt->bind_param('s', $facID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($factionName);
		$stmt->fetch();
	}
	else { throw_msg(300, $httpReferer); }

	return $factionName;
}

function displayStart()
{
	include_once("template/header.php");
	include_once("template/sidebar.php");
	echo("
<body>
	<div id='topbar'></div>
	<div id='leftbar'></div>
	<div id='rightbar'></div>
	<div id='bottombar'></div>

	<div class='content'>");
}

function displayEnd()
{
	echo("
	</div>
</body>");
}

?>
