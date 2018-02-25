<?php
define(PERMISSION_LEVEL, -1);
include("../includes/common.php");
include("./template/header.php");
include("./template/sidebar.php");

$world = getCurrentWorld();
$bunkerID = tools_sanitize_data($_GET['b']);
$httpReferer = tools_get_referer("index.php");
$facID = getFactionID(LOGGED_USER_ID, $world);
$facName = getFactionName($facID);
?>

<body>

<div id='topbar'></div>
<div id='leftbar'></div>
<div id='rightbar'></div>
<div id='bottombar'></div>
<div class="content">

	<h1>Create New Group</h1>
	<h2>Selected Members: <i id="selected_members"></i></h2>
	<script>
		function toggle_index(ind)
		{
			var inselect = selected_indicies.indexOf(ind);
			if(inselect == -1)
			{
				selected_indicies.push(ind);
				selected_names.push(member_names[ind]);
				var member_element = document.getElementById("M" + ind);
				member_element.innerHTML += " *";
				member_element.title = "Remove this member from your group";
				var selected_members_listing = "";
				for(i = 0; i < selected_indicies.length; i++)
					selected_members_listing += member_names[selected_indicies[i]] + " ";
				document.getElementById("selected_members").innerHTML = selected_members_listing;
			}
			else
			{
				selected_indicies.splice(inselect, 1);
				selected_names.splice(inselect, 1);
				var member_element = document.getElementById("M" + ind);
				member_element.style.innerHTML = member_names[i];
				member_element.title = "Add this member to your group";
				var selected_members_listing = "";
				for(i = 0; i < selected_indicies.length; i++)
					selected_members_listing += member_names[selected_indicies[i]] + " ";
				document.getElementById("selected_members").innerHTML = selected_members_listing;
			}
		}
		
		function submit_group()
		{
			selected_names.push("<?php echo $facName; ?>");
			$.post(
				"ajax_create_message_group.php",
				{ "names" : selected_names, "world" : "<?php echo $world; ?>" },
				function() { window.location.href = "messages.php?w=<?php echo $world; ?>"; }
			);	
		}
	</script>
<?php
	$MemberNames = "";
	if ($stmt = $mysqli->prepare("SELECT Factions.Name FROM Factions, Worlds WHERE Factions.WorldID = Worlds.ID AND Worlds.NameSafe = ? AND NOT Factions.ID=?"))
	{
		$stmt->bind_param('ss', $world, $facID);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($Name);
		$counter = 0;
		
		while($stmt->fetch())
		{
			echo("
			<p>
				<a id=\"M" . $counter . "\" title=\"Add this member to your group\"
				href=\"#\" onclick=\"toggle_index(" . $counter . ");return false;\">" 
					. $Name . 
				"</a>
			</p>");
			$counter++;
			$MemberNames .= $Name . ",";
		}
	}
	//$MemberNames = substr($MemberNames, 0, -1); //The last character will be an unneeded ','
?>


	<script>
		var member_names = "<?php echo $MemberNames; ?>".substring(0, -1).split(",");
		var selected_names = [];
		var selected_indicies = [];
	</script>
</div>

</body>