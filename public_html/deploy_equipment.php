<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();
$bunkerID = tools_sanitize_data($_GET['b']);
$httpReferer = tools_get_referer("index.php");
$facID = getFactionID(LOGGED_USER_ID, $world);

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



$rcid = tools_sanitize_data($_GET['rcid']);



# TODO: validity checks


# get the resource id
if ($stmt = $mysqli->prepare("SELECT ResourceID FROM ResourceCollections WHERE ResourceCollections.ID = ?"))
{
	$stmt->bind_param('s', $rcid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($rid);
	$stmt->fetch();
}


# add the equipment row
 if ($stmt = $mysqli->prepare("INSERT INTO Equipment(ResourceID, RCID) VALUES (?, ?)"))
{
	$stmt->bind_param("ss", $rid, $rcid);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "register.php", 86); }


# decrement the resource collection amount
if ($stmt = $mysqli->prepare("UPDATE ResourceCollections SET Amount = Amount - 1 WHERE ID = ?"))
{
	$stmt->bind_param("s", $rcid);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "register.php", 86); }

throw_msg(100, $httpReferer);
