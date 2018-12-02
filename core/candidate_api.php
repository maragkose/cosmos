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
	# $Id: candidate_api.php,v 1.111.2.1 2007-10-13 22:35:13 giallu Exp $
	# --------------------------------------------------------

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'history_api.php' );
	require_once( $t_core_dir . 'email_api.php' );
	require_once( $t_core_dir . 'candidatenote_api.php' );
	require_once( $t_core_dir . 'file_api.php' );
	require_once( $t_core_dir . 'string_api.php' );
	require_once( $t_core_dir . 'sponsorship_api.php' );
	require_once( $t_core_dir . 'twitter_api.php' );
	require_once( $t_core_dir . 'tag_api.php' );

	# MASC RELATIONSHIP
	require_once( $t_core_dir.'relationship_api.php' );
	# MASC RELATIONSHIP

	### Bug API ###

	#===================================
	# Bug Data Structure Definition
	#===================================
	class BugData {
		var $project_id = null;
		var $reporter_id = 0;
		var $handler_id = 0;
		var $duplicate_id = 0;
		var $priority = NORMAL;
		var $severity = MINOR;
		var $reproducibility = 10;
		var $status = NEW_;
		var $resolution = OPEN;
		var $projection = 10;
		var $category = '';
		var $date_submitted = '';
		var $last_updated = '';
		var $eta = '';
		var $os = '';
		var $os_build = '';
		var $platform = '';
		var $version = '';
		var $fixed_in_version = '';
		var $target_version = '';
		var $build = '';
		var $view_state = VS_PUBLIC;
		var $summary = '';
		var $sponsorship_total = 0;
		var $sticky = 0;

		# omitted:
		# var $candidate_text_id
		var $profile_id;

		# extended info
		var $description = '';
		var $steps_to_reproduce = '';
		var $additional_information = '';
		
		#internal helper objects
		var $_stats = null;
	}

	#===================================
	# Caching
	#===================================

	#########################################
	# SECURITY NOTE: cache globals are initialized here to prevent them
	#   being spoofed if register_globals is turned on

	$g_cache_candidate = array();
	$g_cache_candidate_text = array();

	# --------------------
	# Cache an object as a candidate.
	function candidate_cache_database_result( $p_candidate_datebase_result, $p_stats = null ) {
		global $g_cache_candidate;
		
		if ( isset( $g_cache_candidate[ $p_candidate_datebase_result['id'] ] ) ) {
			return $g_cache_candidate[ $p_candidate_datebase_result['id'] ];
		}	
		
		if( !is_int( $p_candidate_datebase_result['date_submitted'] ) )
			$p_candidate_datebase_result['date_submitted']	= db_unixtimestamp( $p_candidate_datebase_result['date_submitted']['date_submitted'] );
		if( !is_int( $p_candidate_datebase_result['last_updated'] ) )
			$p_candidate_datebase_result['last_updated']	= db_unixtimestamp( $p_candidate_datebase_result['last_updated'] );
		$g_cache_candidate[ $p_candidate_datebase_result['id'] ] = $p_candidate_datebase_result;
		if( !is_null( $p_stats ) ) {
			$g_cache_candidate[ $p_candidate_datebase_result['id'] ]['_stats'] = $p_stats;
		}
	}

	# --------------------
	# Cache a candidate row if necessary and return the cached copy
	#  If the second parameter is true (default), trigger an error
	#  if the candidate can't be found.  If the second parameter is
	#  false, return false if the candidate can't be found.
	function candidate_cache_row( $p_candidate_id, $p_trigger_errors=true ) {
		global $g_cache_candidate;

		if ( isset( $g_cache_candidate[$p_candidate_id] ) ) {
			return $g_cache_candidate[$p_candidate_id];
		}

		$c_candidate_id		= db_prepare_int( $p_candidate_id );
		$t_candidate_table	= config_get( 'cosmos_candidate_table' );

		$query = "SELECT *
				  FROM $t_candidate_table
				  WHERE id='$c_candidate_id'";
		$result = db_query( $query );

		if ( 0 == db_num_rows( $result ) ) {
			$g_cache_candidate[$c_candidate_id] = false;

			if ( $p_trigger_errors ) {
				error_parameters( $p_candidate_id );
				trigger_error( ERROR_BUG_NOT_FOUND, ERROR );
			} else {
				return false;
			}
		}

		$row = db_fetch_array( $result );
		$row['date_submitted']	= db_unixtimestamp( $row['date_submitted'] );
		$row['last_updated']	= db_unixtimestamp( $row['last_updated'] );
		$g_cache_candidate[$c_candidate_id] = $row;

		return $row;
	}

	# --------------------
	# Inject a candidate into the candidate cache
	function candidate_add_to_cache( $p_candidate_row ) {
		global $g_cache_candidate;

		if ( !is_array( $p_candidate_row ) )
			return false;

		$c_candidate_id = db_prepare_int( $p_candidate_row['id'] );
		$g_cache_candidate[ $c_candidate_id ] = $p_candidate_row;

		return true;
	}

	# --------------------
	# Clear the candidate cache (or just the given id if specified)
	function candidate_clear_cache( $p_candidate_id = null ) {
		global $g_cache_candidate;

		if ( null === $p_candidate_id ) {
			$g_cache_candidate = array();
		} else {
			$c_candidate_id = db_prepare_int( $p_candidate_id );
			unset( $g_cache_candidate[$c_candidate_id] );
		}

		return true;
	}

	# --------------------
	# Cache a candidate text row if necessary and return the cached copy
	#  If the second parameter is true (default), trigger an error
	#  if the candidate text can't be found.  If the second parameter is
	#  false, return false if the candidate text can't be found.
	function candidate_text_cache_row( $p_candidate_id, $p_trigger_errors=true ) {
		global $g_cache_candidate_text;

		$c_candidate_id			= db_prepare_int( $p_candidate_id );
		$t_candidate_table		= config_get( 'cosmos_candidate_table' );
		$t_candidate_text_table	= config_get( 'cosmos_candidate_text_table' );

		if ( isset ( $g_cache_candidate_text[$c_candidate_id] ) ) {
			return $g_cache_candidate_text[$c_candidate_id];
		}

		$query = "SELECT bt.*
				  FROM $t_candidate_text_table bt, $t_candidate_table b
				  WHERE b.id='$c_candidate_id' AND
				  		b.candidate_text_id = bt.id";
		$result = db_query( $query );

		if ( 0 == db_num_rows( $result ) ) {
			$g_cache_candidate_text[$c_candidate_id] = false;

			if ( $p_trigger_errors ) {
				error_parameters( $p_candidate_id );
				trigger_error( ERROR_BUG_NOT_FOUND, ERROR );
			} else {
				return false;
			}
		}

		$row = db_fetch_array( $result );

		$g_cache_candidate_text[$c_candidate_id] = $row;

		return $row;
	}

	# --------------------
	# Clear the candidate text cache (or just the given id if specified)
	function candidate_text_clear_cache( $p_candidate_id = null ) {
		global $g_cache_candidate_text;

		if ( null === $p_candidate_id ) {
			$g_cache_candidate_text = array();
		} else {
			$c_candidate_id = db_prepare_int( $p_candidate_id );
			unset( $g_cache_candidate_text[$c_candidate_id] );
		}

		return true;
	}

	#===================================
	# Boolean queries and ensures
	#===================================

	# --------------------
	# check to see if candidate exists by id
	# return true if it does, false otherwise
	function candidate_exists( $p_candidate_id ) {
		if ( false == candidate_cache_row( $p_candidate_id, false ) ) {
			return false;
		} else {
			return true;
		}
	}

	# --------------------
	# check to see if candidate exists by id
	# if it doesn't exist then error
	#  otherwise let execution continue undisturbed
	function candidate_ensure_exists( $p_candidate_id ) {
		if ( !candidate_exists( $p_candidate_id ) ) {
			error_parameters( $p_candidate_id );
			trigger_error( ERROR_BUG_NOT_FOUND, ERROR );
		}
	}

	# --------------------
	# check if the given user is the reporter of the candidate
	# return true if the user is the reporter, false otherwise
	function candidate_is_user_reporter( $p_candidate_id, $p_user_id ) {
		if ( candidate_get_field( $p_candidate_id, 'reporter_id' ) == $p_user_id ) {
			return true;
		} else {
			return false;
		}
	}

	# --------------------
	# check if the given user is the handler of the candidate
	# return true if the user is the handler, false otherwise
	function candidate_is_user_handler( $p_candidate_id, $p_user_id ) {
		if ( candidate_get_field( $p_candidate_id, 'handler_id' ) == $p_user_id ) {
			return true;
		} else {
			return false;
		}
	}

	# --------------------
	# Check if the candidate is readonly and shouldn't be modified
	# For a candidate to be readonly the status has to be >= candidate_readonly_status_threshold and
	# current user access level < update_readonly_candidate_threshold.
	function candidate_is_readonly( $p_candidate_id ) {
		$t_status = candidate_get_field( $p_candidate_id, 'status' );
		if ( $t_status < config_get( 'candidate_readonly_status_threshold' ) ) {
			return false;
		}

		if ( access_has_candidate_level( config_get( 'update_readonly_candidate_threshold' ), $p_candidate_id ) ) {
			return false;
		}

		return true;
	}

	# --------------------
	# Check if the candidate is resolved
	function candidate_is_resolved( $p_candidate_id ) {
		$t_status = candidate_get_field( $p_candidate_id, 'status' );
		return ( $t_status >= config_get( 'candidate_resolved_status_threshold' ) );
	}

	# --------------------
	# Validate workflow state to see if candidate can be moved to requested state
	function candidate_check_workflow( $p_candidate_status, $p_wanted_status ) {
		$t_status_enum_workflow = config_get( 'status_enum_workflow' );

		if ( count( $t_status_enum_workflow ) < 1) {
			# workflow not defined, use default enum
			return true;
		} else if ( $p_candidate_status == $p_wanted_status ) {
			# no change in state, allow the transition
			return true;
		} else {
			# workflow defined - find allowed states
			$t_allowed_states = $t_status_enum_workflow[$p_candidate_status];
			$t_arr = explode_enum_string( $t_allowed_states );

			$t_enum_count = count( $t_arr );

			for ( $i = 0; $i < $t_enum_count; $i++ ) {
				# check if wanted status is allowed
				$t_elem  = explode_enum_arr( $t_arr[$i] );
				if ( $p_wanted_status == $t_elem[0] ) {
					return true;
				}
			} # end for
		}

		return false;
	}

	#===================================
	# Creation / Deletion / Updating
	#===================================

	# --------------------
	# Create a new candidate and return the candidate id
	#
	function candidate_create( $p_candidate_data, $p_import=false ) {

		$c_summary				= db_prepare_string( $p_candidate_data->summary );
		$c_description			= db_prepare_string( $p_candidate_data->description );
		$c_project_id			= db_prepare_int( $p_candidate_data->project_id );
		$c_reporter_id			= db_prepare_int( $p_candidate_data->reporter_id );
		$c_handler_id			= db_prepare_int( $p_candidate_data->handler_id );
		$c_priority				= db_prepare_int( $p_candidate_data->priority );
		$c_severity				= db_prepare_int( $p_candidate_data->severity );
		$c_reproducibility		= db_prepare_int( $p_candidate_data->reproducibility );
		$c_category				= db_prepare_string( $p_candidate_data->category );
		$c_os					= db_prepare_string( $p_candidate_data->os );
		$c_os_build				= db_prepare_string( $p_candidate_data->os_build );
		$c_platform				= db_prepare_string( $p_candidate_data->platform );
		$c_version				= db_prepare_string( $p_candidate_data->version );
		$c_build				= db_prepare_string( $p_candidate_data->build );
		$c_profile_id			= db_prepare_int( $p_candidate_data->profile_id );
		$c_view_state			= db_prepare_int( $p_candidate_data->view_state );
		$c_steps_to_reproduce	= db_prepare_string( $p_candidate_data->steps_to_reproduce );
		$c_additional_info		= db_prepare_string( $p_candidate_data->additional_information );
		$c_sponsorship_total 	= 0;
		$c_sticky 				= 0;

		# Summary cannot be blank
		if ( is_blank( $c_summary ) ) {
			error_parameters( lang_get( 'summary' ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}

		# Description cannot be blank
		if ( is_blank( $c_description ) ) {
			error_parameters( lang_get( 'description' ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}

		# If category is not specified and not defaulted + project has categories defined, then error.
		if ( is_blank( $c_category ) && ( category_get_count( $c_project_id ) > 0 ) ) {
			error_parameters( lang_get( 'category' ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}

		# Only set target_version if user has access to do so
		if ( access_has_project_level( config_get( 'roadmap_update_threshold' ) ) ) {
			$c_target_version	= db_prepare_string( $p_candidate_data->target_version );
		} else { 
			$c_target_version	= '';
		}

		$t_candidate_table				= config_get( 'cosmos_candidate_table' );
		$t_candidate_text_table			= config_get( 'cosmos_candidate_text_table' );
		$t_project_category_table	= config_get( 'cosmos_project_category_table' );

		#
		# MANOS: Added check not to allow duplicate name to be inserted in the system.
		# In case we import, do not check. Just skip the import all together, so import will not stop
		#
		$summary_query = "SELECT * from $t_candidate_table WHERE summary = '$c_summary'";
		$db_result = db_query( $summary_query );
		$rows_summary = db_num_rows( $db_result );
		if (( $rows_summary != 0 ) && ($p_import == false)) {
			trigger_error( ERROR_BUG_DUPLICATE_SELF, ERROR );  # never returns
		} 
		if (( $rows_summary != 0 ) && ($p_import == true)) {
			return 0;
		}	
		###################### END MANOS
		# Insert text information
		$query = "INSERT INTO $t_candidate_text_table
				    ( description, steps_to_reproduce, additional_information )
				  VALUES
				    ( '$c_description', '$c_steps_to_reproduce',
				      '$c_additional_info' )";
		db_query( $query );

		# Get the id of the text information we just inserted
		# NOTE: this is guarranteed to be the correct one.
		# The value LAST_INSERT_ID is stored on a per connection basis.

		$t_text_id = db_insert_id($t_candidate_text_table);

		# check to see if we want to assign this right off
		$t_status = config_get( 'candidate_submit_status' );

		# if not assigned, check if it should auto-assigned.
		if ( 0 == $c_handler_id ) {
			# if a default user is associated with the category and we know at this point
			# that that the candidate was not assigned to somebody, then assign it automatically.
			$query = "SELECT user_id
					  FROM $t_project_category_table
					  WHERE project_id='$c_project_id' AND category='$c_category'";
			$result = db_query( $query );

			if ( db_num_rows( $result ) > 0 ) {
				$c_handler_id = $p_handler_id = db_result( $result );
			}
		}

		# Check if candidate was pre-assigned or auto-assigned.
		if ( ( $c_handler_id != 0 ) && ( ON == config_get( 'auto_set_status_to_assigned' ) ) ) {
			$t_status = config_get( 'candidate_assigned_status' );
		}

		# Insert the rest of the data
		if ($p_import) {
			$t_resolution = db_prepare_int( $p_candidate_data->resolution ); 
			$t_candidate_status = db_prepare_int( $p_candidate_data->status ); 
		} else {
			$t_resolution = OPEN;
			$t_candidate_status = NEW_;
		}

		$query = "INSERT INTO $t_candidate_table
				    ( project_id,
				      reporter_id, handler_id,
				      duplicate_id, priority,
				      severity, reproducibility,
				      status, resolution,
				      projection, category,
				      date_submitted, last_updated,
				      eta, candidate_text_id,
				      os, os_build,
				      platform, version,
				      build,
				      profile_id, summary, view_state, sponsorship_total, sticky, fixed_in_version,
				      target_version 
				    )
				  VALUES
				    ( '$c_project_id',
				      '$c_reporter_id', '$c_handler_id',
				      '0', '$c_priority',
				      '$c_severity', '$c_reproducibility',
				      '$t_candidate_status', '$t_resolution',
				       $t_status, '$c_category',
				      " . db_now() . "," . db_now() . ",
				      10, '$t_text_id',
				      '$c_os', '$c_os_build',
				      '$c_platform', '$c_version',
				      '$c_build',
				      '$c_profile_id', '$c_summary', '$c_view_state', '$c_sponsorship_total', '$c_sticky', '',
				      '$c_target_version'
				    )";
		db_query( $query );

		$t_candidate_id = db_insert_id($t_candidate_table);

		# log new candidate
		history_log_event_special( $t_candidate_id, NEW_BUG );

		# log changes, if any (compare happens in history_log_event_direct)
		history_log_event_direct( $t_candidate_id, 'status', config_get( 'candidate_submit_status' ), $t_status );
		history_log_event_direct( $t_candidate_id, 'handler_id', 0, $c_handler_id );

		return $t_candidate_id;
	}

	# --------------------
	# Copy a candidate from one project to another. Also make copies of issue notes, attachments, history,
	# email notifications etc.
	# @@@ Not managed FTP file upload
	# MASC RELATIONSHIP
	function candidate_copy( $p_candidate_id, $p_target_project_id = null, $p_copy_custom_fields = false, $p_copy_relationships = false,
		$p_copy_history = false, $p_copy_attachments = false, $p_copy_candidatenotes = false, $p_copy_monitoring_users = false ) {
		global $g_db;

		$t_cosmos_custom_field_string_table	= config_get( 'cosmos_custom_field_string_table' );
		$t_cosmos_candidate_file_table			= config_get( 'cosmos_candidate_file_table' );
		$t_cosmos_candidatenote_table				= config_get( 'cosmos_candidatenote_table' );
		$t_cosmos_candidatenote_text_table		= config_get( 'cosmos_candidatenote_text_table' );
		$t_cosmos_candidate_monitor_table			= config_get( 'cosmos_candidate_monitor_table' );
		$t_cosmos_candidate_history_table			= config_get( 'cosmos_candidate_history_table' );
		$t_cosmos_db = $g_db;

		$t_candidate_id = db_prepare_int( $p_candidate_id );
		$t_target_project_id = db_prepare_int( $p_target_project_id );


		$t_candidate_data = new BugData;
		$t_candidate_data = candidate_get( $t_candidate_id, true );

		# retrieve the project id associated with the candidate
		if ( ( $p_target_project_id == null ) || is_blank( $p_target_project_id ) ) {
			$t_target_project_id = $t_candidate_data->project_id;
		}

		$t_candidate_data->project_id = $t_target_project_id;

		$t_new_candidate_id = candidate_create( $t_candidate_data );

		# MASC ATTENTION: IF THE SOURCE BUG HAS TO HANDLER THE candidate_create FUNCTION CAN TRY TO AUTO-ASSIGN THE BUG
		# WE FORCE HERE TO DUPLICATE THE SAME HANDLER OF THE SOURCE BUG
		# @@@ VB: Shouldn't we check if the handler in the source project is also a handler in the destination project?
		candidate_set_field( $t_new_candidate_id, 'handler_id', $t_candidate_data->handler_id );

		candidate_set_field( $t_new_candidate_id, 'duplicate_id', $t_candidate_data->duplicate_id );
		candidate_set_field( $t_new_candidate_id, 'status', $t_candidate_data->status );
		candidate_set_field( $t_new_candidate_id, 'resolution', $t_candidate_data->resolution );
		candidate_set_field( $t_new_candidate_id, 'projection', $t_candidate_data->projection );
		candidate_set_field( $t_new_candidate_id, 'date_submitted', $t_cosmos_db->DBTimeStamp( $t_candidate_data->date_submitted ), false );
		candidate_set_field( $t_new_candidate_id, 'last_updated', $t_cosmos_db->DBTimeStamp( $t_candidate_data->last_updated ), false );
		candidate_set_field( $t_new_candidate_id, 'eta', $t_candidate_data->eta );
		candidate_set_field( $t_new_candidate_id, 'fixed_in_version', $t_candidate_data->fixed_in_version );
		candidate_set_field( $t_new_candidate_id, 'target_version', $t_candidate_data->target_version );
		candidate_set_field( $t_new_candidate_id, 'sponsorship_total', 0 );
		candidate_set_field( $t_new_candidate_id, 'sticky', 0 );

		# COPY CUSTOM FIELDS
		if ( $p_copy_custom_fields ) {
			$query = "SELECT field_id, candidate_id, value
					   FROM $t_cosmos_custom_field_string_table
					   WHERE candidate_id = '$t_candidate_id';";
			$result = db_query( $query );
			$t_count = db_num_rows( $result );

			for ( $i = 0 ; $i < $t_count ; $i++ ) {
				$t_candidate_custom = db_fetch_array( $result );

				$c_field_id = db_prepare_int( $t_candidate_custom['field_id'] );
				$c_new_candidate_id = db_prepare_int( $t_new_candidate_id );
				$c_value = db_prepare_string( $t_candidate_custom['value'] );

				$query = "INSERT INTO $t_cosmos_custom_field_string_table
						   ( field_id, candidate_id, value )
						   VALUES ('$c_field_id', '$c_new_candidate_id', '$c_value')";
				db_query( $query );
			}
		}

		# COPY RELATIONSHIPS
		if ( $p_copy_relationships ) {
			if ( ON == config_get( 'enable_relationship' ) ) {
				relationship_copy_all( $t_candidate_id,$t_new_candidate_id );
			}
		}

		# Copy candidatenotes
		if ( $p_copy_candidatenotes ) {
			$query = "SELECT *
					  FROM $t_cosmos_candidatenote_table
					  WHERE candidate_id = '$t_candidate_id';";
			$result = db_query( $query );
			$t_count = db_num_rows( $result );

			for ( $i = 0; $i < $t_count; $i++ ) {
				$t_candidate_note = db_fetch_array( $result );
				$t_candidatenote_text_id = $t_candidate_note['candidatenote_text_id'];

				$query2 = "SELECT *
						   FROM $t_cosmos_candidatenote_text_table
						   WHERE id = '$t_candidatenote_text_id';";
				$result2 = db_query( $query2 );
				$t_count2 = db_num_rows( $result2 );

				$t_candidatenote_text_insert_id = -1;
				if ( $t_count2 > 0 ) {
					$t_candidatenote_text = db_fetch_array( $result2 );
					$t_candidatenote_text['note'] = db_prepare_string( $t_candidatenote_text['note'] );

					$query2 = "INSERT INTO $t_cosmos_candidatenote_text_table
							   ( note )
							   VALUES ( '" . $t_candidatenote_text['note'] . "' );";
					db_query( $query2 );
					$t_candidatenote_text_insert_id = db_insert_id( $t_cosmos_candidatenote_text_table );
				}

				$query2 = "INSERT INTO $t_cosmos_candidatenote_table
						   ( candidate_id, reporter_id, candidatenote_text_id, view_state, date_submitted, last_modified )
						   VALUES ( '$t_new_candidate_id',
						   			'" . $t_candidate_note['reporter_id'] . "',
						   			'$t_candidatenote_text_insert_id',
						   			'" . $t_candidate_note['view_state'] . "',
						   			'" . $t_candidate_note['date_submitted'] . "',
						   			'" . $t_candidate_note['last_modified'] . "' );";
				db_query( $query2 );
			}
		}

		# Copy attachments
		if ( $p_copy_attachments ) {
			$query = "SELECT *
					  FROM $t_cosmos_candidate_file_table
					  WHERE candidate_id = '$t_candidate_id';";
			$result = db_query( $query );
			$t_count = db_num_rows( $result );

			$t_candidate_file = array();
			for ( $i = 0; $i < $t_count; $i++ ) {
				$t_candidate_file = db_fetch_array( $result );

				# prepare the new diskfile name and then copy the file
				$t_file_path = dirname( $t_candidate_file['folder'] );
				$t_new_diskfile_name = $t_file_path . file_generate_unique_name( 'candidate-' . $p_file_name, $t_file_path );
				$t_new_file_name = file_get_display_name( $t_candidate_file['filename'] );
				if ( ( config_get( 'file_upload_method' ) == DISK ) ) {
					copy( $t_candidate_file['diskfile'], $t_new_diskfile_name );
					chmod( $t_new_diskfile_name, config_get( 'attachments_file_permissions' ) );
				}

				$query = "INSERT INTO $t_cosmos_candidate_file_table
						( candidate_id, title, description, diskfile, filename, folder, filesize, file_type, date_added, content )
						VALUES ( '$t_new_candidate_id',
								 '" . db_prepare_string( $t_candidate_file['title'] ) . "',
								 '" . db_prepare_string( $t_candidate_file['description'] ) . "',
								 '" . db_prepare_string( $t_new_diskfile_name ) . "',
								 '" . db_prepare_string( $t_new_file_name ) . "',
								 '" . db_prepare_string( $t_candidate_file['folder'] ) . "',
								 '" . db_prepare_int( $t_candidate_file['filesize'] ) . "',
								 '" . db_prepare_string( $t_candidate_file['file_type'] ) . "',
								 '" . db_prepare_string( $t_candidate_file['date_added'] ) . "',
								 '" . db_prepare_string( $t_candidate_file['content'] ) . "');";
				db_query( $query );
			}
		}

		# Copy users monitoring candidate
		if ( $p_copy_monitoring_users ) {
			$query = "SELECT *
					  FROM $t_cosmos_candidate_monitor_table
					  WHERE candidate_id = '$t_candidate_id';";
			$result = db_query( $query );
			$t_count = db_num_rows( $result );

			for ( $i = 0; $i < $t_count; $i++ ) {
				$t_candidate_monitor = db_fetch_array( $result );
				$query = "INSERT INTO $t_cosmos_candidate_monitor_table
						 ( user_id, candidate_id )
						 VALUES ( '" . $t_candidate_monitor['user_id'] . "', '$t_new_candidate_id' );";
				db_query( $query );
			}
		}

		# COPY HISTORY
		history_delete( $t_new_candidate_id );	# should history only be deleted inside the if statement below?
		if ( $p_copy_history ) {
			$query = "SELECT *
					  FROM $t_cosmos_candidate_history_table
					  WHERE candidate_id = '$t_candidate_id';";
			$result = db_query( $query );
			$t_count = db_num_rows( $result );

			for ( $i = 0; $i < $t_count; $i++ ) {
				$t_candidate_history = db_fetch_array( $result );
				$query = "INSERT INTO $t_cosmos_candidate_history_table
						  ( user_id, candidate_id, date_modified, field_name, old_value, new_value, type )
						  VALUES ( '" . db_prepare_int( $t_candidate_history['user_id'] ) . "',
						  		   '$t_new_candidate_id',
						  		   '" . db_prepare_string( $t_candidate_history['date_modified'] ) . "',
						  		   '" . db_prepare_string( $t_candidate_history['field_name'] ) . "',
						  		   '" . db_prepare_string( $t_candidate_history['old_value'] ) . "',
						  		   '" . db_prepare_string( $t_candidate_history['new_value'] ) . "',
						  		   '" . db_prepare_int( $t_candidate_history['type'] ) . "' );";
				db_query( $query );
			}
		}

		return $t_new_candidate_id;
	}

	# --------------------
	# allows candidate deletion :
	# delete the candidate, candidatetext, candidatenote, and candidatetexts selected
	# used in candidate_delete.php & mass treatments
	function candidate_delete( $p_candidate_id ) {
		$c_candidate_id			= db_prepare_int( $p_candidate_id );
		$t_candidate_table		= config_get( 'cosmos_candidate_table' );
		$t_candidate_text_table	= config_get( 'cosmos_candidate_text_table' );

		# call pre-deletion custom function
		helper_call_custom_function( 'issue_delete_validate', array( $p_candidate_id ) );

		# log deletion of candidate
		history_log_event_special( $p_candidate_id, BUG_DELETED, candidate_format_id( $p_candidate_id ) );

		email_candidate_deleted( $p_candidate_id );

		# call post-deletion custom function.  We call this here to allow the custom function to access the details of the candidate before 
		# they are deleted from the database given it's id.  The other option would be to move this to the end of the function and
		# provide it with candidate data rather than an id, but this will break backward compatibility.
		helper_call_custom_function( 'issue_delete_notify', array( $p_candidate_id ) );

		# Unmonitor candidate for all users
		candidate_unmonitor( $p_candidate_id, null );

		# Delete custom fields
		custom_field_delete_all_values( $p_candidate_id );

		# Delete candidatenotes
		candidatenote_delete_all( $p_candidate_id );

		# Delete all sponsorships
		sponsorship_delete( sponsorship_get_all_ids( $p_candidate_id ) );

		# MASC RELATIONSHIP
		# we delete relationships even if the feature is currently off.
		relationship_delete_all( $p_candidate_id );
		# MASC RELATIONSHIP

		# Delete files
		file_delete_attachments( $p_candidate_id );

		# Detach tags
		tag_candidate_detach_all( $p_candidate_id, false );
		
		# Delete the candidate history
		history_delete( $p_candidate_id );

		# Delete the candidatenote text
		$t_candidate_text_id = candidate_get_field( $p_candidate_id, 'candidate_text_id' );

		$query = "DELETE FROM $t_candidate_text_table
				  WHERE id='$t_candidate_text_id'";
		db_query( $query );

		# Delete the candidate entry
		$query = "DELETE FROM $t_candidate_table
				  WHERE id='$c_candidate_id'";
		db_query( $query );

		candidate_clear_cache( $p_candidate_id );
		candidate_text_clear_cache( $p_candidate_id );

		# db_query() errors on failure so:
		return true;
	}

	# --------------------
	# Delete all candidates associated with a project
	function candidate_delete_all( $p_project_id ) {
		$c_project_id = db_prepare_int( $p_project_id );

		$t_candidate_table = config_get( 'cosmos_candidate_table' );

		$query = "SELECT id
				  FROM $t_candidate_table
				  WHERE project_id='$c_project_id'";
		$result = db_query( $query );

		$candidate_count = db_num_rows( $result );

		for ( $i=0 ; $i < $candidate_count ; $i++ ) {
			$row = db_fetch_array( $result );

			candidate_delete( $row['id'] );
		}

		# @@@ should we check the return value of each candidate_delete() and
		#  return false if any of them return false? Presumable candidate_delete()
		#  will eventually trigger an error on failure so it won't matter...

		return true;
	}

	# --------------------
	# Update a candidate from the given data structure
	#  If the third parameter is true, also update the longer strings table
	function candidate_update( $p_candidate_id, $p_candidate_data, $p_update_extended = false, $p_bypass_mail = false ) {
		$c_candidate_id		= db_prepare_int( $p_candidate_id );
		$c_candidate_data		= candidate_prepare_db( $p_candidate_data );

		# Summary cannot be blank
		if ( is_blank( $c_candidate_data->summary ) ) {
			error_parameters( lang_get( 'summary' ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}

		if ( $p_update_extended ) {
			# Description field cannot be empty
			if ( is_blank( $c_candidate_data->description ) ) {
				error_parameters( lang_get( 'description' ) );
				trigger_error( ERROR_EMPTY_FIELD, ERROR );
			}
		}

		if( !is_blank( $p_candidate_data->duplicate_id ) && ( $p_candidate_data->duplicate_id != 0 ) && ( $p_candidate_id == $p_candidate_data->duplicate_id ) ) {
			trigger_error( ERROR_BUG_DUPLICATE_SELF, ERROR );  # never returns
	    }

		$t_old_data = candidate_get( $p_candidate_id, true );

		$t_candidate_table = config_get( 'cosmos_candidate_table' );
                
		# MANOS DUPLICATE NAME VALIDATION
		$summary_query = "SELECT * from $t_candidate_table WHERE summary = '$c_candidate_data->summary' AND id <> $c_candidate_id";
		$db_result = db_query( $summary_query );
		$rows_summary = db_num_rows( $db_result );
		if ( $rows_summary != 0 ) {
			trigger_error( ERROR_BUG_DUPLICATE_SELF, ERROR );  # never returns
		}
		###################### END MANOS
		#
		# Update all fields
		# Ignore date_submitted and last_updated since they are pulled out
		#  as unix timestamps which could confuse the history log and they
		#  shouldn't get updated like this anyway.  If you really need to change
		#  them use candidate_set_field()
		$query = "UPDATE $t_candidate_table
				SET project_id='$c_candidate_data->project_id',
					reporter_id='$c_candidate_data->reporter_id',
					handler_id='$c_candidate_data->handler_id',
					duplicate_id='$c_candidate_data->duplicate_id',
					priority='$c_candidate_data->priority',
					severity='$c_candidate_data->severity',
					reproducibility='$c_candidate_data->reproducibility',
					status='$c_candidate_data->status',
					resolution='$c_candidate_data->resolution',
					projection='$c_candidate_data->projection',
					category='$c_candidate_data->category',
					eta='$c_candidate_data->eta',
					os='$c_candidate_data->os',
					os_build='$c_candidate_data->os_build',
					platform='$c_candidate_data->platform',
					version='$c_candidate_data->version',
					build='$c_candidate_data->build',
					fixed_in_version='$c_candidate_data->fixed_in_version',";

		$t_roadmap_updated = false;
		if ( access_has_project_level( config_get( 'roadmap_update_threshold' ) ) ) {
			$query .= "
					target_version='$c_candidate_data->target_version',";
			$t_roadmap_updated = true;
		}

		$query .= "
					view_state='$c_candidate_data->view_state',
					summary='$c_candidate_data->summary',
					sponsorship_total='$c_candidate_data->sponsorship_total',
					sticky='$c_candidate_data->sticky'
				WHERE id='$c_candidate_id'";
		db_query( $query );

		candidate_clear_cache( $p_candidate_id );

		# log changes
		history_log_event_direct( $p_candidate_id, 'project_id', $t_old_data->project_id, $p_candidate_data->project_id );
		history_log_event_direct( $p_candidate_id, 'reporter_id', $t_old_data->reporter_id, $p_candidate_data->reporter_id );
		history_log_event_direct( $p_candidate_id, 'handler_id', $t_old_data->handler_id, $p_candidate_data->handler_id );
		history_log_event_direct( $p_candidate_id, 'duplicate_id', $t_old_data->duplicate_id, $p_candidate_data->duplicate_id );
		history_log_event_direct( $p_candidate_id, 'priority', $t_old_data->priority, $p_candidate_data->priority );
		history_log_event_direct( $p_candidate_id, 'severity', $t_old_data->severity, $p_candidate_data->severity );
		history_log_event_direct( $p_candidate_id, 'reproducibility', $t_old_data->reproducibility, $p_candidate_data->reproducibility );
		history_log_event_direct( $p_candidate_id, 'status', $t_old_data->status, $p_candidate_data->status );
		history_log_event_direct( $p_candidate_id, 'resolution', $t_old_data->resolution, $p_candidate_data->resolution );
		history_log_event_direct( $p_candidate_id, 'projection', $t_old_data->projection, $p_candidate_data->projection );
		history_log_event_direct( $p_candidate_id, 'category', $t_old_data->category, $p_candidate_data->category );
		history_log_event_direct( $p_candidate_id, 'eta',	$t_old_data->eta, $p_candidate_data->eta );
		history_log_event_direct( $p_candidate_id, 'os', $t_old_data->os, $p_candidate_data->os );
		history_log_event_direct( $p_candidate_id, 'os_build', $t_old_data->os_build, $p_candidate_data->os_build );
		history_log_event_direct( $p_candidate_id, 'platform', $t_old_data->platform, $p_candidate_data->platform );
		history_log_event_direct( $p_candidate_id, 'version', $t_old_data->version, $p_candidate_data->version );
		history_log_event_direct( $p_candidate_id, 'build', $t_old_data->build, $p_candidate_data->build );
		history_log_event_direct( $p_candidate_id, 'fixed_in_version', $t_old_data->fixed_in_version, $p_candidate_data->fixed_in_version );
		if ( $t_roadmap_updated ) {
			history_log_event_direct( $p_candidate_id, 'target_version', $t_old_data->target_version, $p_candidate_data->target_version );
		}
		history_log_event_direct( $p_candidate_id, 'view_state', $t_old_data->view_state, $p_candidate_data->view_state );
		history_log_event_direct( $p_candidate_id, 'summary', $t_old_data->summary, $p_candidate_data->summary );
		history_log_event_direct( $p_candidate_id, 'sponsorship_total', $t_old_data->sponsorship_total, $p_candidate_data->sponsorship_total );
		history_log_event_direct( $p_candidate_id, 'sticky', $t_old_data->sticky, $p_candidate_data->sticky );

		# Update extended info if requested
		if ( $p_update_extended ) {
			$t_candidate_text_table = config_get( 'cosmos_candidate_text_table' );

			$t_candidate_text_id = candidate_get_field( $p_candidate_id, 'candidate_text_id' );

			$query = "UPDATE $t_candidate_text_table
						SET description='$c_candidate_data->description',
							steps_to_reproduce='$c_candidate_data->steps_to_reproduce',
							additional_information='$c_candidate_data->additional_information'
						WHERE id='$t_candidate_text_id'";
			db_query( $query );

			candidate_text_clear_cache( $p_candidate_id );

			if ( $t_old_data->description != $p_candidate_data->description ) {
				history_log_event_special( $p_candidate_id, DESCRIPTION_UPDATED );
			}
			if ( $t_old_data->steps_to_reproduce != $p_candidate_data->steps_to_reproduce ) {
				history_log_event_special( $p_candidate_id, STEP_TO_REPRODUCE_UPDATED );
			}
			if ( $t_old_data->additional_information != $p_candidate_data->additional_information ) {
				history_log_event_special( $p_candidate_id, ADDITIONAL_INFO_UPDATED );
			}
		}

		# Update the last update date
		candidate_update_date( $p_candidate_id );

		if ( false == $p_bypass_mail ) {		# allow bypass if user is sending mail separately
			$t_action_prefix = 'email_notification_title_for_action_candidate_';
			$t_status_prefix = 'email_notification_title_for_status_candidate_';

			# status changed
			if ( $t_old_data->status != $p_candidate_data->status ) {
				$t_status = get_enum_to_string( config_get( 'status_enum_string' ), $p_candidate_data->status );
				$t_status = str_replace( ' ', '_', $t_status );
				email_generic( $p_candidate_id, $t_status, $t_status_prefix . $t_status );
				return true;
			}

			# candidate assigned
			if ( $t_old_data->handler_id != $p_candidate_data->handler_id ) {
				email_generic( $p_candidate_id, 'owner', $t_action_prefix . 'assigned' );
				return true;
			}

			# @@@ handle priority change if it requires special handling

			# generic update notification
			email_generic( $p_candidate_id, 'updated', $t_action_prefix . 'updated' );
		}

		return true;
	}

	#===================================
	# Data Access
	#===================================

	# --------------------
	# Returns the extended record of the specified candidate, this includes
	# the candidate text fields
	# @@@ include reporter name and handler name, the problem is that
	#      handler can be 0, in this case no corresponding name will be
	#      found.  Use equivalent of (+) in Oracle.
	function candidate_get_extended_row( $p_candidate_id ) {
		$t_base = candidate_cache_row( $p_candidate_id );
		$t_text = candidate_text_cache_row( $p_candidate_id );

		# merge $t_text first so that the 'id' key has the candidate id not the candidate text id
		return array_merge( $t_text, $t_base );
	}

	# --------------------
	# Returns the record of the specified candidate
	function candidate_get_row( $p_candidate_id ) {
		return candidate_cache_row( $p_candidate_id );
	}

	# --------------------
	# Returns an object representing the specified candidate
	function candidate_get( $p_candidate_id, $p_get_extended = false ) {
		if ( $p_get_extended ) {
			$row = candidate_get_extended_row( $p_candidate_id );
		} else {
			$row = candidate_get_row( $p_candidate_id );
		}

		$t_candidate_data = new BugData;
		$t_row_keys = array_keys( $row );
		$t_vars = get_object_vars( $t_candidate_data );

		# Check each variable in the class
		foreach ( $t_vars as $var => $val ) {
			# If we got a field from the DB with the same name
			if ( in_array( $var, $t_row_keys, true ) ) {
				# Store that value in the object
				$t_candidate_data->$var = $row[$var];
			}
		}

		return $t_candidate_data;
	}

	# --------------------
	# return the specified field of the given candidate
	#  if the field does not exist, display a warning and return ''
	function candidate_get_field( $p_candidate_id, $p_field_name ) {
		$row = candidate_get_row( $p_candidate_id );

		if ( isset( $row[$p_field_name] ) ) {
			return $row[$p_field_name];
		} else {
			error_parameters( $p_field_name );
			trigger_error( ERROR_DB_FIELD_NOT_FOUND, WARNING );
			return '';
		}
	}

	# --------------------
	# return the specified text field of the given candidate
	#  if the field does not exist, display a warning and return ''
	function candidate_get_text_field( $p_candidate_id, $p_field_name ) {
		$row = candidate_text_cache_row( $p_candidate_id );

		if ( isset( $row[$p_field_name] ) ) {
			return $row[$p_field_name];
		} else {
			error_parameters( $p_field_name );
			trigger_error( ERROR_DB_FIELD_NOT_FOUND, WARNING );
			return '';
		}
	}

	# --------------------
	# return the candidate summary
	#  this is a wrapper for the custom function
	function candidate_format_summary( $p_candidate_id, $p_context ) {
		return 	helper_call_custom_function( 'format_issue_summary', array( $p_candidate_id , $p_context ) );
	}


	# --------------------
	# Returns the number of candidatenotes for the given candidate_id
	function candidate_get_candidatenote_count( $p_candidate_id ) {
		$c_candidate_id = db_prepare_int( $p_candidate_id );

		$t_project_id = candidate_get_field( $p_candidate_id, 'project_id' );

		if ( !access_has_project_level( config_get( 'private_candidatenote_threshold' ), $t_project_id ) ) {
			$t_restriction = 'AND view_state=' . VS_PUBLIC;
		} else {
			$t_restriction = '';
		}

		$t_candidatenote_table = config_get( 'cosmos_candidatenote_table' );
		$query = "SELECT COUNT(*)
				  FROM $t_candidatenote_table
				  WHERE candidate_id ='$c_candidate_id' $t_restriction";
		$result = db_query( $query );

		return db_result( $result );
	}

	# --------------------
	# return the timestamp for the most recent time at which a candidatenote
	#  associated wiht the candidate was modified
	function candidate_get_newest_candidatenote_timestamp( $p_candidate_id ) {
		$c_candidate_id			= db_prepare_int( $p_candidate_id );
		$t_candidatenote_table	= config_get( 'cosmos_candidatenote_table' );

		$query = "SELECT last_modified
				  FROM $t_candidatenote_table
				  WHERE candidate_id='$c_candidate_id'
				  ORDER BY last_modified DESC";
		$result = db_query( $query, 1 );
		$row = db_result( $result );

		if ( false === $row ) {
			return false;
		} else {
			return db_unixtimestamp( $row );
		}
	}

	# --------------------
	# return the timestamp for the most recent time at which a candidatenote
	#  associated with the candidate was modified and the total candidatenote
	#  count in one db query
	function candidate_get_candidatenote_stats( $p_candidate_id ) {
		global $g_cache_candidate;
		$c_candidate_id			= db_prepare_int( $p_candidate_id );

		if( !is_null( $g_cache_candidate[ $c_candidate_id ]['_stats'] ) ) {
			if( $g_cache_candidate[ $c_candidate_id ]['_stats'] === false ) { 
				return false;			
			} else {
				$t_stats['last_modified'] = db_unixtimestamp( $g_cache_candidate[ $c_candidate_id ]['_stats']['last_modified'] );
				$t_stats['count'] = $g_cache_candidate[ $c_candidate_id ]['_stats']['count'];
			}
			return $t_stats;
		}

		$t_candidatenote_table	= config_get( 'cosmos_candidatenote_table' );

		$query = "SELECT last_modified
				  FROM $t_candidatenote_table
				  WHERE candidate_id='$c_candidate_id'
				  ORDER BY last_modified DESC";
		$result = db_query( $query );
		$row = db_fetch_array( $result );

		if ( false === $row )
			return false;

		$t_stats['last_modified'] = db_unixtimestamp( $row['last_modified'] );
		$t_stats['count'] = db_num_rows( $result );

		return $t_stats;
	}

	# --------------------
	# Get array of attachments associated with the specified candidate id.  The array will be
	# sorted in terms of date added (ASC).  The array will include the following fields:
	# id, title, diskfile, filename, filesize, file_type, date_added.
	function candidate_get_attachments( $p_candidate_id ) {
		if ( !file_can_view_candidate_attachments( $p_candidate_id ) ) {
	        return;
		}

		$c_candidate_id = db_prepare_int( $p_candidate_id );

		$t_candidate_file_table = config_get( 'cosmos_candidate_file_table' );

		$query = "SELECT id, title, diskfile, filename, filesize, file_type, date_added
		                FROM $t_candidate_file_table
		                WHERE candidate_id='$c_candidate_id'
		                ORDER BY date_added";
		$db_result = db_query( $query );
		$num_notes = db_num_rows( $db_result );

		$t_result = array();

		for ( $i = 0; $i < $num_notes; $i++ ) {
			$t_result[] = db_fetch_array( $db_result );
		}

		return $t_result;
	}

	#===================================
	# Data Modification
	#===================================

	# --------------------
	# set the value of a candidate field
	function candidate_set_field( $p_candidate_id, $p_field_name, $p_status, $p_prepare = true ) {
		$c_candidate_id			= db_prepare_int( $p_candidate_id );
		$c_field_name		= db_prepare_string( $p_field_name );
		if( $p_prepare ) {
			$c_status		= '\'' . db_prepare_string( $p_status ) . '\''; #generic, unknown type
		} else {
			$c_status		=  $p_status; #generic, unknown type
		}

		$h_status = candidate_get_field( $p_candidate_id, $p_field_name );

		# return if status is already set
		if ( $c_status == $h_status ) {
			return true;
		}

		$t_candidate_table = config_get( 'cosmos_candidate_table' );

		# Update fields
		$query = "UPDATE $t_candidate_table
				  SET $c_field_name=$c_status
				  WHERE id='$c_candidate_id'";
		db_query( $query );

		# updated the last_updated date
		candidate_update_date( $p_candidate_id );

		# log changes
		history_log_event_direct( $p_candidate_id, $p_field_name, $h_status, $p_status );

		candidate_clear_cache( $p_candidate_id );

		return true;
	}

	# --------------------
	# assign the candidate to the given user
	function candidate_assign( $p_candidate_id, $p_user_id, $p_candidatenote_text='', $p_candidatenote_private = false ) {
		$c_candidate_id	= db_prepare_int( $p_candidate_id );
		$c_user_id	= db_prepare_int( $p_user_id );

		if ( ( $c_user_id != NO_USER ) && !access_has_candidate_level( config_get( 'handle_candidate_threshold' ), $p_candidate_id, $p_user_id ) ) {
		    trigger_error( ERROR_USER_DOES_NOT_HAVE_REQ_ACCESS );
		}

		# extract current information into history variables
		$h_status		= candidate_get_field( $p_candidate_id, 'status' );
		$h_handler_id	= candidate_get_field( $p_candidate_id, 'handler_id' );

		if ( ( ON == config_get( 'auto_set_status_to_assigned' ) ) &&
			 ( NO_USER != $p_user_id ) ) {
			$t_ass_val = config_get( 'candidate_assigned_status' );
		} else {
			$t_ass_val = $h_status;
		}

		$t_candidate_table = config_get( 'cosmos_candidate_table' );

		if ( ( $t_ass_val != $h_status ) || ( $p_user_id != $h_handler_id ) ) {

			# get user id
			$query = "UPDATE $t_candidate_table
					  SET handler_id='$c_user_id', status='$t_ass_val'
					  WHERE id='$c_candidate_id'";
			db_query( $query );

			# log changes
			history_log_event_direct( $c_candidate_id, 'status', $h_status, $t_ass_val );
			history_log_event_direct( $c_candidate_id, 'handler_id', $h_handler_id, $p_user_id );

			# Add candidatenote if supplied ignore false return
			candidatenote_add( $p_candidate_id, $p_candidatenote_text, 0, $p_candidatenote_private, 0, '', NULL, FALSE );

			# updated the last_updated date
			candidate_update_date( $p_candidate_id );

			candidate_clear_cache( $p_candidate_id );

			# send assigned to email
			email_assign( $p_candidate_id );
		}

		return true;
	}

	# --------------------
	# close the given candidate
	function candidate_close( $p_candidate_id, $p_candidatenote_text = '', $p_candidatenote_private = false, $p_time_tracking = '0:00' ) {
		$p_candidatenote_text = trim( $p_candidatenote_text );

		# Add candidatenote if supplied ignore a false return
		# Moved candidatenote_add before candidate_set_field calls in case time_tracking_no_note is off.
		# Error condition stopped execution but status had already been changed
		candidatenote_add( $p_candidate_id, $p_candidatenote_text, $p_time_tracking, $p_candidatenote_private, 0, '', NULL, FALSE );

		candidate_set_field( $p_candidate_id, 'status', CLOSED );

		email_close( $p_candidate_id );

		# MASC RELATIONSHIP
		if ( ON == config_get( 'enable_relationship' ) ) {
			email_relationship_child_closed( $p_candidate_id );
		}
		# MASC RELATIONSHIP

		return true;
	}

	# --------------------
	# resolve the given candidate
	function candidate_resolve( $p_candidate_id, $p_resolution, $p_fixed_in_version = '', $p_candidatenote_text = '', $p_duplicate_id = null, $p_handler_id = null, $p_candidatenote_private = false, $p_time_tracking = '0:00' ) {
		$p_candidatenote_text = trim( $p_candidatenote_text );

		# Add candidatenote if supplied
		# Moved candidatenote_add before candidate_set_field calls in case time_tracking_no_note is off.
		# Error condition stopped execution but status had already been changed
		candidatenote_add( $p_candidate_id, $p_candidatenote_text, $p_time_tracking, $p_candidatenote_private, 0, '', NULL, FALSE );

		$t_duplicate = !is_blank( $p_duplicate_id ) && ( $p_duplicate_id != 0 );
		if ( $t_duplicate ) {
			if ( $p_candidate_id == $p_duplicate_id ) {
			    trigger_error( ERROR_BUG_DUPLICATE_SELF, ERROR );  # never returns
			}

			# the related candidate exists...
			candidate_ensure_exists( $p_duplicate_id );

			if ( ON == config_get( 'enable_relationship' ) ) {
				# check if there is other relationship between the candidates...
				$t_id_relationship = relationship_same_type_exists( $p_candidate_id, $p_duplicate_id, BUG_DUPLICATE );

				if ( $t_id_relationship == -1 ) {
					# the relationship type is already set. Nothing to do
				}
				else if ( $t_id_relationship > 0 ) {
					# Update the relationship
					relationship_update( $t_id_relationship, $p_candidate_id, $p_duplicate_id, BUG_DUPLICATE );

					# Add log line to the history (both candidates)
					history_log_event_special( $p_candidate_id, BUG_REPLACE_RELATIONSHIP, BUG_DUPLICATE, $p_duplicate_id );
					history_log_event_special( $p_duplicate_id, BUG_REPLACE_RELATIONSHIP, BUG_HAS_DUPLICATE, $p_candidate_id );
				}
				else {
					# Add the new relationship
					relationship_add( $p_candidate_id, $p_duplicate_id, BUG_DUPLICATE );

					# Add log line to the history (both candidates)
					history_log_event_special( $p_candidate_id, BUG_ADD_RELATIONSHIP, BUG_DUPLICATE, $p_duplicate_id );
					history_log_event_special( $p_duplicate_id, BUG_ADD_RELATIONSHIP, BUG_HAS_DUPLICATE, $p_candidate_id );
				}
			}

			candidate_set_field( $p_candidate_id, 'duplicate_id', (int)$p_duplicate_id );
		}
		
		$c_resolution = db_prepare_int( $p_resolution );

		candidate_set_field( $p_candidate_id, 'status', config_get( 'candidate_resolved_status_threshold' ) );
		candidate_set_field( $p_candidate_id, 'fixed_in_version', $p_fixed_in_version );
		candidate_set_field( $p_candidate_id, 'resolution', $c_resolution );

		# only set handler if specified explicitly or if candidate was not assigned to a handler
		if ( null == $p_handler_id ) {
			if ( candidate_get_field( $p_candidate_id, 'handler_id' ) == 0 ) {
				$p_handler_id = auth_get_current_user_id();
				candidate_set_field( $p_candidate_id, 'handler_id', $p_handler_id );
			}
		} else {
			candidate_set_field( $p_candidate_id, 'handler_id', $p_handler_id );
		}

		email_resolved( $p_candidate_id );

		if ( $c_resolution == FIXED ) {
			twitter_issue_resolved( $p_candidate_id );
		}

		# MASC RELATIONSHIP
		if ( ON == config_get( 'enable_relationship' ) ) {
			email_relationship_child_resolved( $p_candidate_id );
		}
		# MASC RELATIONSHIP

		return true;
	}

	# --------------------
	# reopen the given candidate
	function candidate_reopen( $p_candidate_id, $p_candidatenote_text='', $p_time_tracking = '0:00', $p_candidatenote_private = false ) {
		$p_candidatenote_text = trim( $p_candidatenote_text );

		# Add candidatenote if supplied
		# Moved candidatenote_add before candidate_set_field calls in case time_tracking_no_note is off.
		# Error condition stopped execution but status had already been changed
		candidatenote_add( $p_candidate_id, $p_candidatenote_text, $p_time_tracking, $p_candidatenote_private, 0, '', NULL, FALSE );

		candidate_set_field( $p_candidate_id, 'status', config_get( 'candidate_reopen_status' ) );
		candidate_set_field( $p_candidate_id, 'resolution', config_get( 'candidate_reopen_resolution' ) );

		email_reopen( $p_candidate_id );

		return true;
	}

	# --------------------
	# updates the last_updated field
	function candidate_update_date( $p_candidate_id ) {
		$c_candidate_id = db_prepare_int( $p_candidate_id );

		$t_candidate_table = config_get( 'cosmos_candidate_table' );

		$query = "UPDATE $t_candidate_table
				  SET last_updated= " . db_now() . "
				  WHERE id='$c_candidate_id'";
		db_query( $query );

		candidate_clear_cache( $p_candidate_id );

		return true;
	}

	# --------------------
	# enable monitoring of this candidate for the user
	function candidate_monitor( $p_candidate_id, $p_user_id ) {
		$c_candidate_id	= db_prepare_int( $p_candidate_id );
		$c_user_id	= db_prepare_int( $p_user_id );

		# Make sure we aren't already monitoring this candidate
		if ( user_is_monitoring_candidate( $p_user_id, $p_candidate_id ) ) {
			return true;
		}

		$t_candidate_monitor_table = config_get( 'cosmos_candidate_monitor_table' );

		# Insert monitoring record
		$query ="INSERT ".
				"INTO $t_candidate_monitor_table ".
				"( user_id, candidate_id ) ".
				"VALUES ".
				"( '$c_user_id', '$c_candidate_id' )";
		db_query( $query );

		# log new monitoring action
		history_log_event_special( $p_candidate_id, BUG_MONITOR, $c_user_id );

		return true;
	}

	# --------------------
	# disable monitoring of this candidate for the user
	# if $p_user_id = null, then candidate is unmonitored for all users.
	function candidate_unmonitor( $p_candidate_id, $p_user_id ) {
		$c_candidate_id	= db_prepare_int( $p_candidate_id );
		$c_user_id	= db_prepare_int( $p_user_id );

		$t_candidate_monitor_table = config_get( 'cosmos_candidate_monitor_table' );

		# Delete monitoring record
		$query ="DELETE ".
				"FROM $t_candidate_monitor_table ".
				"WHERE candidate_id = '$c_candidate_id'";

		if ( $p_user_id !== null ) {
			$query .= " AND user_id = '$c_user_id'";
		}

		db_query( $query );

		# log new un-monitor action
		history_log_event_special( $p_candidate_id, BUG_UNMONITOR, $p_user_id );

		return true;
	}

	#===================================
	# Other
	#===================================

	# --------------------
	# Pads the candidate id with the appropriate number of zeros.
	function candidate_format_id( $p_candidate_id ) {
		$t_padding = config_get( 'display_candidate_padding' );
		return( str_pad( $p_candidate_id, $t_padding, '0', STR_PAD_LEFT ) );
	}

	# --------------------
	# Return a copy of the candidate structure with all the instvars prepared for db insertion
	function candidate_prepare_db( $p_candidate_data ) {
		$t_candidate_data = new BugData;
		$t_candidate_data->project_id			= db_prepare_int( $p_candidate_data->project_id );
		$t_candidate_data->reporter_id		= db_prepare_int( $p_candidate_data->reporter_id );
		$t_candidate_data->handler_id			= db_prepare_int( $p_candidate_data->handler_id );
		$t_candidate_data->duplicate_id		= db_prepare_int( $p_candidate_data->duplicate_id );
		$t_candidate_data->priority			= db_prepare_int( $p_candidate_data->priority );
		$t_candidate_data->severity			= db_prepare_int( $p_candidate_data->severity );
		$t_candidate_data->reproducibility	= db_prepare_int( $p_candidate_data->reproducibility );
		$t_candidate_data->status				= db_prepare_int( $p_candidate_data->status );
		$t_candidate_data->resolution			= db_prepare_int( $p_candidate_data->resolution );
		$t_candidate_data->projection			= db_prepare_int( $p_candidate_data->projection );
		$t_candidate_data->category			= db_prepare_string( $p_candidate_data->category );
		$t_candidate_data->date_submitted		= db_prepare_string( $p_candidate_data->date_submitted );
		$t_candidate_data->last_updated		= db_prepare_string( $p_candidate_data->last_updated );
		$t_candidate_data->eta				= db_prepare_int( $p_candidate_data->eta );
		$t_candidate_data->os					= db_prepare_string( $p_candidate_data->os );
		$t_candidate_data->os_build			= db_prepare_string( $p_candidate_data->os_build );
		$t_candidate_data->platform			= db_prepare_string( $p_candidate_data->platform );
		$t_candidate_data->version			= db_prepare_string( $p_candidate_data->version );
		$t_candidate_data->build				= db_prepare_string( $p_candidate_data->build );
		$t_candidate_data->fixed_in_version	= db_prepare_string( $p_candidate_data->fixed_in_version );
		$t_candidate_data->target_version		= db_prepare_string( $p_candidate_data->target_version );
		$t_candidate_data->view_state			= db_prepare_int( $p_candidate_data->view_state );
		$t_candidate_data->summary			= db_prepare_string( $p_candidate_data->summary );
		$t_candidate_data->sponsorship_total	= db_prepare_int( $p_candidate_data->sponsorship_total );
		$t_candidate_data->sticky				= db_prepare_int( $p_candidate_data->sticky );

		$t_candidate_data->description		= db_prepare_string( $p_candidate_data->description );
		$t_candidate_data->steps_to_reproduce	= db_prepare_string( $p_candidate_data->steps_to_reproduce );
		$t_candidate_data->additional_information	= db_prepare_string( $p_candidate_data->additional_information );

		return $t_candidate_data;
	}

	# --------------------
	# Return a copy of the candidate structure with all the instvars prepared for editing
	#  in an HTML form
	function candidate_prepare_edit( $p_candidate_data ) {
		$p_candidate_data->category			= string_attribute( $p_candidate_data->category );
		$p_candidate_data->date_submitted		= string_attribute( $p_candidate_data->date_submitted );
		$p_candidate_data->last_updated		= string_attribute( $p_candidate_data->last_updated );
		$p_candidate_data->os					= string_attribute( $p_candidate_data->os );
		$p_candidate_data->os_build			= string_attribute( $p_candidate_data->os_build );
		$p_candidate_data->platform			= string_attribute( $p_candidate_data->platform );
		$p_candidate_data->version			= string_attribute( $p_candidate_data->version );
		$p_candidate_data->build				= string_attribute( $p_candidate_data->build );
		$p_candidate_data->target_version     = string_attribute( $p_candidate_data->target_version );
		$p_candidate_data->fixed_in_version	= string_attribute( $p_candidate_data->fixed_in_version );
		$p_candidate_data->summary			= string_attribute( $p_candidate_data->summary );
		$p_candidate_data->sponsorship_total	= string_attribute( $p_candidate_data->sponsorship_total );
		$p_candidate_data->sticky				= string_attribute( $p_candidate_data->sticky );

		$p_candidate_data->description		= string_textarea( $p_candidate_data->description );
		$p_candidate_data->steps_to_reproduce	= string_textarea( $p_candidate_data->steps_to_reproduce );
		$p_candidate_data->additional_information	= string_textarea( $p_candidate_data->additional_information );

		return $p_candidate_data;
	}

	# --------------------
	# Return a copy of the candidate structure with all the instvars prepared for editing
	#  in an HTML form
	function candidate_prepare_display( $p_candidate_data ) {
		$p_candidate_data->category			= string_display_line( $p_candidate_data->category );
		$p_candidate_data->date_submitted		= string_display_line( $p_candidate_data->date_submitted );
		$p_candidate_data->last_updated		= string_display_line( $p_candidate_data->last_updated );
		$p_candidate_data->os					= string_display_line( $p_candidate_data->os );
		$p_candidate_data->os_build			= string_display_line( $p_candidate_data->os_build );
		$p_candidate_data->platform			= string_display_line( $p_candidate_data->platform );
		$p_candidate_data->version			= string_display_line( $p_candidate_data->version );
		$p_candidate_data->build				= string_display_line( $p_candidate_data->build );
		$p_candidate_data->target_version		= string_display_line( $p_candidate_data->target_version );
		$p_candidate_data->fixed_in_version	= string_display_line( $p_candidate_data->fixed_in_version );
		$p_candidate_data->summary			= string_display_line_links( $p_candidate_data->summary );
		$p_candidate_data->sponsorship_total	= string_display_line( $p_candidate_data->sponsorship_total );
		$p_candidate_data->sticky				= string_display_line( $p_candidate_data->sticky );

		$p_candidate_data->description		= string_display_links( $p_candidate_data->description );
		$p_candidate_data->steps_to_reproduce	= string_display_links( $p_candidate_data->steps_to_reproduce );
		$p_candidate_data->additional_information	= string_display_links( $p_candidate_data->additional_information );

		return $p_candidate_data;
	}
	
	function set_custom_field ($cField, $candidate_name, $value) {
		$t_custom_table				= config_get( 'cosmos_custom_field_table' );
		$t_candidate_table			= config_get( 'cosmos_candidate_table' );
		$t_custom_field_table			= config_get( 'cosmos_custom_field_string_table' );
		$query = "SELECT id
			  FROM $t_custom_table 
			  WHERE name='$cField'";
		$result = db_query( $query );
		$rows =  db_num_rows( $result ); 
		$data = db_fetch_array($result);
		$custom_id =  $data['id'];
		
		$query = "SELECT id
			  FROM $t_candidate_table 
			  WHERE summary='$candidate_name'";
		
		$result = db_query( $query );
		$rows = db_num_rows( $result );
		if ($rows == 0){
			echo "$candidate_name";
			trigger_error( ERROR_NO_EXIST, ERROR );
		}

		$data = db_fetch_array($result);
		$candidate_id = $data['id'];
		
		$query_select= "SELECT * from  $t_custom_field_table 
			  	WHERE field_id='$custom_id' AND candidate_id='$candidate_id'"; 

		$query_update = "UPDATE $t_custom_field_table 
			  	 SET value='$value' 
			  	 WHERE field_id='$custom_id' AND candidate_id='$candidate_id'"; 
		$query_insert = "INSERT INTO $t_custom_field_table 
			  	 values ('$custom_id','$candidate_id', '$value')"; 
	    	
		$result_select = db_query( $query_select );
		$r = db_num_rows( $result_select ); 
		if ($r == 0 ) {
			$result_insert = db_query( $query_insert );
			if (!$result_insert){
				trigger_error( ERROR_DB_OPERATION, ERROR );
			}
		} else {
			$result_update = db_query( $query_update );
			if (!$result_update){
				trigger_error( ERROR_DB_OPERATION, ERROR );
			}

		}
			#die("333");
	}
function	get_id_from_name($name, $silent=false){
		$t_candidate_table = config_get( 'cosmos_candidate_table' );
		$query = "SELECT id
			  FROM $t_candidate_table 
			  WHERE summary='$name'";
		
		$result = db_query( $query );
		$rows = db_num_rows( $result );
		if ($rows == 0 && $silent == false){
			echo "$candidate_name";
			trigger_error( ERROR_NO_EXIST, ERROR );
		}

		$data = db_fetch_array($result);
		$candidate_id = $data['id'];
		return $candidate_id;

	}
?>
