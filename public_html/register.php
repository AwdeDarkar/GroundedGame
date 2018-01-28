<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php")
?>
<div id='topbar'></div>
<div id='leftbar'></div>
<div id='rightbar'></div>
<div id='bottombar'></div>
<div class="content">
<h1>Admin page</h1>

<script type='text/javascript' src='scripts/sha512.js'></script>
<script type="text/javascript">

function checkRegisterForm()
{
	var inp_username = document.getElementById("reg_username");
	var inp_email = document.getElementById("reg_email");
	var inp_pass = document.getElementById("reg_pass");
	var inp_passagain = document.getElementById("reg_passagain");

	//check to make sure all fields are filled
	if (inp_username.value == "" || inp_email.value == "" || inp_pass.value == "" || inp_passagain.value == "") { displayMessage(301); return false; }

	//check to make sure email is a valid email
	//TODO: regex? (AWDE HAS A COPY OF PROPER REGEX STRING)
	//TODO: check password length
	if (inp_email.value.indexOf("@") === -1) { displayMessage(400); return false; }

	//check to make sure password fields match
	if (inp_pass.value != inp_passagain.value) { displayMessage(401); return false; }

	//create hidden input field to hold hashed password then reset visible password
	var hashed = document.createElement("input");
	hashed.name = "reg_hashed";
	hashed.type = "hidden";
	hashed.value = hex_sha512(inp_pass.value);
	document.getElementById("form_register").appendChild(hashed);

	inp_pass.value = "";
	inp_passagain.value = "";

	return true;
}

</script>

<h1>Register</h1>

<form id="form_register" action="handle_user.php" onsubmit="return checkRegisterForm();" method="post">
	<input type="text" placeholder="Username" name="reg_username" id="reg_username" /></br></br>
	<input type="text" placeholder="Email" name="reg_email" id="reg_email" /></br></br>
	<input type="password" placeholder="Password" name="reg_pass" id="reg_pass" /></br></br>
	<input type="password" placeholder="Re-type password" name="reg_passagain" id="reg_passagain" /></br></br>
	<input type="submit" name="button_register" id="button_register" value="Register">
</form>
</div>
