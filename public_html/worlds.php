<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");
//throw_msg(1, "register.php", "thing", 5);	
?>
<body>

<div class="content">
<h1>Hello worlds page!</h1>

<?php
	$WorldNames = "";
	$WorldIDs = "";
	$WorldStatuses = "";
	$NumUsers = "";
	$Members = "";
	$MemberOf = array();

	
	if ($stmt = $mysqli->prepare("
	SELECT Worlds.ID FROM Worlds, Factions
	WHERE Worlds.ID = Factions.WorldID AND Factions.UserID = ?
	"))
	{
		$uid = LOGGED_USER_ID;
		$stmt->bind_param('s', $uid);
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($Members);
		
		while($stmt->fetch())
		{
			array_push($MemberOf, $Members);
		}
	}

	if ($stmt = $mysqli->prepare("
	SELECT Worlds.NameSafe, Worlds.Name, Worlds.ID, Worlds.Status, COUNT(*)-1 FROM Worlds, Factions 
	WHERE Worlds.ID = Factions.WorldID 
	GROUP BY Worlds.ID
	ORDER BY COUNT(*) DESC, Worlds.Name
	"))
	{
		$tempResult = $stmt->execute();  
		$stmt->store_result();
		$stmt->bind_result($NameSafe, $WorldNames, $WorldIDs, $WorldStatuses, $NumUsers);
		
		while($stmt->fetch())
		{
			echo $WorldNames . " " . $WorldStatuses . " " . $NumUsers . " ";
			if(in_array($WorldIDs, $MemberOf))
			{
				echo "<a href=world.php?w=" . $NameSafe . ">GOTO</a>";
			}
			else
			{
				echo "<a href=create_faction.php?wid=" . $WorldIDs . ">JOIN</a>";
			}
			echo "<br>";
		}
	}
	
	//else { throw_msg(300, $errorHttpReferer, "register.php", 105); }
?>
</div>
</body>