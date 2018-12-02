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
	# $Id: candidate_assign_reporter.php,v 1.4.14.1 2007-10-13 22:32:35 giallu Exp $
	# --------------------------------------------------------

	# Assign candidate to user then redirect to viewing page

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );

	# helper_ensure_post();

	$f_candidate_id = gpc_get_int( 'candidate_id' );

	access_ensure_candidate_level( config_get( 'update_candidate_threshold' ), $f_candidate_id );

	$t_candidate = candidate_get( $f_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	candidate_assign( $f_candidate_id, candidate_get_field( $f_candidate_id, 'reporter_id') );

	print_successful_redirect_to_candidate( $f_candidate_id );
?>
