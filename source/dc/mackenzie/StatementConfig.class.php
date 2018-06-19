<?php

namespace dc\mackenzie;

require_once('config.php');

// Data structure for the options parameter when preparing SQL queries.
interface iDatabaseConfig
{	
	// Accessors
	function get_error();
	function get_timeout();
	
	// Mutators
	function set_error($value);
	function set_timeout($value);
}

class DatabaseConfig implements iDatabaseConfig
{	
	private 
		$error		= NULL,	// Exception catching flag.
		$scrollable	= NULL,	// Cursor type.
		$sendstream	= NULL,	// Send all stream data at execution (TRUE), or to send stream data in chunks (FALSE)
		$timeout 	= NULL;	// Query timeout in seconds.
		
	public function __construct(Error $error = NULL)
	{
		// Populate defaults.
		$this->error		= $this->construct_error($error);
		$this->timeout		= DEFAULTS::TIMEOUT;
	}
	
	// Accessors
	public function get_error()
	{
		return $this->error;
	}
	
	public function get_timeout()
	{		
		return $this->timeout;
	}
	
	// Mutators
	public function set_error($value)
	{
		$this->error = $value;
	}

	public function set_timeout($value)
	{		
		$this->timeout = $value;
	}	
	
	// Constructors
	private function construct_error(Error $value = NULL)
	{
		$result = NULL;	// Final connection result.
		
		// Verify argument is an object.
		$is_object = is_object($value);
		
		if($is_object)
		{
			$result = $value;		
		}
		else
		{
			$result = new Error();		
		}
		
		// Populate member with result.
		$this->error = $result;
	
		return $result;
	}
}

?>
