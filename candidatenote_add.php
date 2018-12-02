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
	# $Id: candidatenote_add.php,v 1.48.2.1 2007-10-13 22:33:04 giallu Exp $
	# --------------------------------------------------------

	# Insert the candidatenote into the database then redirect to the candidate page

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'candidatenote_api.php' );

	# helper_ensure_post();

	$f_candidate_id		= gpc_get_int( 'candidate_id' );
	$f_private		= gpc_get_bool( 'private' );
	$f_time_tracking	= gpc_get_string( 'time_tracking', '0:00' );
	$f_candidatenote_text	= trim( gpc_get_string( 'candidatenote_text', '' ) );

	if ( candidate_is_readonly( $f_candidate_id ) ) {
		error_parameters( $f_candidate_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	access_ensure_candidate_level( config_get( 'add_candidatenote_threshold' ), $f_candidate_id );

	$t_candidate = candidate_get( $f_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	// We always set the note time to BUGNOTE, and the API will overwrite it with TIME_TRACKING
	// if $f_time_tracking is not 0 and the time tracking feature is enabled.
	$t_candidatenote_added = candidatenote_add( $f_candidate_id, $f_candidatenote_text, $f_time_tracking, $f_private, BUGNOTE );
	if ( !$t_candidatenote_added ) {
		error_parameters( lang_get( 'candidatenote' ) );
		trigger_error( ERROR_EMPTY_FIELD, ERROR );
	}

	print_successful_redirect_to_candidate( $f_candidate_id );
?>
