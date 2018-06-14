<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();
$actorID = tools_sanitize_data($_GET['a']);
$bunkerID = getCurrentBunker();
$httpReferer = tools_get_referer("index.php");
$facID = getFactionID(LOGGED_USER_ID, $world);

#echo("<p>$bunkerID</p>");
#return;

# if this actor doesn't belong to this user, reject
# also get pertinent data here
if ($stmt = $mysqli->prepare("SELECT ResourceCollections.FactionID FROM Actors, ResourceCollections WHERE Actors.RCID = ResourceCollections.ID AND Actors.ID = ?"))
{
	$stmt->bind_param('s', $actorID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($actorFacID);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

if ($actorFacID != $facID || $actorFacID == null) { throw_msg(301, "worlds.php?w=$world"); }

if ($stmt = $mysqli->prepare("SELECT Actors.Name, Actors.Hitpoints, Jobs.Name, Jobs.Description, Jobs.ID FROM Actors, Jobs WHERE Actors.ID = ? AND Actors.JID = Jobs.ID"))
{
	$stmt->bind_param('s', $actorID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($actorName, $actorHP, $actorJobName, $actorJobDesc, $actorJobID);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

displayStart();

echo("<h1>$actorName, $actorJobName ($actorJobDesc)</h1>");

?>