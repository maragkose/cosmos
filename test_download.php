<?php

        require_once( 'core.php' );

        $t_core_path = config_get( 'core_path' );

        require_once( $t_core_path.'ajax_api.php' );
        require_once( $t_core_path.'candidate_api.php' );
        require_once( $t_core_path.'custom_field_api.php' );
        require_once( $t_core_path.'date_api.php' );
        require_once( $t_core_path.'last_visited_api.php' );
        require_once( $t_core_path.'projax_api.php' );
	
	$t_attachment_rows = candidate_get_attachments( 4065 );

	$num_files = sizeof( $t_attachment_rows );
	if ( $num_files === 0 ) {
		echo "oups";
		return;
	}
	$row = $t_attachment_rows[0];
	extract( $row, EXTR_PREFIX_ALL, 'v' );
	$t_href_start	= "<a href=\"file_download.php?file_id=$v_id&amp;type=candidate\">";
	$t_href_end	= '</a>';

  	global $t_icon_path;	
	echo $t_href_start;
	echo '<img width="24" height="24" src="' . $t_icon_path . 'attachment.png' . '" alt="attachments" />';
	echo $t_href_end;
?>
