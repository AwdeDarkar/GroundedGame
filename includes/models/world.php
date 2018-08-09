<?php
class World
{
	private $ID;
	private $Name;
	private $NameSafe;
	private $Status;
	private $Created;

	protected $factions;
	protected $factions_loaded = false;

	public function __construct($fetch)
	{
		$this->$ID = $fetch->$ID;
		$this->$Name = $fetch->$Name;
		$this->$NameSafe = $fetch->$NameSafe;
		$this->$Status = $fetch->$Status;
		$this->$Created = $fetch->$Created;
	}
	public function __construct($name, $nameSafe, $status, $created)
	{
		$this->$Name = $name;
		$this->$NameSafe = $nameSafe;
		$this->$Status = $status;
		$this->$Created = $created;
		$this->$ID = new QueryManager('Worlds')->insert($this);
	}
	public function save() { new QueryManager('Worlds')->update($this); }
	
	public function getID() { return $this->$ID; }
	public function getName() { return $this->$Name; }
	public function getNameSafe() { return $this->$NameSafe; }
	public function getStatus() { return $this->$Status; }
	public function getCreated() { return $this->$Created; }

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
	# TODO: what happens if ID doesn't exist?
	public static function getByID($id)
	{
		$qm = new QueryManager('Worlds');
		return new World($qm->getByID($id));
	}
}

?>

