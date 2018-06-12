<?php

namespace dc\mackenzie;

use \PDO as PDO;

require_once('config.php');

// Query object. Execute SQL queries and return data.
interface iDatabase
{	
	// Accessors
	function get_connect_config();						// Return connection config object.
	function get_dbo_config();							// Return database config object.
	function get_dbo_instance();						// Return active database instance (connection).
	function get_line_config();							// Return line parameters object.
	function get_param_array();							// Return query parameter array.
	function get_sql();									// Return current SQl statement.
	function get_statement();							// Return query statement data member.
	
	// Mutators
	function set_connect_config(ConnectConfig $value);	// Set the object used for connection config attributes.	
	function set_dbo_config(DatabaseConfig $value);		// Set the object to be used for query config attributes.
	function set_dbo_instance($value);					// Set set active database instance (connection).
	function set_line_config(LineConfig $value);		// Set line parameters object.
	function set_param_array(array $value);				// Set query sql parameter array data member.
	function set_sql($value);							// Set query sql string data member.
	function set_statement($value);						// Set query statement reference.
	
	// Operations
	function free_statement();							// Free statement and clear statement member.
	function query_execute();							// Execute prepared query with current parameters.
	function query_prepare();							// Prepare query. Returns statement reference and sends to data member.
	function query_run();								// Prepare and execute query.	
}

class Database implements iDatabase
{
	private $connect_config	= NULL;		// Connection configuration object.
	private $dbo_config		= NULL;		// Query config object.
	private	$dbo_instance	= NULL;		// DB connection object.
	private	$line_config	= NULL;		// Line get config.
	private	$params 		= array();	// SQL parameters.
	private	$sql			= NULL;		// SQL string.
	private	$statement		= NULL;		// Prepared/Executed query reference.
	
	// Magic
	public function __construct(ConnectConfig $connect_config = NULL, DatabaseConfig $dbo_config = NULL, LineConfig $line_config = NULL)
	{
		// Set up memeber objects we'll need. In most cases,
		// if an argument is NULL, a blank object will
		// be created and used. See individual methods
		// for details.
		
		// Construct default configurations.		
		$this->construct_connect_config($connect_config);
		$this->construct_dbo_config($dbo_config);
		
		// Open Connection.
		$this->open_connection($this->connect_config);		
		
		//$this->construct_line_parameters($line_config);	
	}
	
	public function __destruct()
	{
	}
	
	// *Constructors
	// Connect to database host. Returns connection.
	
	private function construct_connect_config(ConnectConfig $value = NULL)
	{			
		// Set connection parameters member. If no argument
		// is provided, then create a blank connection
		// parameter instance.
		if(is_object($value))
		{
			$this->connect_config = $value;
		}
		else
		{
			$this->connect_config = new ConnectConfig();
		}
	
		return $this->connect_config;
	}
	
	private function construct_dbo_config(DatabaseConfig $value = NULL)
	{			
		// Set connection parameters member. If no argument
		// is provided, then create a blank connection
		// parameter instance.
		if(is_object($value))
		{
			$this->dbo_config = $value;
		}
		else
		{
			$this->dbo_config = new DatabaseConfig();
		}
	
		return $this->dbo_config;
	}
	
	public function open_connection(ConnectConfig $connect_config)
	{			
		$dbo_instance 	= NULL; // Database connection reference.
		$db_cred 		= NULL; // Credentials array.
		
		// Default to class member if no connection 
		// argument is passed.
		if(!$connect_config)
		{
			$connect_config = $this->connect_config;
		}																
		
		$error_handler	= $this->dbo_config->get_error();
		
		try 
		{
			// Can't connect if there's no host.
			if(!$connect_config->get_host())
			{
				$msg = EXCEPTION_MSG::CONNECT_OPEN_HOST;
				$msg .= ', Host: '.$config->get_host();
				$msg .= ', DB: '.$config->get_name();
				
				$error->exception_throw(new Exception($msg, EXCEPTION_CODE::CONNECT_OPEN_HOST));				
			}
			
			// Initialize database object.
			$dbo_instance = new PDO('mysql:host='.$connect_config->get_host().';dbname='.$connect_config->get_name(), $connect_config->get_user(), $connect_config->get_password());

			// False returned. Database connection has failed.
			if(!$dbo_instance)
			{				
				$error->exception_throw(new Exception(EXCEPTION_MSG::CONNECT_OPEN_FAIL, EXCEPTION_CODE::CONNECT_OPEN_FAIL));
			}			
		}
		catch (Exception $exception) 
		{
			// Catch exception internally if configured to do so.
			$error->exception_catch();
		}
		
		// Set database instance member.
		$this->dbo_instance = $dbo_instance;
		
		return $dbo_instance;
	}
	
