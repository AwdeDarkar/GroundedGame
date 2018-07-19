<?php
class Actor 
{
	private $name = "unnamed";
	private $id = -1;
	private $hp = 0;
	private $birthday = 0;
	private $skills = {};
	private $job = NULL;
	
	function getName() { return $name; }
	function setName($in_name) { $name = $in_name; }
	
	function $getID() { return $id; }
	function $setID($in_id) { $id = $in_id; }
	
	function $getHP() { return $hp; }
	function $setHP($in_hp) { $hp = $in_hp }
	function $alive() { return ($hp > 0); }
	
	function $getBirthday() { return $birthday; }
	function $getAge($current_day) { return $current_day - $birthday; }
	
	function $getSkillLevel($skill)
	{
		if (array_key_exists($skill, $skills) { return $skills[$skill]; }
		else { return 0; }
	}
	function $setSkillLevel($skill, $level) { $skills[$skill] = $level; }
	
	function $getJob() { return $job; }
	function $setJob($in_job) { $job = $in_job; }
	
	function __construct($in_name, $last_id, $current_day)
	{
		$name = $in_name;
		$hp = 10;
		$birthday = $current_day;
		$id = $last_id + 1;
	}
	
}
?>