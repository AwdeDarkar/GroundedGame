<?php

// find all running production jobs for a particular world
// 
/*foreach($argv as $value)
{
	
}*/

include("../includes/db_connect.php");

$worldname = $argv[1]; // NameSafe
echo("World: ".$worldname."\n");


$pj_ids = array();
$pj_last = array();
$pj_bunkers = array();
if ($stmt = $mysqli->prepare("SELECT ProductionJobs.ID, ProductionJobs.LastYieldDate, ProductionJobs.BunkerID FROM ProductionJobs, Processes, Factions, Worlds WHERE ProductionJobs.ProcessID = Processes.ID AND ProductionJobs.FactionID = Factions.ID AND Worlds.ID = Factions.WorldID AND Worlds.NameSafe = ?"))
{
	$stmt->bind_param('s', $worldname);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $last, $bunkerID);
	while ($stmt->fetch()) 
	{ 
		array_push($pj_ids, $id); 
		array_push($pj_last, $last); 
		array_push($pj_bunkers, $bunkerID); 
	}
}
else { echo("ERROR"); }

// for each production job
for ($i = 0; $i < count($pj_ids); $i++)
{
	//echo($pj_ids[$i]);

	$date = $pj_last[$i];
	$now = new DateTime("now");

	$time1 = $date->format('Y-m-d H:i:s');
	$time2 = $now->format('Y-m-d H:i:s');
	echo($time1."\n");
	echo($time2."\n");
	
	$difference = floor(abs($now - $date) / 60);
	
	echo("\n".$difference." minutes");
}
	
// - check current time with last yield time + base time (skip if not ready)
// - find all output components, and increase associated resource collection by designated amount in process component
// - if sufficient material to repeat process, find all input components and decrease associated resource collection by designated amount in process component


?>
