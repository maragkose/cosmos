<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'email_api.php' );

	$users = project_get_all_user_rows( ALL_PROJECTS, ANYBODY );
	foreach( $users as $user ) {

		$recipient_email = user_get_email( $user['id'] );
		$subject = '[' .  lang_get('interview_available_subject') .  ']';
		$user_name = $user['realname'];

		$message = 'Hello ' . $user_name . ',' . "\n\n" . 
			   lang_get( 'unassigned_interviews_msg' )     .  " \n\n" .
		  	   lang_get( 'unassigned_home_box')            .  " \n\n" . 
		  	   lang_get( 'unassigned_interviews_msg_end' ) .  " \n  "; 

		if( !is_blank( $recipient_email ) ) {
			if ($_POST['send_reminder'] == 'yes'){
				email_store( $recipient_email, $subject, $message );
			}
		}
	}
	html_page_top1();
	html_meta_redirect('my_view_page.php');
	html_page_top2();
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ).'<br />';
?>
</div>
<?php html_page_bottom1( __FILE__ ) ?>

