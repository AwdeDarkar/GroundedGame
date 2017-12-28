<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = $_GET['w'];

if ($stmt = $mysqli->prepare("SELECT Factions.Name FROM Factions, Worlds WHERE Factions.WorldID = Worlds.ID AND Worlds.NameSafe = ?"))
{
	$stmt->bind_param('s', $world);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($Name);
	
	while($stmt->fetch())
	{
		echo("<p>$Name</p>");
	}
}
?>


