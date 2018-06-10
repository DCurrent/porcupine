<?php 
	
	use \dc\mackenzie as mackenzie;

	// Central config and libraries.
	require(__DIR__.'/source/main.php');

	// Initialize connection configuration.
	$_dbo_database = new mackenzie\Database();

	// SQL string.
	$sql = 'CALL sp_account_login(:param_account, :param_password)';

	// Prepare SQL statement.
	$sth = $_dbo_database->get_connection()->get_connection()->prepare($sql);
	
	$account 	= '';
	$password	= '';

	// Bind arguments for SQl statement.
	$sth->bindValue(':param_account', $account, PDO::PARAM_STR);
	$sth->bindValue(':param_password', $password, PDO::PARAM_STR);		

	// Execute query from statement.
	$sth->execute();

	$_account = new \dc\data\Account();
	$_account->set_statement($sth);

	$_obj_data_main_list = $_account->build_object_list();

	echo "Iterating over: " . $_obj_data_main_list->count() . " values\n";

	// Iterate over the values in the ArrayObject:
	while($_obj_data_main_list->valid())
	{		
		$_obj_data_main = $_obj_data_main_list->current();
			
		echo $_obj_data_main->get_name_l().', ';
		echo $_obj_data_main->get_name_f().' ';
		echo $_obj_data_main->get_name_m();
		
		$_obj_data_main_list->next();
	}

	//echo $dbh;

	/*
	function common_form_redirect($yukon_connection)
	{
		$result = FALSE;
		$request_form = NULL;
		$request_list = NULL;
		
		if(isset($_REQUEST['id_form']))
		{
			$request_form = $_REQUEST['id_form'];
		}
		else
		{
			return $result;
		}
		
		if(isset($_REQUEST['list']))
		{
			$request_list = $_REQUEST['list'];
		}
		
		$database = new \dc\yukon\Database($yukon_connection);
		
		$_main_data = new \dc\application\CommonEntry();	
		
		// Populate from request so that we have an 
		// ID and KEY ID (if nessesary) to work with.
		$_main_data->populate_from_request();
		
		// Set up primary query with parameters and arguments.
		$database->set_sql('{call config_form(@param_filter_id = ?)}');
		$params = array(array($request_form, 		SQLSRV_PARAM_IN));
	
		// Apply arguments and execute query.
		$database->set_param_array($params);
		$database->query_run();
		
		// Skip navigation data and get primary data record set.	
		$database->get_next_result();
		
		$database->get_line_config()->set_class_name('\dc\application\CommonEntry');	
		if($database->get_row_exists() === TRUE) 
		{
			$_main_data = $database->get_line_object();
			
			if($_main_data->get_file_name())
			{
				$base_target = $_main_data->get_file_name();
			}
			else
			{
				$base_target = 'common_entry.php';
			}
			
			// Open record navigation object so we can
			// get variables for redirect URL.
			$obj_navigation_rec = new \dc\recordnav\RecordNav();	
			
			// Initialize redirect url object and 
			// populate variables.
			$url_query	= new \dc\url\URLFix;
			$url_query->set_data('action', $obj_navigation_rec->get_action());
			$url_query->set_data('id', $obj_navigation_rec->get_id());
			$url_query->set_data('id_key', $obj_navigation_rec->get_id_key());
			$url_query->set_data('id_form', $request_form);
			
			if($request_list)
			{
				// Final result, and the target forwarding destination.
				$result 	= '#';
			
				// First thing we need is the self path.				
				$file = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);
				
				// List giles are the name of a single record file
				// with _list added on, so all we need to do is
				// remove the file suffix, and add '_list.php' to
				// get the list file's name. This is also all we
				// need for forwarding purposes.	
				$target_name	= basename($base_target, '.php').'_list.php';		
				
				// To verify the list file exists, we have to target the
				// file system path. We can combine the document root
				// and self's directory to get it.
				$root			= filter_input(INPUT_SERVER, 'DOCUMENT_ROOT', FILTER_SANITIZE_URL);
				$directory 		= dirname($file);
				$target_file	= $root.$directory.'/'.$target_name;
				
				// Does the list file exisit? If so we can
				// redirect to it. Otherwise, do nothing.
				if(file_exists($target_file))
				{	
					// Set target url.					
					$result = $target_name;
				}
				else
				{
					$result = $base_target;
				}								
			}
			else
			{
				$result = $base_target;
			}
			
			$url_query->set_url_base($result);
			
			header('Location: '.$url_query->return_url());			
			exit;
		}
				
	}	
	
	common_form_redirect($yukon_connection);
	*/
	
	$page_obj = new \dc\cache\PageCache();
	
	//$access_obj_process = new \dc\stoeckl\process();
	//$access_obj_process->get_config()->set_authenticate_url(APPLICATION_SETTINGS::DIRECTORY_PRIME);	
	//$access_obj_process->get_config()->set_database($yukon_database);
	//$access_obj_process->process_control();
	
	//var_dump($_POST);
	//echo '<br />';
	//var_dump($_SESSION);
	
	//Get and verify log in status.
	//$access_obj = new \dc\stoeckl\status();
	//$access_obj->get_config()->set_authenticate_url(APPLICATION_SETTINGS::DIRECTORY_PRIME);	
	//$access_obj->get_config()->set_database($yukon_database);
	//$access_obj->verify();
	
	// Set up navigaiton.
	$navigation_obj = new class_navigation();
	$navigation_obj->generate_markup_nav();
	$navigation_obj->generate_markup_footer();
