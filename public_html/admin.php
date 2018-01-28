<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");
?>
<body>
<div id='topbar'></div>
<div id='leftbar'></div>
<div id='rightbar'></div>
<div id='bottombar'></div>
<div class="content">
<h1>Admin page</h1>

<h3>Create world</h3>

<script type="text/javascript">
function checkCreateWorldForm()
{
	var inp_world = document.getElementById("cw_worldname");

	//check to make sure both fields are filled
	if (inp_world.value == "") { displayMessage(301); return false; }

	return true;
}
</script>

<form id='form_createworld' action='admin_action.php' onsubmit='return checkCreateWorldForm();' method='post'>
	<input type='text' placeholder='World Name' name='cw_worldname' id='cw_worldname' /></br></br>
	<input type='submit' value='Create' name='button_createworld'>
</form>

<p><a href='admin_resource_builder.php'>Resource Builder</a></p>
<p><a href='admin_process_builder.php'>Process Builder</a></p>
</div>
