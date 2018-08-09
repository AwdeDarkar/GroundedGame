<?php

class QueryManager extends Connection
{
	private $table;
	private $connection;
	
	public function __construct($table)
	{
		$this->setTable($table);
		$this->connection = new Connection();
	}
		
	public function getTable() { return $this->table; }
	public function setTable($name) { $this->table = '`' . $name . '`'; }
	public function getConnection() { return $this->connection; }

	// query templates
	public function getAll()
	{
		$result_array = array();
		$result = parent::query('SELECT * FROM ' . $this->table);
		while ($record = $result->fetch_object()) { array_push($result_array, $record); }
		return $record;
	}
	public function getByID($id)
	{
		return parent::query('SELECT * FROM ' . $this->table . ' WHERE id = ' . $id)->fetch_object();
	}
	public function getWhere($where)
	{
		$result_array = array();
		$result = parent::query('SELECT * FROM ' . $this->table . ' WHERE ' . $where);
		while ($record = $result->fetch_object()) { array_push($result_array, $record); }
		return $record;
	}

	/*public function update($setterList, $where)
	{
		$setterString = "";
		for ($i = 0; $i < count($setterList); $i++)
		{
			$setterString .= $setterList[$i][0] . " = " . $setterList[$i][1];
			if ($i <= count($setterList) - 1) { $setterString .= ", "; }
		}
		parent::query('update ' . $this->table . ' set ' . $setterString . ' where ' . $where);
	}*/

	// http://php.net/manual/en/reflectionclass.getproperties.php	
	public function update($object)
	{
		$reflect = new ReflectionClass($object);
		$props = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);

		$setterStringArray = array();
		foreach ($props as $prop) { array_push($setterStringArray, $prop->getName() . " = " . $prop->getValue()); }
		$setterString = implode(",", setterStringArray);
		
		parent::query('UPDATE ' . $this->table . ' SET ' . $setterString . ' WHERE ID = ' . $object->getID());
	}

	// returns the id of the newly inserted row
	public function insert($object)
	{
		$reflect = new ReflectionClass($object);
		$props = $reflect->getProperties(ReflectionProperty::IS_PRIVATE);

		#$setterStringArray = array();
		$namesArray = array();
		$valuesArray = array();
		foreach ($props as $prop)  
		{
			if ($prop.getName() != "ID")
			{
				array_push($namesArray, $prop->getName());
				array_push($valuesArray, $prop->getValue());
			}
		}
		$names = implode(",", $namesArray);
		$values = implode(",", $valuesArray);
		
		parent::query('INSERT INTO ' . $this->table . '('.$names.') VALUES ('.$values')');
		return plarent::getInsertID();
	}
}

?>
