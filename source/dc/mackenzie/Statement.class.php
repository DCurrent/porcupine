<?php

namespace dc\mackenzie;

require_once('config.php');

// Query object. Execute SQL queries and return data.
interface iStatement
{	
	// Accessors
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
	
	// Results
	function get_field_count();						// Return number of fields from query result.
	function get_field_metadata();					// Fetch and return table row's metadata array (column names, types, etc.).
	function get_line_array();						// Fetch line array from table rows.
	function get_line_array_all();					// Create and return a 2D array consisting of all line arrays from database query.
	function get_line_array_list(); 				// Create and return a linked list consisting of all line arrays from database query.
	function get_line_object();						// Fetch and return line object from table rows.
	function get_line_object_all();					// Create and return a 2D array consisting of all line arrays from database query.
	function get_line_object_list(); 				// Create and return a linked list consisting of all line objects from database query.
	function get_next_result();						// Move to and return next result set.
	function get_row_count();						// Return number of records from query result.
	function get_row_exists();						// Verify the result contains rows.
}

class Database implements iDatabase
{
	private $sto_instance	= NULL;		// Statement instance from database.
	private	$params 		= array();	// SQL parameters.
	private	$sql			= NULL;		// SQL string.
	private	$statement		= NULL;		// Prepared/Executed query reference.
	
	// Magic
	public function __construct(Database $dbo_instance = NULL)
	{
	}
	
	public function __destruct()
	{		
	}
	
		
	
	// *Accessors
	public function get_error()
	{
		return $this->error;	
	}
			
	public function get_param_array()
	{
		return $this->params;	
	}
	
