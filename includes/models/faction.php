<?php
class Faction
{
	private $id;
	private $userID;
	private $worldID;
	private $name;
	private $joined;
	private $nameSafe;

	private $world;
	private $world_loaded = false;
	
	private $user;
	//private $user_loaded;
	
	// TODO: loaded for the rest
	private $bunkers;
	private $resourceCollections;
	//private $equipment;
	//private $actors;


	public function __construct($fetch)
	{
		$this->$id = $fetch->$ID;
		$this->$userID = $fetch->$UserID;
		$this->$worldID = $fetch->$WorldID;
		$this->$joined = $fetch->$Joined;
		$this->$name = $fetch->$Name;
		$this->$nameSafe = $fetch->$NameSafe;
	}


	public getWorld()
	{
		if (!$this->$world_loaded) { $this->setWorld(World.getByID($this->$WorldID)); }
		return $this->$world;
	}
	public setWorld($world)
	{
		$this->$world = $world;
		$this->$world_loaded = true;
	}


	public static function getAllForWorld($world)
	{
		$qm = new QueryManager('Factions');
		$faction_fetches = $qm.getWhere('WorldID = ' . $world->getID()); // TODO: fix, not getall
		$factions = array();
		for($i = 0; $i < count($world_fetches); $i++) 
		{ 
			$faction = new Faction($faction_fetches[$i]);
			$faction->setWorld($world);
			array_push($factions, $faction); 
		}
		return $factions;
	}
	
}

?>
