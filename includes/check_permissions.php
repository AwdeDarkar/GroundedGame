<?php
//CONSTANTS:
//	LOGGED_USER_ID, -1 if not logged in
//	LOGGED_USER_LEVEL, 0 by default (don't use to check if logged in)
//	LOGGED_USER_NAME

if (!defined("PERMISSION_LEVEL")) { define("PERMISSION_LEVEL", 10); } //lock the page if no permission level defined

//defaults
$userLevel = 0;
$userID = -1;
$userName = "";
$userSafe = "NULL";

if (isset($_SESSION['UserID']))
{
	$userID = $_SESSION['UserID'];	
	if ($stmt = $mysqli->prepare("SELECT Level, Name, NameSafe FROM Users WHERE ID = ? LIMIT 1"))
	{
		$stmt->bind_param('i', $userID);
		$stmt->execute();
		$stmt->store_result();

		if ($stmt->num_rows > 0) 
		{
			$stmt->bind_result($UserLevel, $UserName, $UserSafe);
			$stmt->fetch();
			$userLevel = $UserLevel;
			$userName = $UserName;
			$userSafe = $UserSafe;
		}
		else { $userID = -1; }
	}
}

//define the constants
define("LOGGED_USER_ID", $userID);
define("LOGGED_USER_LEVEL", $userLevel);
define("LOGGED_USER_NAME", $userName);
define("LOGGED_USER_SAFE", $userSafe);

//check permissions and deny if necessary
if (PERMISSION_LEVEL != -1 && LOGGED_USER_LEVEL < PERMISSION_LEVEL) { throw_msg(302, tools_get_referer("index.php")); }

?>
