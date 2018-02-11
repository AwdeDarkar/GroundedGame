<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$httpReferer = tools_get_referer("index.php");
$world = $_SESSION['world']; 

# get faction id
$userid = LOGGED_USER_ID;

if ($stmt = $mysqli->prepare("SELECT Worlds.ID, Factions.ID FROM Users, Factions, Worlds WHERE Factions.WorldID = Worlds.ID AND Factions.UserID = Users.ID AND Worlds.NameSafe = ? AND Users.ID = ?"))
{
	$stmt->bind_param('ss', $world, $userid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($worldID, $facID);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }



$resourceID = tools_sanitize_data($_POST['resource']);
$amount = tools_sanitize_data($_POST['amount']);
$cost = tools_sanitize_data($_POST['cost']);
$comments = tools_sanitize_data($_POST['comments']);


// create the production job
$startdate = date("Y-m-d H:i:s");
if ($stmt = $mysqli->prepare("INSERT INTO Orders(WID, SellingFactionID, RID, AmountRemaining, Cost, DatePosted, Status, Comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"))
{
	$stmt->bind_param("ssssssss", $worldID, $facID, $resourceID, $amount, $cost, $startdate, 0, $comments);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "register.php", 86); }
throw_msg(100, "exchange.php");
