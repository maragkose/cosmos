<?php
	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'graph_api.php' );

	access_ensure_project_level( config_get( 'view_summary_threshold' ) );

	html_page_top1();
	html_page_top2();
	print_summary_menu( 'summary_page.php' );

	echo '<br />';
	print_menu_graph();

	$t_width = config_get( 'graph_window_width' );
	$t_graph_width = (int) ( ( $t_width - 50 ) * 0.9 );

	# gather the data for the graphs
	$t_metrics = create_interviewer_summary();
	$t_token = token_set( TOKEN_GRAPH, serialize( $t_metrics ) );

 ?>

<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="document-form">
		<?php echo lang_get( 'graph_imp_pipeline_title' ) ?>
	</td>
</tr>
<tr valign="top">
	<td width='100%'>

		<img src="summary_graph_bypipeline_stats.php?width=<?php echo $t_graph_width?>" border="0" alt="Per recruitment phase bar chart" />
	</td>
	<td width='100%'>
		<img src="summary_graph_bypipeline_stats_pie.php?width=<?php echo $t_graph_width?>" border="0"  alt="Per recruitment phase pie chart" />
	</td>
</tr>
<tr valign="top">
	<td width='100%'>

		<img src="summary_graph_bypipeline_stats_perc.php?width=<?php echo $t_graph_width?>" border="0" alt="Per recruitment phase bar chart" />
	</td>
</tr>
</table>

<?php html_page_bottom1( __FILE__ ) ?>
