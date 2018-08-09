<?php
include "../global_tools";

class Resource
{
    private $id;
    private $name;
    private $nameSafe;
    private $description;
    private $type;

	public function __construct($fetch)
	{
		$id = $fetch->$id;
		$name = $fetch->$name;
		$nameSafe = $fetch->$nameSafe;
		$description = $fetch->$description;
		$type = $fetch->$type;
	}
	
	public function getID() { return $id; }
	
	public function getName() { return $id; }
	public function getNameSafe() { return $nameSafe; }
	public function setName($in_name)
	{
		$name = $in_name;
		$nameSafe = tools_iterative_web_safe($in_name, "Resources");
	}
	
	public function getDescription() { return $description; }
	public function setDescription($in_description) { $description = $in_description; }
	
	public function getType() { return $type; }
	public function setType($in_type) { $type = $in_type; }
	
	public function save()
	{
		$qm = new QueryManager("Resources");
		$qm->update([["ID", $id], ["Name", $name], ["NameSafe", $nameSafe], ["Description", $description], ["Type", $type]]);
	}
	
	public static function getByID($id)
	{
		$qm = new QueryManager("Resources");
		$fetch = $qm->getByID($id);
		$resource = new Resource($fetch);
		return $faction;
	}
}

?>
