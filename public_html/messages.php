<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");

$world = $_GET['w'];
$fac = -1;
if (isset($_GET['f'])) { $fac = $_GET['f']; }
?>

<body>

<div class="content">

<h1>Messages Page</h1>
<h2>Inbox</h2>

<?php
$MessageContent = "";
$MessageSource = "";

if ($stmt = $mysqli->prepare("
	SELECT Messages.Content, Messages.SrcFactionID FROM Messages, MessageGroups, MessageGroupsParticipants
	WHERE MessageGroups.WorldID = ? AND MessageGroupsParticipants.FactionID = ? 
	AND MessageGroups.ID = Messages.MGID AND Messages.MGID = MessageGroupsParticipants.MGID
	"))
{
	$stmt->bind_param('ss', $world, $fac);
	$tempResult = $stmt->execute();  
	$stmt->store_result();
	$stmt->bind_result($MessageContent, $MessageSource);
	
	while($stmt->fetch())
	{
		echo "From: " . $MessageSource . "<br>" . $MessageContent . "<br><br>";
	}
}

?>

<h2>Compose</h2>
<form action="/send_message.php">
  Targets:<br>
  <input type="text" name="targets" value="1"><br>
  Content:<br>
  <input type="text" name="content" value="Text Here"><br><br>
  <input type="submit" value="Send">
</form>
</body>
</div>