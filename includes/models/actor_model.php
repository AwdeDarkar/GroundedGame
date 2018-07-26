<?php
include 'query_manager.php';

class Actor 
{
	private $name;
	private $id;
	private $hp;
	private $birthday;
	private $skills;
	private $skills_loaded;
	private $job;
	
	private $query_manager;
	
	public function __construct($fetch)
	{
		$this->$query_manager = new QueryManager('generic');
		$this->$name = $fetch->$name;
		$this->$hp = $fetch->$id
		$this->$birthday = $fetch->$birthday;
		$this->$id = $fetch->$id;
		
		$skills = array();
		$skills_loaded = false;
	}
	
	public function getName() { return $name; }
	public function setName($in_name) { $name = $in_name; }
	
	public function getID() { return $id; }
	public function setID($in_id) { $id = $in_id; }
	
	public function getHP() { return $hp; }
	public function setHP($in_hp) { $hp = $in_hp }
	public function alive() { return ($hp > 0); }
	
	public function getBirthday() { return $birthday; }
	public function getAge($current_day) { return $current_day - $birthday; }
	
	public function getSkillLevel($skill)
	{
		if (array_key_exists($skill, $skills) { return $skills[$skill]; }
		else { return 0; }
	}
	public function setSkillLevel($skill, $level) { $skills[$skill] = $level; }
	
	public function getJob() { return $job; }
	public function setJob($in_job) { $job = $in_job; }
	
	private function load_skills()
	{
		if (!$this->$skills_loaded())
		{
			if ($stmt = $query_manager->getConnection()->prepare("SELECT Skills.Name, ActorSkills.Level FROM Skills, ActorSkills WHERE ActorSkills.AID = ? AND ActorSkills.SID = Skills.ID"))
			{
				$stmt->bind_param('s', $id);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($Name, $Level);
				
				while($stmt->fetch())
				{
					$this->$skills[$Name] = $Level;
				}
			}
			else { echo("Problem!"); }
			
			$this->$skills_loaded = true;
		}
		return $this->$skills;
	}
	
	private function save()
	{
		$conn = $query_manager->getConnection();
		$sql = "UPDATE Actor SET name = ?, hp = ? WHERE Actor.ID = ?";
		
		if($stmt = $conn->prepare($sql))
		{
			$stmt->bind_param('sss', $name, $hp, $id);
			$stmt->execute();
		}
		else { return false; }
		
		foreach ($skills as $skill => $level)
		{
			$sql = "UPDATE ActorSkills SET ActorSkills.Level = ? WHERE Skills.Name = ? AND ActorSkills.AID = ? AND Skills.ID = ActorSkills.SID";
			if($stmt = $conn->prepare($sql))
			{
				$stmt->bing_param('sss', $level, $skill, $id);
				$stmt->execute();
			}
			else { return false; }
		}
		
		return true;
	}
		
	
	
}
?>

