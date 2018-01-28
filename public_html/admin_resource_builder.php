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



?>
<h1>Resource Builder</h1>

<h3>Resources CSV Upload</h3>

<form id='form_resourcecsv' action='admin_action.php' method='post' enctype="multipart/form-data">
	<input type='file' name='rc_csv'>
	<input type='submit' value='Upload' name='button_uploadresource'>
</form>

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
			<td>".$names[$i]."</td>
			<td>".$namesafes[$i]."</td>
			<td>".$types[$i]."</td>
			<td>".$frequencies[$i]."</td>
			<td>".$descriptions[$i]."</td>
		</tr>");
}

?>




</table>







</form>
