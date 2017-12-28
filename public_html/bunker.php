<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = tools_sanitize_data($_GET['w']);
$bunkerID = tools_sanitize_data($_GET['b']);

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
if ($stmt = $mysqli->prepare("SELECT FactionID FROM Bunkers WHERE ID = ?"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($bunkerFacID);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

if ($bunkerFacID != $facID) { throw_msg(200, "worlds.php?w=$world"); }


?>
<h1>Bunker <?php echo("$bunkerID"); ?></h1>
