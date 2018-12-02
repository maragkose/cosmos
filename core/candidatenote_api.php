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
	# $Id: candidatenote_api.php,v 1.46.2.1 2007-10-13 22:35:14 giallu Exp $
	# --------------------------------------------------------

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'current_user_api.php' );
	require_once( $t_core_dir . 'email_api.php' );
	require_once( $t_core_dir . 'history_api.php' );
	require_once( $t_core_dir . 'candidate_api.php' );

	### Bugnote API ###

	#===================================
	# Bugnote Data Structure Definition
	#===================================
	class BugnoteData {
		var $id;
		var $candidate_id;
		var $reporter_id;
		var $note;
		var $view_state;
		var $date_submitted;
		var $last_modified;
		var $note_type;
		var $note_attr;
		var $time_tracking;
	}

	#===================================
	# Boolean queries and ensures
	#===================================

	# --------------------
	# Check if a candidatenote with the given ID exists
	#
	# return true if the candidatenote exists, false otherwise
	function candidatenote_exists( $p_candidatenote_id ) {
		$c_candidatenote_id   	= db_prepare_int( $p_candidatenote_id );
		$t_candidatenote_table	= config_get( 'cosmos_candidatenote_table' );

		$query 	= "SELECT COUNT(*)
		          	FROM $t_candidatenote_table
		          	WHERE id='$c_candidatenote_id'";
		$result	= db_query( $query );

		if ( 0 == db_result( $result ) ) {
			return false;
		} else {
			return true;
		}
	}

	# --------------------
	# Check if a candidatenote with the given ID exists
	#
	# return true if the candidatenote exists, raise an error if not
	function candidatenote_ensure_exists( $p_candidatenote_id ) {
		if ( !candidatenote_exists( $p_candidatenote_id ) ) {
			trigger_error( ERROR_BUGNOTE_NOT_FOUND, ERROR );
		}
	}

	# --------------------
	# Check if the given user is the reporter of the candidatenote
	# return true if the user is the reporter, false otherwise
	function candidatenote_is_user_reporter( $p_candidatenote_id, $p_user_id ) {
		if ( candidatenote_get_field( $p_candidatenote_id, 'reporter_id' ) == $p_user_id ) {
			return true;
		} else {
			return false;
		}
	}

	#===================================
	# Creation / Deletion / Updating
	#===================================

	# --------------------
	# Add a candidatenote to a candidate
	#
	# return the ID of the new candidatenote
	function candidatenote_add ( $p_candidate_id, $p_candidatenote_text, $p_time_tracking = '0:00', $p_private = false, $p_type = 0, $p_attr = '', $p_user_id = null, $p_send_email = TRUE ) {
		$c_candidate_id            	= db_prepare_int( $p_candidate_id );
		$c_candidatenote_text      	= db_prepare_string( $p_candidatenote_text );
		$c_time_tracking	= db_prepare_time( $p_time_tracking );
		$c_private           	= db_prepare_bool( $p_private );
		$c_type            	= db_prepare_int( $p_type );
		$c_attr      	= db_prepare_string( $p_attr );

		$t_candidatenote_text_table	= config_get( 'cosmos_candidatenote_text_table' );
		$t_candidatenote_table     	= config_get( 'cosmos_candidatenote_table' );

		$t_time_tracking_enabled = config_get( 'time_tracking_enabled' );
		$t_time_tracking_without_note  = config_get( 'time_tracking_without_note' );
		if ( ON == $t_time_tracking_enabled && $c_time_tracking > 0 ) {
			if ( is_blank( $p_candidatenote_text ) && OFF == $t_time_tracking_without_note ) {
				error_parameters( lang_get( 'candidatenote' ) );
				trigger_error( ERROR_EMPTY_FIELD, ERROR );
			}
			$c_type = TIME_TRACKING;
		} else if ( is_blank( $p_candidatenote_text ) ) {
			return false;
		}

		# insert candidatenote text
		$query = "INSERT INTO $t_candidatenote_text_table
		          		( note )
		          	 VALUES
		          		( '$c_candidatenote_text' )";
		db_query( $query );

		# retrieve candidatenote text id number
		$t_candidatenote_text_id = db_insert_id( $t_candidatenote_text_table );

		# get user information
		if ( $p_user_id === null ) {
			$c_user_id = auth_get_current_user_id();
		} else {
			$c_user_id = db_prepare_int( $p_user_id );
		}

		# Check for private candidatenotes.
		# @@@ VB: Should we allow users to report private candidatenotes, and possibly see only their own private ones
		if ( $p_private && access_has_candidate_level( config_get( 'private_candidatenote_threshold' ), $p_candidate_id, $c_user_id ) ) {
			$t_view_state = VS_PRIVATE;
		} else {
			$t_view_state = VS_PUBLIC;
		}

		# insert candidatenote info
		$query = "INSERT INTO $t_candidatenote_table
					(candidate_id, reporter_id, candidatenote_text_id, view_state, date_submitted, last_modified, note_type, note_attr, time_tracking )
		          	 VALUES
					('$c_candidate_id', '$c_user_id','$t_candidatenote_text_id', '$t_view_state', " . db_now() . "," . db_now() . ", '$c_type', '$c_attr', '$c_time_tracking' )";
		db_query( $query );

		# get candidatenote id
		$t_candidatenote_id = db_insert_id( $t_candidatenote_table );

		# update candidate last updated
		candidate_update_date( $p_candidate_id );

		# log new candidate
		history_log_event_special( $p_candidate_id, BUGNOTE_ADDED, candidatenote_format_id( $t_candidatenote_id ) );

		# only send email if the text is not blank, otherwise, it is just recording of time without a comment.
		if ( $p_send_email && !is_blank( $p_candidatenote_text ) ) {
			email_candidatenote_add( $p_candidate_id );
		}
		return $t_candidatenote_id;
	}

	# --------------------
	# Delete a candidatenote
	function candidatenote_delete( $p_candidatenote_id ) {
		$c_candidatenote_id        	= db_prepare_int( $p_candidatenote_id );
		$t_candidate_id            	= candidatenote_get_field( $p_candidatenote_id, 'candidate_id' );
		$t_candidatenote_text_id   	= candidatenote_get_field( $p_candidatenote_id, 'candidatenote_text_id' );
		$t_candidatenote_text_table	= config_get( 'cosmos_candidatenote_text_table' );
		$t_candidatenote_table     	= config_get( 'cosmos_candidatenote_table' );

		# Remove the candidatenote
		$query = "DELETE FROM $t_candidatenote_table
		          	WHERE id='$c_candidatenote_id'";
		db_query( $query );

		# Remove the candidatenote text
		$query = "DELETE FROM $t_candidatenote_text_table
		          	WHERE id='$t_candidatenote_text_id'";
		db_query( $query );

		# log deletion of candidate
		history_log_event_special( $t_candidate_id, BUGNOTE_DELETED, candidatenote_format_id( $p_candidatenote_id ) );

		return true;
	}

	# --------------------
	# delete all candidatenotes associated with the given candidate
	function candidatenote_delete_all( $p_candidate_id ) {
		$c_candidate_id            	= db_prepare_int( $p_candidate_id );
		$t_candidatenote_table     	= config_get( 'cosmos_candidatenote_table' );
		$t_candidatenote_text_table	= config_get( 'cosmos_candidatenote_text_table' );

		# Delete the candidatenote text items
		$query = "SELECT candidatenote_text_id
		          	FROM $t_candidatenote_table
		          	WHERE candidate_id='$c_candidate_id'";
		$result = db_query( $query );
		$candidatenote_count = db_num_rows( $result );
		for ( $i = 0 ; $i < $candidatenote_count ; $i++ ) {
			$row = db_fetch_array( $result );
			$t_candidatenote_text_id = $row['candidatenote_text_id'];

			# Delete the corresponding candidatenote texts
			$query = "DELETE FROM $t_candidatenote_text_table
			          	WHERE id='$t_candidatenote_text_id'";
			db_query( $query );
		}

		# Delete the corresponding candidatenotes
		$query = "DELETE FROM $t_candidatenote_table
		          	WHERE candidate_id='$c_candidate_id'";
		$result = db_query( $query );

		# db_query() errors on failure so:
		return true;
	}


	#===================================
	# Data Access
	#===================================

	# --------------------
	# Get the text associated with the candidatenote
	function candidatenote_get_text( $p_candidatenote_id ) {
		$t_candidatenote_text_id   	= candidatenote_get_field( $p_candidatenote_id, 'candidatenote_text_id' );
		$t_candidatenote_text_table	= config_get( 'cosmos_candidatenote_text_table' );

		# grab the candidatenote text
		$query = "SELECT note
		          	FROM $t_candidatenote_text_table
		          	WHERE id='$t_candidatenote_text_id'";
		$result = db_query( $query );

		return db_result( $result );
	}

	# --------------------
	# Get a field for the given candidatenote
	function candidatenote_get_field( $p_candidatenote_id, $p_field_name ) {
		$c_candidatenote_id   	= db_prepare_int( $p_candidatenote_id );
		$c_field_name   	= db_prepare_string( $p_field_name );
		$t_candidatenote_table 	= config_get( 'cosmos_candidatenote_table' );

		$query = "SELECT $c_field_name
		          	FROM $t_candidatenote_table
		          	WHERE id='$c_candidatenote_id' ";
		$result = db_query( $query, 1 );

		return db_result( $result );
	}

	# --------------------
	# Get latest candidatenote id
	function candidatenote_get_latest_id( $p_candidate_id ) {
		$c_candidate_id   	= db_prepare_int( $p_candidate_id );
		$t_candidatenote_table 	= config_get( 'cosmos_candidatenote_table' );

		$query = "SELECT id
		          	FROM $t_candidatenote_table
		          	WHERE candidate_id='$c_candidate_id'
		          	ORDER by last_modified DESC";
		$result = db_query( $query, 1 );

		return db_result( $result );
	}

	# --------------------
	# Build the candidatenotes array for the given candidate_id filtered by specified $p_user_access_level.
	# Bugnotes are sorted by date_submitted according to 'candidatenote_order' configuration setting.
	#
	# Return BugnoteData class object with raw values from the tables except the field
	# last_modified - it is UNIX_TIMESTAMP.
	function candidatenote_get_all_visible_candidatenotes( $p_candidate_id, $p_user_access_level, $p_user_candidatenote_order, $p_user_candidatenote_limit ) {
		$t_all_candidatenotes	            	= candidatenote_get_all_candidatenotes( $p_candidate_id, $p_user_candidatenote_order, $p_user_candidatenote_limit );
		$t_private_candidatenote_threshold	= config_get( 'private_candidatenote_threshold' );

		$t_private_candidatenote_visible = access_compare_level( $p_user_access_level, config_get( 'private_candidatenote_threshold' ) );
		$t_time_tracking_visible = access_compare_level( $p_user_access_level, config_get( 'time_tracking_view_threshold' ) );

		$t_candidatenotes = array();
		foreach ( $t_all_candidatenotes as $t_note_index => $t_candidatenote ) {
			if ( $t_private_candidatenote_visible || ( VS_PUBLIC == $t_candidatenote->view_state ) ) {
				# If the access level specified is not enough to see time tracking information
				# then reset it to 0.
				if ( !$t_time_tracking_visible ) {
					$t_candidatenote->time_tracking = 0;
				}

				$t_candidatenotes[$t_note_index] = $t_candidatenote;
			}
		}

		return $t_candidatenotes;
	}

	# --------------------
	# Build the candidatenotes array for the given candidate_id. Bugnotes are sorted by date_submitted
	# according to 'candidatenote_order' configuration setting.
	# Return BugnoteData class object with raw values from the tables except the field
	# last_modified - it is UNIX_TIMESTAMP.
	# The data is not filtered by VIEW_STATE !!
	function candidatenote_get_all_candidatenotes( $p_candidate_id, $p_user_candidatenote_order, $p_user_candidatenote_limit ) {
		global $g_cache_candidatenotes;

		if ( !isset( $g_cache_candidatenotes ) )  {
			$g_cache_candidatenotes = array();
		}

		# the cache should be aware of the sorting order
		if ( !isset( $g_cache_candidatenotes[$p_candidate_id][$p_user_candidatenote_order] ) )  {
			$c_candidate_id            	= db_prepare_int( $p_candidate_id );
			$t_candidatenote_table     	= config_get( 'cosmos_candidatenote_table' );
			$t_candidatenote_text_table	= config_get( 'cosmos_candidatenote_text_table' );

			if ( 0 == $p_user_candidatenote_limit ) {
				## Show all candidatenotes
				$t_candidatenote_limit = -1;
				$t_candidatenote_offset = -1;
			} else {
				## Use offset only if order is ASC to get the last candidatenotes
				if ( 'ASC' == $p_user_candidatenote_order ) {
					$result = db_query( "SELECT COUNT(*) AS row_count FROM $t_candidatenote_table WHERE candidate_id = '$c_candidate_id'" );
					$row    = db_fetch_array( $result );

					$t_candidatenote_offset = $row['row_count'] - $p_user_candidatenote_limit;
				} else {
					$t_candidatenote_offset = -1;
				}

				$t_candidatenote_limit = $p_user_candidatenote_limit;
			}

			# sort by candidatenote id which should be more accurate than submit date, since two candidatenotes
			# may be submitted at the same time if submitted using a script (eg: COSMOSConnect).
			$query = "SELECT b.*, t.note
			          	FROM      $t_candidatenote_table b
			          	LEFT JOIN $t_candidatenote_text_table t ON b.candidatenote_text_id = t.id
			          	WHERE b.candidate_id = '$c_candidate_id'
			          	ORDER BY b.id $p_user_candidatenote_order";
			$t_candidatenotes = array();

			# BUILD candidatenotes array
			$result	= db_query( $query, $t_candidatenote_limit, $t_candidatenote_offset );
			$count 	= db_num_rows( $result );
			for ( $i=0; $i < $count; $i++ ) {
				$row = db_fetch_array( $result );

				$t_candidatenote = new BugnoteData;

				$t_candidatenote->id            = $row['id'];
				$t_candidatenote->candidate_id        = $row['candidate_id'];
				$t_candidatenote->note          = $row['note'];
				$t_candidatenote->view_state    = $row['view_state'];
				$t_candidatenote->reporter_id   = $row['reporter_id'];
				$t_candidatenote->date_submitted = db_unixtimestamp( $row['date_submitted'] );
				$t_candidatenote->last_modified = db_unixtimestamp( $row['last_modified'] );
				$t_candidatenote->note_type     = $row['note_type'];
				$t_candidatenote->note_attr     = $row['note_attr'];
				$t_candidatenote->time_tracking = $row['time_tracking'];

				$t_candidatenotes[] = $t_candidatenote;
			}
			$g_cache_candidatenotes[$p_candidate_id][$p_user_candidatenote_order] = $t_candidatenotes;
		}

		return $g_cache_candidatenotes[$p_candidate_id][$p_user_candidatenote_order];
	}

	#===================================
	# Data Modification
	#===================================

	# --------------------
	# Update the time_tracking field of the candidatenote
	function candidatenote_set_time_tracking( $p_candidatenote_id, $p_time_tracking ) {
		$c_candidatenote_id            = db_prepare_int( $p_candidatenote_id );
		$c_candidatenote_time_tracking = db_prepare_time( $p_time_tracking );
		$t_candidatenote_table         = config_get( 'cosmos_candidatenote_table' );

		$query = "UPDATE $t_candidatenote_table
				SET time_tracking = '$c_candidatenote_time_tracking'
				WHERE id='$c_candidatenote_id'";
		db_query( $query );

		# db_query() errors if there was a problem so:
		return true;
	}

	# --------------------
	# Update the last_modified field of the candidatenote
	function candidatenote_date_update( $p_candidatenote_id ) {
		$c_candidatenote_id		= db_prepare_int( $p_candidatenote_id );
		$t_candidatenote_table	= config_get( 'cosmos_candidatenote_table' );

		$query = "UPDATE $t_candidatenote_table
		          	SET last_modified=" . db_now() . "
		          	WHERE id='$c_candidatenote_id'";
		db_query( $query );

		# db_query() errors if there was a problem so:
		return true;
	}

	# --------------------
	# Set the candidatenote text
	function candidatenote_set_text( $p_candidatenote_id, $p_candidatenote_text ) {
		$c_candidatenote_text	     	= db_prepare_string( $p_candidatenote_text );
		$t_candidate_id            	= candidatenote_get_field( $p_candidatenote_id, 'candidate_id' );
		$t_candidatenote_text_id   	= candidatenote_get_field( $p_candidatenote_id, 'candidatenote_text_id' );
		$t_candidatenote_text_table	= config_get( 'cosmos_candidatenote_text_table' );

		$query = "UPDATE $t_candidatenote_text_table
		          	SET note='$c_candidatenote_text'
		          	WHERE id='$t_candidatenote_text_id'";
		db_query( $query );

		# updated the last_updated date
		candidatenote_date_update( $p_candidatenote_id );

		# log new candidatenote
		history_log_event_special( $t_candidate_id, BUGNOTE_UPDATED, candidatenote_format_id( $p_candidatenote_id ) );

		return true;
	}

	# --------------------
	# Set the view state of the candidatenote
	function candidatenote_set_view_state( $p_candidatenote_id, $p_private ) {
		$c_candidatenote_id	= db_prepare_int( $p_candidatenote_id );
		$t_candidate_id    	= candidatenote_get_field( $p_candidatenote_id, 'candidate_id' );

		if ( $p_private ) {
			$t_view_state = VS_PRIVATE;
		} else {
			$t_view_state = VS_PUBLIC;
		}

		$t_candidatenote_table = config_get( 'cosmos_candidatenote_table' );

		# update view_state
		$query = "UPDATE $t_candidatenote_table
		          	SET view_state='$t_view_state'
		          	WHERE id='$c_candidatenote_id'";
		db_query( $query );

		history_log_event_special( $t_candidate_id, BUGNOTE_STATE_CHANGED, candidatenote_format_id( $t_view_state ), $p_candidatenote_id );

		return true;
	}


	#===================================
	# Other
	#===================================

	# --------------------
	# Pad the candidatenote id with the appropriate number of zeros for printing
	function candidatenote_format_id( $p_candidatenote_id ) {
		$t_padding	= config_get( 'display_candidatenote_padding' );

		return str_pad( $p_candidatenote_id, $t_padding, '0', STR_PAD_LEFT );
	}


	#===================================
	# Bugnote Stats
	#===================================

	# --------------------
	# Returns an array of candidatenote stats
	# $p_from - Starting date (yyyy-mm-dd) inclusive, if blank, then ignored.
	# $p_to - Ending date (yyyy-mm-dd) inclusive, if blank, then ignored.
	function candidatenote_stats_get_events_array( $p_candidate_id, $p_from, $p_to ) {
		$c_candidate_id = db_prepare_int( $p_candidate_id );
		$c_from = db_prepare_date( $p_from );
		$c_to = db_prepare_date( $p_to );

		$t_user_table = config_get( 'cosmos_user_table' );
		$t_candidatenote_table = config_get( 'cosmos_candidatenote_table' );

		if ( !is_blank( $c_from ) ) {
			$t_from_where = " AND bn.date_submitted >= '$c_from 00:00:00'";
		} else {
			$t_from_where = '';
		}

		if ( !is_blank( $c_to ) ) {
			$t_to_where = " AND bn.date_submitted <= '$c_to 23:59:59'";
		} else {
			$t_to_where = '';
		}

		$t_results = array();

		$query = "SELECT username, SUM(time_tracking) AS sum_time_tracking
				FROM $t_user_table u, $t_candidatenote_table bn
				WHERE u.id = bn.reporter_id AND
				bn.candidate_id = '$c_candidate_id'
				$t_from_where $t_to_where
			GROUP BY u.id, u.username";

		$result = db_query( $query );

		while ( $row = db_fetch_array( $result ) ) {
			$t_results[] = $row;
		}

		return $t_results;
	}

	# --------------------
	# Returns an array of candidatenote stats
	# $p_from - Starting date (yyyy-mm-dd) inclusive, if blank, then ignored.
	# $p_to - Ending date (yyyy-mm-dd) inclusive, if blank, then ignored.
	function candidatenote_stats_get_project_array( $p_project_id, $p_from, $p_to, $p_cost ) {
		$c_project_id = db_prepare_int( $p_project_id );
		$c_to = db_prepare_date( $p_to );
		$c_from = db_prepare_date( $p_from );
		$c_cost = db_prepare_double( $p_cost );

		// MySQL
		$t_candidate_table = config_get( 'cosmos_candidate_table' );
		$t_user_table = config_get( 'cosmos_user_table' );
		$t_candidatenote_table = config_get( 'cosmos_candidatenote_table' );

		if ( !is_blank( $c_from ) ) {
			$t_from_where = " AND bn.date_submitted >= '$c_from 00:00:00'";
		} else {
			$t_from_where = '';
		}

		if ( !is_blank( $c_to ) ) {
			$t_to_where = " AND bn.date_submitted <= '$c_to 23:59:59'";
		} else {
			$t_to_where = '';
		}

		if ( ALL_PROJECTS != $c_project_id ) {
			$t_project_where = " AND b.project_id = '$c_project_id' AND bn.candidate_id = b.id ";
		} else {
			$t_project_where = '';
		}

		$t_results = array();

		$query = "SELECT username, summary, bn.candidate_id, SUM(time_tracking) AS sum_time_tracking
			FROM $t_user_table u, $t_candidatenote_table bn, $t_candidate_table b
			WHERE u.id = bn.reporter_id AND bn.time_tracking != 0 AND bn.candidate_id = b.id
			$t_project_where $t_from_where $t_to_where
			GROUP BY bn.candidate_id, u.id, u.username, b.summary
			ORDER BY bn.candidate_id";

		$result = db_query( $query );

		$t_cost_min = $c_cost / 60;

		while ( $row = db_fetch_array( $result ) ) {
			$t_total_cost = $t_cost_min * $row['sum_time_tracking'];
			$row['cost'] = $t_total_cost;
			$t_results[] = $row;
		}

		return $t_results;
	}
?>
