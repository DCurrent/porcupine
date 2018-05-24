<?php
	namespace dc\data;

	interface iAccount
	{
		// Accessors.
		function get_id_key();
		function get_account();
		function get_credential();
		function get_name_f();		// First name.		
		function get_name_l();		// Last name.
		function get_name_m();		// Middle name.
		
		// Mutators.
		function set_account(string $value);
		function set_credential(string $value);
		function set_id_key(int $value);
		function set_name_f(string $value);		// First name.		
		function set_name_l(string $value);		// Last name.
		function set_name_m(string $value);		// Middle name.
	}

	trait Database
	{
		private $data_object;		// Single data object, populated from query.
		private $data_object_list;	// list of data objects.
		private $engine_statement;	// Statement reference from database engine.
		
		public function set_statement($value)
		{
			$this->engine_statement = $value;
		}
		
		public function get_statement()
		{
			return $this->engine_statement;
		}
		
		// Create and return an SplDoublyLinkedList of 
		// data objects.
		public function build_object_list()
		{	
			$statement = $this->engine_statement;
			//$_obj_data_main_list = new \SplDoublyLinkedList();
			
			$_object_array = $statement->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
			
			
			$_obj_data_main_list = new \ArrayObject($_object_array);
			
			$this->data_object_list = $_obj_data_main_list->getIterator();			
			
			// Return object list.
			return $this->data_object_list;
		}
	}
	
	class Account implements iAccount
	{
		use Database;
		
		private $id_key		= NULL;
		private $account	= NULL;	
		private $credential	= NULL;
		private $name_f		= NULL;			
		private $name_l		= NULL;
		private $name_m		= NULL;		
		
		public function get_namespace()
		{			
			return __CLASS__;
		}
		
		// Magic methods
		public function __construct()
		{}
		
		// Called if a property does not exisit 
		// for database fetch to populate.
		//
		// Do nothing. This prevents creation of
		// new public members when a field from the
		// database doesn't have a matching member 
		// in class.
		public function __set($name, $value)
		{}
		
		// Accessors					
		public function get_account()
		{			
			return $this->account;
		}
		
		public function get_credential()
		{
			return $this->credential;
		}
		
		public function get_id_key()
		{
			return $this->id_key;
		}
		
		public function get_name_f()
		{
			return $this->name_f;
		}		
		
		public function get_name_l()
		{
			return $this->name_l;
		}
		
		public function get_name_m()
		{
			return $this->name_m;
		}
		
		// Mutators
		public function set_account(string $value)
		{
			$this->account = $value;
		}
		
		public function set_credential(string $value)
		{
			$this->credential = $value; 
		}
		
		public function set_id_key(int $value)
		{
			$this->id_key = $value;
		}	
		
		public function set_name_f(string $value)
		{
			$this->name_f = $value;
		}
		
		public function set_name_l(string $value)
		{
			$this->name_l = $value;
		}
		
		public function set_name_m(string $value)
		{
			$this->name_m = $value;
		}			
	}

?>