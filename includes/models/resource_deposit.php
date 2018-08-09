<?php


class ResourceDeposit
{
	private $ID;
	private $BunkerID;
	private $ResourceID;
	private $Amount;
	private $ReplenishRate;
	private $Maximum;


	protected $bunker;
	protected $bunker_loaded = false;
	protected $resource;
	protected $resource_loaded = false;

	
	public function __construct($fetch)
	{
		$this->$ID = $fetch->ID;
		$this->$BunkerID = $fetch->BunkerID;
		$this->$ResourceID = $fetch->ResourceID;
		$this->$Amount = $fetch->Amount;
		$this->$ReplenishRate = $fetch->ReplenishRate;
		$this->$Maximum = $fetch->Maximum;
	}
	
	public function getID() { return $this->id; }
	
	public function getBunkerID() { return $this->bunkerID; }
		
	public function getResourceID() { return $this->resourceID; }

	public function getAmount() { return $this->amount; }
	public function setAmount($amount) { $this->amount = $amount; }

	public function getReplenishRate() { return $this->replenishRate; } 
	public function setReplenishRate($replenishRate) { $this->replenishRate = $replenishRate; }
	
	public function getMaximum() { return $this->maximum; } 
	public function setMaximum($maximum) { $this->maximum = $maximum; }
	
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
		$qm = new QueryManager('ResourceDeposits');
		$qm->update([["ReplenishRate", $this->entityID], ["BunkerID", $this->bunkerID], ["Maximum", $this->maximum], ["ResourceID", $this->resourceID], ["Amount", $this->amount]], "ID = " . $this->id);
	}

	public function getByID($id)
	{
		$qm = new QueryManager('ResourceDeposits');
		$rd_fetch = $qm->getByID($id);
		$rd = new ResourceDeposit($rd_fetch);
		return $rc;
	}
	

}


?>
