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
		$this->$id = $fetch->ID;
		$this->$userID = $fetch->UserID;
		$this->$worldID = $fetch->WorldID;
		$this->$joined = $fetch->Joined;
		$this->$name = $fetch->Name;
		$this->$nameSafe = $fetch->NameSafe;
	}

	public function getID() { return $this->id; }

	public function getUserID() { return $this->userID; }
	
	public function getWorldID() { return $this->worldID; }
	
	public function getName() { return $this->name; }
	public function getNameSafe() { return $this->nameSafe; }
	
	public function getJoined() { return $this->joined; }
	
	public function getWorld()
	{
		if (!$this->world_loaded) { $this->setWorld(World.getByID($this->worldID)); }
		return $this->world;
	}
	public function setWorld($world)
	{
		$this->world = $world;
		$this->world_loaded = true;
	}

	public function save()
	{
		$qm = new QueryManager('Factions');
		$qm->update([["UserID", $this->userID], ["WorldID", $this->worldID], ["Name", $this->name], ["Joined", $this->joined], ["NameSafe", $this->nameSafe]], "ID = " . $this->id);
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

	public static function getByID($id)
	{
		$qm = new QueryManager('Factions');
		$faction_fetch = $qm->getByID($id);
		$faction = new Faction($faction_fetch);
		return $faction;
	}
	
}

?>
