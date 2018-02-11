<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");

$world = -1;
if ($_GET['w']) 
{ 
	$world = tools_sanitize_data($_GET['w']); 
	$_SESSION['world'] = $world;
}
elseif($_SESSION['world']) { $world = $_SESSION['world']; }

$httpReferer = tools_get_referer("index.php");

// get some pertinent info

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

// get all of user's resource collections

$rc_ids = array();
$rc_names = array();
$rc_amts = array();
//$rc_types = array();
$rc_bids = array();
if ($stmt = $mysqli->prepare("SELECT ResourceCollections.ID, Resources.Name, ResourceCollections.Amount, Resources.Type, ResourceCollections.BunkerID FROM Resources, ResourceCollections WHERE Resources.ID = ResourceCollections.ResourceID AND ResourceCollections.FactionID = ?"))
{
	$stmt->bind_param('s', $facID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $amt, $type, $bid);
	while ($stmt->fetch())
	{
		array_push($rc_ids, $id);
		array_push($rc_names, $name);
		array_push($rc_amts, $amt);
		array_push($rc_bids, $bid);
		//array_push($rc_types, $type);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

// query all possible resources

$r_ids = array();
$r_names = array();
//$r_types = array();
if ($stmt = $mysqli->prepare("SELECT Resources.ID, Resources.Name FROM Resources"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name);
	while ($stmt->fetch())
	{
		array_push($r_ids, $id);
		array_push($r_names, $name);
		//array_push($r_types, $type);
	}
}


?>

<body>
<div id='topbar'></div>
<div id='leftbar'></div>
<div id='rightbar'></div>
<div id='bottombar'></div>

<div class="content">
<h1>Post Resource Sell</h1>

<form id='form_postsell' action='post_sell.php' method='post'>
	<select name='resource'>
		<?php
			for ($i = 0; $i < count($r_ids); $i++) { echo("<option value='".$r_ids[$i]."'>".$r_names[$i]."</option>"); }
		?>
	</select>
	<input type='text' placeholder='Amount' name='amount' size='5'/>
	<input type='text' placeholder='Cost' name='cost' size='5' />
	<input type='text' placeholder='Sale comments' name='comments' size='100' />
	<button type='submit' value='submit' name='post_submit'>Post Sell</button>
</form>

<h2>Owned Resources</h2>
<table>
	<tr>
		<th>Resource</th>
		<th>Location</th>
		<th>Collection Amounts</th>
	</tr>

<?php

$ownedResourceNames = array_unique($rc_names);
for ($i = 0; $i < count($ownedResourceNames); $i++)
{
	echo("<tr><td>".$ownedResourceNames[$i]."</td></tr>");

	// organize all bunkers with resources of this type, and all associated
	// resource collections in each
	$bunkers = array();
	for ($j = 0; $j < count($rc_ids); $j++)
	{
		if ($rc_names[$j] == $ownedResourceNames[$i])
		{
			if (!array_key_exists((string)$rc_bids[$j], $bunkers)) { $bunkers[(string)$rc_bids[$j]] = array(); }
			array_push($bunkers[(string)$rc_bids[$j]], $rc_amts[$j]);
		}
	}

	foreach ($bunkers as $id => $rcolls) { echo("<tr><td></td><td>Bunker ".$id."</td><td>".join(',',$rcolls)."</tr>"); }
}

?>

</table>

</div>
</body>


