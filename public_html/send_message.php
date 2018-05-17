<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

echo var_dump($_POST);

if( isset($_POST["comment"]) && isset($_POST["mgid"]) && isset($_POST["fac"]) )
{
	$text = tools_sanitize_data($_POST["comment"]);
	$mgid = tools_sanitize_data($_POST["mgid"]);
	$fac = tools_sanitize_data($_POST["fac"]);
	
	//$worldID = getWorldID($_POST["world"]);
	if ($stmt = $mysqli->prepare("INSERT INTO Messages(SrcFactionID, MGID, DateSent, Content) VALUES (?, ?, ?, ?)"))
	{
		$stmt->bind_param("ssss", $fac, $mgid, date("Y-m-d H:i:s"), $text);
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
	
}
else { echo "oops"; }
echo "<script> window.location.replace(" . $_SERVER['HTTP_REFERER'] . ");</script>";
?>