<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = tools_sanitize_data($_POST['p_w']);
$bunkerID = tools_sanitize_data($_POST['p_b']);

$httpReferer = tools_get_referer("index.php");

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


# if this bunker doesn't belong to this user, reject
# also get pertinent data here
if ($stmt = $mysqli->prepare("SELECT FactionID, WorldX, WorldY FROM Bunkers WHERE ID = ?"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($bunkerFacID, $x, $y);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

if ($bunkerFacID != $facID || $bunkerFacID == null) { throw_msg(200, "worlds.php?w=$world"); }



var_dump($_POST);



$processID = (int)(tools_sanitize_data($_POST['start_button']));
echo("<p>$processID</p>");


foreach ($_POST as $key => $value)
{
	echo("<p>$key</p>");
	if (preg_match("^p".$processID."[\w]*", $key))
	{
		echo("<p>MATCH! $key</p>");
	}
}
