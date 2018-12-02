<?php
#
# Created by Emmanouil Maragkos
#
	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'candidate_api.php' );
	require_once( 'includes/spyc.php' );
	
	access_ensure_global_level( config_get( 'manage_create_threshold' ) );      
	
#======================================================================================================
# MAIN SECTION 
#======================================================================================================

        html_page_top1();
	html_page_top2();

	$new_test = gpc_get_string('new_test');
	$test_path = config_get('tests_pool_path') . $new_test . ".yml";

	if ( file_exists($test_path) ) {
		$message = 'File already exists. Enter a different filename' . '<br >';
	} else {
	        $message = "<br /> Test $new_test has been added to the pool <br />" . lang_get( 'operation_successful' ) . '<br />'; 
	}
	$handle = fopen($test_path, 'w');
	fclose($handle);
	$info = pathinfo($test_path);
	$initial_test1['quiz'] = array( 'title' => $info['filename'] , 
					'description' => 'Welcome in the technical test round of the Nokia Siemens Networks Assessment Day.', 
					'thanks' => 'Thanks for your participation.',
					'intro' => 's');
	
	$initial_test1['personal_data'] = array(  'title' => "Enter your First Name:",	
					   	  'items'=>  array('Name' => array('formato'=>abierta, 'texto'=>'Name')));
	$initial_test1['personal_data']['items']['Surname'] = array('formato'=>abierta, 'texto'=>'Surname');
	$initial_test1['personal_data']['items']['ID']      = array('formato'=>abierta, 'texto'=>'ID');
						             
	$i1 = Spyc::YAMLDump($initial_test1,2,60);
	
	file_put_contents($test_path, $i1);

	$t_redirect = 'manage_tests_page.php';
	echo '<br /><div align="center">';
	echo $message;
	echo "<br />" ;
	print_bracket_link( $t_redirect, lang_get( 'proceed' ) );
	html_meta_redirect( $t_redirect );
	echo '</div>';

?>
