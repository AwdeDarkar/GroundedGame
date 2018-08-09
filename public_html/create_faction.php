<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");


// assume world id passed in as get var
$worldid = $_GET['wid'];
$userid = LOGGED_USER_ID;

$httpReferer = tools_get_referer("index.php");

// check that we can actually make a faction (no faction yet associating this
// user and world)
if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Factions WHERE UserID = ? AND WorldID = ? LIMIT 1"))
{
	$stmt->bind_param('ss', $userid, $worldid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($UserCount);
	$stmt->fetch();

	if ($UserCount > 0) { throw_msg(402, $httpReferer); }
}
else { throw_msg(300, $httpReferer, "create_faction.php", 30); }


// get world name
$worldname = "";
if ($stmt = $mysqli->prepare("SELECT Name FROM Worlds WHERE ID = ? LIMIT 1"))
{
	$stmt->bind_param('s', $worldid);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($worldname);
	$stmt->fetch();

	if ($UserCount > 0) { throw_msg(402, $httpReferer); }
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


displayStart();
?>

<h1>Create new faction in <?php print("$worldname"); ?> </h1>


<script type="text/javascript">

function checkFactionName()
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

<form id="form_newfaction" action="handle_faction.php" onsubmit="return checkFactionName();" method="post">
	<input type="text" placeholder="Faction Name" name="nf_name" id="nf_name" /></br></br>
	<input type="hidden" name="nf_wid" id='nf_wid' value=<?php print("'$worldid'");?> />
	<input type="submit" value="New Faction" name='button_nf'>
</form>

<?php displayEnd(); ?>
