<?php

var_dump($_POST);

if ($_POST['update_button'])
{
	$id = tools_sanitize_data($_POST['update_button']);
	$name = tools_sanitize_data($_POST["name_$id"]);
	$namesafe = tools_sanitize_data($_POST["namesafe_$id"]);
	$type = tools_sanitize_data($_POST["type_$id"]);
	$frequency = tools_sanitize_data($_POST["frequency_$id"]);
	$description = tools_sanitize_data($_POST["description_$id"]);
	
	if ($stmt = $mysqli->prepare("UPDATE Resources 
		SET
			Name = ?,
			NameSafe = ?,
			Type = ?,
			Frequency = ?,
			Description = ?
		WHERE ID = ?"))
	{
		$stmt->bind_param('ssssss', $name, $namesafe, $type, $frequency, $description, $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }
	throw_msg(100, 'admin_resource_builder.php');
}
elseif ($_POST['new_button'])
{
	$name = tools_sanitize_data($_POST["name_new"]);
	$namesafe = tools_sanitize_data($_POST["namesafe_new"]);
	$type = tools_sanitize_data($_POST["type_new"]);
	$frequency = tools_sanitize_data($_POST["frequency_new"]);
	$description = tools_sanitize_data($_POST["description_new"]);
	
	if ($stmt = $mysqli->prepare("INSERT INTO Resources (Name, NameSafe, Type, Frequency, Description) VALUES (?,?,?,?,?)"))
	{
		$stmt->bind_param('sssss', $name, $namesafe, $type, $frequency, $description);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }
	throw_msg(100, 'admin_resource_builder.php');
}
elseif ($_POST['delete_button'])
{
	$id = tools_sanitize_data($_POST['delete_button']);
	if ($stmt = $mysqli->prepare("DELETE FROM Resources WHERE ID = ?"))
	{
		$stmt->bind_param('s', $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }
	throw_msg(100, 'admin_resource_builder.php');
}

?>
