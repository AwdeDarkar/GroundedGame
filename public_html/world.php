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
<canvas id="testing" width="450px" height="400px"></canvas>
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
			args = this.responseText.split("???");
			console.log(args);
			document.getElementById("bunkerlist").innerHTML = args[0];
			
			world_map = eval(args[1]);
			SCALE = 4;

			var canvas = document.getElementById("testing");
			var ctx = canvas.getContext("2d");
			ctx.fillStyle="#109709";

			for (var i = 0; i < world_map.length; i++)
			{
				labelPoint(ctx, world_map[i][0], world_map[i][1], world_map[i][2]);
			}

			function labelPoint(ctx, xpos, ypos, label)
			{
				ctx.beginPath();
				ctx.arc(xpos*SCALE, ypos*SCALE, 4, 0, 2*Math.PI);
				ctx.fill()
				ctx.fillText(label, xpos*SCALE + 6, ypos*SCALE);
			}
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
