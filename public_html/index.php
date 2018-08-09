<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");

displayStart();
?>

<h1>Hello world</h1>

<?php

	if (LOGGED_USER_ID == -1) {	echo("<p><a href='login.php'>Login</a> | <a href='register.php'>Register</a></p>"); }
	else { echo("<p>Welcome " . LOGGED_USER_NAME . "! <a href='logout.php'>Logout</a><?p>"); }
?>

<?php displayEnd(); ?>
