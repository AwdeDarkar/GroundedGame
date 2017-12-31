<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = tools_sanitize_data($_GET['w']);
$bunkerID = tools_sanitize_data($_GET['b']);

$httpReferer = tools_get_referer("index.php");

# get faction id
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


# if this bunker doesn't belong to this user, reject
# also get pertinent data here
if ($stmt = $mysqli->prepare("SELECT FactionID, WorldX, WorldY FROM Bunkers WHERE ID = ?"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($bunkerFacID, $x, $y);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

if ($bunkerFacID != $facID) { throw_msg(200, "worlds.php?w=$world"); }



$processNames = array();
$processComponentAmts = array();
$processResources = array();
$processAmts = array();

# get list of all processes user can do
if ($stmt = $mysqli->prepare("
	SELECT 
		Processes.Name, 
		ProcessComponents.Amount, 
		Resources.Name, 
		ResourceCollections.Amount 
	FROM Processes, ProcessComponents, Resources, ResourceCollections 
	WHERE 
		ResourceCollections.ResourceID = ProcessComponents.RID AND 
		Processes.ID = ProcessComponents.PID AND 
		Resources.ID = ResourceCollections.ResourceID AND 
		ResourceCollections.BunkerID = ?"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($name, $cmpamt, $resName, $amt);
	while ($stmt->fetch())
	{
		array_push($processNames, $name);
		array_push($processComponentAmts, $cmpamt);
		array_push($processResources, $resName);
		array_push($processAmts, $amt);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

?>
<h1>Processes</h1>

<table>
	<tr>
		<th>Process</th>
		<th>Component Name</th>
		<th>Base Component Amount</th>
		<th>Amount Available</th>
	</tr>

<?php
for ($i = 0; $i < count($processNames); $i++)
{
	echo("<tr>");
	echo("<td>".$processNames[$i]."</td><td>".$processResources[$i]."</td><td>".$processComponentAmts[$i]."</td><td>".$processAmts[$i]."</td>");
	echo("</tr>");
}
?>
</table>
