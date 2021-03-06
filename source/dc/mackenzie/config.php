<?php

namespace dc\mackenzie;

// Basic configuration and default values.
abstract class DEFAULTS
{
	// Ini File. This is the reccomended way to set
	// configuration.
	const CONFIG_SOURCE			= './source/config.ini';			
	
	// Connection options
	const HOST 					= '';						// Database host (server name or address)
	const NAME 					= '';						// Database logical name.
	const USER 					= '';						// User name to access database.
	const PASSWORD				= '';						// Password to access database.
	const CHARSET				= 'UTF-8';					// Character set.
	
	// Query options.
	const SCROLLABLE 			= SQLSRV_CURSOR_FORWARD;	// Cursor type.
	const SENDSTREAM 			= TRUE;						// Send whole parameter stream (TRUE), or send chunks (FALSE).
	const TIMEOUT				= 300;						// Query time out in seconds.	
	
	// Line options. 
	const FETCHTYPE				= SQLSRV_FETCH_ASSOC;		// Default row array fetch type.
	const ROW					= SQLSRV_SCROLL_NEXT;		// Row to access in a result set that uses a scrollable cursor.
	const OFFSET				= 0;						// Row to access if row is absolute or relative. 
	
	// Error exemptions. These codes will be ignored
	// by the respective layer of error trapping.
	const EXEMPT_ALL			= -1;						// If this is placed into any list, then all codes in that list will be considered exempt.
	const EXEMPT_CODES_CATCH	= '-1';						// Catching of thrown exceptions.
	const EXEMPT_CODES_DRIVER	= '0, 5701, 5703';			// Detection of errors from database driver.
	const EXEMPT_CODES_THROW	= '';						// Throwing exception codes.
	
	// New IDs. Passing these as IDs in upsert type quries ensures 
	// the databse engine will find no matches and create 
	// a new record.
	const NEW_ID				= -1;					
	const NEW_GUID				= '00000000-0000-0000-0000-000000000000';
}

// Codes output by thrown exceptions. Use these to take action
// in catch blocks outside of Yukon.
abstract class EXCEPTION_CODE
{
	// 0
	const CONNECT_CLOSE_FAIL		= 0X0;		// Driver returned failure response attempting to close database connection.
	const CONNECT_CLOSE_CONNECTION	= 0X1;		// There is no connection to close.
	const CONNECT_OPEN_FAIL			= 0X2;		// Driver returned failure response attempting to connect to database.
	const CONNECT_OPEN_HOST			= 0X3; 		// Application did not provide a host target to connect.
	
	// 100
	const FIELD_COUNT_ERROR			= 0X64;		// Field count returned an error code.
	const FIELD_COUNT_STATEMENT		= 0X65;		// Missing or invalid query statement getting field count.
	
	// 200
	const FREE_STATEMENT_ERROR		= 0Xc8;		// Free statement returned an error code.
	const FREE_STATEMENT_FAIL		= 0Xc9;		// Free statement returned failure.
	const FREE_STATEMENT_STATEMENT	= 0Xca;
	
	// 300
	const LINE_ARRAY_ERROR			= 0X12c;	// Line array - Line array fetch method returned an error code.
	const LINE_ARRAY_FAIL			= 0X12d;	// Line array - Line array fetch method returned a failure response.
	const LINE_ARRAY_STATEMENT		= 0X12e;	// Line array - Invalid query statement.
	
	// 400
	const METADATA_ERROR			= 0X190;	// Metadata returned an error code.
	const METADATA_STATEMENT		= 0X191;	// Missing or invalid query statement getting metadata.
	
	// 500	
	const QUERY_ACTION_ERROR		= 0X1f4;	// Query action returned an error code.
	const QUERY_ACTION_FAIL			= 0X1f5;	// Query action returned a failure response.
	const QUERY_ACTION_STATEMENT	= 0X1f6;	// Missing or invalid query statement on Query action.
	
	// 600
	const QUERY_EXECUTE_ERROR		= 0X258;	// Execute returned an error code.
	const QUERY_EXECUTE_FAIL		= 0X259;	// Execute returned a failure response.
	const QUERY_EXECUTE_STATEMENT	= 0X25a;	// Missing or invalid query statement on execution.
	
	// 700
	const QUERY_PREPARE_CONFIG		= 0X2bc;	// Prepare query - No valid database config.
	const QUERY_PREPARE_CONNECTION	= 0X2bd;	// Prepare query - No connection to database.
	const QUERY_PREPARE_ERROR		= 0X2be;	// Prepare query - Driver prepare method returned an error code.
	const QUERY_PREPARE_FAIL		= 0X2bf;	// Prepare query - Driver prepare method returned a failure response.
	const QUERY_PREPARE_PARAM_ARRAY	= 0X2c0;	// Prepare query - No valid array of parameters.
	const QUERY_PREPARE_PARAM_LIST	= 0X2c1;	// Prepare query - No valid list of parameters.
	const QUERY_PREPARE_SQL			= 0X2c2;	// Prepare query - No valid SQL string.
	
