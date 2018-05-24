<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();
$facID = getFactionID(LOGGED_USER_ID, $world);

$listString = "[ ";

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
		$listString .= ", [$x, $y, ";
		if (is_null($Name))
		{
			$listString .= "\"Unowned\"]";
			echo("<td>Unowned</td>");
		}
		else 
		{
			echo("<td>$Name</td>");
			$listString .= "\"" . $Name . "\"]";
		}

		if ($BunkerFacID == $facID) { echo("<td><a href='bunker.php?w=$world&b=$BunkerID'>Manage</a></td>"); }
		else { echo("<td></td>"); }
		
		echo("</tr>");
	}

	echo("</table>???$listString ]");
}
?>
