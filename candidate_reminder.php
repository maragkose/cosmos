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
	# $Id: candidate_reminder.php,v 1.21.2.1 2007-10-13 22:32:49 giallu Exp $
	# --------------------------------------------------------

	# This page allows an authorized user to send a reminder by email to another user

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'email_api.php' );
	require_once( $t_core_path.'candidatenote_api.php' );

	# helper_ensure_post();

	$f_candidate_id		= gpc_get_int( 'candidate_id' );
	$f_to			= gpc_get_int_array( 'to' );
	$f_body			= gpc_get_string( 'body' );

	if ( candidate_is_readonly( $f_candidate_id ) ) {
		error_parameters( $f_candidate_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	access_ensure_candidate_level( config_get( 'candidate_reminder_threshold' ), $f_candidate_id );

	$t_candidate = candidate_get( $f_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	# Automically add recipients to monitor list if they are above the monitor
	# threshold, option is enabled, and not reporter or handler.
	foreach ( $f_to as $t_recipient )
	{
		if ( ON == config_get( 'reminder_recipents_monitor_candidate' ) &&
			access_has_candidate_level( config_get( 'monitor_candidate_threshold' ), $f_candidate_id ) &&
			!candidate_is_user_handler( $f_candidate_id, $t_recipient ) &&
			!candidate_is_user_reporter( $f_candidate_id, $t_recipient ) ) {
			candidate_monitor( $f_candidate_id, $t_recipient );
		}
	}

	$result = email_candidate_reminder( $f_to, $f_candidate_id, $f_body );

	# Add reminder as candidatenote if store reminders option is ON.
	if ( ON == config_get( 'store_reminders' ) ) {
		if ( count( $f_to ) > 50 ) {		# too many recipients to log, truncate the list
			$t_to = array();
			for ( $i=0; $i<50; $i++ ) {
				$t_to[] = $f_to[$i];
			}
			$f_to = $t_to;
		}
		$t_attr = '|' . implode( '|', $f_to ) . '|';
		candidatenote_add( $f_candidate_id, $f_body, 0, config_get( 'default_reminder_view_status' ) == VS_PRIVATE, REMINDER, $t_attr, NULL, FALSE );
	}

	html_page_top1();
	html_meta_redirect( string_get_candidate_view_url( $f_candidate_id ) );
	html_page_top2();
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ).'<br />';
	print_bracket_link( string_get_candidate_view_url( $f_candidate_id ), lang_get( 'proceed' ) );
?>
</div>
<?php html_page_bottom1( __FILE__ ) ?>
