<?php

function content_message_handling()
{
	//print out message window code
	echo("
		<script type='text/javascript' src='res/scripts/message_display.js'></script>
		<div id='MessageWindow' class='msgError' onclick='hideMessage();'>
			<p id='Message'>Hey man, what's going on?</p>
		</div>
	");

	//check if message needs to be displayed
	if (isset($_GET["m"])) { echo("<script type='text/javascript'>displayMessage(".$_GET["m"].");</script>"); }
}

//call this to throw error or redirect with predefined message ready to display
//for error debugging, optionally include a location and line number
function throw_msg($message, $redirect, $loc="N/A", $line=0)
{
	//check to see if message should be logged
	if ($loc != "N/A" && $line > 0) { error_log("ERROR $message, occured in $loc at line reference $line"); }
	
	$redirect = tools_remove_get_variable($redirect, "m");
	$redirect = tools_add_get_variable($redirect, "m=".$message);
	
	tools_redirect($redirect);
	exit;
}