	public function get_sto_instance()
	{
		return $this->sto_instance;
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
	
	
	public function set_error(Error $value)
	{
		$this->error = $value;
	}			
	
	public function set_sql($value)
	{
		$this->sql = $value;
	}
	
	public function set_sto_instance($value)
	{
		$this->sto_instance = $value;
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
	
	// *Results.
	public function get_field_count()
	{
		$error_handler 	= $this->config->get_error();
		$result			= 0;
		
		try 
		{
			// Missing statement?
			if(!$this->statement)
			{
				throw new Exception(EXCEPTION_MSG::FIELD_COUNT_STATEMENT, EXCEPTION_CODE::FIELD_COUNT_STATEMENT);
			}
			
			// Get field count.
			$result = sqlsrv_num_fields($this->statement);
			
			// Any errors?
			if($error_handler->detect_error())
			{
				throw new Exception(EXCEPTION_MSG::FIELD_COUNT_ERROR, EXCEPTION_CODE::FIELD_COUNT_ERROR);
			}
			
		}
		catch (Exception $exception) 
		{	
			$error_handler->exception_catch($exception);
		}
		
		// Return field count.
		return $result;
	}
	
	// Fetch and return table row's metadata array (column names, types, etc.).
	public function get_field_metadata()
	{
		$result = array();
		
		try 
		{
			// Missing statement?
			if(!$this->statement)
			{
				throw new Exception(EXCEPTION_MSG::METADATA_STATEMENT, EXCEPTION_CODE::METADATA_STATEMENT);
			}
			
			// Get metadata array.
			$result = sqlsrv_field_metadata($this->statement);
			
			// Any errors?
			if($error_handler->detect_error())
			{
				throw new Exception(EXCEPTION_MSG::METADATA_ERROR, EXCEPTION_CODE::METADATA_ERROR);
			}
			
			
			
		}
		catch (Exception $exception) 
		{	
			$error_handler->exception_catch($exception);
		}
		
		// Return metadata array.
		return $result;
	}
	
	// Fetch line array from table rows.
	public function get_line_array()
	{
		$result		= FALSE;	// Database line array.
		$statement	= NULL; 	// Query result reference.
		$fetchType	= NULL;		// Line array fetchtype.
		$row		= NULL;		// Row type.
		$offset		= NULL;		// Row position if absolute.
		
		// Dereference data members.
		$statement 	= $this->statement;
		$fetchType	= $this->line_config->get_fetchtype();
		$row		= $this->line_config->get_row();
		$offset		= $this->line_config->get_offset();		
		
		
		try 
		{
			// Missing statement?
			if(!$statement)
			{
				throw new Exception(EXCEPTION_MSG::LINE_ARRAY_STATEMENT, EXCEPTION_CODE::LINE_ARRAY_STATEMENT);
			}
			
			// Get line array.
			$result = sqlsrv_fetch_array($statement, $fetchType, $row, $offset);
			
			// Any errors?
			if($error_handler->detect_error())
			{
				throw new Exception(EXCEPTION_MSG::LINE_ARRAY_ERROR, EXCEPTION_CODE::LINE_ARRAY_ERROR);
			}
			
			// False/Failure returned.
			if($result === FALSE)
			{				
				$error->exception_throw(new Exception(EXCEPTION_MSG::LINE_ARRAY_FAIL, EXCEPTION_CODE::LINE_ARRAY_FAIL));
			}
			
		}
		catch (Exception $exception) 
		{	
			$error_handler->exception_catch($exception);
		}
		
		
		// Return line array.
		return $result;
	}
	
	// Create and return a 2D array consisting of all line arrays from database query.
	public function get_line_array_all()
	{
		$line_array	= FALSE;	// 2D array of all line arrays.
		$line		= NULL;		// Database line array.
				
		// Loop all rows from database results.
		while($line = $this->get_line_array())
		{				
			// Add line array to 2D array of lines.
			$line_array[] = $line;				
		}		
		
		// Return line array.
		return $line_array;
	}	
	
	// Create and return a linked list consisting of all line elements from database query.
	public function get_line_array_list()
	{		
		$result = new SplDoublyLinkedList();	// Linked list object.		
		$line	= NULL;				// Database line array.
		
		// Loop all rows from database results.
		while($line = $this->get_line_array())
		{				
			// Add line array to list of arrays.
			$result->push($line);
		}
	
		// Return results.
		return $result;
	}
	
	// Fetch and return line object from table rows.
	public function get_line_object()
	{
		$line		= NULL;		// Database line object.
		$statement	= NULL;		// Query result reference.
		$fetchType	= NULL;		// Line array fetchtype.
		$row		= NULL;		// Row type.
		$offset		= NULL;		// Row position if absolute.
		$class_name	= NULL;		// Class name.
		$class_params	= array();	// Class parameter array.
		
		// Dereference data members.
		$statement 	= $this->statement;
		$fetchType	= $this->line_config->get_fetchtype();
		$row		= $this->line_config->get_row();
		$offset		= $this->line_config->get_offset();
		$class		= $this->line_config->get_class_name();
		$class_params	= $this->line_config->get_class_params();
				
		// Get line object.
		$line = sqlsrv_fetch_object($statement, $class, $class_params, $row, $offset);
			
		// Return line object.
		return $line;
	}
	
	// Create and return an array consisting of all line objects from database query.
	public function get_line_object_all()
	{
		$line_array	= array();	// 2D array of all line objects.
		$line		= NULL;		// Database line objects.
		
		// Loop all rows from database results.
		while($line = $this->get_line_object())
		{				
			// Add line object to array of object.
			$line_array[] = $line;
		}
	
		// Return line object.
		return $line_array;
	}
	
	// Create and return a linked list consisting of all line objects from database query.
	public function get_line_object_list()
	{
		$result = new \SplDoublyLinkedList();	// Linked list object.	
		$line	= NULL;				// Database line objects.
		
		// Loop all rows from database results.
		while($line = $this->get_line_object())
		{				
			// Add line object to linked list.
			$result->push($line);
		}
	
		// Return linked list object.
		return $result;
	}
	
	// Move to and return next result set.
	public function get_next_result()
	{
		$result = FALSE;
		
		$result = sqlsrv_next_result($this->statement);
		
		return $result;
	
	}
	
	// Return number of records from query result.	
	public function get_row_count()
	{
		$count = 0;
		
		// Get row count.
		$count = sqlsrv_num_rows($this->statement);	
		
		// Return count.
		return $count;
	}
	
	// Verify result set contains any rows.	
	public function get_row_exists()
	{
		$result = FALSE;
		
		// Get row count.
		$result = sqlsrv_has_rows($this->statement);	
		
		// Return result.
		return $result;
	}
	
}

?>
