<?php

$unitOfTime = 1; // minutes per unit (60 = 60 minutes for one unit of time)
// TODO: potentially store unit of time in DB under world

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
$pj_baseTime = array();
$pj_pids = array();
$pj_fids = array();
if ($stmt = $mysqli->prepare("SELECT ProductionJobs.ID, ProductionJobs.LastYieldDate, ProductionJobs.BunkerID, Processes.BaseTime, Processes.ID, Factions.ID FROM ProductionJobs, Processes, Factions, Worlds WHERE ProductionJobs.ProcessID = Processes.ID AND ProductionJobs.FactionID = Factions.ID AND Worlds.ID = Factions.WorldID AND Worlds.NameSafe = ?"))
{
	$stmt->bind_param('s', $worldname);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $last, $bunkerID, $baseTime, $pid, $fid);
	while ($stmt->fetch()) 
	{ 
		array_push($pj_ids, $id); 
		array_push($pj_last, $last); 
		array_push($pj_bunkers, $bunkerID); 
		array_push($pj_baseTime, $baseTime); 
		array_push($pj_pids, $pid); 
		array_push($pj_fids, $fid); 
	}
}
else { echo("ERROR"); }

// for each production job
for ($i = 0; $i < count($pj_ids); $i++)
{
	//echo($pj_ids[$i]);

	$date = $pj_last[$i];
	$now = new DateTime("now");
	
	$convertedDate = strtotime($date);
	$convertedNow = strtotime($now->format('Y-m-d H:i:s'));

	#$time1 = $convertedDate->format('Y-m-d H:i:s');
	#$time2 = $now->format('Y-m-d H:i:s');
	#echo($time1."\n");
	#echo($time2."\n");
	
	$difference = floor(abs($convertedNow - $convertedDate) / 60);
	echo($difference." minutes\n");

	if ($difference < $unitOfTime) { continue; }

	// divide difference by time unit, by base time to determine next base yield
	//$baseCount = ($difference / $unitOfTime) / $pj_baseTime[$i];
	//$baseCount = ($difference / $unitOfTime) / $pj_baseTime[$i];


	// find all output process components (if no associated production job
	// components, create them) and increase by designated amount by process
	// component
	$p_pid = array();
	$p_rid = array();
	$p_type = array();
	$p_amt = array();
	$p_cid = array();
	$p_rcid = array();
	if ($stmt = $mysqli->prepare("
		SELECT 
			ProcessComponents.ID, 
			ProcessComponents.RID, 
			ProcessComponents.Type, 
			ProcessComponents.Amount, 
			ProductionJobComponents.ID, 
			ProductionJobComponents.RCID
		FROM 
			(ProcessComponents LEFT OUTER JOIN ProductionJobComponents 
				ON ProcessComponents.ID = ProductionJobComponents.PCID), Processes
		WHERE
			ProcessComponents.PID = Processes.ID AND
			Processes.ID = ?"))
	{
		$stmt->bind_param('s', $pj_pids[$i]);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id, $rid, $type, $amt, $cid, $rcid);
		while ($stmt->fetch()) 
		{ 
			array_push($p_pid, $id); 
			array_push($p_rid, $rid); 
			array_push($p_type, $type); 
			array_push($p_amt, $amt); 
			array_push($p_cid, $cid); 
			array_push($p_rcid, $rcid); 
		}
	}
	else { echo("ERROR"); }

	for ($j = 0; $j < count($p_pid); $j++)
	{
		if ($p_type[$j] == 1) // output
		{
			//$amountYielded = $baseCount * $p_amt[$j];
			$amountYielded = $p_amt[$j];

			// if resource collection and production job component doesn't exist yet, make it
			if ($p_cid[$j] == null)
			{
				if ($stmt2 = $mysqli->prepare("INSERT INTO ResourceCollections (ResourceID,BunkerID,FactionID,Amount) VALUES (?, ?, ?, ?)"))
				{
					echo("Creating new resource collection\n");
					//set variables
					$stmt2->bind_param("ssss", $p_rid[$j], $pj_bunkers[$i], $pj_fids[$i], $amountYielded);
					$stmt2->execute();
				}
				else { echo("ERROR"); }

				$rcid = $mysqli->insert_id;
				$aid = 0;
				$eid = 0;
				
				if ($stmt2 = $mysqli->prepare("INSERT INTO ProductionJobComponents(PJID, PCID, RCID, AID, EID, Amount) VALUES (?, ?, ?, ?, ?, ?)"))
				{
					echo("Creating output production job component\n");
					$stmt2->bind_param("ssssss", $pj_pids[$i], $p_pid[$j], $rcid, $aid, $eid, $amountYielded);
					$stmt2->execute();
				}
				else { echo("ERROR"); }
			}
			else
			{
				if ($stmt2 = $mysqli->prepare("UPDATE ResourceCollections SET Amount = Amount + ? WHERE ID = ?"))
				{
					echo("Updating resource collection amount\n");
					//set variables
					$stmt2->bind_param("ss", $amountYielded, $p_rcid[$j]);
					$stmt2->execute();
				}
				else { echo("ERROR"); }

				$rcid = $mysqli->insert_id;
				$aid = 0;
				$eid = 0;
				
				/*if ($stmt2 = $mysqli->prepare("UPDATE ProductionJobComponents(PJID, PCID, RCID, AID, EID, Amount) VALUES (?, ?, ?, ?, ?, ?)"))
				{
					$stmt2->bind_param("ssssss", $pj_pids[$i], $p_pid[$j], $rcid, $aid, $eid, $amountYielded);
					$stmt2->execute();
				}
	else { echo("ERROR"); }*/
			}
		}
	}
	
	// update yield time
	$newYieldDate = $now->format("Y-m-d H:i:s");
	if ($stmt = $mysqli->prepare("UPDATE ProductionJobs SET LastYieldDate = ? WHERE ID = ?"))
	{
		echo("Updating production job yield date\n");
		//set variables
		$stmt->bind_param("ss", $newYieldDate, $pj_ids[$i]);
		$stmt->execute();
	}
	else { echo("ERROR"); }

	// determine if each input component has enough to continue again
	$canContinue = true;
	for ($j = 0; $j < count($p_pid); $j++)
	{
		if ($p_type[$j] == 0)
		{
			$amount = 0;
			if ($stmt = $mysqli->prepare("SELECT ResourceCollections.Amount FROM ResourceCollections WHERE ID = ?"))
			{
				$stmt->bind_param('s', $p_rcid[$j]);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($amount);
				$stmt->fetch();
			}
			else { echo("ERROR"); }

			if ($amount < $p_amt[$j])
			{
				echo("Not enough input resources!\n");

				$canContinue = false;
				break;
			}
		}
	}

	if ($canContinue)
	{
		// subtract amount from all input components
		for ($j = 0; $j < count($p_pid); $j++)
		{
			if ($p_type[$j] == 0)
			{
				if ($stmt = $mysqli->prepare("UPDATE ResourceCollections SET Amount = Amount - ? WHERE ID = ?"))
				{
					echo("Burning up resources...\n");
					//set variables
					$stmt->bind_param("ss", $p_amt[$j], $p_rcid[$j]);
					$stmt->execute();
				}
				else { echo("ERROR"); }
			}
		}
	}
	else
	{
		// remove production job and production job components
		for ($j = 0; $j < count($p_pid); $j++)
		{
			if ($stmt = $mysqli->prepare("DELETE FROM ProductionJobComponents Where ID = ?"))
			{
				echo("Removing production job component\n");
				//set variables
				$stmt->bind_param("s", $p_cid[$j]);
				$stmt->execute();
			}
			else { echo("ERROR"); }
		}
		if ($stmt = $mysqli->prepare("DELETE FROM ProductionJobs Where ID = ?"))
		{
			echo("Removing production job\n");
			//set variables
			$stmt->bind_param("s", $pj_ids[$i]);
			$stmt->execute();
		}
		else { echo("ERROR"); }
	}
}
	
// - check current time with last yield time + base time (skip if not ready)
// - find all output components, and increase associated resource collection by designated amount in process component
// - if sufficient material to repeat process, find all input components and decrease associated resource collection by designated amount in process component


?>
