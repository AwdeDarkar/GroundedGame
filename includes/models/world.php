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
		$self->$id = $fetch->$id;
		$self->$name = $fetch->$name;
		$self->$nameSafe = $fetch->$nameSafe;
		$self->$status = $fetch->$status;
		$self->$created = $fetch->$created;

		$factions = array();
		$factions_loaded = false;
	}

	public function getName() { return $self->$name; }
	public function getNameSafe() { return $self->$nameSafe; }
	public function getStatus() { return $self->$status; }
	public function getCreated() { return $self->$created; }

	public function getFactions()
	{
		if (!$self->$factions_loaded())
		{
			$self->$factions = Faction.getAllForWorld($self);
			$self->$factions_loaded = true;
		}
		return $self->$factions;
	}



	public static getAll()
	{
		$object = new Object('World');
		$world_fetches = $object.getAll();
		$worlds = array();
		for($i = 0; $i < count($world_fetches); $i++) { array_push($worlds, new World($world_fetches[$i])); }
		return $worlds;
	}
}

?>

