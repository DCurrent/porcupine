<?php

namespace dc\mackenzie;

require_once('config.php');

// Query object. Execute SQL queries and return data.
interface iDatabase
{	
	// Accessors
	function get_config();							// Return config object.
	function get_connection();						// Return connection object.
	function get_line_config();						// Return line parameters object.
	function get_param_array();						// Return query parameter array.
	function get_sql();								// Return current SQl statement.
	function get_statement();						// Return query statement data member.
	
	// Mutators
	function set_config(DatabaseConfig $value);		// Set the object to be used for query config settings.
	function set_connection(Connect $value);		// Set connection data member.
	function set_line_config(LineConfig $value);	// Set line parameters object.
	function set_param_array(array $value);			// Set query sql parameter array data member.
	function set_sql($value);						// Set query sql string data member.
	function set_statement($value);					// Set query statement reference.
	
	// Operations
	function free_statement();						// Free statement and clear statement member.
	function query_execute();						// Execute prepared query with current parameters.
	function query_prepare();						// Prepare query. Returns statement reference and sends to data member.
	function query_run();							// Prepare and execute query.	
}

class Database implements iDatabase
{
	private $config			= NULL;		// Query config object.
	private	$connect		= NULL;		// DB connection object.
	private	$line_config	= NULL;		// Line get config.
	private	$params 		= array();	// SQL parameters.
	private	$sql			= NULL;		// SQL string.
	private	$statement		= NULL;		// Prepared/Executed query reference.
	
	// Magic
	public function __construct(Connect $connect = NULL, DatabaseConfig $config = NULL, LineConfig $line_config = NULL)
	{
		// Set up memeber objects we'll need. In most cases,
		// if an argument is NULL, a blank object will
		// be created and used. See individual methods
		// for details.
		$this->construct_connection($connect);
		//$this->construct_config($config);
		//$this->construct_line_parameters($line_config);	
	}
	
	public function __destruct()
	{
	}
	
	// *Constructors
	private function construct_connection(Connect $value = NULL)
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
			$result = new Connect();		
		}
		
		// Populate member with result.
		$this->connect = $result;
	
		return $result;		
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
		$this->config = $result;
	
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
	public function get_config()
	{
		return $this->config;
	}
	
	public function get_connection()
	{
		return $this->connect;
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
	
	// *Mutators
	public function get_sql()
	{
		return $this->sql;
	}
	
	// Set query sql parameters data member.
	public function set_param_array(array $value)
	{		
		$this->params = $value;
	}
	
	public function set_config(DatabaseConfig $value)
	{
		$this->config = $value;
	}
	
	public function set_connection(Connect $value)
	{
		$this->connect = $value;
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
		$error_handler 	= $this->config->get_error();
		
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
		$error_handler 	= $this->config->get_error();
		
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
		$error_handler 	= $this->config->get_error();
		
		$connect	= NULL;		// Database connection reference.
		$statement	= NULL;		// Database statement reference.			
		$sql		= NULL;		// SQL string.
		$params		= array(); 	// Parameter array.
		$config		= NULL;		// Query config object.
		$config_a	= array();	// Query config array.
		
		// Dereference data members.
		$connect	= $this->connect->get_connection();
		$sql 		= $this->sql;
		$params 	= $this->params;
		$config		= $this->config;
		
		try 
		{
			// Verify connection.
			if(!$connect)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_PREPARE_CONNECTION, EXCEPTION_CODE::QUERY_PREPARE_CONNECTION));				
			}		
			
			// Verify config object.
			if(!is_object($config))
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
				if($config)
				{
					//$config_a['Scrollable'] 			= $config->get_scrollable();
					//$config_a['SendStreamParamsAtExec']	= $config->get_sendstream();
					//$config_a['QueryTimeout'] 			= $config->get_timeout();
				}

				// Prepare query and get statement.
				$statement = $connect->prepare($sql, $config_a);				

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
		$error_handler 	= $this->config->get_error();
		
		$connect	= NULL;		// Database connection reference.
		$statement	= NULL;		// Database statement reference.			
		$sql		= NULL;		// SQL string.
		$params		= array(); 	// Parameter array.
		$config		= NULL;		// Query config object.
		$config_a	= array();	// Query config array.
		
		// Dereference data members.
		$connect	= $this->connect->get_connection();
		$sql 		= $this->sql;
		$params 	= $this->params;
		$config		= $this->config;
		
		try 
		{
			// Verify connection.
			if(!$connect)
			{				
				$error_handler->exception_throw(new Exception(EXCEPTION_MSG::QUERY_RUN_CONNECTION, EXCEPTION_CODE::QUERY_RUN_CONNECTION));				
			}		
			
			// Verify config object.
			if(!is_object($config))
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
				// Break down config object to array. This is the only 
				// way prepare functions will accept optional config values.
				if($config)
				{
					//$config_a['Scrollable'] 			= $config->get_scrollable();
					//$config_a['SendStreamParamsAtExec']	= $config->get_sendstream();
					//$config_a['QueryTimeout'] 			= $config->get_timeout();
				}

				// Prepare query and get statement.
				$statement = $connect->query($sql, $config_a);				

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
