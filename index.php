<?php 
	
	use \dc\mackenzie as mackenzie;

	// Central config and libraries.
	require(__DIR__.'/source/main.php');

	// Initialize connection configuration.
	$_dbo_database = new mackenzie\Database();

	// SQL string.
	$sql = 'CALL sp_account_login(:param_account, :param_password)';

	// Prepare SQL statement.
	$sth = $_dbo_database->get_dbo_instance()->prepare($sql);

	$sto = new mackenzie\Statement($sth);

	$account 	= '';
	$password	= '';

	// Bind arguments for SQl statement.
	$sto->get_sto_instance()->bindValue(':param_account', $account, PDO::PARAM_STR);
	$sto->get_sto_instance()->bindValue(':param_password', $password, PDO::PARAM_STR);		

	// Execute query from statement.
	$sto->get_sto_instance()->execute();

	$_account = new \dc\data\Account();
	$_account->set_statement($sto->get_sto_instance());

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
		
		<!-- Required meta tags -->
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
		
        <title><?php echo APPLICATION_SETTINGS::NAME; ?></title>        
        
		<!-- Icon sets -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		
        <!-- CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
        <link rel="stylesheet" href="source/css/style.css" />
        <link rel="stylesheet" href="source/css/print.css" media="print" />
        
        
        <!-- Bootstrap support components -->	
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    
	</head>
    
    <body>              
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
                		<p class="lead">Welcome to <?php echo APPLICATION_SETTINGS::NAME; ?>. To get started, please sign in with your account and password.</p>
            		
                    	<p><?php //echo $access_obj->dialog(); ?></p>
                    	
                        <!--Note: PHP self is nessesary to override any link vars.-->
                        <form role="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <div class="form-group">
								<div class="input-group col-sm-10">
                    				<span class="input-group-prepend input-group-text fa fa-user-o"></span>                           
                                	<input type="text" class="form-control" name="account" id="account" placeholder="Account" required>
								</div>
                            </div>
                            <br>
                            <div class="form-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                <input type="password" class="form-control" name="credential" id="credential" placeholder="Password" required>
                            </div>
                            
                            <br>
                            
                            <button type="submit" name="access_action" value="<?php //echo \dc\stoeckl\ACTION::LOGIN; ?>" class="btn btn-lg btn-primary btn-block">Sign in</button>
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