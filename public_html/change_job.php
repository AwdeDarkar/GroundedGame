<?php
define(PERMISSION_LEVEL, 1);
include("../includes/common.php");

$actorID = tools_sanitize_data($_GET['a']);
$jobID = tools_sanitize_data($_GET['j']);

if ($stmt = $mysqli->prepare("UPDATE Actors SET JID = ?  WHERE ID = ?"))
{
	$stmt->bind_param("ss", $jobID, $actorID);
	$result = $stmt->execute();
}

?>