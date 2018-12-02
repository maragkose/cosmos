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
	# $Id: candidate_relationship_delete.php,v 1.10.14.1 2007-10-13 22:32:46 giallu Exp $
	# --------------------------------------------------------

	# ======================================================================
	# Author: Marcello Scata' <marcelloscata at users.sourceforge.net> ITALY
	# ======================================================================
	# To delete a relationship we need to ensure that:
	# - User not anomymous
	# - Source candidate exists and is not in read-only state (peer candidate could not exist...)
	# - User that update the source candidate and at least view the destination candidate
	# - Relationship must exist
	# ----------------------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path . 'relationship_api.php' );

	# helper_ensure_post();

	$f_rel_id = gpc_get_int( 'rel_id' );
	$f_candidate_id = gpc_get_int( 'candidate_id' );

	# user has access to update the candidate...
	access_ensure_candidate_level( config_get( 'update_candidate_threshold' ), $f_candidate_id );

	# candidate is not read-only...
	if ( candidate_is_readonly( $f_candidate_id ) ) {
		error_parameters( $f_candidate_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	$t_candidate = candidate_get( $f_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	# retrieve the destination candidate of the relationship
	$t_dest_candidate_id = relationship_get_linked_candidate_id( $f_rel_id, $f_candidate_id );

	# user can access to the related candidate at least as viewer, if it's exist...
	if ( candidate_exists( $t_dest_candidate_id )) {
		if ( !access_has_candidate_level( VIEWER, $t_dest_candidate_id ) ) {
			error_parameters( $t_dest_candidate_id );
			trigger_error( ERROR_RELATIONSHIP_ACCESS_LEVEL_TO_DEST_BUG_TOO_LOW, ERROR );
		}
	}

	helper_ensure_confirmed( lang_get( 'delete_relationship_sure_msg' ), lang_get( 'delete_relationship_button' ) );

	$t_candidate_relationship_data = relationship_get( $f_rel_id );
	$t_rel_type = $t_candidate_relationship_data->type;

	# delete relationship from the DB
	relationship_delete( $f_rel_id );

	# update candidate last updated (just for the src candidate)
	candidate_update_date( $f_candidate_id );

	# set the rel_type for both candidate and dest_candidate based on $t_rel_type and on who is the dest candidate
	if ($f_candidate_id == $t_candidate_relationship_data->src_candidate_id) {
		$t_candidate_rel_type = $t_rel_type;
		$t_dest_candidate_rel_type = relationship_get_complementary_type( $t_rel_type );
	}
	else {
		$t_candidate_rel_type = relationship_get_complementary_type( $t_rel_type );
		$t_dest_candidate_rel_type = $t_rel_type;
	}

	# send email and update the history for the src issue
	history_log_event_special( $f_candidate_id, BUG_DEL_RELATIONSHIP, $t_candidate_rel_type, $t_dest_candidate_id );
	email_relationship_deleted( $f_candidate_id, $t_dest_candidate_id, $t_candidate_rel_type );

	if ( candidate_exists( $t_dest_candidate_id )) {
		# send email and update the history for the dest issue
		history_log_event_special( $t_dest_candidate_id, BUG_DEL_RELATIONSHIP, $t_dest_candidate_rel_type, $f_candidate_id );
		email_relationship_deleted( $t_dest_candidate_id, $f_candidate_id, $t_dest_candidate_rel_type );
	}

	print_header_redirect_view( $f_candidate_id );
?>
