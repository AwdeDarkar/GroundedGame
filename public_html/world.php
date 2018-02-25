<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = getCurrentWorld();

#$fac = -1;
#if (isset($_GET['f'])) { $fac = $_GET['f']; }

$httpReferer = tools_get_referer("index.php");

// get some pertinent info

// get world name
$worldname = getWorldName($world);

displayStart();
?>

<h1><?php echo("$worldname"); ?> World Map </h1>


<div id='demo'>
</div>
<div id='bunkerlist'>
</div>

<script type="text/javascript">

function getFactionList()
{
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("demo").innerHTML = this.responseText;
		}
	};
	xhttp.open("GET", "ajax_faction_list.php?w=<?php echo("$world")?>", true);
	xhttp.send();	

	//setTimeout(getFactionList, 1000);
}

function getBunkerList()
{
	var xhttp2 = new XMLHttpRequest();
	xhttp2.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("bunkerlist").innerHTML = this.responseText;
		}
	};
	xhttp2.open("GET", "ajax_bunker_list.php?w=<?php echo("$world")?>", true);
	xhttp2.send();	

	//setTimeout(getFactionList, 1000);
}

getFactionList();
getBunkerList();
</script>

<?php displayEnd(); ?>
