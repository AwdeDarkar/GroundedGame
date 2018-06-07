<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = tools_sanitize_data($_POST['p_w']);
$bunkerID = tools_sanitize_data($_POST['p_b']);

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

if ($bunkerFacID != $facID || $bunkerFacID == null) { throw_msg(200, "worlds.php?w=$world"); }



//var_dump($_POST);



$processID = (int)(tools_sanitize_data($_POST['start_button']));
echo("<p>$processID</p>");

$processComponents = array();

$allRCIDs = array();


# get all passed resource collection ids associated with pertinent component ids
foreach ($_POST as $key => $value)
{
	echo("<p>$key</p>");
	if (strpos($key, "p".$processID) !== false)
	{
		echo("<p>MATCH! $key</p>");

		preg_match('/(\d)*$/', $key, $matches);
		$pcid = $matches[0];
		$values = explode(',', $value);
		$processComponents[$pcid] = $values;

		for ($i = 0; $i < count($values); $i++) { array_push($allRCIDs, $values[$i]); }
	}
}

var_dump($processComponents);

// get details on process components
$pcIDs = array();
$pcTypes = array();
$pcRIDs = array();
$pcAmts = array();

# get list of all processes user can do
if ($stmt = $mysqli->prepare("
	SELECT 
		ProcessComponents.Amount,
		ProcessComponents.Type,
		ProcessComponents.RID,
		ProcessComponents.ID
	FROM Processes, ProcessComponents
	WHERE 
		Processes.ID = ProcessComponents.PID AND 
		Processes.ID = ?"))
{
	$stmt->bind_param('s', $processID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($amt, $type, $rid, $id);
	while ($stmt->fetch())
	{
		array_push($pcIDs, $id);
		array_push($pcTypes, $type);
		array_push($pcRIDs, $rid);
		array_push($pcAmts, $name);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


// check that all input and equipment component ids exist as keys in process
// components
for ($i = 0; $i < count($pcTypes); $i++)
{
	if ($pcTypes[$i] != 1)
	{
		if (!array_key_exists($pcIDs[$i], $processComponents)) { throw_msg(200, $httpReferer, "handle_start_production.php", 109); }
	}
}

// get details on all specified resource collections
$preparedStatementIDs = array();
$typesString = "";
for ($i = 0; $i < count($allRCIDs); $i++) { $typesString .= "s"; }

$preparedStatementIDs[] = &$typesString;
for ($i = 0; $i < count($allRCIDs); $i++) { $preparedStatementIDs[] = &$allRCIDs[$i]; }

$query = "SELECT ID, BunkerID, FactionID, ResourceID, Amount FROM ResourceCollections WHERE ID in (";

$questionString = "";
for ($i = 0; $i < count($allRCIDs); $i++) { $questionString .= "?,"; }
$questionString = rtrim($questionString,',');

$query .= $questionString.")";

$rcIDs = array();
$rcBunkerIDs = array();
$rcFactionIDs = array();
$rcResourceIDs = array();
$rcAmts = array();
# get list of all process components user can do
if ($stmt = $mysqli->prepare($query))
{
	call_user_func_array(array($stmt, 'bind_param'), $preparedStatementIDs);
	
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $rcBunkerID, $rcFacID, $rcResourceID, $rcAmt);
	while ($stmt->fetch())
	{
		// make sure owned by faction 
		if ($rcFacID != $facID) { throw_msg(201, $httpReferer); }

		// make sure in correct bunker
		if ($rcBunkerID != $bunkerID) { throw_msg(202, $httpReferer); }
		
		array_push($rcIDs, $id);
		array_push($rcBunkerIDs, $rcBunkerID);
		array_push($rcFactionIDs, $rcFactionID);
		array_push($rcResourceIDs, $rcResourceID);
		array_push($rcAmts, $rcAmt);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


// check to make sure all purported resource collections are what they say they
// are for that associated process component


echo("<p>rcResourceIDs</p>");
var_dump($rcResourceIDs);
echo("<p>processComponents</p>");
var_dump($processComponents);
echo("<p>About to loop through check</p>");

foreach ($processComponents as $key => $value)
{
	echo("<p>Inside loop</p>");
	echo("<p>key: $key</p>");
	// find resource id of that process component 
	$pcIndex = tools_find($pcIDs, (int)$key);
	echo("<p>pc index: $pcIndex</p>");
	$resID = $pcRIDs[$pcIndex];
	echo("<p>Resource id: $resID</p>");
	
	// check each resource collection resource id
	$totalAmt = 0;
	for ($i = 0; $i < count($value); $i++)
	{
		// find in rcids
		$rcIndex = tools_find($rcIDs, $value[$i]);
		if ($rcResourceIDs[$rcIndex] != $resID) { return; throw_msg(203, $httpReferer); }
		$totalAmt += $rcAmts[$rcIndex];
		
	}

	// make sure there is a sufficient amount of everything
	if ($totalAmt < $pcAmts[$pcIndex]) { throw_msg(204, $httpReferer); }
}

echo("<p>finished loop through check</p>");
return;


// TODO TODO TODO TODO TODO - deplete initial resource collections upon inserting this


// create the production job
$startdate = date("Y-m-d H:i:s");
if ($stmt = $mysqli->prepare("INSERT INTO ProductionJobs(FactionID, StartDate, LastYieldDate, ProcessID, BunkerID) VALUES (?, ?, ?, ?, ?)"))
{
	$stmt->bind_param("sssss", $facID, $startdate, $startdate, $processID, $bunkerID);
	$result = $stmt->execute();
}
else { throw_msg(300, $httpReferer, "register.php", 86); }

$pjid = $mysqli->insert_id;

// add all components
foreach ($processComponents as $key => $value)
{
	// find resource id of that process component 
	$pcIndex = tools_find($pcIDs, (int)$key);
	$resID = $pcRIDs[$pcIndex];

	// check each resource collection resource id
	//$totalAmt = 0;
	for ($i = 0; $i < count($value); $i++)
	{
		// find in rcids
		$rcIndex = tools_find($rcIDs, $value[$i]);
		if ($rcResourceIDs[$rcIndex] != $resID) { throw_msg(203, $httpReferer); }

		$pcid = $pcIDs[$pcIndex];
		$rcid = $rcIDs[$rcIndex];
		$aid = 0;
		$eid = 0;
		$amt = 0;

		if ($stmt = $mysqli->prepare("INSERT INTO ProductionJobComponents(PJID, PCID, RCID, AID, EID, Amount) VALUES (?, ?, ?, ?, ?, ?)"))
		{
			$stmt->bind_param("ssssss", $pjid, $pcid, $rcid, $aid, $eid, $amt);
			$result = $stmt->execute();
		}
		else { throw_msg(300, $httpReferer, "register.php", 86); }
	}
}

throw_msg(101, "bunker.php?w=$world&b=$bunkerID");
