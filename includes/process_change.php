<?php

#var_dump($_POST);


if ($_POST['update_process_button'])
{
	$id = tools_sanitize_data($_POST['update_process_button']); //process id
	$name = tools_sanitize_data($_POST['name_'.$id]); 
	$namesafe = tools_sanitize_data($_POST['namesafe_'.$id]); 
	$basetime = tools_sanitize_data($_POST['basetime_'.$id]); 
	
	if ($stmt = $mysqli->prepare("UPDATE Process 
		SET
			Name = ?, 
			NameSafe = ?, 
			BaseTime = ?
		WHERE ID = ?"))
	{
		$stmt->bind_param('ssss', $name, $namesafe, $basetime, $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }

	$id = $mysqli->insert_id;
	throw_msg(100, 'admin_resource_builder.php#'.$id);
}
elseif ($_POST['delete_process_button'])
{
	$id = tools_sanitize_data($_POST['delete_process_button']); 
	
	if ($stmt = $mysqli->prepare("DELETE FROM Process WHERE ID = ?"))
	{
		$stmt->bind_param('s', $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }
	throw_msg(100, 'admin_resource_builder.php');
}
elseif ($_POST['new_process_button'])
{
	$name = tools_sanitize_data($_POST['name_new']); 
	$namesafe = tools_sanitize_data($_POST['namesafe_new']); 
	$basetime = tools_sanitize_data($_POST['basetime_new']); 
	
	if ($stmt = $mysqli->prepare("INSERT INTO Process (Name, NameSafe, BaseTime) VALUES (?,?,?)"))
	{
		$stmt->bind_param('sss', $name, $namesafe, $basetime);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }

	$id = $mysqli->insert_id;
	throw_msg(100, 'admin_resource_builder.php#'.$id);
}
elseif ($_POST['update_pc_button'])
{
	$id = tools_sanitize_data($_POST['update_pc_button']); 
	$rid = tools_sanitize_data($_POST['resource_'.$id]); 
	$type = tools_sanitize_data($_POST['type_'.$id]); 
	$amt = tools_sanitize_data($_POST['amt_'.$id]); 
	
	if ($stmt = $mysqli->prepare("UPDATE ProcessComponents 
		SET 
			RID = ?, 
			Amount = ?, 
			Type = ?
		WHERE ID = ?"))
	{
		$stmt->bind_param('ssss', $rid, $type, $amt, $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }
	throw_msg(100, 'admin_resource_builder.php');
}
elseif ($_POST['delete_pc_button'])
{
	$id = tools_sanitize_data($_POST['delete_pc_button']); 
	
	if ($stmt = $mysqli->prepare("DELETE FROM ProcessComponents WHERE ID = ?"))
	{
		$stmt->bind_param('s', $id);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }
	throw_msg(100, 'admin_resource_builder.php');
}
elseif ($_POST['new_pc_button'])
{
	$id = tools_sanitize_data($_POST['new_pc_button']); //process id
	$rid = tools_sanitize_data($_POST['resource_new_'.$id]); 
	$type = tools_sanitize_data($_POST['type_new_'.$id]); 
	$amt = tools_sanitize_data($_POST['amt_new_'.$id]); 
	
	if ($stmt = $mysqli->prepare("INSERT INTO ProcessComponents (PID, RID, Amount, Type) VALUES (?,?,?,?)"))
	{
		$stmt->bind_param('ssss', $id, $rid, $type, $amt);
		$stmt->execute();
	}
	else { throw_msg(300, "admin_resource_builder.php"); }
	throw_msg(100, 'admin_resource_builder.php#'.$id);
}
