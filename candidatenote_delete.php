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
	# $Id: candidatenote_delete.php,v 1.39.14.1 2007-10-13 22:33:06 giallu Exp $
	# --------------------------------------------------------

	# Remove the candidatenote and candidatenote text and redirect back to
	# the viewing page

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'candidatenote_api.php' );
	require_once( $t_core_path.'current_user_api.php' );

	# helper_ensure_post();

	$f_candidatenote_id = gpc_get_int( 'candidatenote_id' );

	$t_candidate_id = candidatenote_get_field( $f_candidatenote_id, 'candidate_id' );

	$t_candidate = candidate_get( $t_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	# Check if the current user is allowed to delete the candidatenote
	$t_user_id = auth_get_current_user_id();
	$t_reporter_id = candidatenote_get_field( $f_candidatenote_id, 'reporter_id' );

	if ( ( $t_user_id != $t_reporter_id ) || ( OFF == config_get( 'candidatenote_allow_user_edit_delete' ) ) ) {
		access_ensure_candidatenote_level( config_get( 'delete_candidatenote_threshold' ), $f_candidatenote_id );
	}

	helper_ensure_confirmed( lang_get( 'delete_candidatenote_sure_msg' ),
							 lang_get( 'delete_candidatenote_button' ) );

	candidatenote_delete( $f_candidatenote_id );

	print_successful_redirect( string_get_candidate_view_url( $t_candidate_id ) . '#candidatenotes' );
?>
