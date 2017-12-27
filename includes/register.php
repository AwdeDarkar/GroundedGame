<?php

if (isset($_POST['button_register'], $_POST['reg_username'], $_POST['reg_email'], $_POST['reg_hashed']))
{
	$username = tools_sanitize_data($_POST['reg_username']);
	$email = tools_sanitize_data($_POST['reg_email']);
	$password = tools_sanitize_data($_POST['reg_hashed']);

	$httpReferer = tools_get_referer("index.php");

	
	//remove window variable from regular referer, but for errors, re display the register window on reload
	$httpReferer = tools_remove_get_variable($httpReferer, "w");
	$errorHttpReferer = tools_add_get_variable($httpReferer, "w=reg");
	//$errorHttpReferer = tools_add_get_variable($errorHttpReferer, "something=YOMAN");

	//check database to make sure this username doesn't already exist
	if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Users WHERE Name = ? LIMIT 1"))
	{
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($UserCount);
		$stmt->fetch();

		if ($UserCount > 0) { throw_msg(402, $errorHttpReferer); }
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 30); }

	//get an unused websafe name
	$foundFreeSafeName = false;
	$nameIndex = 1; //the number added onto the end
	$webName = tools_web_safe($username);
	$analysisName = $webName;
	while(!$foundFreeSafeName)
	{
		//check current iteration
		if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Users WHERE NameSafe = ? LIMIT 1"))
		{
			$stmt->bind_param('s', $analysisName);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($UserSafeCount);
			$stmt->fetch();

			if ($UserSafeCount > 0) { $nameIndex++; $analysisName = $webName . $nameIndex; continue; } //haven't found one yet
		}
		else { throw_msg(300, $errorHttpReferer, "register.php", 50); }
		$foundFreeSafeName = true;
	}

	//assign safe websafe name to official websafe name variable
	$webName = $analysisName;

	//check if user with that email already exists
	if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM Users WHERE Email = ? LIMIT 1"))
	{
		$stmt->bind_param('s', $email);
		$tempResult = $stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($EmailCount);
		$stmt->fetch();

		if ($EmailCount > 0) { throw_msg(403, $errorHttpReferer); }
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 105); }
	
	//once we've gotten to this point, all data is validated, need to insert the user into the database, and then send a validiation email
	
	//hash password, create validation hash, insert user
	$hashedPassword = hash('sha512', $password);
	$validationHash = hash('sha512', mt_rand(0,10000000000)); //validation hash is a random number between 1 and ten billion
	$regDate = date("Y-m-d");

	if ($stmt = $mysqli->prepare("INSERT INTO Users(Name, Hash, Verification, Email, DateJoined, NameSafe) VALUES (?, ?, ?, ?, ?, ?)"))
	{
		//set variables
		$stmt->bind_param("ssssss", $username, $hashedPassword, $validationHash, $email, $regDate, $webName);
		
		$result = $stmt->execute();
		$errorMSG = $stmt->error;
	}
	else { throw_msg(300, $errorHttpReferer, "register.php", 86); }
	
	//at this point, user is inserted. Now send email
	$recip = $email;
	$subject = "New Account Verification";
	$veriLink = "http://groundeddev.awdefy.com/verify.php?u=" . rawurlencode($webName) . "&e=" .$email . "&v=" . $validationHash;
	
	$message = "
You have successfully created an account for Grounded!
Please click the following link to verify your email and activate your account:
" . $veriLink . "

If you did not register an account with us, please ignore this email.

(This is an automated message, please do not reply to this email.)

- The AWDE Grounded Team";
	
	$headers = "From: AWDE Grounded Accounts <noreply@awdefy.com>" . PHP_EOL .
		"X-Mailer: PHP/" . phpversion();
	if(!mail($recip, $subject, $message, $headers))
	{
		throw_msg(404, $errorHttpReferer);
	}
	throw_msg(100, $httpReferer);
}
?>
