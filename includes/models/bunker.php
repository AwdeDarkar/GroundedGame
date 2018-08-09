<?php
include 'query_manager.php';
include 'world.php';

class Bunker
{
    private $id;
    private $worldID;
    private $worldX;
    private $worldY;
    private $factionID;

    private $world;
    private $faction;
	private $query_manager;
	
	public function __construct($fetch)
	{
		$this->$query_manager = new QueryManager('generic');
		$this->$id = $fetch->$id;
		$this->$worldID = $fetch->$worldID;
		$this->$worldX = $fetch->$worldX;
		$this->$worldY = $fetch->$worldY;
		$this->$factionID = $fetch->$factionID;
		
		$this->$world = new World(World->getByID($worldID));
		$this->$faction = new Faction(Faction->getByID($factionID));
	}
	
	public function getID() { return $id; }
	
	public function getWorld() { return $world; }
	
	public function getFaction() { return $faction; }
	public function setFaction($in_faction)
	{
		$faction = new Faction(Faction->($in_faction->getID());
		$factionID = $in_faction->getID();
	}
	
	public function getX() { return $worldX; }
	public function getY() { return $worldY; }
	
	public function save()
	{
		$conn = $query_manager->getConnection();
		$sql = "UPDATE Bunker SET Bunker.factionID = ? WHERE Bunker.ID = ?";
		
		if($stmt = $conn->prepare($sql))
		{
			$stmt->bind_param('ss', $factionID, $id);
			$stmt->execute();
			return true;
		}
		else { return false; }
	}
	
	public static function getByID($id)
	{
		$qm = new QueryManager('Bunkers');
		$fetch = $qm->getByID();
		$bunker = new Bunker($fetch);
		return $bunker
	}
}

?>
