<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");

include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");

$httpReferer = tools_get_referer("index.php");


$ids = array();
$names = array();
$namesafes = array();
$basetimes = array();
if ($stmt = $mysqli->prepare("SELECT ID, Name, NameSafe, BaseTime FROM Processes"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $namesafe, $basetime);
	while ($stmt->fetch())
	{
		array_push($ids, $id);
		array_push($names, $name);
		array_push($namesafes, $namesafe);
		array_push($basetimes, $basetime);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


$pcids = array();
$pcpids = array();
$pcrids = array();
$pcamts = array();
$pctypes = array();
if ($stmt = $mysqli->prepare("SELECT ID, PID, RID, Amount, Type FROM ProcessComponents"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $pid, $rid, $amt, $type);
	while ($stmt->fetch())
	{
		array_push($pcids, $id);
		array_push($pcpids, $pid);
		array_push($pcrids, $rid);
		array_push($pcamts, $amt);
		array_push($pctypes, $type);
	}
}
else { throw_msg(301, $httpReferer, "create_faction.php", 39); }

$rids = array();
$rnames = array();
$rtypes = array();
if ($stmt = $mysqli->prepare("SELECT ID, Name, Type FROM Resources"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $type);
	while ($stmt->fetch())
	{
		array_push($rids, $id);
		array_push($rnames, $name);
		array_push($rtypes, $type);
	}
}
else { throw_msg(302, $httpReferer, "create_faction.php", 39); }

function resourceById($id)
{
	for ($i = 0; $i < count($rids); $i++)
	{
		if ($rids[$i] == $id) { return [ "name" => $rnames[$id], "type" => $rnames[$id] ]; }
	}
	return ["name" => "", "type" => -1];
}

?>

<body>
<div id='topbar'></div>
<div id='leftbar'></div>
<div id='rightbar'></div>
<div id='bottombar'></div>
<div class="content">
<h1>Process Builder</h1>

<h3>Processes CSV Download</h3>

<p><a href='bak/process_components.csv'>process_components.csv</a></p>
<p><a href='bak/processes.csv'>processes.csv</a></p>

<h3>Processes</h3>

<form id='form_processes' action='admin_action.php' method='post'>

	<table border='1'>
		<tr>
			<th>Name</th>
			<th>NameSafe</th>
			<th>BaseTime</th>
			<th>Resource</th> <!-- sub -->
			<th>Amount</th> <!-- sub -->
		</tr>

<?php

for ($i = 0; $i < count($ids); $i++)
{
	echo("<tr>
		<td><input type='text' name='name_".$ids[$i]."' value='".$names[$i]."'></td>
		<td><input type='text' name='namesafe_".$ids[$i]."' value='".$namesafes[$i]."'></td>
		<td><input type='text' name='basetime_".$ids[$i]."' value='".$basetimes[$i]."'></td>");



	echo("</tr>");
}


?>




	</table>





</form>


</div>
</body>
