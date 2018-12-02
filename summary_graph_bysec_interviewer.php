<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'graph_api.php' );

	access_ensure_project_level( config_get( 'view_summary_threshold' ) );

	$f_width = gpc_get_int( 'width', 300 );
	$t_ar = config_get( 'graph_bar_aspect' );

	$t_metrics = create_second_interviewer_summary();
	graph_bar( $t_metrics, lang_get( 'by_second_interviewer' ), $f_width, $f_width * $t_ar );
?>
