<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");

# name, description, type, frequency

?>
<h1>Resource Builder</h1>

<h3>New Resource</h3>

<form id='form_newresource' action='admin_action.php' onsubmit='return checkNewResourceForm();' method='post'>
	<input type='text' placeholder='Resource Name' name='nr_name' id='nr_name' />
	<input type='text' placeholder='Type' name='nr_type' id='nr_type' />
	<input type='text' placeholder='Frequency' name='nr_freq' id='nr_freq' />
	<input type='text' placeholder='Frequency' name='nr_freq' id='nr_freq' />
	<textarea name='nr_desc'></textarea>
	<input type='submit' value='Create' name='button_createresource'>
</form>
