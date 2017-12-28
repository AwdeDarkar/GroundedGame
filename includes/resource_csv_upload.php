<?php
echo("<p>Hello?</p>");

if (isset($_POST['button_uploadresource']) && is_uploaded_file($_FILES['filename']['tmp_name']))
{
	echo("<p>hi there</p>");
	readfile($_FILES['filename']['tmp_name']);	
	
	$httpReferer = tools_get_referer("index.php");
	
	//check database to make sure this world name doesn't already exist
	if ($stmt = $mysqli->prepare("TRUNCATE TABLE Resources")) { $stmt->execute(); }
	else { throw_msg(300, $httpReferer, "admin.php", 23); }


	$handle = fopen($_FILES['filename']['tmp_name'], "r");

	while (($data = fgetcsv($handle, 1000, ",")) !== false) 
	{
		$websafe = tools_iterative_web_safe($data[0], "Resources", $httpReferer);
		
		if ($stmt = $mysqli->prepare("INSERT INTO Resources (Name, NameSafe, Type, Frequency, Description) VALUES (?,?,?,?,?)"))
		{
			$stmt->bind_param('sssss', $data[0], $websafe, $data[1], $data[2], $data[3]);
			$stmt->execute();
		}
		else { throw_msg(300, $httpReferer, "admin.php", 23); }
	}

	fclose($handle);	


	
	//throw_msg(100, $httpReferer);
}
?>
