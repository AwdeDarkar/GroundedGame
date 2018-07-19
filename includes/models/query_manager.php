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
		$result = parent::query('select * from ' . $this->table);
		while ($record = $result->fetch_object()) { array_push($result_array, $record); }
		return $record;
	}
	public function getByID($id)
	{
		return parent::query('select * from ' . $this->table . ' where id = ' . $id)->fetch_object();
	}
	public function getWhere($where)
	{
		$result_array = array();
		$result = parent::query('select * from ' . $this->table . ' where ' . $where);
		while ($record = $result->fetch_object()) { array_push($result_array, $record); }
		return $record;
	}
}

?>
