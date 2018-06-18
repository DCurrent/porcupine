<?php

namespace dc\mackenzie;

require_once('config.php');

// Query object. Execute SQL queries and return data.
interface iStatement
{	
	// Accessors
	function get_sto_instance();					// Return statement instance data member.
	
	// Mutators
	function get_sto_instance($value);				// Set statement instance data member.
	
	// Operations
	function free_statement();						// Free statement and clear statement member.
	
	// Results
	function get_field_count();						// Return number of fields.
	function get_field_metadata();					// Fetch and return table row's metadata array (column names, types, etc.).
	function get_line_object();						// Fetch and return line object from table rows.
	function get_line_object_all();					// Create and return a 2D array consisting of all line arrays from database query.
	function get_line_object_list(); 				// Create and return a linked list consisting of all line objects from database query.
	function get_next_result();						// Move to and return next result set.
	function get_row_count();						// Return number of records.
	function get_row_exists();						// Verify the statement contains rows.
}

class Database implements iDatabase
{
	private $sto_instance	= NULL;		// Statement instance from database.
		
	// Magic
	public function __construct(Database $sto_instance = NULL)
	{
	}
	
	public function __destruct()
	{		
	}		
	
	// *Accessors	
	public function get_sto_instance()
	{
		return $this->sto_instance;
	}
	
	// *Mutators
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
