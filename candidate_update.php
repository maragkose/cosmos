<?php
# COSMOS - a php based candidatetracking system

# 
# 

# COSMOS is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# COSMOS is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with COSMOS.  If not, see <http://www.gnu.org/licenses/>.

	# --------------------------------------------------------
	# $Id: candidate_update.php,v 1.91.2.3 2007-10-26 08:52:18 giallu Exp $
	# --------------------------------------------------------

	# Update candidate data then redirect to the appropriate viewing page

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'email_invitation_api.php' );
	require_once( $t_core_path.'candidatenote_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
	form_security_validate( 'candidate_update' );

	$f_candidate_id = gpc_get_int( 'candidate_id' );
	$f_update_mode = gpc_get_bool( 'update_mode', FALSE ); # set if called from generic update page
	$f_new_status	= gpc_get_int( 'status', candidate_get_field( $f_candidate_id, 'status' ) );

	$t_candidate_data = candidate_get( $f_candidate_id, true );
	if( $t_candidate_data->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate_data->project_id;
	}

	if ( ! (
				( access_has_candidate_level( access_get_status_threshold( $f_new_status, candidate_get_field( $f_candidate_id, 'project_id' ) ), $f_candidate_id ) ) ||
				( access_has_candidate_level( config_get( 'update_candidate_threshold' ) , $f_candidate_id ) ) ||
				( ( candidate_get_field( $f_candidate_id, 'reporter_id' ) == auth_get_current_user_id() ) &&
						( ( ON == config_get( 'allow_reporter_reopen' ) ) ||
								( ON == config_get( 'allow_reporter_close' ) ) ) )
			) ) {
		access_denied();
	}

	# extract current extended information
	$t_old_candidate_data = $t_candidate_data; 
	$t_old_candidate_status = $t_candidate_data->status;
	$t_old_first_intr = $t_candidate_data->handler_id;
	$t_old_second_intr = $t_candidate_data->target_version;
	$t_candidate_data->reporter_id		= gpc_get_int( 'reporter_id', $t_candidate_data->reporter_id );
	$t_candidate_data->handler_id			= gpc_get_int( 'handler_id', $t_candidate_data->handler_id );
	$t_candidate_data->duplicate_id		= gpc_get_int( 'duplicate_id', $t_candidate_data->duplicate_id );
	$t_candidate_data->priority			= gpc_get_int( 'priority', $t_candidate_data->priority );
	$t_candidate_data->severity			= gpc_get_int( 'severity', $t_candidate_data->severity );
	$t_candidate_data->reproducibility		= gpc_get_int( 'reproducibility', $t_candidate_data->reproducibility );
	$t_candidate_data->status			= gpc_get_int( 'status', $t_candidate_data->status );
	$t_candidate_data->resolution			= gpc_get_int( 'resolution', $t_candidate_data->resolution );
	$t_candidate_data->projection			= gpc_get_int( 'projection', $t_candidate_data->projection );
	$t_candidate_data->category			= gpc_get_string( 'category', $t_candidate_data->category );
	$t_candidate_data->eta			= gpc_get_int( 'eta', $t_candidate_data->eta );
	$t_candidate_data->os				= gpc_get_string( 'os', $t_candidate_data->os );
	$t_candidate_data->os_build			= gpc_get_string( 'os_build', $t_candidate_data->os_build );
	$t_candidate_data->platform			= gpc_get_string( 'platform', $t_candidate_data->platform );
	$t_candidate_data->version			= gpc_get_string( 'version', $t_candidate_data->version );
	$t_candidate_data->build			= gpc_get_string( 'build', $t_candidate_data->build );
	$t_candidate_data->fixed_in_version		= gpc_get_string( 'fixed_in_version', $t_candidate_data->fixed_in_version );
	$t_candidate_data->target_version		= gpc_get_string( 'target_version', $t_candidate_data->target_version );
	$t_candidate_data->view_state			= gpc_get_int( 'view_state', $t_candidate_data->view_state );
	$t_candidate_data->summary			= gpc_get_string( 'summary', $t_candidate_data->summary );
	$t_candidate_data->description		= gpc_get_string( 'description', $t_candidate_data->description );
	$t_candidate_data->steps_to_reproduce	= gpc_get_string( 'steps_to_reproduce', $t_candidate_data->steps_to_reproduce );
	$t_candidate_data->additional_information	= gpc_get_string( 'additional_information', $t_candidate_data->additional_information );

	$f_private						= gpc_get_bool( 'private' );
	$f_candidatenote_text					= gpc_get_string( 'candidatenote_text', '' );
	$f_time_tracking			= gpc_get_string( 'time_tracking', '0:00' );
	$f_close_now					= gpc_get_string( 'close_now', false );

	#
	# Ensure the Final Result field is populated with a value other than OPEN
	#
	if ($t_candidate_data->resolution == OPEN && $t_candidate_data->status == CLOSED) {
		trigger_error( ERROR_FINAL_RESULT_OPEN, ERROR );
	}
	#
	# Handle auto-assigning 
	#
	if ( ( NEW_ == $t_candidate_data->status )
	  && ( 0 != $t_candidate_data->handler_id )
	  && ( ON == config_get( 'auto_set_status_to_assigned' ) ) ) {
		$t_candidate_data->status = config_get( 'candidate_assigned_status' );
	}	
	
	$first_date_changed  = custom_field_has_changed('1st Interview Date', $f_candidate_id, $_POST);
	$first_time_changed  = custom_field_has_changed('1st Interview Time', $f_candidate_id, $_POST);
	$second_date_changed = custom_field_has_changed('2nd Interview Date', $f_candidate_id, $_POST);
	$second_time_changed = custom_field_has_changed('2nd Interview Time', $f_candidate_id, $_POST);
	$room_changed        = custom_field_has_changed('Interview Room', $f_candidate_id, $_POST);
	$already_sent = false;

	if ((gpc_get('handler_id') == NO_USER) && ($t_candidate_data->status == CONFIRMED)){
		$t_invitation_o = invitation::create(FIRST_INTERVIEW_INVITE, $t_candidate_data->summary, $f_candidate_id, $_POST); 
		$t_invitation_o->send(config_get('interview_organiser_email'));	
		$already_sent=true;	
		log_event(LOG_EMAIL, "Sending mail to organiser");
	}
	if ((gpc_get('handler_id') != NO_USER) && ($t_candidate_data->status == CONFIRMED) && ($t_old_first_intr != $t_candidate_data->handler_id) ){
		$t_invitation_1 = invitation::create(FIRST_INTERVIEW_INVITE, $t_candidate_data->summary, $f_candidate_id, $_POST); 
		$t_invitation_1->send(user_get_email($t_candidate_data->handler_id));		
		$already_sent=true;	
		log_event(LOG_EMAIL, "Sending mail to first interviewer $t_bud_data->handler_id");
	}
	if (gpc_isset('target_version')){
		if ((gpc_get('target_version') != '') && ($t_candidate_data->status == CONFIRMED) && ($t_old_second_intr != $t_candidate_data->target_version) ){
			log_event(LOG_EMAIL, "Sending mail to second interviewer $t_candidate_data->target_version");
			$t_invitation_1 = invitation::create(FIRST_INTERVIEW_INVITE, $t_candidate_data->summary, $f_candidate_id, $_POST); 
			$already_sent = $t_invitation_1->send(user_get_email($t_candidate_data->target_version));		
		}
	}
	if ((gpc_get('handler_id') != NO_USER) && ($t_candidate_data->status == COMPLETED) && ($t_old_first_intr != $t_candidate_data->handler_id) ){
		$t_invitation_2 = invitation::create(SECOND_INTERVIEW_INVITE, $t_candidate_data->summary, $f_candidate_id, $_POST); 
		$t_invitation_2->send('MANAGERS');		
		log_event(LOG_EMAIL, "Sending mail to MANAGERS");
		$already_sent=true;	
	}
	# Changed inviation details whter on while in interview status already.
	# So need to send invitation again
	if ($t_candidate_data->status == CONFIRMED && $t_old_candidate_status == CONFIRMED && $already_sent == false ){
		if($first_date_changed == true || $first_time_changed || $room_changed){
			$update_1 = invitation::create(FIRST_INTERVIEW_INVITE, $t_candidate_data->summary, $f_candidate_id, $_POST, true); 
			$update_1->send(user_get_email($t_candidate_data->handler_id));		
			$update_1->send(second_inter_get_email($t_candidate_data->target_version));		
			$update_1->send(config_get('interview_organiser_email'));		
			log_event(LOG_EMAIL, "Sending invitation UPDATE to interviewers");
		}
	}
	if ($t_candidate_data->status == COMPLETED && $t_old_candidate_status == COMPLETED && $already_sent == false){
		if($second_date_changed == true || $second_time_changed || $room_changed){
			$update_2 = invitation::create(SECOND_INTERVIEW_INVITE, $t_candidate_data->summary, $f_candidate_id, $_POST, true); 
			$update_2->send('MANAGERS');		
			$update_2->send(config_get('interview_organiser_email'));		
			log_event(LOG_EMAIL, "Sending invitation UPDATE to MANAGERS");
		}
	}

	if ( ($t_old_candidate_status != $t_candidate_data->status) && ($t_candidate_data->status == CONFIRMED)  ) {
		email_send_interview_reminders('yes', $t_candidate_data->summary, $f_candidate_id);
	}

	helper_call_custom_function( 'issue_update_validate', array( $f_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
	helper_call_custom_function( 'trigger', array( $f_candidate_id, $t_candidate_data, $t_old_candidate_data, gpc_get('handler_id') ) );

	$t_resolved = config_get( 'candidate_resolved_status_threshold' );

	$t_custom_status_label = "update"; # default info to check
	if ( $t_candidate_data->status == $t_resolved ) {
		$t_custom_status_label = "resolved";
	}
	if ( $t_candidate_data->status == CLOSED ) {
		$t_custom_status_label = "closed";
	}

	$t_related_custom_field_ids = custom_field_get_linked_ids( $t_candidate_data->project_id );
	
	foreach( $t_related_custom_field_ids as $t_id ) {
		$t_def = custom_field_get_definition( $t_id );

		 #Only update the field if it would have been display for editing MANOS COMMENTED OUT
		
		if( !( ( ! $f_update_mode && $t_def['require_' . $t_custom_status_label] ) ||
						( ! $f_update_mode && $t_def['display_' . $t_custom_status_label] && in_array( $t_custom_status_label, array( "resolved", "closed" ) ) ) ||
					( $f_update_mode && $t_def['display_update'] ) ||
						( $f_update_mode && $t_def['require_update'] ) ) ) {
			continue;
		}

		# Do not set custom field value if user has no write access.
		if( !custom_field_has_write_access( $t_id, $f_candidate_id ) ) {
			continue;
		}

		if ( $t_def['require_' . $t_custom_status_label] && !gpc_isset_custom_field( $t_id, $t_def['type'] ) ) {
			error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}

		# Only update the field if it is posted, 
		#	or if it is empty, and the current value isn't the default 
		if ( !gpc_isset_custom_field( $t_id, $t_def['type'] ) && 
			( custom_field_get_value( $t_id, $f_candidate_id ) == $t_def['default_value'] ) ) {
			continue;
		}
		if ( !gpc_isset_custom_field( $t_id, $t_def['type'] )) { 
			continue;
		}
		#
		# Manos Patched a problem observed with IE. Do not allow someon to empty the fields in case they are posted with empty value. 
		#
                if (gpc_get_custom_field( "custom_field_$t_id", $t_def['type'], null ) == '' ) {
	   		continue;	
		}	
		if ( !custom_field_set_value( $t_id, $f_candidate_id, gpc_get_custom_field( "custom_field_$t_id", $t_def['type'], null ) ) ) {
			error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
			trigger_error( ERROR_CUSTOM_FIELD_INVALID_VALUE, ERROR );
		}
	}
	        #die("hello");	

	$t_notify = true;
	$t_candidate_note_set = false;
	if ( ( $t_old_candidate_status != $t_candidate_data->status ) && ( FALSE == $f_update_mode ) ) {
		# handle status transitions that come from pages other than candidate_*update_page.php
		# this does the minimum to act on the candidate and sends a specific message
		switch ( $t_candidate_data->status ) {
			case $t_resolved:
				# candidate_resolve updates the status, fixed_in_version, resolution, handler_id and candidatenote and sends message
				candidate_resolve( $f_candidate_id, $t_candidate_data->resolution, $t_candidate_data->fixed_in_version,
						$f_candidatenote_text, $t_candidate_data->duplicate_id, $t_candidate_data->handler_id,
						$f_private, $f_time_tracking );
				$t_notify = false;
				$t_candidate_note_set = true;

				if ( $f_close_now ) {
					candidate_set_field( $f_candidate_id, 'status', CLOSED );
				}

				// update candidate data with fields that may be updated inside candidate_resolve(), otherwise changes will be overwritten
				// in candidate_update() call below.
				$t_candidate_data->handler_id = candidate_get_field( $f_candidate_id, 'handler_id' );
				$t_candidate_data->status = candidate_get_field( $f_candidate_id, 'status' );
				break;

			case CLOSED:
				# candidate_close updates the status and candidatenote and sends message
				#
				candidate_close( $f_candidate_id, $f_candidatenote_text, $f_private, $f_time_tracking );
				$t_notify = false;
				$t_candidate_note_set = true;
				break;

			case config_get( 'candidate_reopen_status' ):
				if ( $t_old_candidate_status >= $t_resolved ) {
					candidate_set_field( $f_candidate_id, 'handler_id', $t_candidate_data->handler_id ); # fix: update handler_id before calling candidate_reopen
					# candidate_reopen updates the status and candidatenote and sends message
					candidate_reopen( $f_candidate_id, $f_candidatenote_text, $f_time_tracking, $f_private );
					$t_notify = false;
					$t_candidate_note_set = true;

					// update candidate data with fields that may be updated inside candidate_resolve(), otherwise changes will be overwritten
					// in candidate_update() call below.
					$t_candidate_data->status = candidate_get_field( $f_candidate_id, 'status' );
					$t_candidate_data->resolution = candidate_get_field( $f_candidate_id, 'resolution' );
					break;
				} # else fall through to default
		}
	}

	# Add a candidatenote if there is one
	if ( !$t_candidate_note_set ) {
		candidatenote_add( $f_candidate_id, $f_candidatenote_text, $f_time_tracking, $f_private, 0, '', NULL, FALSE );
	}

	# Update the candidate entry, notify if we haven't done so already
	candidate_update( $f_candidate_id, $t_candidate_data, true, ( false == $t_notify ) );

	form_security_purge( 'candidate_update' );

	helper_call_custom_function( 'issue_update_notify', array( $f_candidate_id ) );
	print_successful_redirect_to_candidate( $f_candidate_id );
?>
