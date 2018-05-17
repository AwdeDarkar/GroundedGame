<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();
$bunkerID = tools_sanitize_data($_GET['b']);
$httpReferer = tools_get_referer("index.php");
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


# query all resource deposits
$rd_ids = array();
$rd_names = array();
$rd_rates = array();
$rd_amts = array();
$rd_maxes = array();
if ($stmt = $mysqli->prepare("SELECT ResourceDeposits.ResourceID, Resources.Name, ResourceDeposits.ReplenishRate, ResourceDeposits.Amount, ResourceDeposits.Maximum FROM Resources, ResourceDeposits WHERE Resources.ID = ResourceDeposits.ResourceID AND ResourceDeposits.BunkerID = ?"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($resID, $name, $rate, $amt, $max);
	while ($stmt->fetch())
	{
		array_push($rd_ids, $resID);
		array_push($rd_names, $name);
		array_push($rd_rates, $rate);
		array_push($rd_amts, $amt);
		array_push($rd_maxes, $max);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


# query all resource collections
$rc_ids = array();
$rc_names = array();
$rc_amts = array();
$rc_types = array();
if ($stmt = $mysqli->prepare("SELECT ResourceCollections.ID, Resources.Name, ResourceCollections.Amount, Resources.Type FROM Resources, ResourceCollections WHERE Resources.ID = ResourceCollections.ResourceID AND ResourceCollections.BunkerID = ?"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $amt, $type);
	while ($stmt->fetch())
	{
		array_push($rc_ids, $id);
		array_push($rc_names, $name);
		array_push($rc_amts, $amt);
		array_push($rc_types, $type);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


# query all actors



# query all equipment
$equip_ids = array();
$equip_rcids = array();
$equip_rids = array();
$equip_names = array();
if ($stmt = $mysqli->prepare("
	SELECT 
		Equipment.ID,
		Equipment.RCID,
		Equipment.ResourceID,
		Resources.Name
	FROM
		Resources,
		ResourceCollections,
		Equipment
	WHERE
		Resources.ID = ResourceCollections.ResourceID AND
		ResourceCollections.BunkerID = ? AND
		ResourceCollections.ID = Equipment.RCID"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $rcid, $rid, $name);
	while ($stmt->fetch())
	{
		array_push($equip_ids, $id);
		array_push($equip_rcids, $rcid);
		array_push($equip_rids, $rid);
		array_push($equip_names, $name);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

# remove all deployed equipment from displayed resource collections
for ($i = 0; $i < count($equip_ids); $i++)
{
	$removeFrom = $equip_rcids[$i]; 
	# find it in rc
	for ($j = 0; $j < count($rc_ids); $j++)
	{
		if ($rc_ids[$j] == $removeFrom) { $rc_amts[$j]--; }
	}
}


# query all production jobs

$pj_dates = array();
$pj_lastdates = array();
$pj_names = array();
if ($stmt = $mysqli->prepare("SELECT ProductionJobs.LastYieldDate, ProductionJobs.StartDate, Processes.Name FROM ProductionJobs, Processes WHERE ProductionJobs.ProcessID = Processes.ID AND ProductionJobs.BunkerID = ?"))
{
	$stmt->bind_param('s', $bunkerID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($lastdate, $date, $name);
	while ($stmt->fetch())
	{
		array_push($pj_names, $name);
		array_push($pj_dates, $date);
		array_push($pj_lastdates, $lastdate);
	}
}
else { throw_msg(300, $httpReferer, "bunker.php", 39); }

displayStart();
?>

<h1>Bunker <?php echo("$bunkerID"); ?></h1>
<p>World Coordinates: (<?php echo("$x,$y");?>)</p>

<h3>Resource Deposits</h3>
<table>
	<tr>
		<th>Deposit</th>
		<th>Replenish Rate</th>
		<th>Amount</th>
		<th>Capacity</th>
	</tr>
<?php
for ($i = 0; $i < count($rd_ids); $i++)
{
	echo("
		<tr>
			<td>".$rd_names[$i]."</td>
			<td>".$rd_rates[$i]."</td>
			<td>".$rd_amts[$i]."</td>
			<td>".$rd_maxes[$i]."</td>
		</tr>");
}
?>
</table>

<h3>Resource Collections</h3>
<table>
	<tr>
		<th>Resource</th>
		<th>Amount</th>
	</tr>
<?php
for ($i = 0; $i < count($rc_ids); $i++)
{
	# only display if not deployed/active
	if ($rc_amts[$i] > 0)
	{
		echo("
			<tr>
				<td>".$rc_names[$i]."</td>
				<td>".$rc_amts[$i]."</td>");
		if ($rc_types[$i] == 2) 
		{ 
			echo("<td>(Equipment)</td>"); 
			echo("<td><a href='deploy_equipment.php?rcid=".$rc_ids[$i]."'>Deploy</a></td>");
		}
		else { echo("<td></td>"); }
		echo("</tr>");
	}
}
?>
</table>


<h3>Equipment</h3>
<table>
	<tr>
		<th>Type</th>
	</tr>
<?php
for($i; $i < count($equip_ids); $i++)
{
	echo("
		<tr>
			<td>".$equip_names[$i]."</td>
			<td><a href='#'>Pack</td>
		</tr>");
}
?>
</table>


<p><a href='start_production.php?w=<?php echo("$world"); ?>&b=<?php echo("$bunkerID"); ?>'>Start Production Job</a></p>


<h3>Ongoing Production Jobs</h3>
<table>
	<tr>
		<th>Job Name</th>
		<th>Started</th>
		<th>Last Yield</th>
	</tr>
<?php
for ($i = 0; $i < count($pj_names); $i++)
{
	echo("
		<tr>
			<td>".$pj_names[$i]."</td>
			<td>".$pj_dates[$i]."</td>
			<td>".$pj_lastdates[$i]."</td>
		</tr>");
}
?>
</table>

<?php displayEnd(); ?>
