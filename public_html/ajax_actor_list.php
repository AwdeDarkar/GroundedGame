<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$BunkerID = getCurrentBunker();
$RCID = "";
$RID = "";
$Name = "";
$Job = "";

if ($stmt = $mysqli->prepare("SELECT ID FROM Resources WHERE Name='People'"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($RID);
	$stmt->fetch();
}
else { echo("Problem line: 17"); }

if ($stmt = $mysqli->prepare("SELECT ID FROM ResourceCollections WHERE BunkerID=? and ResourceID=?"))
{
	$stmt->bind_param('ss', $BunkerID, $RID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($RCID);
	$stmt->fetch();
}
else { echo("Problem line: 27"); }

if ($stmt = $mysqli->prepare("SELECT Actors.Name, Jobs.Name FROM Actors, Jobs WHERE Actors.RCID = ? AND Actors.JID=Jobs.ID"))
{
	$stmt->bind_param('s', $RCID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($Name, $Job);
	
	while($stmt->fetch())
	{
		echo("<tr><td>$Name</td><td>$Job</td></tr>");
	}
}
else { echo("Problem line: 41"); }
?>

