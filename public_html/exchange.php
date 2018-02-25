<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();
$worldname = getWorldName($world);

displayStart();
?>

<h1><?php echo("$worldname"); ?> Resource Exchange</h1>

<a href='exchange_resource_sell.php'>Post Resource Sell</a>

<h2>Current Sells</h2>
<div id='sells'>
</div>

<script type='text/javascript'>
function getExchange()
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("sells").innerHTML = this.responseText;
		}
	};
	xhttp.open("GET", "ajax_exchange_query.php?w=<?php echo("$world")?>", true);
	xhttp.send();	

	//setTimeout(getFactionList, 1000);
}

getExchange();
</script>

<?php displayEnd(); ?>


