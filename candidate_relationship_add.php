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
	# $Id: candidate_relationship_add.php,v 1.7.4.1 2007-10-13 22:32:44 giallu Exp $
	# --------------------------------------------------------

	# ======================================================================
	# Author: Marcello Scata' <marcelloscata at users.sourceforge.net> ITALY
	# ======================================================================

	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path . 'relationship_api.php' );

	# helper_ensure_post();

	$f_rel_type = gpc_get_int( 'rel_type' );
	$f_src_candidate_id = gpc_get_int( 'src_candidate_id' );
	$f_dest_candidate_id_string = gpc_get_string( 'dest_candidate_id' );

	# user has access to update the candidate...
	access_ensure_candidate_level( config_get( 'update_candidate_threshold' ), $f_src_candidate_id );

	$f_dest_candidate_id_string = str_replace( ',', '|', $f_dest_candidate_id_string );

	$f_dest_candidate_id_array = explode( '|', $f_dest_candidate_id_string );

	foreach( $f_dest_candidate_id_array as $f_dest_candidate_id ) {
		$f_dest_candidate_id = (int)$f_dest_candidate_id;

		# source and destination candidates are the same candidate...
		if ( $f_src_candidate_id == $f_dest_candidate_id ) {
			trigger_error( ERROR_RELATIONSHIP_SAME_BUG, ERROR );
		}

		# the related candidate exists...
		candidate_ensure_exists( $f_dest_candidate_id );

		# candidate is not read-only...
		if ( candidate_is_readonly( $f_src_candidate_id ) ) {
			error_parameters( $f_src_candidate_id );
			trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
		}

		# user can access to the related candidate at least as viewer...
		if ( !access_has_candidate_level( VIEWER, $f_dest_candidate_id ) ) {
			error_parameters( $f_dest_candidate_id );
			trigger_error( ERROR_RELATIONSHIP_ACCESS_LEVEL_TO_DEST_BUG_TOO_LOW, ERROR );
		}

		$t_candidate = candidate_get( $f_src_candidate_id, true );
		if( $t_candidate->project_id != helper_get_current_project() ) {
			# in case the current project is not the same project of the candidate we are viewing...
			# ... override the current project. This to avoid problems with categories and handlers lists etc.
			$g_project_override = $t_candidate->project_id;
		}

		# check if there is other relationship between the candidates...
		$t_old_id_relationship = relationship_same_type_exists( $f_src_candidate_id, $f_dest_candidate_id, $f_rel_type );

		if ( $t_old_id_relationship == -1 ) {
			# the relationship type is exactly the same of the new one. No sense to proceed
			trigger_error( ERROR_RELATIONSHIP_ALREADY_EXISTS, ERROR );
		}
		else if ( $t_old_id_relationship > 0 ) {
			# there is already a relationship between them -> we have to update it and not to add a new one
			helper_ensure_confirmed( lang_get( 'replace_relationship_sure_msg' ), lang_get( 'replace_relationship_button' ) );

			# Update the relationship
			relationship_update( $t_old_id_relationship, $f_src_candidate_id, $f_dest_candidate_id, $f_rel_type );

			# Add log line to the history (both candidates)
			history_log_event_special( $f_src_candidate_id, BUG_REPLACE_RELATIONSHIP, $f_rel_type, $f_dest_candidate_id );
			history_log_event_special( $f_dest_candidate_id, BUG_REPLACE_RELATIONSHIP, relationship_get_complementary_type( $f_rel_type ), $f_src_candidate_id );
		}
		else {
			# Add the new relationship
			relationship_add( $f_src_candidate_id, $f_dest_candidate_id, $f_rel_type );

			# Add log line to the history (both candidates)
			history_log_event_special( $f_src_candidate_id, BUG_ADD_RELATIONSHIP, $f_rel_type, $f_dest_candidate_id );
			history_log_event_special( $f_dest_candidate_id, BUG_ADD_RELATIONSHIP, relationship_get_complementary_type( $f_rel_type ), $f_src_candidate_id );
		}

		# update candidate last updated (just for the src candidate)
		candidate_update_date( $f_src_candidate_id );

		# send email notification to the users addressed by both the candidates
		email_relationship_added( $f_src_candidate_id, $f_dest_candidate_id, $f_rel_type );
		email_relationship_added( $f_dest_candidate_id, $f_src_candidate_id, relationship_get_complementary_type( $f_rel_type ) );
	}

	print_header_redirect_view( $f_src_candidate_id );
?>
