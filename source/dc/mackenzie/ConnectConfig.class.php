<?php

namespace dc\mackenzie;

require_once('config.php');

// Structure of parameters used for database connection attempt.
interface iConnectConfig
{	
	function get_charset();				// Return character set.
	function get_config_source();		// Return config file name.
	function get_error();				// Return error handler.
	function get_host();				// Return host name.
	function get_name();				// Return logical database name.
	function get_user();				// Return user.
	function get_password();			// Return password.
	function set_config_source($value);	// Set config file.
	function set_error($value);			// Set error handler.
	function set_host($value);			// Set host name.
	function set_name($value);			// Set logical database name.
	function set_user($value);			// Set user.
	function set_password($value);		// Set password.	
}

class ConnectConfig implements iConnectConfig
{		
	private
		$charset		= NULL,	// Character set.
		$config_source	= NULL,	// Config file to populate other members.
		$error			= NULL,	// Internal exception handling toggle.
		$host			= NULL,	// Server name or address.
		$name			= NULL,	// Database name.
		$user			= NULL,	// User name to access database.
		$password		= NULL;	// Password for user to access database.
	
	public function __construct(Error $error = NULL)
	{
		//$this->error	= $this->construct_error($error);
		$this->config_source = DEFAULTS::CONFIG_SOURCE;
		
		$this->populate_defaults();
	}
	
	// Constructors
	
	// If there is no config file, then use default constants.
	public function populate_defaults()
	{
		$config_array;
		
		if($this->config_source)
		{
			$config_array = parse_ini_file($this->config_source);
			
			$this->host 	= $config_array['HOST'];
			$this->name 	= $config_array['NAME'];
			$this->user 	= $config_array['USER'];
			$this->password	= $config_array['PASSWORD'];
		}
			
		// Populate defaults.
		if(!$this->charset) 	$this->charset	= DEFAULTS::CHARSET;
		if(!$this->host) 		$this->host 	= DEFAULTS::HOST;
		if(!$this->name)		$this->name 	= DEFAULTS::NAME;
		if(!$this->user) 		$this->user		= DEFAULTS::USER;
		if(!$this->password)	$this->password	= DEFAULTS::PASSWORD;
	}
	
	// Accessors.
	public function get_charset()
	{		
		return $this->charset;
	}	
	
	public function get_config_source()
	{
		return $this->config_source;
	}
	
	public function get_error()
	{
		return $this->error;
	}
	
	public function get_host()
	{		
		return $this->host;
	}	
	
	public function get_name()
	{		
		return $this->name;
	}

	public function get_user()
	{		
		return $this->user;
	}

	public function get_password()
	{		
		return $this->password;
	}

	// Mutators.
	public function set_charset($value)
	{		
		$this->charset = $value;
	}

	public function set_config_source($value)
	{
		$this->config_source = $value;
	}
		
	public function set_error($value)
	{
		$this->error = $value;
	}

	public function set_host($value)
	{		
		$this->host = $value;
	}

	public function set_name($value)
	{		
		$this->name = $value;
	}

	public function set_user($value)
	{		
		$this->user = $value;
	}

	public function set_password($value)
	{		
		$this->password = $value;
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
