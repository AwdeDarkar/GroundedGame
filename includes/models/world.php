<?php
class World
{
	private $id;
	private $name;
	private $nameSafe;
	private $status;
	private $created;

	private $factions;
	private $factions_loaded;

	public function __construct($fetch)
	{
		$this->$id = $fetch->$ID;
		$this->$name = $fetch->$Name;
		$this->$nameSafe = $fetch->$NameSafe;
		$this->$status = $fetch->$Status;
		$this->$created = $fetch->$Created;

		$factions = array();
		$factions_loaded = false;
	}

	public function getName() { return $this->$name; }
	public function getNameSafe() { return $this->$nameSafe; }
	public function getStatus() { return $this->$status; }
	public function getCreated() { return $this->$created; }

	public function getFactions()
	{
		if (!$this->$factions_loaded())
		{
			$this->$factions = Faction.getAllForWorld($this);
			$this->$factions_loaded = true;
		}
		return $this->$factions;
	}



	public static getAll()
	{
		$object = new QueryManager('World');
		$world_fetches = $object.getAll();
		$worlds = array();
		for($i = 0; $i < count($world_fetches); $i++) { array_push($worlds, new World($world_fetches[$i])); }
		return $worlds;
	}
}

?>

