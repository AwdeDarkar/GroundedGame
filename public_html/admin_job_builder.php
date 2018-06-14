<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");

$httpReferer = tools_get_referer("index.php");

// type: 
// 0 = none
// 1 = natural resource
// 2 = equipment
// 3 = actor

// query all jobs
$ids = array();
$names = array();
$descriptions = array();
if ($stmt = $mysqli->prepare("SELECT ID, Name, Description FROM Jobs"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name,$description);
	while ($stmt->fetch())
	{
		array_push($ids, $id);
		array_push($names, $name);
		array_push($descriptions, $description);
	}
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }

// query all skill names
$skillNames = array();
if ($stmt = $mysqli->prepare("SELECT Name FROM Skills"))
{
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($skillName);
	while ($stmt->fetch())
	{
		array_push($skillNames, $skillName);
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

$jobFile = "bak/jobs.csv";
$handle = fopen($jobFile, 'w');

$data = "";

for ($i = 0; $i < count($ids); $i++)
{
	if ($i != 0) { $data .= "\n"; }
	$data .= $ids[$i].",".$names[$i].",".$descriptions[$i];
}
fwrite($handle, $data);
fclose($handle);


displayStart();
?>

<h1>Job Builder</h1>

<!--<h3>Resources CSV Upload</h3>

<form id='form_resourcecsv' action='admin_action.php' method='post' enctype="multipart/form-data">
	<input type='file' name='rc_csv'>
	<input type='submit' value='Upload' name='button_uploadresource'>
</form>-->

<h3>Jobs CSV Download</h3>

<p><a href='bak/jobs.csv'>jobs.csv</a></p>

<h3>Jobs</h3>

<div class='float_bottom_left'>
<?php
for($i = 0; $i < count($skillNames); $i++) { echo("<p>".$skillNames[$i]."</p>"); }
?>
</div>

</div>

<form id='form_resources' action='admin_action.php' method='post'>

<table border='1'>
	<tr>
		<th>Name</th>
		<th>Description</th>
		<th>Required Skills</th>
	</tr>

<?php

for ($i = 0; $i < count($ids); $i++)
{
	echo("<tr>
			<td><input type='text' name='name_".$ids[$i]."' value='".$names[$i]."'></td>
			<td><input type='text' name='description_".$ids[$i]."' value=\"".tools_fix_escaped_content_normal($descriptions[$i])."\" size='100'></td>");


	// query all skill names
	$jobSkillNames = array();
	$sids = array();
	if ($stmt = $mysqli->prepare("
		SELECT 
			Skills.Name, 
			JobSkills.SID 
		FROM
			Skills,
			JobSkills
		WHERE
			Skills.ID = JobSkills.SID AND 
			JobSkills.JID = ?"))
	{
		$stmt->bind_param('s', $ids[$i]);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($skillName, $sid);
		while ($stmt->fetch())
		{
			array_push($jobSkillNames, $jobSkillName);
			array_push($sids, $sid);
		}
	}
	else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


	$skillString = implode(',', $jobSkillNames);
		
	echo("
			<td><input type='text' name='skills_'".$ids[$i]."' value='".$skillString."'></td>
			<td><button type='submit' value='".$ids[$i]."' name='update_job_button'>Update</button></td>
			<td><button type='submit' value='".$ids[$i]."' name='delete_job_button'>DELETE</button></td>
		</tr>");
}

?>
	<tr>
		<td><input type='text' name='name_new'></td>
		<td><input type='text' name='description_new' size='100'></td>
		<td><button type='submit' value='new' name='new_job_button'>Insert</button></td>
	</tr>

</table>


</form>

<?php displayEnd(); ?>
