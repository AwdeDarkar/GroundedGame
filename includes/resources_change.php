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
	throw_msg(100, 'admin_resource_builder.php');
}
elseif ($_POST['new_button'])
{
	
}

?>
