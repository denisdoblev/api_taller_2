<?php

class AuthModel
{
	private $connection;
	
	public function __construct(){
		$this->connection = new mysqli('127.0.0.1', 'root', '', 'api', '3306');

		if($this->connection->connect_errno){
			echo 'Error de conexión a la base de datos';
			exit;
		}
	}

	public function login($username, $password)
	{
		$query = "SELECT id, nombres, username FROM personas WHERE username = '$username' AND password = '$password'";

		$results = $this->connection->query($query);

		$resultArray = array();

		if($results != false){
			foreach ($results as $value) {
				$resultArray[] = $value;
			}
		}

		return $resultArray;
	}

	public function update($id, $token)
	{
		$query = "UPDATE personas SET token = '$token' WHERE id = $id";

		$this->connection->query($query);
		
		if(!$this->connection->affected_rows){
			return 0;
		}

		return $this->connection->affected_rows;
	}

	public function getById($id)
	{
		$query = "SELECT token FROM personas WHERE id = $id";

		$results = $this->connection->query($query);

		$resultArray = array();

		if($results != false){
			foreach ($results as $value) {
				$resultArray[] = $value;
			}
		}

		return $resultArray;
	}
}
