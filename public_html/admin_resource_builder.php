<?php
define(PERMISSION_LEVEL, 2);
include("../includes/common.php");

# name, description, type, frequency

?>
<h1>Resource Builder</h1>

<h3>Resources CSV Upload</h3>

<form id='form_resourcecsv' action='admin_action.php' method='post' enctype="multipart/form-data">
	<input type='file' name='rc_csv'>
	<input type='submit' value='Upload' name='button_uploadresource'>
</form>

<h3>Resources</h3>
