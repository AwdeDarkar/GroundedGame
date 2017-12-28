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
	echo("
		<tr>
			<td>".$rc_names[$i]."</td>
			<td>".$rc_amts[$i]."</td>");
	if ($rc_types[$i] == 2) { echo("<td>(Equipment)</td>"); }
	else { echo("<td></td>"); }
	echo("</tr>");
}
?>
</table>
