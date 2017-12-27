<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php")
?>

<h1>Login</h1>

<script type='text/javascript' src='scripts/sha512.js'></script>

<script type="text/javascript">


function checkLoginForm()
{
	var inp_username = document.getElementById("log_username");
	var inp_pass = document.getElementById("log_pass");

	//check to make sure both fields are filled
	if (inp_username.value == "" || inp_pass.value == "") { displayMessage(301); return false; }

	//create hidden input field to hold hashed password then reset visible password (prevent man in the middle)
	var hashed = document.createElement("input");
	hashed.name = "log_hashed";
	hashed.type = "hidden";
	hashed.value = hex_sha512(inp_pass.value);
	document.getElementById("form_login").appendChild(hashed);

	inp_pass.value = "";
	
	return true;
}
</script>

<form id="form_login" action="handle_user.php" onsubmit="return checkLoginForm();" method="post">
	<input type="text" placeholder="Username" name="log_username" id="log_username" /></br></br>
	<input type="password" placeholder="Password" name="log_pass" id="log_pass" /></br></br>
	<input type="submit" value="Log in">&nbsp;&nbsp;<a href='reset_password.php'>forgot password</a>
</form>