?>

<!DOCtype html>
<html lang="en">
    <head>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1" />
        <title><?php echo APPLICATION_SETTINGS::NAME; ?></title>        
        
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="source/css/style.css" />
        <link rel="stylesheet" href="source/css/print.css" media="print" />
        
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        
        <!-- Latest compiled JavaScript -->
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </head>
    
    <body>          
        <!-- Modal -->
        <div id="help_link_blue" class="modal fade" role="dialog">
          <div class="modal-dialog">
        
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Link Blue</h4>
              </div>
              <div class="modal-body">
                <p>Link Blue is the University of Kentucky's campus wide Active Directory login. It is the same account name and password you use to log into a workstation. <a href="//www.uky.edu/UKHome/subpages/linkblue.html" target="_blank">Click here</a> for more information.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>        
          </div>
        </div>
    
        <div id="container" class="container">            
            <?php echo $navigation_obj->get_markup_nav(); ?>                                                                                
            <div class="page-header">
                <h1><?php echo APPLICATION_SETTINGS::NAME; ?></h1>
                <p class="lead">
				<?php
				
					/*
					echo '<!--account:'.$access_obj->get_account().'-->';
					echo '<!--id:'.$access_obj->get_id().'-->';
					// Logged in?
					if($access_obj->get_account())
					{
						// Get current hour in the 24 hour clock format.
						$time = date('H');
						
						// Give user a greeting based on hour of the day.
						if ($time < '12') 
						{
							echo 'Good morning ';
						} 
						else if ($time >= '12' && $time < '17') 
						{
							echo 'Good afternoon ';
						} 
						else if ($time >= "17") 
						{
							echo 'Good evening ';
						}
						echo $access_obj->get_name_f();
				?>! Thank you for using <?php echo APPLICATION_SETTINGS::NAME; ?>.</p>
                <?php
					}
					else
					{
					*/
				?>
                		<p class="lead">Welcome to <?php echo APPLICATION_SETTINGS::NAME; ?>. In order to use <?php echo APPLICATION_SETTINGS::NAME; ?>, please log in using your account and password.</p>
            		
                    	<p><?php //echo $access_obj->dialog(); ?></p>
                    	
                        <!--Note: PHP self is nessesary to override any link vars.-->
                        <form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                <input type="text" class="form-control" name="account" id="account" placeholder="Account" required>
                            </div>
                            <br>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                <input type="password" class="form-control" name="credential" id="credential" placeholder="Password" required>
                            </div>
                            
                            <br>
                            
                            <button type="submit" name="access_action" value="<?php //echo \dc\stoeckl\ACTION::LOGIN; ?>" class="btn btn-default"><span class="glyphicon glyphicon-log-in"></span> Login</button>
                        </form>
            
                <?php
					//}					
				?>
            </div> 
                    
            <?php echo $navigation_obj->get_markup_footer(); ?>
        </div><!--container-->    
</body>
</html>

<?php
	// Collect and output page markup.
	echo $page_obj->markup_and_flush();
?>