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
	# $Id: candidate_actiongroup.php,v 1.52.2.1 2007-10-13 22:32:30 giallu Exp $
	# --------------------------------------------------------

	# This page allows actions to be performed an an array of candidates

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );

	auth_ensure_user_authenticated();
	helper_begin_long_process();

	$f_action	= gpc_get_string( 'action' );
	$f_custom_field_id = gpc_get_int( 'custom_field_id', 0 );
	$f_candidate_arr	= gpc_get_int_array( 'candidate_arr', array() );

	$t_form_name = 'candidate_actiongroup_' . $f_action;
	form_security_validate( $t_form_name );

	$t_custom_group_actions = config_get( 'custom_group_actions' );

	foreach( $t_custom_group_actions as $t_custom_group_action ) {
		if ( $f_action == $t_custom_group_action['action'] ) {
			require_once( $t_custom_group_action['action_page'] );
			exit;
		}
	}

	$t_failed_ids = array();

	if ( 0 != $f_custom_field_id ) {
		$t_custom_field_def = custom_field_get_definition( $f_custom_field_id );
	}

	foreach( $f_candidate_arr as $t_candidate_id ) {
		candidate_ensure_exists( $t_candidate_id );
		$t_candidate = candidate_get( $t_candidate_id, true );

		if( $t_candidate->project_id != helper_get_current_project() ) {
			# in case the current project is not the same project of the candidate we are viewing...
			# ... override the current project. This to avoid problems with categories and handlers lists etc.
			$g_project_override = $t_candidate->project_id;
			# @@@ (thraxisp) the next line goes away if the cache was smarter and used project
			config_flush_cache(); # flush the config cache so that configs are refetched
		}

		$t_status = $t_candidate->status;

		switch ( $f_action ) {

		case 'CLOSE':
			if ( access_can_close_candidate( $t_candidate_id ) &&
					( $t_status < CLOSED ) &&
					candidate_check_workflow($t_status, CLOSED) &&
			       		$t_candidate->resolution != OPEN ) {

				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $f_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
				candidate_close( $t_candidate_id );
				helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			} else {
				if ( ! access_can_close_candidate( $t_candidate_id ) ) {
						$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
				} else {
					if($t_candidate->resolution == OPEN){
						$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_final_result' );
					} else {
						$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_status' );
					}
				}
			}
			break;

		case 'DELETE':
			if ( access_has_candidate_level( config_get( 'delete_candidate_threshold' ), $t_candidate_id ) ) {
				candidate_delete( $t_candidate_id );
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'MOVE':
			if ( access_has_candidate_level( config_get( 'move_candidate_threshold' ), $t_candidate_id ) ) {
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
				$f_project_id = gpc_get_int( 'project_id' );
				candidate_set_field( $t_candidate_id, 'project_id', $f_project_id );
				helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'COPY':
			$f_project_id = gpc_get_int( 'project_id' );

			if ( access_has_project_level( config_get( 'report_candidate_threshold' ), $f_project_id ) ) {
				candidate_copy( $t_candidate_id, $f_project_id, true, true, true, true, true, true );
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'ASSIGN':
			$f_assign = gpc_get_int( 'assign' );
			if ( ON == config_get( 'auto_set_status_to_assigned' ) ) {
				$t_assign_status = config_get( 'candidate_assigned_status' );
			} else {
				$t_assign_status = $t_status;
			}
			# check that new handler has rights to handle the issue, and
			#  that current user has rights to assign the issue
			$t_threshold = access_get_status_threshold( $t_assign_status, candidate_get_field( $t_candidate_id, 'project_id' ) );
			if ( access_has_candidate_level( $t_threshold , $t_candidate_id, $f_assign ) &&
				 access_has_candidate_level( config_get( 'update_candidate_assign_threshold', config_get( 'update_candidate_threshold' ) ), $t_candidate_id ) &&
					candidate_check_workflow($t_status, $t_assign_status )	) {
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
				candidate_assign( $t_candidate_id, $f_assign );
				helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			} else {
				if ( candidate_check_workflow($t_status, $t_assign_status ) ) {
					$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
				} else {
					$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_status' );
				}
			}
			break;

		case 'RESOLVE':
			$t_resolved_status = config_get( 'candidate_resolved_status_threshold' );
			if ( access_has_candidate_level( access_get_status_threshold( $t_resolved_status, candidate_get_field( $t_candidate_id, 'project_id' ) ), $t_candidate_id )){ 
				$f_resolution = gpc_get_int( 'resolution' );
				candidate_set_field( $t_candidate_id, 'resolution', $f_resolution );
				#$f_fixed_in_version = gpc_get_string( 'fixed_in_version', '' );
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
				#candidate_resolve( $t_candidate_id, $f_resolution, $f_fixed_in_version );
				helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			} else {
				if ( ( $t_status < $t_resolved_status ) &&
						candidate_check_workflow($t_status, $t_resolved_status ) ) {
					$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
				} else {
					$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_status' );
				}
			}
			break;

		case 'UP_PRIOR':
			if ( access_has_candidate_level( config_get( 'update_candidate_threshold' ), $t_candidate_id ) ) {
				$f_priority = gpc_get_int( 'priority' );
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
				candidate_set_field( $t_candidate_id, 'priority', $f_priority );
				helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'UP_STATUS':
			$f_status = gpc_get_int( 'status' );
			$t_project = candidate_get_field( $t_candidate_id, 'project_id' );
			if ( access_has_candidate_level( access_get_status_threshold( $f_status, $t_project ), $t_candidate_id ) ) {
				if ( TRUE == candidate_check_workflow($t_status, $f_status ) ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
					candidate_set_field( $t_candidate_id, 'status', $f_status );
					helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
				} else {
					$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_status' );
				}
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'UP_CATEGORY':
			$f_category = gpc_get_string( 'category' );
			$t_project = candidate_get_field( $t_candidate_id, 'project_id' );

			if ( access_has_candidate_level( config_get( 'update_candidate_threshold' ), $t_candidate_id ) ) {
				if ( category_exists( $t_project, $f_category ) ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
					candidate_set_field( $t_candidate_id, 'category', $f_category );
					helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
				} else {
					$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_category' );
				}
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;
		
		case 'UP_FIXED_IN_VERSION':
			$f_fixed_in_version = gpc_get_string( 'fixed_in_version' );
			$t_project_id = candidate_get_field( $t_candidate_id, 'project_id' );
			$t_success = false;

			if ( access_has_candidate_level( config_get( 'update_candidate_threshold' ), $t_candidate_id ) ) {
				if ( version_get_id( $f_fixed_in_version, $t_project_id ) !== false ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
					candidate_set_field( $t_candidate_id, 'fixed_in_version', $f_fixed_in_version );
					helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
					$t_success = true;
				}
			}

			if ( !$t_success ) {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'UP_TARGET_VERSION':
			$f_target_version = gpc_get_string( 'target_version' );
			$t_project_id = candidate_get_field( $t_candidate_id, 'project_id' );
			$t_success = false;

			if ( access_has_candidate_level( config_get( 'roadmap_update_threshold' ), $t_candidate_id ) ) {
				if ( version_get_id( $f_target_version, $t_project_id ) !== false ) {
					# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
					candidate_set_field( $t_candidate_id, 'target_version', $f_target_version );
					helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
					$t_success = true;
				}
			}

			if ( !$t_success ) {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'VIEW_STATUS':
			if ( access_has_candidate_level( config_get( 'change_view_status_threshold' ), $t_candidate_id ) ) {
				$f_view_status = gpc_get_int( 'view_status' );
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
				candidate_set_field( $t_candidate_id, 'view_state', $f_view_status );
				helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'SET_STICKY':
			if ( access_has_candidate_level( config_get( 'set_candidate_sticky_threshold' ), $t_candidate_id ) ) {
				$f_sticky = candidate_get_field( $t_candidate_id, 'sticky' );
				// The new value is the inverted old value
				# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
				candidate_set_field( $t_candidate_id, 'sticky', intval( !$f_sticky ) );
				helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			} else {
				$t_failed_ids[$t_candidate_id] = lang_get( 'candidate_actiongroup_access' );
			}
			break;

		case 'CUSTOM':
			if ( 0 === $f_custom_field_id ) {
				trigger_error( ERROR_GENERIC, ERROR );
			}

			# @@@ we need to issue a helper_call_custom_function( 'issue_update_validate', array( $t_candidate_id, $t_candidate_data, $f_candidatenote_text ) );
			$t_form_var = "custom_field_$f_custom_field_id";
			$t_custom_field_value = gpc_get_custom_field( $t_form_var, $t_custom_field_def['type'], null );
			custom_field_set_value( $f_custom_field_id, $t_candidate_id, $t_custom_field_value );
			helper_call_custom_function( 'issue_update_notify', array( $t_candidate_id ) );
			break;

		default:
			trigger_error( ERROR_GENERIC, ERROR );
		}
	}

	form_security_purge( $t_form_name );

	$t_redirect_url = 'view_all_candidate_page.php';

	if ( count( $t_failed_ids ) > 0 ) {
		html_page_top1();
		html_page_top2();

		echo '<div align="center"><br />';
		echo '<table class="width75">';
		foreach( $t_failed_ids as $t_id => $t_reason ) {
			printf( "<tr><td width=\"50%%\">%s: %s</td><td>%s</td></tr>\n", string_get_candidate_view_link( $t_id ), candidate_get_field( $t_id, 'summary' ), $t_reason );
		}
		echo '</table><br />';
		print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
		echo '</div>';

		html_page_bottom1( __FILE__ );
	} else {
		print_header_redirect( $t_redirect_url );
	}
?>
