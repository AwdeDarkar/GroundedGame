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



$processIDs = array();
$processNames = array();
$processComponentAmts = array();
$processComponentIDs = array();
$processResources = array();
$processAmts = array();

# get list of all processes user can do
if ($stmt = $mysqli->prepare("
	SELECT 
		Processes.ID,
		Processes.Name, 
		ProcessComponents.Amount,
		ProcessComponents.ID,
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
	$stmt->bind_result($id, $name, $cmpamt, $pcID, $resName, $amt);
	while ($stmt->fetch())
	{
		array_push($processIDs, $id);
		array_push($processNames, $name);
		array_push($processComponentAmts, $cmpamt);
		array_push($processComponentIDs, $pcID);
		array_push($processResources, $resName);
		array_push($processAmts, $amt);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


$preparedStatementIDs = array();
$typesString = "";
for ($i = 0; $i < count($processIDs); $i++) { $typesString .= "s"; }
array_push($preparedStatementIDs, $typesString);
for ($i = 0; $i < count($processIDs); $i++) { array_push($preparedStatementIDs, $processIDs[$i]); }


$pcIDs = array();
$pcNames = array();
$pcTypes = array();
$pcReq = array(); # required amount of the stuff
# get list of all process components user can do
if ($stmt = $mysqli->prepare("
	SELECT 
		ProcessComponents.ID,
		ProcessComponents.Amount,
		ProcessComponents.Type,
		Resources.Name
	FROM Processes, ProcessComponents, Resources
	WHERE 
		Processes.ID = ProcessComponents.PID AND 
		Resources.ID = ProcessComponents.RID AND 
		Processes.ID in (?)"))
{
	#$stmt->bind_param('s', $bunkerID);

	#call_user_func_array(array($stmt, 'bind_param'), $processIDs);
	call_user_func_array(array($stmt, 'bind_param'), $preparedStatementIDs);
	
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id,$amt,$type,$name);
	while ($stmt->fetch())
	{
		array_push($pcIDs, $id);
		array_push($pcNames, $name);
		array_push($pcTypes, $type);
		array_push($pcReq, $amt);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


$uniqueProcessIDs = array_unique($processIDs);
$uniqueProcessNames = array_unique($processNames);


?>
<h1>Processes</h1>

<table>
	<tr>
		<th>Process</th>
		<th>Component Name</th>
		<th>Base Requirement</th>
		<th>Amount Available</th>
		<th>Base Yield</th>
	</tr>

<?php

for ($i = 0; $i < count($uniqueProcessNames); $i++)
{
	echo("<tr><td>".$uniqueProcessNames[$i]."</td></tr>");

	# print all input and equipment components
	for ($j = 0; $j < count($pcIDs); $j++)
	{
		# check for process components of this process id
		if ($pcIDs[$j] == $uniqueProcessIDs[$i] && $pcTypes[$j] != 1)
		{
			echo("<tr><td/><td>".$pcNames[$j]."</td><td>".$pcReq[$j]."</td><td>");
			
			$foundRes = false;
			$ownedString = "";
			# check for resource collections of this requirement
			for ($k = 0; $k < count($processAmts); $k++)
			{
				if ($processComponentIDs[$k] == $pcIDs[$j]) { $ownedString .= $processAmts[$k].","; }
			}
			# remove trailing comma
			if ($foundRes) { $ownedString = rtrim($ownedString, ","); }

			echo ($ownedString."</td></tr>");
		}
	}

	# print all output components
	for ($j = 0; $j < count($pcIDs); $j++)
	{
		# check for process components of this process id
		if ($pcIDs[$j] == $uniqueProcessIDs[$i] && $pcTypes[$j] == 1) { echo("<tr><td/><td/><td/><td/><td>".$pcAmts[$j]." ".$pcNames."</td></tr>"); }
	}
}

/*for ($i = 0; $i < count($processNames); $i++)
{
	echo("<tr>");
	echo("<td>".$processNames[$i]."</td><td>".$processResources[$i]."</td><td>".$processComponentAmts[$i]."</td><td>".$processAmts[$i]."</td>");
	echo("</tr>");
}*/

?>
</table>
