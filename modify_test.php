<?php
#
# Created by Emmanouil Maragkos
#
	require_once( 'core.php' );
	require_once( 'csv_form_keys.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'yml_api.php' );
	require_once( 'includes/spyc.php' );
	
	access_ensure_global_level( config_get( 'manage_activate_threshold' ) );      
	
#======================================================================================================
# MAIN SECTION 
#======================================================================================================

        html_page_top1();
	html_page_top2();
	
	$filename =  gpc_get_string('test_name');
	$set  = gpc_get_string('section');
	$question = gpc_get_string('new_question');
	$solution = gpc_get_string('solution');
	
	$loaded_test = Spyc::YAMLLoad($filename);

	if (gpc_isset('add_question')) {

		$arr = array();
		
		$question = preg_replace('/\r\n?/', "<br />", $question);
		$question = preg_replace("@<p>@", "", $question);
		$question = preg_replace("@</p>@", "", $question);
		$option1  = strip_tags(gpc_get_string('o1'));
		$option2  = strip_tags(gpc_get_string('o2'));
		$option3  = strip_tags(gpc_get_string('o3'));
		$option4  = strip_tags(gpc_get_string('o4'));
		$option1 = preg_replace('/\r\n?/', "<br />", $option1);
		$option2 = preg_replace('/\r\n?/', "<br />", $option2);
		$option3 = preg_replace('/\r\n?/', "<br />", $option3);
		$option4 = preg_replace('/\r\n?/', "<br />", $option4);
		$option1 = 'a) ' . $option1;
		$option2 = 'b) ' . $option2;
		$option3 = 'c) ' . $option3;
		$option4 = 'd) ' . $option4;
		

		if (is_array($loaded_test[$set]['items'])) {
			$item_keys = array_keys($loaded_test[$set]['items']);
			$item_count =  str_replace('item', '', end($item_keys));
			$item_count++;
			$next_item = 'item' . $item_count;
		} else {
			$next_item = 'item1';
		}
		
		$solution_string = $set . '__' . $next_item . ':' .  $solution . "\n";
		
		$loaded_test[$set]['items'][$next_item] = array('texto' => $question, 'formato'=>'unica', 'respuestas'=>array($option1, $option2, $option3, $option4));

		$yaml = Spyc::YAMLDump($loaded_test,2,60);
		
		file_put_contents($filename, $yaml);
		
		$info = pathinfo($filename);
		$solution_filename =  config_get('solutions_path') . $info['filename'] . '.csv';
		file_put_contents($solution_filename, $solution_string, FILE_APPEND);
	} else {
		#Edit Question Button is clicked in the manage_test.php page
		$oYML = new yml($loaded_test);
		die("Not Yet");		
	}
	$t_redirect = 'manage_test.php';
	echo '<br /><div align="center">';
	echo lang_get( 'operation_successful' ) . '<br />';
	html_meta_redirect( $t_redirect . '?' . "test_name=$filename");
	echo '</div>';
?>
