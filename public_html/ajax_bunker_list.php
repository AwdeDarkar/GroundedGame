<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = tools_sanitize_data($_GET['w']);


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



echo("
<table>
	<tr>
		<th>Bunker X</th>
		<th>Bunker Y</th>
		<th>Bunker Owner</th>
		<th></th>
	</tr>
");

if ($stmt = $mysqli->prepare("SELECT Bunkers.ID, Bunkers.WorldX, Bunkers.WorldY, Factions.Name, Factions.NameSafe, Factions.ID FROM ((Bunkers LEFT OUTER JOIN Factions ON Bunkers.FactionID = Factions.ID) INNER JOIN Worlds on Bunkers.WorldID = Worlds.ID) WHERE Worlds.NameSafe = ?"))
{
	$stmt->bind_param('s', $world);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($BunkerID, $x, $y, $Name, $NameSafe, $BunkerFacID);
	
	while($stmt->fetch())
	{
		echo("<tr>");
		echo("<td>$x</td><td>$y</td>");
		if (is_null($Name)) { echo("<td>Unowned</td>"); }
		else { echo("<td>$Name</td>"); }

		if ($BunkerFacID == $facID) { echo("<td><a href='bunker.php?w=$world&b=$BunkerID'>Manage</a></td>"); }
		else { echo("<td></td>"); }
		
		echo("</tr>");
	}

	echo("</table>");
}
?>


