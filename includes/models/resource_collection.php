<?php


class ResourceCollection
{
	private $id;
	private $entityID;
	private $bunkerID;
	private $factionID;
	private $resourceID;
	private $amount;


	private $faction;
	private $faction_loaded = false;
	private $bunker;
	private $bunker_loaded = false;
	private $resource;
	private $resource_loaded = false;

	
	public function __construct($fetch)
	{
		$this->$id = $fetch->ID;
		$this->$entityID = $fetch->EntityID;
		$this->$bunkerID = $fetch->BunkerID;
		$this->$factionID = $fetch->FactionID;
		$this->$resourceID = $fetch->ResourceID;
		$this->$amount = $fetch->Amount;
	}
	
	public function getID() { return $this->id; }
	
	public function getEntityID() { return $this->entityID; }
	
	public function getBunkerID() { return $this->bunkerID; }
	public function setBunkerID($bunkerID) { $this->bunkerID = $bunkerID; }
		
	public function getFactionID() { return $this->factionID; }
	public function setFactionID($factionID) { $this->factionID = $factionID; }
		
	public function getResourceID() { return $this->resourceID; }
	public function setResourceID($resourceID) { $this->resourceID = $resourceID; }

	public function getAmount() { return $this->amount; }
	public function setAmount($amount) { $this->amount = $amount; }


	public function getFaction()
	{
		if (!$this->faction_loaded) { $this->setFaction(Faction.getByID($this->factionID)); }
		return $this->faction;
	}
	public function setFaction($faction)
	{
		$this->faction = $faction;
		$this->faction_loaded = true;
	}
	
	public function getBunker()
	{
		if (!$this->bunker_loaded) { $this->setBunker(Bunker.getByID($this->bunkerID)); }
		return $this->bunker;
	}
	public function setBunker($bunker)
	{
		$this->bunker = $bunker;
		$this->bunker_loaded = true;
	}
	
	public function getResource()
	{
		if (!$this->resource_loaded) { $this->setResource(Resource.getByID($this->resourceID)); }
		return $this->resource;
	}
	public function setResource($resource)
	{
		$this->resource = $resource;
		$this->resource_loaded = true;
	}

	public function save()
	{
		$qm = new QueryManager('ResourceCollections');
		$qm->update([["EntityID", $this->entityID], ["BunkerID", $this->bunkerID], ["FactionID", $this->factionID], ["ResourceID", $this->resourceID], ["Amount", $this->amount]], "ID = " . $this->id);
	}

	public function getByID($id)
	{
		$qm = new QueryManager('ResourceCollections');
		$rc_fetch = $qm->getByID($id);
		$rc = new ResourceCollection($rc_fetch);
		return $rc;
	}
	

}


?>


