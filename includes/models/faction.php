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
	private $user;
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
		$this->$name = $fetch->$Joined;
		
	}
	
}

?>
