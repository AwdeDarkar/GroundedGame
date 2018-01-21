<?php

if (isset($_POST['button_login'], $_POST['log_username'], $_POST['log_hashed']))
{
	$username = tools_sanitize_data($_POST['log_username']);
	$password = tools_sanitize_data($_POST['log_hashed']);

	$httpReferer = tools_get_referer("index.php");


	$httpReferer = tools_remove_get_variable($httpReferer, "w");
	$errorHttpReferer = tools_add_get_variable($httpReferer, "w=log");
	
	if (login($username, $password) == true) { throw_msg(101, "index.php"); }
	else { throw_msg(405, $errorHttpReferer); }
}

function login($user, $password)
{
	global $mysqli;

	if ($stmt = $mysqli->prepare("SELECT ID, Name, Hash FROM Users WHERE Name = ? LIMIT 1"))
	{
		$stmt->bind_param('s', $user);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($UserID, $UserName, $UserHash);
		$stmt->fetch();

		$password = hash('sha512', $password);

		if ($stmt->num_rows() < 1) { return false; }
		if ($password === $UserHash)
		{
			$_SESSION['UserID'] = $UserID;
			return true;
		}
		else { return false; }
	}
	else { return false; }
}

?>
