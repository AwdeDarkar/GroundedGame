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
		
		}?[M
	*/
	
	$GroupListing = "";
	$MessageGroups = "";
	$SelectedGroupName = "";
	$SelectedMGID = -1;
	if($stmt = $mysqli->prepare("
		SELECT MessageGroups.ID FROM MessageGroups, MessageGroupParticipants, Worlds
		WHERE MessageGroups.ID = MessageGroupParticipants.MGID AND MessageGroups.WorldID = Worlds.ID AND Worlds.NameSafe = ? AND MessageGroupParticipants.FactionID = ?
		"))
	{
		$stmt->bind_param('ss', $world, $facID);
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($MessageGroup);
		
		while($stmt->fetch())
		{
			if($MessageGroup == $sel)
				$GroupListing .= "<li>";
			else
				$GroupListing .= "<li><a href=messages.php?w=" . $world . "&s=" . $MessageGroup . ">";
			
			if($stmt0 = $mysqli->prepare("
				SELECT Factions.Name FROM Factions, MessageGroupParticipants
				WHERE Factions.ID = MessageGroupParticipants.FactionID AND MessageGroupParticipants.MGID = ?
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
			else { throw_msg(300, $httpReferer, "messages.php", 94); }
			
			if($MessageGroup == $sel)
				$GroupListing .= "</li>";
			else
				$GroupListing .= "</a></li>";
			
		}
	}
	else { throw_msg(300, $httpReferer, "messages.php", 103); }
	
	echo '
	<div id="msgGroups">
		<a href="create_message_group.php?w=' . $world . '">New Group</a>
		<ul>
			' . $GroupListing . '
		</ul>
	</div>';
	
	if($sel == -1)
	{
		echo
		'<div id="msgCont">
			<h3>Select a message group to view on the right or create a new one.
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
				$SelectedGroupMessageHistory = "<p>" . $MessageSource . ") " . $MessageContent . "</p>" . $SelectedGroupMessageHistory;
			}
		
		}
		
		echo '
	<div id="msgCont">
		<h3>' . $SelectedGroupName . '</h3>
		<div id="msgListing">
		' . tools_fix_escaped_content_normal($SelectedGroupMessageHistory) . '
		</div>
		
		
		<form action="/send_message.php" id="msgComposer" method="post">
			<textarea name="comment" form="msgComposer" placeholder="Enter text here..." cols=64 rows=3></textarea>
			<input type="hidden" name="mgid" value="' . $sel . '">
			<input type="hidden" name="fac" value="' . $facID . '"><br>
			<input type="submit" class="button"><br><br><br><br>
		</form>

	</div>
	';
	}
?>
</div>

</body>
