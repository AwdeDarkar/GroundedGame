<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
require_once "../includes/external/crush/css-crush/CssCrush.php";
echo "<head>
	<title>Grounded Game Dev</title>
	<meta charset='UTF-8'>
	<meta name='description' content='Development of the \'grounded\' game'>
	<meta name='keywords' content='Incomplete,Testing'>
	<meta name='author' content='WildfireXIII,Darkar'>
	<meta name'viewport' content='width=device-width, initial-scale=1'>
		
	<!-- jQuery library -->
	<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
	
	<!-- Fonts from Google -->
	<link href='https://fonts.googleapis.com/css?family=David+Libre|Inconsolata|Work+Sans' rel='stylesheet'>
	
	<!-- Css Crush Stylesheet -->
	" . csscrush_tag('resources/styles/styles.css') . "
	
	<script>
		//page load optimization
	</script>
		
	<script>
		//analytics
	</script>
</head>"
?>

<h1>Hello world</h1>

<?php

	if (LOGGED_USER_ID == -1) {	echo("<p><a href='login.php'>Login</a> | <a href='register.php'>Register</a></p>"); }
	else { echo("<p>Welcome " . LOGGED_USER_NAME . "! <a href='logout.php'>Logout</a><?p>"); }
?>
