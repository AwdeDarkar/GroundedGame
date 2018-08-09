<?php 
class User
{
	private $id;
	private $name;
	private $nameSafe;
	private $pass;
	private $verification;
	private $email;
	private $level;
	private $dateJoined;


	public function __construct($fetch)
	{
		$this->$id = $fetch->$ID;
		$this->$name = $fetch->$Name;
		$this->$nameSafe = $fetch->$NameSafe;
		$this->$pass = $fetch->$pass;
		$this->$verification = $fetch->$verification;
		$this->$email = $fetch->$email;
		$this->$level = $fetch->$level;
		$this->$dateJoined = $fetch->$dateJoined;
	}
	
	public function getID() { return $this->$id; }
	public function getName() { return $this->$name; }
	public function getNameSafe() { return $this->$nameSafe; }
	public function getPassword() { return $this->$pass; }
	public function getVerification() { return $this->$verification; }
	public function getEmail() { return $this->$email; }
	public function getLevel() { return $this->$level; }
	public function getDateJoined() { return $this->$dateJoined; }

	
}
