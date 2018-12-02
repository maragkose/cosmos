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
	# $Id: candidate_file_delete.php,v 1.30.14.1 2007-10-13 22:32:38 giallu Exp $
	# --------------------------------------------------------

	# Delete a file from a candidate and then view the candidate

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'file_api.php' );

	# helper_ensure_post();

	$f_file_id = gpc_get_int( 'file_id' );

	$t_candidate_id = file_get_field( $f_file_id, 'candidate_id' );

	access_ensure_candidate_level( config_get( 'update_candidate_threshold' ), $t_candidate_id );

	$t_candidate = candidate_get( $t_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	helper_ensure_confirmed( lang_get( 'delete_attachment_sure_msg' ), lang_get( 'delete_attachment_button' ) );

	file_delete( $f_file_id, 'candidate' );

	print_header_redirect_view( $t_candidate_id );
?>
