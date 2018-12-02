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
	#graph_bar( $t_metrics, lang_get( 'by_first_interviewer' ), $f_width, $f_width * $t_ar );

 ?>

<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="document-form">
		<?php echo lang_get( 'graph_imp_interviewer_title' ) ?>
	</td>
</tr>
<tr valign="top">
	<td width='100%'>

		<left><img src="summary_graph_byinterviewer_total.php?width=<?php echo $t_graph_width?>" border="0" /></left>
	</td>
	<td width='100%'>
		<left><img src="summary_graph_byinterviewer_total_pie.php?width=<?php echo $t_graph_width?>" border="0" /></left>
	</td>
</tr>
<tr valign="top">
	<td width='100%'>
		<left><img src="summary_graph_byinterviewer.php?width=<?php echo $t_graph_width?>" border="0" /></left>
	</td>
	<td width='100%'>
		<left><img src="summary_graph_byinterviewer_pie.php?width=<?php echo $t_graph_width?>" border="0" /></left>
	</td>
</tr>
<tr valign="top">
	<td width='100%'>
		<left><img src="summary_graph_bysec_interviewer.php?width=<?php echo $t_graph_width?>" border="0" /></left>
	</td>
	<td width='100%'>
		<left><img src="summary_graph_bysec_interviewer_pie.php?width=<?php echo $t_graph_width?>" border="0" /></left>
	</td>
</tr>
</table>

<?php html_page_bottom1( __FILE__ ) ?>
