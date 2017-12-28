<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
//throw_msg(1, "register.php", "thing", 5);	
?>

<h1>Hello worlds page!</h1>

<?php
	$WorldNames = "";
	$WorldStatuses = "";
	
	if ($stmt = $mysqli->prepare("SELECT Worlds.Name, Worlds.Status FROM Worlds"))
	{
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($WorldNames, $WorldStatuses);
		
		while($stmt->fetch())
		{
			echo $WorldNames . " " . $WorldStatuses . "<br>";
		}
	}
	
	else { throw_msg(300, $errorHttpReferer, "register.php", 105); }
?>
