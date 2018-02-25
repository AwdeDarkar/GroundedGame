<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");

if(isset($_POST["names"]) && isset($_POST["world"]))
{
	/*
	$worldID = getWorldID($_POST["world"]);
	if ($stmt = $mysqli->prepare("INSERT INTO MessageGroups(WorldID) VALUES (?)"))
	{
		$stmt->bind_param("s", $worldID);
		$result = $stmt->execute();
	}
	*/
	var_dump($_POST["names"]);
}
?>
Hi.