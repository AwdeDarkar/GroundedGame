<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$httpReferer = tools_get_referer("index.php");

$world = getCurrentWorld();
$worldname = getWorldName($world);
$facID = getFactionID(LOGGED_USER_ID, $world);

$orderID = tools_sanitize_data($_GET['o']);

# get info about order
if ($stmt = $mysqli->prepare("
	SELECT 
		Factions.Name, 
		Resources.Name, 
		Orders.AmountRemaining, 
		Orders.Cost, 
		Orders.DatePosted
	FROM Orders, Factions, Resources
	WHERE
		Factions.ID = Orders.SellingFactionID AND
		Resources.ID = Orders.RID AND
		Orders.ID = ?"))
{
	$stmt->bind_param('s', $orderID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($factionName, $resourceName, $amtRemaining, $cost, $datePosted);
	$stmt->fetch();
}
else { throw_msg(300, $httpReferer); }

# get all owned bunkers
$bunkerIDs = array();
if ($stmt = $mysqli->prepare("SELECT Bunkers.ID FROM Bunkers WHERE Bunkers.FactionID = ?"))
{
	$stmt->bind_param('s', $facID);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($bid);
	while ($stmt->fetch())
	{
		array_push($bunkerIDs, $bid);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }



$costper = (float)$cost / (float)$amtRemaining;

displayStart();

echo("<h1>Buy $resourceName from $factionName</h1>");
echo("<p>Amount in sell order: $amtRemaining</p>");
echo("<p>Total cost: \$$cost</p>");
echo("<p>Price per: \$$costper</p>");
?>

<script> 
	var resourceName = "<?php echo($resourceName); ?>";
	var costPer = <?php echo($costper); ?>;
	
	function onSliderChange()
	{
		var slider = document.getElementById("buy_amt");
		var text = document.getElementById("buy_string");
		var amt = slider.value * costPer;

		text.innerHTML = slider.value.toString() + " " + resourceName + ", $" + amt.toString();
	}
</script>


<form id='form_postbuy' action='post_buy.php' method='post'>
	<input type='hidden' name='buy_id' value='<?php echo("$orderID"); ?>'>
	Ship to: <select name='buy_dest'>
		<?php for ($i = 0; $i < count($bunkerIDs); $i++) { echo("<option value='".$bunkerIDs[$i]."'>Bunker ".$bunkerIDs[$i]."</option>"); } ?>
	</select></br>
	<input name='buy_amt' type='range' min='0' max='<?php echo($amtRemaining); ?>' value='<?php echo($amtRemaining); ?>' oninput='onSliderChange();'>
	<button type='submit' value='submit' name='post_submit'>Place Order</button> 
</form>
<p id='buy_string'><?php echo("$amtRemaining $resourceName, \$$cost"); ?></p>


<?php displayEnd(); ?>
