<?php
include_once '../db/db_connect_config.php';

class Connection
{
	private $connection;

	public function __construct() { $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE_NAME); }
	public function query($sql) { return $this->connection->query($sql); }
}

?>
