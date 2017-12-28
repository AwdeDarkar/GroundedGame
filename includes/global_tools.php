<?php
function tools_redirect($location)
{
	if (!headers_sent()) { header("Location: " . $location); }
	else { echo("<script>window.location = '" . $location . "';</script>"); }
}

function tools_sanitize_data($data, $allowTags="")
{
	global $mysqli;
	
	$data = strip_tags($data, $allowTags);
	$data = mysqli_real_escape_string($mysqli, $data);
	return $data;
}

function tools_iterative_web_safe($name, $table, $referer)
{
	global $mysqli;
	//get an unused websafe name
	$foundFreeSafeName = false;
	$nameIndex = 1; //the number added onto the end
	$webName = tools_web_safe($name);
	$analysisName = $webName;
	while(!$foundFreeSafeName)
	{
		//check current iteration
		if ($stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table WHERE NameSafe = ? LIMIT 1"))
		{
			$stmt->bind_param('s', $analysisName);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($SafeCount);
			$stmt->fetch();

			if ($SafeCount > 0) { $nameIndex++; $analysisName = $webName . $nameIndex; continue; } //haven't found one yet
		}
		else { throw_msg(300, $referer, "tools.php", 50); }
		$foundFreeSafeName = true;
	}

	return $webName;
}
	

function tools_web_safe($name)
{
	//TODO: add all symbols
	$name = strtolower($name);
	$name = str_replace(" ","_", $name);
	$name = str_replace("?","", $name);
	$name = str_replace("!","", $name);
	$name = str_replace(".","", $name);
	$name = str_replace(",","", $name);
	$name = str_replace("=","-", $name);
	$name = str_replace("+","-", $name);
	$name = str_replace("&","-", $name);
	$name = str_replace("%","", $name);
	$name = str_replace(":", "", $name);
	$name = str_replace("\'", "", $name);
	$name = str_replace("\"", "", $name);
	
	return $name;
}

function tools_remove_get_variable($url, $variable)
{
	if (strpos($url, "?") != false) //we found variables!
	{
		$getVariables = substr($url, strpos($url, "?")); //get everything after ? (including the ?)

		//check for the given variable name
		if (strpos($getVariables , $variable."=") != false)
		{
			$varPos = strpos($getVariables, $variable."=");
			$afterVarPos = substr($getVariables, $varPos); //everything after (and including) the variable
			$beforeVarPos = substr($getVariables, 0, $varPos - 1); //everything before the variable (DOES NOT INCLUDE VARIABLE DELIMITER)

			if (strpos($afterVarPos, "&") != false) //found more variables after this one
			{
				$nextVarPos = strpos($afterVarPos, "&");
				$afterNextVar = substr($afterVarPos, $nextVarPos); //everything after (and including) "&" of the NEXT variable

				$getVariables = $beforeVarPos . $afterNextVar;
			}
			else { $getVariables = $beforeVarPos; }
		}

		//check to make sure we don't have ?&
		if ($getVariables[0] == "&") { $getVariables = "?" . substr($getVariables, 1); }
		if ($getVariables[1] == "&") { $getVariables = "?" . substr($getVariables, 2); }

		//make sure if still variables that the first letter is a ?
		if (strlen($getVariables) > 2 && $getVariables[0] != "?") { $getVariables = "?" . $getVariables; } 

		//put everything back together
		$url = substr($url, 0, strpos($url, "?")) . $getVariables;
	}

	return $url;
}

function tools_add_get_variable($url, $variableString)
{
	$addString = "";
	if (strpos($url, "?") != false) { $addString = "&".$variableString; }
	else { $addString = "?".$variableString; }
	return $url.$addString;
}

//get the page referer, or use the passed in default if for some reason the browser can't get it
function tools_get_referer($default)
{
	$zeReferer = $default;
	if (isset($_SERVER["HTTP_REFERER"])) { $zeReferer = $_SERVER["HTTP_REFERER"]; }
	return $zeReferer;
}

//this is for the post format (5:10 PM March 16, 2015)
function tools_get_date_string($dateTime) { return date("g:i A F j, Y", strtotime($dateTime)); }

//this is for comment format (5/1/2015 3:34 PM)
function tools_get_date_string_normal($dateTime) { return date("n/j/Y g:i A", strtotime($dateTime)); }

//ONLY FOR POSTS
function tools_fix_escaped_content($content)
{
	$content = str_replace("\\r\\n\\r\\n", "</p><p>", $content);
	$content = str_replace("\\'", "'", $content);
	$content = str_replace("\\\"", "\"", $content);
	return $content;
}

function tools_fix_escaped_content_normal($content)
{
	$content = str_replace("\\r\\n", "</br>", $content);
	$content = str_replace("\\'", "'", $content);
	$content = str_replace("\\\"", "\"", $content);
	return $content;
}

function tools_escape_escaped_content($text)
{
	$text = str_replace(array("\\r\\n", "\\r", "\\n"), "\r\n", $text);
	$text = str_replace(array("\\'"), "'", $text);
	$text = str_replace(array("\\\""), "\"", $text);
	return $text;
}

function tools_get_quote($area)
{
	$quotes = array();
	if ($area == "music") { $quotes = file("res/quotes/music.txt"); }
	if ($area == "programming") { $quotes = file("res/quotes/programming.txt"); }
	if ($area == "games") { $quotes = file("res/quotes/games.txt"); }
	if ($area == "video") { $quotes = file("res/quotes/videos.txt"); }
	

	$index = mt_rand(0, count($quotes) - 1);
	return $quotes[$index];
}


function sec_session_start()
{
	session_start();
	session_regenerate_id();
}

?>
