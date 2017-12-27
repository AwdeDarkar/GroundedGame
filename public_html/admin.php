<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php")
?>
<h1>Admin page</h1>

<h3>Create world</h3>

<script type="text/javascript">


function checkCreateWorldForm()
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

<form id='form_createworld' action='admin_action.php' onsubmit='return checkCreateWorldForm();' method='post'>
	<input type='text' placeholder='World Name' name='cw_worldname' id='cw_worldname' /></br></br>
	<input type='submit' value='Create' name='button_createworld'>
</form>
