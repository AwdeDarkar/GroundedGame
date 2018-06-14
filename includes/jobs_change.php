<?php

if ($_POST['update_job_button'])
{
	$id = tools_sanitize_data($_POST['update_job_button']);
	$name = tools_sanitize_data($_POST["name_$id"]);
	$description = tools_sanitize_data($_POST["description_$id"]);
	$skills = tools_sanitize_data($_POST["skills_$id"]);
	$skills_array = explode(',', $skills);

	var_dump($skills);
	var_dump($skills_array);
	
	// update jobs table
	if ($stmt = $mysqli->prepare("UPDATE Jobs 
		SET
			Name = ?,
			Description = ?
		WHERE ID = ?"))
	{
		$stmt->bind_param('sss', $name, $description, $id);
		$stmt->execute();
	}
	else { throw_msg(301, "admin_job_builder.php"); }

	// drop old elems from jobskills table
	if ($stmt = $mysqli->prepare("DELETE FROM JobSkills 
		WHERE JID = ?"))
	{
		$stmt->bind_param('s', $id);
		$stmt->execute();
	}
	else { throw_msg(302, "admin_job_builder.php"); }

	echo("<p>About to do things: ".count($skills_array)."</p>");
	// insert new elems from jobskills table
	for($i = 0; $i < count($skills_array); $i++)
	{
		echo("<p>In a thing</p>");
		//$skills_array[$i];
		
		// obtain skill id
		$sid = 0;
		if ($stmt = $mysqli->prepare("SELECT Skills.ID FROM Skills WHERE Skills.Name = ?"))
		{
			$stmt->bind_param('s', $skills_array[$i]);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($sid);
			$stmt->fetch();
		}
		else { throw_msg(303, $httpReferer, "create_faction.php", 39); }
		echo("<p>sid: ".$sid."</p>");
		
			
		// insert new job skill
		if ($stmt = $mysqli->prepare("INSERT INTO JobSkils (JID, SID) VALUES (?,?)"))
		{
			$stmt->bind_param('ss', $id, $sid);
			$stmt->execute();
		}
		else { throw_msg(304, "admin_job_builder.php"); }
	}
	throw_msg(100, 'admin_job_builder.php');
}
elseif ($_POST['new_job_button'])
{
	$name = tools_sanitize_data($_POST["name_new"]);
	$description = tools_sanitize_data($_POST["description_new"]);
	$skills = tools_sanitize_data($_POST["skills_new"]);
	$skills_array = explode(',', $skills);
	
	if ($stmt = $mysqli->prepare("INSERT INTO Jobs (Name, Description) VALUES (?,?)"))
	{
		$stmt->bind_param('ss', $name, $description);
		$stmt->execute();
	}
	else { throw_msg(305, "admin_job_builder.php"); }
	
	// insert new elems from jobskills table
	for($i = 0; $i < count($skills_array); $i++)
	{
		//$skills_array[$i];
		
		// obtain skill id
		$sid = 0;
		if ($stmt = $mysqli->prepare("SELECT Skills.ID FROM Skills, WHERE Skills.Name = ?"))
		{
			$stmt->bind_param('s', $skills_array[$i]);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($sid);
			$stmt->fetch();
		}
		else { throw_msg(306, $httpReferer, "create_faction.php", 39); }
		
			
		// insert new job skill
		if ($stmt = $mysqli->prepare("INSERT INTO JobSkils (JID, SID) VALUES (?,?)"))
		{
			$stmt->bind_param('ss', $id, $sid);
			$stmt->execute();
		}
		else { throw_msg(307, "admin_job_builder.php"); }
	}
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
	else { throw_msg(308, "admin_job_builder.php"); }
	
	// drop old elems from jobskills table
	if ($stmt = $mysqli->prepare("DELETE FROM JobSkills 
		WHERE JID = ?"))
	{
		$stmt->bind_param('s', $id);
		$stmt->execute();
	}
	else { throw_msg(309, "admin_job_builder.php"); }
	
	throw_msg(100, 'admin_job_builder.php');
}

?>
