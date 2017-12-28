<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$world = $_GET['w'];
$fac = -1;
if (isset($_GET['f'])) { $fac = $_GET['f']; }

$httpReferer = tools_get_referer("index.php");

// get some pertinent info

// get world name
$worldname = "";
if ($stmt = $mysqli->prepare("SELECT Name FROM Worlds WHERE NameSafe = ? LIMIT 1"))
{
	$stmt->bind_param('s', $world);
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($worldname);
	$stmt->fetch();

	if ($UserCount > 0) { throw_msg(402, $httpReferer); }
}
else { throw_msg(300, $httpReferer, "create_faction.php", 39); }


?>

<h1><?php echo("$worldname"); ?> World Map </h1>
