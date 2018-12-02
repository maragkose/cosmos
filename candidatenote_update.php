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
	# $Id: candidatenote_update.php,v 1.44.2.1 2007-10-13 22:33:09 giallu Exp $
	# --------------------------------------------------------

	# Update candidatenote data then redirect to the appropriate viewing page

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'candidatenote_api.php' );
	require_once( $t_core_path.'current_user_api.php' );

	# helper_ensure_post();

	$f_candidatenote_id	 = gpc_get_int( 'candidatenote_id' );
	$f_candidatenote_text	 = gpc_get_string( 'candidatenote_text', '' );
	$f_time_tracking = gpc_get_string( 'time_tracking', '0:00' );

	# Check if the current user is allowed to edit the candidatenote
	$t_user_id = auth_get_current_user_id();
	$t_reporter_id = candidatenote_get_field( $f_candidatenote_id, 'reporter_id' );

	if ( ( $t_user_id != $t_reporter_id ) || ( OFF == config_get( 'candidatenote_allow_user_edit_delete' ) )) {
		access_ensure_candidatenote_level( config_get( 'update_candidatenote_threshold' ), $f_candidatenote_id );
	}

	# Check if the candidate is readonly
	$t_candidate_id = candidatenote_get_field( $f_candidatenote_id, 'candidate_id' );
	if ( candidate_is_readonly( $t_candidate_id ) ) {
		error_parameters( $t_candidate_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	$f_candidatenote_text = trim( $f_candidatenote_text ) . "\n\n";

	candidatenote_set_text( $f_candidatenote_id, $f_candidatenote_text );
	candidatenote_set_time_tracking( $f_candidatenote_id, $f_time_tracking );

	print_successful_redirect( string_get_candidate_view_url( $t_candidate_id ) . '#candidatenotes' );
?>
