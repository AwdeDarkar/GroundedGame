<?php
class World
{
	private $id;
	private $name;
	private $nameSafe;
	private $status;
	private $created;

	private $factions;
	private $factions_loaded = false;

	public function __construct($fetch)
	{
		$this->$id = $fetch->$ID;
		$this->$name = $fetch->$Name;
		$this->$nameSafe = $fetch->$NameSafe;
		$this->$status = $fetch->$Status;
		$this->$created = $fetch->$Created;
	}

	public function getID() { return $this->$id; }
	public function getName() { return $this->$name; }
	public function getNameSafe() { return $this->$nameSafe; }
	public function getStatus() { return $this->$status; }
	public function getCreated() { return $this->$created; }

	public function getFactions()
	{
		if (!$this->$factions_loaded)
		{
			$this->$factions = Faction.getAllForWorld($this);
			$this->$factions_loaded = true;
		}
		return $this->$factions;
	}



	public static function getAll()
	{
		$qm = new QueryManager('Worlds');
		$world_fetches = $qm.getAll();
		$worlds = array();
		for($i = 0; $i < count($world_fetches); $i++) { array_push($worlds, new World($world_fetches[$i])); }
		return $worlds;
	}
	public static function getByID($id)
	{
		$qm = new QueryManager('Worlds');
		return new World($qm->getByID($id));
	}
}

?>

