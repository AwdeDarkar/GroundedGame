<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");

include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");

$httpReferer = tools_get_referer("index.php");


// process component types: 0 = input, 1 = output, 2 = equipment

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
			<th></th>
			<th>Name</th>
			<th>NameSafe</th>
			<th>BaseTime</th>
			<th>Resource</th> <!-- sub -->
			<th>Type</th> <!-- sub -->
			<th>Amount</th> <!-- sub -->
		</tr>

<?php

for ($i = 0; $i < count($ids); $i++)
{
	echo("<tr>
		<td></td>
		<td><input type='text' name='name_".$ids[$i]."' value='".$names[$i]."'></td>
		<td><input type='text' name='namesafe_".$ids[$i]."' value='".$namesafes[$i]."'></td>
		<td><input type='text' name='basetime_".$ids[$i]."' value='".$basetimes[$i]."' size='2'></td><td></td><td></td><td></td><td><button type='submit' value='".$ids[$i]."' name='update_process_button'>Update</td><td><button type='submit' value='".$ids[$i]."' name='delete_process_button'>DELETE</td></tr>");

	for ($j = 0; $j < count($pcids); $j++)
	{
		if ($pcpids[$j] == $ids[$i])
		{
			echo("<tr>
				<td></td><td></td><td></td><td></td>
				<td><select name='resource_".$pcids[$j]."'>");

			$selected = $pcrids[$j];
			for ($k = 0; $k < count($rids); $k++)
			{
				echo("<option value='".$rids[$k]."'");
				if ($rids[$k] == $selected) { echo(" selected"); }
				echo(">".$rnames[$k]."</option>");
			}

			echo("</select></td>
				<td>
					<select name='type_".$pcids[$j]."'>");

			if ($pctypes[$j] == 0) { echo("<option value='0' selected>Input</option>"); }
			else { echo("<option value='0'>Input</option>"); }
			if ($pctypes[$j] == 1) { echo("<option value='1' selected>Output</option>"); }
			else { echo("<option value='1'>Output</option>"); }
			if ($pctypes[$j] == 2) { echo("<option value='2' selected>Equipment</option>"); }
			else { echo("<option value='2'>Equipment</option>"); }


			echo("
					</select>
				</td>
				<td><input type='text' name='amt_".$pcids[$j]."' value='".$pcamts[$j]."' size='2'></td>

				<td><button type='submit' value='".$pcids[$j]."' name='update_pc_button'>Update</button></td>
				<td><button type='submit' value='".$pcids[$j]."' name='delete_pc_button'>DELETE</button></td>
			</tr>");
		}
	}
	echo("<tr>
		<td></td><td></td><td></td><td></td>
		<td><select name='resource_new_".$ids[$i]."'>");

	echo("<option value='' selected></option>");
	for ($k = 0; $k < count($rids); $k++) { echo("<option value='".$rids[$k]."'>".$rnames[$k]."</option>"); }

	echo("</select></td>
		<td>
			<select name='type_new_".$ids[$i]."'>");

	echo("<option value='' selected></option>");
	echo("<option value='0'>Input</option>");
	echo("<option value='1'>Output</option>");
	echo("<option value='2'>Equipment</option>");


	echo("
			</select>
		</td>
		<td><input type='text' name='amt_new_".$ids[$i]."' value='' size='2'></td>

		<td><button type='submit' value='".$ids[$i]."' name='new_pc_button'>Insert</button></td>
	</tr>");
	if ($i != count($ids) - 1) { echo("<tr><td>&nbsp;</td></tr>"); }


}


?>




	</table>





</form>


</div>
</body>
