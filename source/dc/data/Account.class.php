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
	
	class Account implements iAccount
	{
		protected
			$id_key		= NULL,
			$account	= NULL,	
			$credential	= NULL,
			$name_f		= NULL,			
			$name_l		= NULL,
			$name_m		= NULL;
			
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