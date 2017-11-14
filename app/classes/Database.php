<?php
/*
* Database.php
* Base class for providing database functionality
*/

class Database
{
	// Database connection settings
	private $dbHost;
	private $dbName;
	private $dbUser;
	private $dbPassword;

	// PDO database connection
	private static $conn = null;

	// Prepared statement SQL
	private $sql = '';


	/**
    * Constructor
    */
	function __construct(&$config)
	{
		// Set DB configuration
		$this->dbHost = $config['db_host'];
		$this->dbName = $config['db_name'];
		$this->dbUser = $config['db_user'];
		$this->dbPassword = $config['db_password'];
	}


	/**
    * Attempts to create a database connection
    */
	protected function connect()
	{
		try
		{
			if(self::$conn == null)
			{	
				// Connect to database
				self::$conn = new PDO("mysql:host=$this->dbHost;dbname=$this->dbName;charset=utf8", $this->dbUser, $this->dbPassword);

			 	// set the PDO error mode to exception
	    		self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    	}
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}


	/**
    * Prepare SQL statement
    */
	protected function prepare($str)
	{
		try
		{
			$this->sql = self::$conn->prepare($str);
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}


	/**
    * Execute statement
    */
	protected function execute($array = array())
	{
		try
		{
			if(empty($array))
			{
				$this->sql->execute();
			}
			else
			{
				$this->sql->execute($array);
			}
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}


	/**
    * Get ALL results
    */
	protected function results()
	{
		try
		{
			return $this->sql->fetchAll();
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}


	/**
    * Get SINGLE result
    */
	protected function result()
	{
		try
		{
			return $this->sql->fetch();
		}
		catch(PDOException $ex)
		{
			echo $ex->getMessage();
		}
	}


	/**
    * Get last insert id
    */
	protected function getLastInsertID()
	{
		return self::$conn->lastInsertId();
	}
}