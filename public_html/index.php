<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
//throw_msg(1, "register.php", "thing", 5);
echo csscrush_tag('css/styles.css');
?>

<h1>Hello world</h1>

<?php

	if (LOGGED_USER_ID == -1) {	echo("<p><a href='login.php'>Login</a> | <a href='register.php'>Register</a></p>"); }
	else { echo("<p>Welcome " . LOGGED_USER_NAME . "! <a href='logout.php'>Logout</a><?p>"); }
?>
