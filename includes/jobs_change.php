<?php

if ($_POST['update_job_button'])
{
	$id = tools_sanitize_data($_POST['update_job_button']);
	$name = tools_sanitize_data($_POST["name_$id"]);
	$description = tools_sanitize_data($_POST["description_$id"]);
	
	if ($stmt = $mysqli->prepare("UPDATE Jobs 
		SET
			Name = ?,
			Description = ?
		WHERE ID = ?"))
	{
		$stmt->bind_param('sss', $name, $description, $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_job_builder.php"); }
	throw_msg(100, 'admin_job_builder.php');
}
elseif ($_POST['new_job_button'])
{
	$name = tools_sanitize_data($_POST["name_new"]);
	$description = tools_sanitize_data($_POST["description_new"]);
	
	if ($stmt = $mysqli->prepare("INSERT INTO Jobs (Name, Description) VALUES (?,?)"))
	{
		$stmt->bind_param('ss', $name, $description);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_job_builder.php"); }
	throw_msg(100, 'admin_job_builder.php');
}
elseif ($_POST['delete_job_button'])
{
	$id = tools_sanitize_data($_POST['delete_job_button']);
	if ($stmt = $mysqli->prepare("DELETE FROM Jobs WHERE ID = ?"))
	{
		$stmt->bind_param('s', $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_job_builder.php"); }
	throw_msg(100, 'admin_job_builder.php');
}

?>
