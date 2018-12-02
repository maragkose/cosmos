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
	# $Id: sponsorship_api.php,v 1.6.16.1 2007-10-13 22:35:43 giallu Exp $
	# --------------------------------------------------------

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'email_api.php' );
	require_once( $t_core_dir . 'candidate_api.php' );
	require_once( $t_core_dir . 'history_api.php' );

	#=========================================
	# Sponsorship Data Structure Definition
	#===================================
	class SponsorshipData {
		var $id = 0;
		var $candidate_id = 0;
		var $user_id = 0;
		var $amount = 0;
		var $logo = '';
		var $url = '';
		var $paid = 0;

		var $date_submitted = '';
		var $last_updated = '';
	}

	#########################################
	# SECURITY NOTE: cache globals are initialized here to prevent them
	#   being spoofed if register_globals is turned on

	$g_cache_sponsorships = array();

	# --------------------
	# Cache a sponsorship row if necessary and return the cached copy
	#  If the second parameter is true (default), trigger an error
	#  if the sponsorship can't be found.  If the second parameter is
	#  false, return false if the sponsorship can't be found.
	function sponsorship_cache_row( $p_sponsorship_id, $p_trigger_errors = true ) {
		global $g_cache_sponsorships;

		$c_sponsorship_id = db_prepare_int( $p_sponsorship_id );
		$t_sponsorship_table = config_get( 'cosmos_sponsorship_table' );

		if ( isset( $g_cache_sponsorships[$c_sponsorship_id] ) ) {
			return $g_cache_sponsorships[$c_sponsorship_id];
		}

		$query = "SELECT *
				  FROM $t_sponsorship_table
				  WHERE id='$c_sponsorship_id'";
		$result = db_query( $query );

		if ( 0 == db_num_rows( $result ) ) {
			$g_cache_sponsorships[$c_sponsorship_id] = false;

			if ( $p_trigger_errors ) {
				error_parameters( $p_sponsorship_id );
				trigger_error( ERROR_SPONSORSHIP_NOT_FOUND, ERROR );
			} else {
				return false;
			}
		}

		$row = db_fetch_array( $result );
		$row['date_submitted']	= db_unixtimestamp( $row['date_submitted'] );
		$row['last_updated']	= db_unixtimestamp( $row['last_updated'] );
		$g_cache_sponsorships[$c_sponsorship_id] = $row;

		return $row;
	}

	# --------------------
	# Clear the sponsorship cache (or just the given id if specified)
	function sponsorship_clear_cache( $p_sponsorship_id = null ) {
		global $g_cache_sponsorships;

		if ( $p_sponsorship_id === null ) {
			$g_cache_sponsorships = array();
		} else {
			$c_sponsorship_id = db_prepare_int( $p_sponsorship_id );
			unset( $g_cache_sponsorships[$c_sponsorship_id] );
		}
	}

	# --------------------
	# check to see if sponsorship exists by id
	# return true if it does, false otherwise
	function sponsorship_exists( $p_sponsorship_id ) {
		return sponsorship_cache_row( $p_sponsorship_id, false ) !== false;
	}

	# --------------------
	# return false if not found
	# otherwise returns sponsorship id
	function sponsorship_get_id( $p_candidate_id, $p_user_id = null ) {
		$c_candidate_id = db_prepare_int( $p_candidate_id );

		if ( $p_user_id === null ) {
			$c_user_id = auth_get_current_user_id();
		} else {
			$c_user_id = db_prepare_int( $p_user_id );
		}

		$t_sponsorship_table = config_get( 'cosmos_sponsorship_table' );

		$query = "SELECT id FROM $t_sponsorship_table WHERE candidate_id = '$c_candidate_id' AND user_id = '$c_user_id'";
		$t_result = db_query( $query, 1 );

		if ( db_num_rows( $t_result ) == 0 ) {
			return false;
		}

		$row = db_fetch_array( $t_result );

		return (integer)$row['id'];
	}

	# --------------------
	# get information about a sponsorship given its id
	function sponsorship_get( $p_sponsorship_id ) {
		$row = sponsorship_cache_row( $p_sponsorship_id );

		$t_sponsorship_data = new SponsorShipData;
		$t_row_keys = array_keys( $row );
		$t_vars = get_object_vars( $t_sponsorship_data );

		# Check each variable in the class
		foreach ( $t_vars as $var => $val ) {
			# If we got a field from the DB with the same name
			if ( in_array( $var, $t_row_keys, true ) ) {
				# Store that value in the object
				$t_sponsorship_data->$var = $row[$var];
			}
		}

		return $t_sponsorship_data;
	}

	# --------------------
	# Return an array of Sponsorships associated with the specified candidate id
        function sponsorship_get_all_ids( $p_candidate_id ) {
		$c_candidate_id = db_prepare_int( $p_candidate_id );

		$t_sponsorship_table = config_get( 'cosmos_sponsorship_table' );

		$query = "SELECT id FROM $t_sponsorship_table
				WHERE candidate_id = '$c_candidate_id'";
		$t_result = db_query( $query );

		$t_sponsorship_ids = array();
		while ( $row = db_fetch_array( $t_result ) ) {
			$t_sponsorship_ids[] = $row['id'];
		}
		return $t_sponsorship_ids;
	}

	# --------------------
	# Get the amount of sponsorships for the specified id(s)
	# handles the case where $p_sponsorship_id is an array or an id.
	function sponsorship_get_amount( $p_sponsorship_id ) {
		if ( is_array( $p_sponsorship_id ) ) {
			$t_total = 0;

			foreach( $p_sponsorship_id as $id ) {
				$t_total += sponsorship_get_amount( $id );
			}

			return $t_total;
		} else {
			$sponsorship = sponsorship_get( $p_sponsorship_id );
			return $sponsorship->amount;
		}
	}

	# --------------------
	# Return the currency used for all sponsorships
	function sponsorship_get_currency() {
		return config_get( 'sponsorship_currency' );
	}

	# --------------------
	# This function should return the string in a globalized format.
	function sponsorship_format_amount( $amount ) {
		# @@@ add some currency formating in the future
		$t_currency = sponsorship_get_currency();
		return "$t_currency $amount";
	}

	# --------------------
	# Update candidate to reflect sponsorship change
	# This is to be called after adding/updating/deleting sponsorships
	function sponsorship_update_candidate( $p_candidate_id ) {
		$t_total_amount = sponsorship_get_amount( sponsorship_get_all_ids( $p_candidate_id ) );
		candidate_set_field( $p_candidate_id, 'sponsorship_total', $t_total_amount );
		candidate_update_date( $p_candidate_id );
	}

	# --------------------
	# if sponsorship contains a non-zero id, then update the corresponding record.
	# if sponsorship contains a zero id, search for candidate_id/user_id, if found, then update the entry
	# otherwise add a new entry
	function sponsorship_set( $p_sponsorship ) {
		$t_min_sponsorship = config_get( 'minimum_sponsorship_amount' );
		if ( $p_sponsorship->amount < $t_min_sponsorship ) {
			error_parameters( $p_sponsorship->amount, $t_min_sponsorship );
			trigger_error( ERROR_SPONSORSHIP_AMOUNT_TOO_LOW, ERROR );
		}

		# if id == 0, check if the specified user is already sponsoring the candidate, if so, overwrite
		if ( $p_sponsorship->id == 0 ) {
			$t_sponsorship_id = sponsorship_get_id( $p_sponsorship->candidate_id, $p_sponsorship->user_id );
			if ( $t_sponsorship_id !== false ) {
				$p_sponsorship->id = $t_sponsorship_id;
			}
		}

		$t_sponsorship_table = config_get( 'cosmos_sponsorship_table' );

		$c_id = db_prepare_int( $p_sponsorship->id );
		$c_candidate_id = db_prepare_int( $p_sponsorship->candidate_id );
		$c_user_id = db_prepare_int( $p_sponsorship->user_id );
		$c_amount = db_prepare_int( $p_sponsorship->amount );
		$c_logo = db_prepare_string( $p_sponsorship->logo );
		$c_url = db_prepare_string( $p_sponsorship->url );
		$c_now = db_now();

		# if new sponsorship
		if ( $c_id == 0 ) {
			# Insert
			$query = "INSERT INTO $t_sponsorship_table
				    ( candidate_id, user_id, amount, logo, url, date_submitted, last_updated )
				  VALUES
				    ( '$c_candidate_id', '$c_user_id', '$c_amount', '$c_logo', '$c_url', $c_now, $c_now )";

			db_query( $query );
			$t_sponsorship_id = db_insert_id( $t_sponsorship_table );

			history_log_event_special( $c_candidate_id, BUG_ADD_SPONSORSHIP, $c_user_id, $c_amount );
		} else {
			$t_old_amount = sponsorship_get_amount( $c_id );
			$t_sponsorship_id = $c_id;

			if ( $t_old_amount == $c_amount ) {
				return $t_sponsorship_id;
			}

			# Update
			$query = "UPDATE $t_sponsorship_table
					SET	candidate_id = '$c_candidate_id',
						user_id = '$c_user_id',
						amount = '$c_amount',
						logo = '$c_logo',
						url = '$c_url',
						last_updated = $c_now
					WHERE	id = '$c_id'";

			sponsorship_clear_cache( $c_id );

			db_query( $query );

			history_log_event_special( $c_candidate_id, BUG_UPDATE_SPONSORSHIP, $c_user_id, $c_amount );
		}

		sponsorship_update_candidate( $c_candidate_id );
		candidate_monitor( $c_candidate_id, $c_user_id );

		if ( $c_id == 0 ) {
			email_sponsorship_added( $c_candidate_id );
		} else {
			email_sponsorship_updated( $c_candidate_id );
		}

		return $t_sponsorship_id;
	}

	# --------------------
	# delete a sponsorship given its id
	# id can be an array of ids or just an id.
	function sponsorship_delete( $p_sponsorship_id ) {
		# handle the case of array of ids
		if ( is_array( $p_sponsorship_id ) ) {
			foreach( $p_sponsorship_id as $id ) {
				sponsorship_delete( $id );
			}

			return;
		}

		$c_sponsorship_id = db_prepare_int( $p_sponsorship_id );

		$t_sponsorship = sponsorship_get( $c_sponsorship_id );

		$t_sponsorship_table = config_get( 'cosmos_sponsorship_table' );

		# Delete the candidate entry
		$query = "DELETE FROM $t_sponsorship_table
				  WHERE id='$c_sponsorship_id'";
		db_query( $query );

		sponsorship_clear_cache( $p_sponsorship_id );

		history_log_event_special( $t_sponsorship->candidate_id, BUG_DELETE_SPONSORSHIP, $t_sponsorship->user_id, $t_sponsorship->amount );
		sponsorship_update_candidate( $t_sponsorship->candidate_id );

		email_sponsorship_deleted( $t_sponsorship->candidate_id );
	}

	# --------------------
	# updates the paid field
	function sponsorship_update_paid( $p_sponsorship_id, $p_paid ) {
		$c_sponsorship_id = db_prepare_int( $p_sponsorship_id );
		$t_sponsorship = sponsorship_get( $c_sponsorship_id );

		$c_paid = db_prepare_int( $p_paid );

		$t_sponsorship_table = config_get( 'cosmos_sponsorship_table' );

		$query = "UPDATE $t_sponsorship_table
				  SET last_updated= " . db_now() . ", paid=$c_paid
				  WHERE id='$c_sponsorship_id'";
		db_query( $query );

		history_log_event_special( $t_sponsorship->candidate_id, BUG_PAID_SPONSORSHIP, $t_sponsorship->user_id, $p_paid );
		sponsorship_clear_cache( $p_sponsorship_id );

		return true;
	}

	# --------------------
	# updates the last_updated field
	function sponsorship_update_date( $p_sponsorship_id ) {
		$c_sponsorship_id = db_prepare_int( $p_sponsorship_id );

		$t_sponsorship_table = config_get( 'cosmos_sponsorship_table' );

		$query = "UPDATE $t_sponsorship_table
				  SET last_updated= " . db_now() . "
				  WHERE id='$c_sponsorship_id'";
		db_query( $query );

		sponsorship_clear_cache( $p_sponsorship_id );

		return true;
	}
?>
