<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();
$worldID = getWorldID($world);
$httpReferer = tools_get_referer("index.php");

$o_ids = array();
$o_rNames = array();
$o_fNames = array();
$o_amts = array();
$o_costs = array();
$o_costpers = array();
$o_dates = array();
$o_statuses = array();
$o_comments = array();
// query all exchanges by default
if ($stmt = $mysqli->prepare("
	SELECT 
		Orders.ID, 
		Resources.Name, 
		Factions.Name, 
		Orders.AmountRemaining, 
		Orders.Cost, 
		Orders.Status, 
		Orders.DatePosted, 
		Orders.Comment
	FROM Orders, Factions, Resources 
	WHERE
		Resources.ID = Orders.RID AND
		Factions.ID = Orders.SellingFactionID AND
		Orders.WID = ?"))
{
	$stmt->bind_param('s', $worldID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($oid, $rname, $fname, $oamt, $ocost, $ostatus, $odate, $ocomment);
	while ($stmt->fetch())
	{
		array_push($o_ids, $oid);
		array_push($o_rNames, $rname);
		array_push($o_fNames, $fname);
		array_push($o_amts, $oamt);
		array_push($o_costs, $ocost);
		$costper = (float)$ocost / (float)$oamt;
		array_push($o_costpers, $costper);
		array_push($o_dates, $odate);
		array_push($o_statuses, $ostatus);
		array_push($o_comments, $ocomment);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

?>

<table>
	<tr>
		<th>Resource</th>
		<th>Cost</th>
		<th>Amount</th>
		<th>(Total)</th>
		<th>Seller</th>
		<th>Comment</th>
		<th>Posted</th>
		<th></th>
	</tr>

<?php

for ($i = 0; $i < count($o_ids); $i++)
{
	echo("<tr>
			<td>".$o_rNames[$i]."</td>
			<td>$".$o_costpers[$i]."</td>
			<td>".$o_amts[$i]."</td>
			<td>($".$o_costs[$i].")</td>
			<td>".$o_fNames[$i]."</td>
			<td>".$o_comments[$i]."</td>
			<td>".$o_dates[$i]."</td>
			<td><a href='post_buy.php?o=".$o_ids[$i]."'>Buy</a></td>
		</tr>");
}
	
?>

</table>
 
