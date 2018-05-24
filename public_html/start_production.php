<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();
$bunkerID = tools_sanitize_data($_GET['b']);

$httpReferer = tools_get_referer("index.php");

# get faction id
$facID = getFactionID(LOGGED_USER_ID, $world);

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



$processIDs = array();
$processNames = array();
$processComponentAmts = array();
$processComponentIDs = array();
$processResources = array();
$processResourceIDs = array();
$processAmts = array();

# get list of all processes user can do
if ($stmt = $mysqli->prepare("
	SELECT 
		Processes.ID,
		Processes.Name, 
		ProcessComponents.Amount,
		ProcessComponents.ID,
		Resources.Name, 
		ResourceCollections.ID,
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
	$stmt->bind_result($id, $name, $cmpamt, $pcID, $resName, $resID, $amt);
	while ($stmt->fetch())
	{
		array_push($processIDs, $id);
		array_push($processNames, $name);
		array_push($processComponentAmts, $cmpamt);
		array_push($processComponentIDs, $pcID);
		array_push($processResources, $resName);
		array_push($processResourceIDs, $resID);
		array_push($processAmts, $amt);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }






$preparedStatementIDs = array();
$typesString = "";
for ($i = 0; $i < count($processIDs); $i++) { $typesString .= "s"; }
#array_push($preparedStatementIDs, $typesString);
#for ($i = 0; $i < count($processIDs); $i++) { array_push($preparedStatementIDs, &$processIDs[$i]); }

$preparedStatementIDs[] = &$typesString;
for ($i = 0; $i < count($processIDs); $i++) { $preparedStatementIDs[] = &$processIDs[$i]; }


$typesString = "";
for ($i = 0; $i < count($processIDs); $i++) { $typesString .= "s"; }

$preparedStatementRCIDs[] = &$typesString;
for ($i = 0; $i < count($processIDs); $i++) { $preparedStatementIDs[] = &$processResourceIDs[$i]; }




# get all associated process component information for each possible process
$query_pc = "
	SELECT 
		Processes.ID,
		ProcessComponents.ID,
		ProcessComponents.Amount,
		ProcessComponents.Type,
		Resources.Name
	FROM Processes, ProcessComponents, Resources
	WHERE 
		Processes.ID = ProcessComponents.PID AND 
		Resources.ID = ProcessComponents.RID AND 
		Processes.ID in (";

$questionString = "";
for ($i = 0; $i < count($processIDs); $i++) { $questionString .= "?,"; }
$questionString = rtrim($questionString,',');

$query_pc .= $questionString.")";

# get all resource collection associated equipment
$query_eq = "
	SELECT
		Equipment.ID,
		Equipment.RCID
	FROM Equipment, ResourceCollections
	WHERE
		ResourceCollections.ID = Equipment.RCID AND
		Equipment.RCID in (".$questionString.")";




$pcProcessIDs = array();
$pcIDs = array();
$pcNames = array();
$pcTypes = array();
$pcReq = array(); # required amount of the stuff
# get list of all process components user can do
if ($stmt = $mysqli->prepare($query_pc))
{
	#$stmt->bind_param('s', $bunkerID);

	#call_user_func_array(array($stmt, 'bind_param'), $processIDs);
	call_user_func_array(array($stmt, 'bind_param'), $preparedStatementIDs);
	
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($pid,$id,$amt,$type,$name);
	while ($stmt->fetch())
	{
		array_push($pcProcessIDs, $pid);
		array_push($pcIDs, $id);
		array_push($pcNames, $name);
		array_push($pcTypes, $type);
		array_push($pcReq, $amt);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


$uniqueProcessIDs = array_unique($processIDs);
$uniqueProcessNames = array_unique($processNames);


$eqIDs = array();
$eqRCIDs = array();
if ($stmt = $mysqli->prepare($query_eq))
{
	#call_user_func_array(array($stmt, 'bind_param'), $processIDs);
	call_user_func_array(array($stmt, 'bind_param'), $preparedStatementRCIDs);
	
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id,$rcid);
	while ($stmt->fetch())
	{
		array_push($eqIDs, $id);
		array_push($eqRCIDs, $rcid);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

# organize equipment into dictionary by RCID
$equipment = array();
for($i = 0; $i < count($eqIDs); $i++)
{
	if (!array_key_exists((string)($eqRCIDs[$i]), $equipment)) { $equipment[(string)($eqRCIDs[$i])] = array(); }
	array_push($equipment[(string)($eqRCIDs[$i])], $eqIDs[$i]);
}

var_dump($eqIDs);
var_dump($eqRCIDs);
var_dump($equipment);


# get list of all 



displayStart();
?>



<script type="text/javascript">

function useResource(pid, rid, amt, pcid, eid)
{
	var hiddenElemPc = document.getElementById("p" + pid + "_pc" + pcid);
	var hiddenElemEq = document.getElementById("p" + pid + "_pc" + pcid + "_eq");
	var btn = document.getElementById("btn" + pid + "_" + rid + "_" + eid);

	// check if this rid is already there (if so, toggle it)
	subtract = false;
	var rids = hiddenElemPc.value.split(','); 
	var eids = hiddenElemEq.value.split(',');
	console.log(rids);
	console.log(eids);

	if (eid == 0)
	{
		if (rids.indexOf(rid.toString()) != -1)
		{
			console.log("Already there!");
			subtract = true;
			var index = rids.indexOf(rid.toString());
			rids.splice(index, 1);
		}
	}
	else
	{
		if (eids.indexOf(eid.toString()) != -1)
		{
			console.log("Already there!");
			subtract = true;
			var index = eids.indexOf(eid.toString());
			eids.splice(index, 1);
		}
	}

	if (subtract) 
	{ 
		btn.style = '';
		if (eid == 0) { hiddenElemPc.value = rids.join(','); }
		else { hiddenElemEq.value = eids.join(','); }
	}
	else
	{
		btn.style = 'color: red;';
		if (hiddenElemPc.value != "" && eid == 0) { hiddenElemPc.value += ","; }
		else if (hiddenElemEq.value != "" && eid != 0) { hiddenElemEq.value += ","; }
		if (eid == 0) { hiddenElemPc.value += rid; }
		else { hiddenElemEq.value += eid; }
	}

	var display = document.getElementById("disp" + pid + "_" + pcid);
	var count = parseInt(display.innerHTML);

	
	if (subtract) { count -= amt; }
	else { count += amt; }
	
	display.innerHTML = count;
}

</script>





<h1>Processes</h1>


<form id='form_production' action='handle_start_production.php' method='post'>
<input type='hidden' name='p_w' id='p_w' value=<?php echo("'$world'"); ?> />
<input type='hidden' name='p_b' id='p_b' value=<?php echo("'$bunkerID'"); ?> />

<table border='1'>
	<tr>
		<th>Process</th>
		<th>Component Name</th>
		<th>Base Requirement</th>
		<th>Amount Available</th>
		<th>Base Yield</th>
		<th>Staging</th>
	</tr>

<?php




for ($i = 0; $i < count($uniqueProcessNames); $i++)
{
	echo("<tr><td>".$uniqueProcessNames[$i]."</td><td/><td/><td/><td/></td><td><button type='submit' value='".$uniqueProcessIDs[$i]."' name='start_button'>Start</button></td></tr>");

	# print all input and equipment components
	for ($j = 0; $j < count($pcIDs); $j++)
	{
		#echo("<script>console.log('".$pcIDs[$j]."');</script>");
		
		# check for process components of this process id
		if ($pcProcessIDs[$j] == $uniqueProcessIDs[$i] && $pcTypes[$j] != 1)
		{
			echo("<tr><input type='hidden' id='p".$uniqueProcessIDs[$i]."_pc".$pcIDs[$j]."_eq' name='p".$uniqueProcessIDs[$i]."_eq".$pcIDs[$j]."_eq' value=''><input type='hidden' id='p".$uniqueProcessIDs[$i]."_pc".$pcIDs[$j]."' name='p".$uniqueProcessIDs[$i]."_pc".$pcIDs[$j]."' value=''><td/><td>".$pcNames[$j]."</td><td>".$pcReq[$j]."</td><td>");

				
			$foundRes = false;
			$ownedString = "";
			# check for resource collections of this requirement
			for ($k = 0; $k < count($processAmts); $k++)
			{
				if ($processComponentIDs[$k] == $pcIDs[$j]) 
				{ 
					$foundRes = true;
					echo("type: ".$pcTypes[$j]);
					
					# equipment component
					if ($pcTypes[$j] == 2)
					{
						echo("|".$processResourceIDs[$k]."|");
						$equipments = $equipment[(string)($processResourceIDs[$k])];
						var_dump($equipments);
						for($m = 0; $m < count($equipments); $m++)
						{
							$ownedString .= "<button id='btn".$pcProcessIDs[$j]."_".$processResourceIDs[$k]."_".$equipments[$m]."' type='button' onclick='useResource(".$pcProcessIDs[$j].",".$processResourceIDs[$k].",1,".$pcIDs[$j].",".$equipments[$m].");'>1</button>";
						}
						if ($processAmts[$k] > 0)
						{
							$ownedString .= " (".$processAmts[$k]." undeployed)";
						}
					}
					else
					{
						$ownedString .= "<button id='btn".$pcProcessIDs[$j]."_".$processResourceIDs[$k]."_0' type='button' onclick='useResource(".$pcProcessIDs[$j].",".$processResourceIDs[$k].",".$processAmts[$k].",".$pcIDs[$j].",0);'>".$processAmts[$k]."</button>";
					}
				}
			}
			# remove trailing comma
			if ($foundRes) { $ownedString = rtrim($ownedString, ","); }

			echo ($ownedString."</td><td/><td id='disp".$pcProcessIDs[$j]."_".$pcIDs[$j]."'>0</td></tr>");
		}
	}

	# print all output components
	for ($j = 0; $j < count($pcIDs); $j++)
	{
		# check for process components of this process id
		if ($pcProcessIDs[$j] == $uniqueProcessIDs[$i] && $pcTypes[$j] == 1) { echo("<tr><td/><td/><td/><td/><td>".$pcReq[$j]." ".$pcNames[$j]."</td></tr>"); }
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
<?php displayEnd(); ?>
