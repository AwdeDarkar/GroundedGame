<?php

// find all running production jobs for a particular world
// 
/*foreach($argv as $value)
{
	
}*/

include("includes/db_connect.php");

$worldname = $argv[1]; // NameSafe


$pj_ids = array();
if ($stmt = $mysqli->prepare("SELECT ProductionJobs.ID FROM ProductionJobs, Processes, Factions, Worlds WHERE ProductionJobs.ProcessID = Processes.ID AND ProductionJobs.FactionID = Factions.ID AND Worlds.ID = Factions.WorldID AND Worlds.NameSafe = ?"))
{
	$stmt->bind_param('s', $worldname);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id);
	while ($stmt->fetch()) { array_push($pj_ids, $id); }
}
else { echo("ERROR"); }

// for each production job
for ($i = 0; $i < count($pj_ids); $i++)
{
	echo($pj_ids[$i]);
}
	
// - check current time with last yield time + base time (skip if not ready)
// - find all output components, and increase associated resource collection by designated amount in process component
// - if sufficient material to repeat process, find all input components and decrease associated resource collection by designated amount in process component


?>
