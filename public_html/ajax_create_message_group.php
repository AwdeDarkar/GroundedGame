<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");

if(isset($_POST["names"]) && isset($_POST["world"]))
{

	$worldID = getWorldID($_POST["world"]);
	if ($stmt = $mysqli->prepare("INSERT INTO MessageGroups(WorldID) VALUES (?)"))
	{
		$stmt->bind_param("s", $worldID);
		$result = $stmt->execute();
	}
	
	$groupID = $mysqli->insert_id;

	$NamesList = json_decode($_POST["names"]);
	foreach($NamesList as &$Name)
	{
		if ($stmt = $mysqli->prepare("INSERT INTO MessageGroupParticipants(MGID, FactionID) SELECT ?, Factions.ID FROM Factions WHERE Faction.Name = ?"))
		{
			$stmt->bind_param("ss", $groupID, $Name);
			$result = $stmt->execute();
		}
	}
	
	echo "" . $groupID;
}
?>