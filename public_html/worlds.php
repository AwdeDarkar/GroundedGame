<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
//throw_msg(1, "register.php", "thing", 5);	
?>

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
	
	echo "here" . var_dump($MemberOf);
	
	if ($stmt = $mysqli->prepare("
	SELECT Worlds.Name, Worlds.Status, COUNT(*)-1, FROM Worlds, Factions 
	WHERE Worlds.ID = Factions.WorldID 
	GROUP BY Worlds.ID
	ORDER BY COUNT(*) DESC, Worlds.Name
	"))
	{
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($WorldNames, $WorldIDs, $WorldStatuses, $NumUsers);
		
		while($stmt->fetch())
		{
			echo $WorldNames . " " . $WorldStatuses . " " . $NumUsers . " ";
			if(in_array($WorldIDs, $MemberOf))
			{
				echo "[GOTO]";
			}
			else
			{
				echo "[JOIN]";
			}
			echo "<br>";
		}
	}
	
	//else { throw_msg(300, $errorHttpReferer, "register.php", 105); }
?>