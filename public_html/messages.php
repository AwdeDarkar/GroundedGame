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

<div id='topbar'></div>
<div id='leftbar'></div>
<div id='rightbar'></div>
<div id='bottombar'></div>
<div class="content">

<h1>Messages Page</h1>
<h2>Inbox</h2>

<?php
/*
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
*/
?>
<div id="msgGroups">
	<a href="nowhere">New Group</a>
	<ul>
		<li>1 2 4 5</li>
		<li>2</li>
		<li>2 3</li>
		<li><b>1 2 3</b></li>
		<li>5</li>
	</ul>
</div>

<div id="msgCont">
	<h3>1 2 3</h3>
	<p class="msg other">(1) Yes, I think our conspiracy against 5 is proceeding well. As long as none of us betray the other everything will go perfectly</p>
	<p class="msg user">Of course, thank goodness we're all so loyal to each other.</p>
	<p class="msg other">(3) I was just going to remark on that. The loyalty, I mean.</p>
	
	<textarea name="comment" form="msgComposer">Enter text here...</textarea>
	<form action="/send_message.php" id="msgComposer">
		Name: <input type="text" name="usrname">
		<input type="submit">
	</form>

</div>

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
