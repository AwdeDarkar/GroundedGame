<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$httpReferer = tools_get_referer("index.php");
$world = getCurrentWorld();
$facID = getFactionID(LOGGED_USER_ID, $world);


$resourceID = tools_sanitize_data($_POST['resource']);
$amount = tools_sanitize_data($_POST['amount']);
$cost = tools_sanitize_data($_POST['cost']);
$comments = tools_sanitize_data($_POST['comments']);


// create the production job
$startdate = date("Y-m-d H:i:s");
if ($stmt = $mysqli->prepare("INSERT INTO Orders(WID, SellingFactionID, RID, AmountRemaining, Cost, DatePosted, Status, Comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"))
{
	$status = 0;
	$stmt->bind_param("ssssssss", $worldID, $facID, $resourceID, $amount, $cost, $startdate, $status, $comments);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "register.php", 86); }
throw_msg(100, "exchange.php");
