<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
//throw_msg(1, "register.php", "thing", 5);	
?>

<h1>Hello world</h1>

<?php
	$WorldList = "";
	
	if ($stmt = $mysqli->prepare("SELECT Worlds.Name, Worlds.Status FROM Worlds"))
	{
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($WorldList);
		$stmt->fetch();

		foreach($WorldList as $world)
		{
			echo var_dump($world) . "<br>";
		}
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 105); }
?>
