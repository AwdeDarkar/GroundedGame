<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");

$sel = -1;
if (isset($_GET['s'])) { $sel = $_GET['s']; } //Need some nice way of checking this or someone could spy on arbitrary MessageGroups

$world = getCurrentWorld();
$bunkerID = tools_sanitize_data($_GET['b']);
$httpReferer = tools_get_referer("index.php");
$facID = getFactionID(LOGGED_USER_ID, $world);

/* Bad, bad code...
$fac = -1;
if (isset($_GET['f'])) { $fac = $_GET['f']; }
if (isset($_GET['gid'])) { $gid = $_GET['gid']; }
*/
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
	
	$GroupListing = "";
	$MessageGroups = "";
	$SelectedGroupName = "";
	if($stmt = $mysqli->prepare("
		SELECT MessageGroups.ID FROM MessageGroups, MessageGroupsParticipants
		WHERE MessageGroups.ID = MessageGroupParticipants.MGID AND MessageGroups.WorldID = ? AND MessageGroupParticipants.FactionID = ?
		"))
	{
		$stmt->bind_param('ss', $world, $facID);
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($MessageGroup);
		
		while($stmt->fetch())
		{
			if($MessageGroup == $sel)
				$GroupListing .= "<li> >";
			else
				$GroupListing .= "<li><a href=messages.php?w=" . $world . "&s=" . $MessageGroup . ">";
			
			if($stmt0 = $mysqli->prepare("
				SELECT Factions.Name FROM Factions, MessageGroupParticipants
				WHERE Factions.ID = MessageGroupParticipants.FactionID AND MessaheGroupParticipants.MGID = ?
				"))
			{
				$stmt0->bind_param('s', $MessageGroup);
				$tempResult = $stmt0->execute();
				$stmt0->store_result();
				$stmt0->bind_result($ParticipantFaction);
				
				while($stmt0->fetch())
				{
					$GroupListing .= $ParticipantFaction  . " ";
					if($MessageGroup == $sel)
						$SelectedGroupName .= $ParticipantFaction  . " ";
				}
			}
			
			if($MessageGroup == $sel)
				$GroupListing .= "</li>";
			else
				$GroupListing .= "</a></li>";
			
		}
	
	echo '
	<div id="msgGroups">
		<a href="nowhere">New Group</a>
		<ul>
			' . $GroupListing . '
		</ul>
	</div>';
	
	if($sel == -1)
	{
		echo
		'<div id="msgCont">
			<h3>Select a message group to view on the right or <a href="nowhere">Create a New One</a>.
		</div>';
	}
	else
	{
		$SelectedGroupMessageHistory = "";
		if ($stmt = $mysqli->prepare("
			SELECT Messages.Content, Factions.Name FROM Factions, Messages
			WHERE Messages.MGID = ? AND Factions.ID = Messages.SrcFactionID
			ORDER BY Messages.DateSent DESC
			"))
		{
			$stmt->bind_param('s', $sel);
			$tempResult = $stmt->execute();  
			$stmt->store_result();
			$stmt->bind_result($MessageContent, $MessageSource);
			
			while($stmt->fetch())
			{
				$SelectedGroupMessageHistory .= "<p>" . $MessageSource . ") " . $MessageContent . "</p><br>";
			}
		
		}
		
		echo '
	<div id="msgCont">
		<h3>' . $SelectedGroupName . '</h3>
		' . $SelectedGroupMessageHistory . '
		
		<textarea name="comment" form="msgComposer">Enter text here...</textarea>
		<form action="/send_message.php" id="msgComposer">
			Name: <input type="text" name="usrname">
			<input type="submit">
		</form>

	</div>
	';
	}
?>
</div>

</body>