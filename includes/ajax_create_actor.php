<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");

$BunkerID = getCurrentBunker();
$RCID = "";
$RID = "";
$Name = "";

if ($stmt = $mysqli->prepare("SELECT ID FROM Resources WHERE Name='People'"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($RID);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "ajax_create_actor.php", 17); }

if ($stmt = $mysqli->prepare("SELECT ID FROM ResourceCollections WHERE BunkerID=? and ResourceID=?"))
{
	$stmt->bind_param('ss', $BunkerID, $RID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($RCID);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "ajax_create_actor.php", 27); }

if ($stmt = $mysqli->prepare("SELECT firstname FROM Names ORDER BY RAND() LIMIT 1"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($Name);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "ajax_create_actor.php", 36); }

if ($stmt = $mysqli->prepare("INSERT INTO Actors(Name, ResourceID, RCID, Hitpoints, JobID) VALUES (?, ?, 10, 0)"))
{
	$stmt->bind_param("sss", $Name, $RID, $RCID);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "ajax_create_actor.php", 43); }

if ($stmt = $mysqli->prepare("UPDATE ResourceCollections SET Amount = Amount + 1 WHERE ID=?"))
{
	$stmt->bind_param("s", $RCID);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "ajax_create_actor.php", 50); }

?>