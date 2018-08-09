<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");

if (isset($_GET['u'], $_GET['e'], $_GET['v']))
{
	$username = tools_sanitize_data($_GET['u']);
	$email = tools_sanitize_data($_GET['e']);
	$verification = tools_sanitize_data($_GET['v']);

	if ($stmt = $mysqli->prepare("SELECT Level FROM Users WHERE NameSafe = ? AND Email = ? AND Verification = ?"))
	{
		$stmt->bind_param('sss', $username, $email, $verification);
		$stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows() < 1) { throw_msg(406, "index.php"); }
		
		$stmt->bind_result($UserLevel);
		$stmt->fetch();

		if ($UserLevel != 0) { throw_msg(407, "index.php"); }
	}
	else { throw_msg(300, "index.php", "verify.php", 33); }

	//update user permissions
	if ($stmt = $mysqli->prepare("UPDATE Users Set Level = 1 WHERE NameSafe = ? AND Email = ? AND Verification = ? LIMIT 1"))
	{
		$stmt->bind_param('sss', $username, $email, $verification);
		$stmt->execute();
		throw_msg(103, "index.php");
	}
	else { throw_msg(300, "index.php", "verify.php", 41); }
}
else { throw_msg(303, "index.php", "verify.php", 44); }
?>
