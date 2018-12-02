<?php

require_once( 'core.php' );

$t_core_path = config_get( 'core_path' );

require_once( $t_core_path.'graph_api.php' );

function get_experience_level($years) {

	$years = intval($years);

	if ($years < 1) {
		return 10;
	} else if ($years >= 1 and $years <= 2){
		return 20;
	} else if ($years >=3 and $years <= 5){
		return 30;
	} else if ($years >=6 and $years <= 8){
		return 40;
	} else if ($years >=9 and $years <=10){
		return 50; 
	} else if ($years >=10){
		return 60; 
	}	
}

test_stats();

?>
