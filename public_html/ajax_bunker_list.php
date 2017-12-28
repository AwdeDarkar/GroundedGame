<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = $_GET['w'];

echo("
<table>
	<tr>
		<th>Bunker X</th>
		<th>Bunker Y</th>
		<th>Bunker Owner</th>
	</tr>
");

if ($stmt = $mysqli->prepare("SELECT Bunkers.ID, Bunkers.WorldX, Bunkers.WorldY, Factions.Name, Factions.NameSafe FROM ((Bunkers LEFT OUTER JOIN Factions ON Bunkers.FactionID = Factions.ID) INNER JOIN Worlds on Bunkers.WorldID = Worlds.ID) WHERE Worlds.NameSafe = ?"))
{
	$stmt->bind_param('s', $world);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($BunkerID, $x, $y, $Name, $NameSafe);
	
	while($stmt->fetch())
	{
		echo("<tr>");
		echo("<td>$x</td><td>$y</td>");
		if (is_null($Name)) { echo("<td>Unowned</td>"); }
		else { echo("<td>$Name</td>"); }
		echo("</tr>");
	}

	echo("</table>");
}
?>


