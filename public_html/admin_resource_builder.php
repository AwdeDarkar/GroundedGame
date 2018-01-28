<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");

$httpReferer = tools_get_referer("index.php");



// type: 1 = natural resource
// 0 = none
// 2 = equipment

// query all resource collections
$ids = array();
$names = array();
$namesafes = array();
$types = array();
$frequencies = array();
$descriptions = array();
if ($stmt = $mysqli->prepare("SELECT ID, Name, NameSafe, Type, Frequency, Description FROM Resources"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $namesafe, $type, $frequency, $description);
	while ($stmt->fetch())
	{
		array_push($ids, $id);
		array_push($names, $name);
		array_push($namesafes, $namesafe);
		array_push($types, $type);
		array_push($frequencies, $frequency);
		array_push($descriptions, $description);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

// prepare a csv backup file for download
// thanks to: https://stackoverflow.com/questions/356578/how-to-output-mysql-query-results-in-csv-format 
// 
/*if ($stmt = $mysqli->prepare("SELECT *
	FROM Resources
	INTO OUTFILE '/home/awdefy/CSV_EXPORT/resources.csv'
	FIELDS TERMINATED BY ','
	ENCLOSED BY '\"'
	LINES TERMINATED BY '\n';"))
{
	$stmt->execute();
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }*/

$resourceFile = "bak/resources.csv";
$handle = fopen($resourceFile, 'w');

$data = "";

for ($i = 0; $i < count($ids); $i++)
{
	if ($i != 0) { $data .= "\n"; }
	$data .= $ids[$i].",".$names[$i].",".$namesafes[$i].",".$types[$i].",".$frequencies[$i].",".$descriptions[$i];
}
fwrite($handle, $data);
fclose($handle);



?>
<h1>Resource Builder</h1>

<!--<h3>Resources CSV Upload</h3>

<form id='form_resourcecsv' action='admin_action.php' method='post' enctype="multipart/form-data">
	<input type='file' name='rc_csv'>
	<input type='submit' value='Upload' name='button_uploadresource'>
</form>-->

<h3>Resources CSV Download</h3>

<p><a href='bak/resources.csv'>resources.csv</a></p>

<h3>Resources</h3>

<form id='form_resources' action='admin_action.php' method='post'>

<table border='1'>
	<tr>
		<th>Name</th>
		<th>Name (Web Safe)</th>
		<th>Type</th>
		<th>Frequency</th>
		<th>Description</th>
	</tr>

<?php

for ($i = 0; $i < count($ids); $i++)
{
	echo("<tr>
			<td><input type='text' name='name_".$ids[$i]."' value='".$names[$i]."'></td>
			<td><input type='text' name='namesafe_".$ids[$i]."' value='".$namesafes[$i]."'></td>

			<td>
				<select name='type_".$ids[$i]."'>");

	if ($types[$i] == 0) { echo("<option value='0' selected>Regular</option>"); }
	else { echo("<option value='0'>Regular</option>"); }
	if ($types[$i] == 1) { echo("<option value='1' selected>Natural</option>"); }
	else { echo("<option value='1'>Natural</option>"); }
	if ($types[$i] == 2) { echo("<option value='2' selected>Equipment</option>"); }
	else { echo("<option value='2'>Equipment</option>"); }


	echo("
				</select>
			</td>

			<td><input type='text' name='frequency_".$ids[$i]."' value='".$frequencies[$i]."'></td>
			<td><input type='text' name='description_".$ids[$i]."' value='".$descriptions[$i]."' size='100'></td>
			<td><button type='submit' value='".$ids[$i]."' name='update_button'>Update</button></td>
		</tr>");
}

?>
	<tr>
		<td><input type='text' name='name_new'></td>
		<td><input type='text' name='namesafe_new'></td>

		<td>
			<select name='type_new'>"
				<option value='0' selected>Regular</option>
				<option value='1'>Natural</option>
				<option value='2'>Equipment</option>
			</select>
		</td>

		<td><input type='text' name='frequency_new'></td>
		<td><input type='text' name='description_new' size='100'></td>
		<td><button type='submit' value='new' name='new_button'>Insert</button></td>
	</tr>

</table>


</form>
