<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");


// assume world id passed in as get var
$worldid = $_GET['wid'];
$userid = LOGGED_USER_ID;

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

	if ($UserCount > 0) { throw_msg(402, $HttpReferer); }
}
else { throw_msg(300, $httpReferer, "create_faction.php", 30); }


// get world name
$worldname = "";
if ($stmt = $mysqli->prepare("SELECT Name FROM Worlds WHERE WorldID = ? LIMIT 1"))
{
	$stmt->bind_param('s', $worldid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($worldname);
	$stmt->fetch();

	if ($UserCount > 0) { throw_msg(402, $HttpReferer); }
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

?>

<h1>Create new faction in <?php print("$worldname"); ?> </h1>