	// 800
	const QUERY_RUN_CONFIG			= 0X320;	// Run query - No valid database config.
	const QUERY_RUN_CONNECTION		= 0X321;	// Run query - No connection to database.
	const QUERY_RUN_ERROR			= 0X322;	// Run query - Driver prepare method returned an error code.
	const QUERY_RUN_FAIL			= 0X323;	// Run query - Driver prepare method returned a failure response.
	const QUERY_RUN_PARAM_ARRAY		= 0X324;	// Run query - No valid array of parameters.
	const QUERY_RUN_PARAM_LIST		= 0X325;	// Run query - No valid list of parameters.
	const QUERY_RUN_SQL				= 0X326;	// Run query - No valid SQL string.
	
	// 900
	const ROW_COUNT_ERROR			= 0X384;	// Row count returned an error code.
	const ROW_COUNT_STATEMENT		= 0X385;	// Missing or invalid query statement getting row count.
}

// Output given by interal exception handler.
abstract class EXCEPTION_MSG
{
	const CONNECT_CLOSE_FAIL		= 'Close Connection - Failed closing connection to host.';
	const CONNECT_CLOSE_CONNECTION	= 'Close Connection - No valid connection to close.';
	const CONNECT_OPEN_FAIL			= 'Close Connection - Failed to open connection with host.';
	const CONNECT_OPEN_HOST			= 'Close Connection - Missing or invalid host argument.';
	
	const FIELD_COUNT_ERROR			= 'Field Count - Error occurred.';
	const FIELD_COUNT_STATEMENT		= 'Field Count - Missing or invalid statement.';
	
	const FREE_STATEMENT_ERROR		= 'Free Statement - Error occurred.';
	const FREE_STATEMENT_FAIL		= 'Free statement - Failed to free statement.';
	const FREE_STATEMENT_STATEMENT	= 'Free Statement - No valid statement to free.';
	
	const LINE_ARRAY_ERROR			= 'Line Array - Error occurred..';
	const LINE_ARRAY_FAIL			= 'Line Array - Failed to get line array.';
	const LINE_ARRAY_STATEMENT		= 'Line Array - Missing or invalid statement.';
	
	const METADATA_ERROR			= 'Get Metadata - Error occurred.';
	const METADATA_STATEMENT		= 'Get Metadata - Missing or invalid statement.';
	
	const QUERY_ACTION_ERROR		= 'Query Action - Error occurred.';
	const QUERY_ACTION_FAIL			= 'Query Action - Failed to execute prepared query.';
	const QUERY_ACTION_STATEMENT	= 'Query Action - Missing or invalid statement.';
	
	const QUERY_EXECUTE_ERROR		= 'Query Execute - Error occurred.';
	const QUERY_EXECUTE_FAIL		= 'Query Execute - Failed to execute prepared query.';
	const QUERY_EXECUTE_STATEMENT	= 'Query Execute - Missing or invalid statement.';
	
	const QUERY_PREPARE_CONFIG		= 'Query Prepare - Missing or invalid database config.';
	const QUERY_PREPARE_CONNECTION	= 'Query Prepare - Missing or invalid database connection.';
	const QUERY_PREPARE_ERROR		= 'Query Prepare - Error occurred.';
	const QUERY_PREPARE_FAIL		= 'Query Prepare - Failed to prepare query statement.';
	const QUERY_PREPARE_PARAM_ARRAY	= 'Query prepare - Missing or invalid parameter array.';
	const QUERY_PREPARE_PARAM_LIST	= 'Query prepare - Missing or invalid parameter list.';
	const QUERY_PREPARE_SQL			= 'Query prepare - Missing or invalid SQL string.';
	
	const QUERY_RUN_CONFIG			= 'Query Run - Missing or invalid database config.';
	const QUERY_RUN_CONNECTION		= 'Query Run - Missing or invalid database connection.';
	const QUERY_RUN_ERROR			= 'Query Run - Error occurred.';
	const QUERY_RUN_FAIL			= 'Query Run - Failed to Run query statement.';
	const QUERY_RUN_PARAM_ARRAY		= 'Query Run - Missing or invalid parameter array.';
	const QUERY_RUN_PARAM_LIST		= 'Query Run - Missing or invalid parameter list.';
	const QUERY_RUN_SQL				= 'Query Run - Missing or invalid SQL string.';
	
	const ROW_COUNT_ERROR			= 'Get Row Count - Error occurred.';
	const ROW_COUNT_STATEMENT		= 'Get Row Count - Missing or invalid statement.';
}

?>