	private function construct_config(DatabaseConfig $value = NULL)
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
			$result = new DatabaseConfig();		
		}
		
		// Populate member with result.
		$this->dbo_config = $result;
	
		return $result;		
	}
	
	private function construct_line_parameters(LineConfig $value = NULL)
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
			$result = new LineConfig();		
		}
		
		// Populate member with result.
		$this->line_config = $result;
	
		return $result;		
	}
	
	
	// *Accessors
	public function get_connect_config()
	{
		return $this->connect_config;	
	}
	
	public function get_dbo_config()
	{
		return $this->dbo_config;
	}
	
	public function get_dbo_instance()
	{
		return $this->dbo_instance;
	}
	
	public function get_error()
	{
		return $this->error;	
	}
		
	public function get_line_config()
	{
		return $this->line_config;
	}
	
	public function get_param_array()
	{
		return $this->params;	
	}
	
	public function get_statement()
	{
		return $this->statement;
	}
	
	public function get_sql()
	{
		return $this->sql;
	}
	
	// *Mutators
	
	// Set query sql parameters data member.
	public function set_param_array(array $value)
	{		
		$this->params = $value;
	}

	public function set_connect_config(ConnectConfig $value)
	{
		$this->connect_config = $value;
	}
	
	public function set_dbo_config(DatabaseConfig $value)
	{
		$this->dbo_config = $value;
	}
	
	public function set_dbo_instance($value)
	{
		$this->dbo_instance = $value;
	}
	
	public function set_error(Error $value)
	{
		$this->error = $value;
	}
			
	public function set_line_config(LineConfig $value)
	{
		$this->line_config = $value;
	}
	
	public function set_sql($value)
	{
		$this->sql = $value;
	}
	
	public function set_statement($value)
	{
		$this->statement = $value;
	}
	
	
	// *Request
	// Free statement and clear statement member.
	public function free_statement()
	{
		$result;
		$error_handler 	= $this->dbo_config->get_error();
		
		try 
		{
			// Verify statement.
			if(!$this->statement)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::FREE_STATEMENT_STATEMENT, EXCEPTION_CODE::FREE_STATEMENT_STATEMENT));				
			}
			
			// Attempt to free the statement. If PDO throws an exception,
			// then we catch it and throw our own exception.
			try 
			{
				$result = $this->statement->closeCursor();
				unset($this->statement);
			}
			catch(\PDOException $exception) 
			{	
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::FREE_STATEMENT_ERROR, EXCEPTION_CODE::FREE_STATEMENT_STATEMENT));
			}
			
			// False/Failure returned.
			if(!$result)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::FREE_STATEMENT_FAIL, EXCEPTION_CODE::FREE_STATEMENT_FAIL));
			}			
		}
		catch (Exception $exception) 
		{
			// Catch exception internally if configured to do so.
			$error->exception_catch();
		}
	}
	
	// Execute prepared query with current parameters.
	public function query_execute()
	{
		$result;
		$error_handler 	= $this->dbo_config->get_error();
		
		try 
		{
			// Verify statement.
			if(!$this->statement)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_EXECUTE_STATEMENT, EXCEPTION_CODE::QUERY_EXECUTE_STATEMENT));				
			}			
			
			// Attempt execute the statement. If PDO throws an exception,
			// then we catch it and throw our own exception.
			try 
			{
				$result = $this->statement->execute();
			}
			catch(\PDOException $exception) 
			{	
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_EXECUTE_ERROR, EXCEPTION_CODE::QUERY_EXECUTE_ERROR));
			}
			
			// False/Failure returned.
			if(!$result)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_EXECUTE_FAIL, EXCEPTION_CODE::QUERY_EXECUTE_FAIL));
			}			
		}
		catch (Exception $exception) 
		{
			// Catch exception internally if configured to do so.
			$error_handler->exception_catch();
		}
				
		return $result;	
	}
	
	// Prepare query. Returns statement reference and updates data member.
	public function query_prepare()
	{
		// Dereference error handler.
		$error_handler 	= $this->dbo_config->get_error();
		
		$dbo_instance	= NULL;		// Database connection reference.
		$statement	= NULL;		// Database statement reference.			
		$sql		= NULL;		// SQL string.
		$params		= array(); 	// Parameter array.
		$dbo_config		= NULL;		// Query config object.
		$dbo_config_a	= array();	// Query config array.
		
		// Dereference data members.
		$dbo_instance	= $this->dbo_instance;
		$sql 			= $this->sql;
		$params 		= $this->params;
		$dbo_config		= $this->dbo_config;
		
		try 
		{
			// Verify an active dbo instance.
			if(!$dbo_instance)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_PREPARE_CONNECTION, EXCEPTION_CODE::QUERY_PREPARE_CONNECTION));				
			}		
			
			// Verify dbo_config object.
			if(!is_object($dbo_config))
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_PREPARE_CONFIG, EXCEPTION_CODE::QUERY_PREPARE_CONFIG));				
			}		
			
			// Verify sql string. We can't really tell if it's a
			// valid SQL string, but we can at least verify it
			// is actually a string value and not empty.
			if(!is_string($sql) || $sql == '')
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_PREPARE_SQL, EXCEPTION_CODE::QUERY_PREPARE_SQL));				
			}
			
			// Attempt to prepare the query statement. If PDO throws an exception,
			// then we catch it and throw our own exception.
			try 
			{				
				// Break down config object to array. This is the only 
				// way prepare functions will accept optional config values.
				if($dbo_config)
				{
					//$dbo_config_a['Scrollable'] 			= $dbo_config->get_scrollable();
					//$dbo_config_a['SendStreamParamsAtExec']	= $dbo_config->get_sendstream();
					//$dbo_config_a['QueryTimeout'] 			= $dbo_config->get_timeout();
				}

				// Prepare query and get statement.
				$statement = $dbo_instance->prepare($sql, $dbo_config_a);				

				// Set DB statement data member.
				$this->statement = $statement;
			}
			catch(\PDOException $exception) 
			{	
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_PREPARE_ERROR, EXCEPTION_CODE::QUERY_PREPARE_ERROR));
			}
						
			// False/Failure returned.
			if(!$statement)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_PREPARE_FAIL, EXCEPTION_CODE::QUERY_PREPARE_FAIL));
			}			
		}
		catch (Exception $exception) 
		{
			// Catch exception internally if configured to do so.
			$error->exception_catch();
		}
		
		// Return statement reference.
		return $statement;		
	}
	
	// Prepare and execute query in a single action. This is
	// for one shot queries that do not need to be
	// prepared and executed separately.
	public function query_run()
	{
		// Dereference error handler.
		$error_handler 	= $this->dbo_config->get_error();
		
		$dbo_instance	= NULL;		// Database connection reference.
		$statement		= NULL;		// Database statement reference.			
		$sql			= NULL;		// SQL string.
		$params			= array(); 	// Parameter array.
		$dbo_config		= NULL;		// Query config object.
		$dbo_config_a	= array();	// Query config array.
		
		// Dereference data members.
		$dbo_instance	= $this->dbo_instance;
		$sql 			= $this->sql;
		$params 		= $this->params;
		$dbo_config		= $this->dbo_config;
		
		try 
		{
			// Verify active database instance.
			if(!$dbo_instance)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_RUN_CONNECTION, EXCEPTION_CODE::QUERY_RUN_CONNECTION));				
			}		
			
			// Verify dbo_config object.
			if(!is_object($dbo_config))
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_RUN_CONFIG, EXCEPTION_CODE::QUERY_RUN_CONFIG));				
			}		
			
			// Verify sql string. We can't really tell if it's a
			// valid SQL string, but we can at least verify it
			// is actually a string value and not empty.
			if(!is_string($sql) || $sql == '')
			{				
				$error->exception_throw(new Exception(EXCEPTION_MSG::QUERY_RUN_SQL, EXCEPTION_CODE::QUERY_RUN_SQL));				
			}
			
			// Attempt to prepare the query statement. If PDO throws an exception,
			// then we catch it and throw our own exception.
			try 
			{				
				// Break down dbo_config object to array. This is the only 
				// way prepare functions will accept optional config values.
				if($dbo_config)
				{
					//$dbo_config_a['Scrollable'] 			= $dbo_config->get_scrollable();
					//$dbo_config_a['SendStreamParamsAtExec']	= $dbo_config->get_sendstream();
					//$dbo_config_a['QueryTimeout'] 			= $dbo_config->get_timeout();
				}

				// Prepare query and get statement.
				$statement = $dbo_instance->query($sql, $dbo_config_a);				

				// Set DB statement data member.
				$this->statement = $statement;
			}
			catch(\PDOException $exception) 
			{	
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_RUN_ERROR, EXCEPTION_CODE::QUERY_RUN_ERROR));
			}
			
			// False/Failure returned.
			if(!$statement)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_RUN_FAIL, EXCEPTION_CODE::QUERY_RUN_FAIL));
			}			
		}
		catch (Exception $exception) 
		{
			// Catch exception internally if configured to do so.
			$error_handler->exception_catch();
		}
		
		// Return statement reference.
		return $statement;	
	}
	
}

?>
