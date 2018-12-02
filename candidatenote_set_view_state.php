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
	# $Id: candidatenote_set_view_state.php,v 1.27.14.1 2007-10-13 22:33:08 giallu Exp $
	# --------------------------------------------------------

	# Set an existing candidatenote private or public.

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'candidatenote_api.php' );

	# helper_ensure_post();

	$f_candidatenote_id	= gpc_get_int( 'candidatenote_id' );
	$f_private		= gpc_get_bool( 'private' );

	$t_candidate_id = candidatenote_get_field( $f_candidatenote_id, 'candidate_id' );

	$t_candidate = candidate_get( $t_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	access_ensure_candidatenote_level( config_get( 'update_candidatenote_threshold' ), $f_candidatenote_id );

	# Check if the candidate is readonly
	$t_candidate_id = candidatenote_get_field( $f_candidatenote_id, 'candidate_id' );
	if ( candidate_is_readonly( $t_candidate_id ) ) {
		error_parameters( $t_candidate_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	candidatenote_set_view_state( $f_candidatenote_id, $f_private );

	print_successful_redirect( string_get_candidate_view_url( $t_candidate_id ) . '#candidatenotes' );
?>
