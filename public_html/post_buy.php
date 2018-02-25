<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$httpReferer = tools_get_referer("index.php");
$world = getCurrentWorld();
$facID = getFactionID(LOGGED_USER_ID, $world);


$orderID = tools_sanitize_data($_POST['buy_id']);
$amount = tools_sanitize_data($_POST['buy_amt']);
$bunkerID = tools_sanitize_data($_POST['buy_dest']);

// get order information
# get info about order
if ($stmt = $mysqli->prepare("
	SELECT 
		Orders.AmountRemaining, 
		Orders.Cost, 
		Orders.SellingFactionID,
		Orders.Status,
		Orders.RID
	FROM Orders
	WHERE Orders.ID = ?"))
{
	$stmt->bind_param('s', $orderID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($amtRemaining, $cost, $sellingFacID, $status, $rid);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer); }

echo("sfid:$sellingFacID");

// TODO: status checking (make sure sale is valid)



$costper = (float)$cost / (float)$amtRemaining;
$totalCost = $amt*$costper;

// create the transaction
$postdate = date("Y-m-d H:i:s");
if ($stmt = $mysqli->prepare("INSERT INTO Transactions(OID, RID, Amount, Cost, RequestBunkerID, Status, SellingFactionID, BuyingFactionID, DatePosted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"))
{
	$status = 0;
	$stmt->bind_param("sssssssss", $orderID, $rid, $amount, $totalCost, $bunkerID, $status, $sellingFacID, $facID, $postdate);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "register.php", 86); }

#throw_msg(100, "exchange.php");
