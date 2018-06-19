<?php

namespace dc\mackenzie;

declare(strict_types=1);

require_once('config.php');

// Data structure for the options parameter when preparing SQL queries.
interface iDatabaseConfig
{	
	// Accessors
	function get_error();
	function get_timeout(): int;
	
	// Mutators
	function set_error(Error $value);
	function set_timeout(int $value);
}

class StatementConfig implements iStatementConfig
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
	public function get_error(): Error
	{
		return $this->error;
	}
	
	public function get_timeout(): int 
	{		
		return $this->timeout;
	}
	
	// Mutators
	public function set_error(Error $value)
	{
		$this->error = $value;
	}

	public function set_timeout(int $value)
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
