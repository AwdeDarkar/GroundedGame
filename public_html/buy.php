<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");

$world = -1;
if ($_GET['w']) 
{ 
	$world = tools_sanitize_data($_GET['w']); 
	$_SESSION['world'] = $world;
}
elseif($_SESSION['world']) { $world = $_SESSION['world']; }

$httpReferer = tools_get_referer("index.php");

// get some pertinent info

// get world name
$worldname = "";
if ($stmt = $mysqli->prepare("SELECT Name FROM Worlds WHERE NameSafe = ? LIMIT 1"))
{
	$stmt->bind_param('s', $world);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($worldname);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }



# get faction id
$userid = LOGGED_USER_ID;

if ($stmt = $mysqli->prepare("SELECT Factions.ID FROM Users, Factions, Worlds WHERE Factions.WorldID = Worlds.ID AND Factions.UserID = Users.ID AND Worlds.NameSafe = ? AND Users.ID = ?"))
{
	$stmt->bind_param('ss', $world, $userid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($facID);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

?>
