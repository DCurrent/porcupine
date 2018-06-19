<?php

namespace dc\mackenzie;
use \PDOStatement as STO;

require_once('config.php');

// Query object. Execute SQL queries and return data.
interface iStatement
{	
	// Accessors
	function get_fetch_class_name();					 
	function get_sto_config();
	function get_sto_instance();
	
	// Mutators
	function set_fetch_class_name(string $value);
	function set_sto_config(StatementConfig $value);
	function set_sto_instance(STO $value);
	
	// Operations
	function field_count();			// Return number of fields.
	function field_metadata();		// Fetch and return table row's metadata array (column names, types, etc.).
	function free_statement();		// Free statement and clear statement member.
	function line_object();			// Fetch and return line object from table rows.
	function line_object_list();	// Create and return a linked list consisting of all line objects from database query.
	function next_result();			// Move to and return next result set.
	function row_count();			// Return number of records.
	function row_exists();			// Verify the statement contains rows.
}

class Statement implements iStatement
{
	private $sto_config			= NULL;		// Statement config object.
	private $sto_instance		= NULL;		// Statement instance from database.
	private $fetch_class_name	= NULL;		// Class name when fetching to a class.
		
	// Magic
	public function __construct(STO $sto_instance = NULL, StatementConfig $sto_config = NULL)
	{
		$this->construct_config($sto_config);	
	}
	
	public function __destruct()
	{		
	}		
	
	// Constructors
	private function construct_config(StatementConfig $value = NULL)
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
			$result = new StatementConfig();		
		}
		
		// Populate member with result.
		$this->sto_config = $result;
	
		return $result;
	}
	
	// *Accessors	
	public function get_fetch_class_name()
	{
		return $this->fetch_class_name;
	}
	
	public function get_sto_config()
	{
		return $this->sto_config;
	}
	
	public function get_sto_instance()
	{
		return $this->sto_instance;
	}
	
	// *Mutators
	public function set_fetch_class_name(string $value)
	{
		$this->fetch_class_name = $value;
	}
	
	public function set_sto_config(StatementConfig $value)
	{
		$this->sto_config = $value;
	}
	
	public function set_sto_instance(STO $value)
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
	public function field_count()
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
	public function field_metadata()
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
	public function line_object()
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
	public function line_object_all()
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
	public function line_object_list()
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
	public function next_result()
	{
		$result = FALSE;
		
		$result = $this->sto_instance->nextRowset();
		
		return $result;
	
	}
	
	// Return number of records from query result.	
	public function row_count()
	{
		$count = 0;
		
		// Get row count.
		$count = sqlsrv_num_rows($this->statement);	
		
		// Return count.
		return $count;
	}
	
	// Verify result set contains any rows.	
	public function row_exists()
	{
		$result = FALSE;
		
		// Get row count.
		$result = sqlsrv_has_rows($this->statement);	
		
		// Return result.
		return $result;
	}
	
}

?>
