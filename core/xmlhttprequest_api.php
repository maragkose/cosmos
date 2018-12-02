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
	# $Id: xmlhttprequest_api.php,v 1.3.2.1 2007-10-13 22:35:51 giallu Exp $
	# --------------------------------------------------------

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'candidate_api.php' );
	require_once( $t_core_dir . 'profile_api.php' );
	require_once( $t_core_dir . 'logging_api.php' );
	require_once( $t_core_dir . 'projax_api.php' );

	### XmlHttpRequest API ###

	function xmlhttprequest_issue_reporter_combobox() {
		$f_candidate_id = gpc_get_int( 'issue_id' );

		access_ensure_candidate_level( config_get( 'update_candidate_threshold' ), $f_candidate_id );

		$t_reporter_id = candidate_get_field( $f_candidate_id, 'reporter_id' );
		$t_project_id = candidate_get_field( $f_candidate_id, 'project_id' );

		echo '<select name="reporter_id">';
		print_reporter_option_list( $t_reporter_id, $t_project_id );
		echo '</select>';
	}

	/**
	 * Print a generic combobox with a list of users above a given access level.
	 */
	function xmlhttprequest_user_combobox() {
		$f_user_id = gpc_get_int( 'user_id' );
		$f_user_access = gpc_get_int( 'access_level' );
		
		echo '<select name="user_id">';
		print_user_option_list( $f_user_id, ALL_PROJECTS, $f_user_access );
		echo '</select>';
	}

	# ---------------
	# Echos a serialized list of platforms starting with the prefix specified in the $_POST
	function xmlhttprequest_platform_get_with_prefix() {
		$f_platform = gpc_get_string( 'platform' );

		$t_unique_entries = profile_get_field_all_for_user( 'platform' );
		$t_matching_entries = projax_array_filter_by_prefix( $t_unique_entries, $f_platform );

		echo projax_array_serialize_for_autocomplete( $t_matching_entries );
	}

	# ---------------
	# Echos a serialized list of OSes starting with the prefix specified in the $_POST
	function xmlhttprequest_os_get_with_prefix() {
		$f_os = gpc_get_string( 'os' );

		$t_unique_entries = profile_get_field_all_for_user( 'os' );
		$t_matching_entries = projax_array_filter_by_prefix( $t_unique_entries, $f_os );

		echo projax_array_serialize_for_autocomplete( $t_matching_entries );
	}

	# ---------------
	# Echos a serialized list of OS Versions starting with the prefix specified in the $_POST
	function xmlhttprequest_os_build_get_with_prefix() {
		$f_os_build = gpc_get_string( 'os_build' );

		$t_unique_entries = profile_get_field_all_for_user( 'os_build' );
		$t_matching_entries = projax_array_filter_by_prefix( $t_unique_entries, $f_os_build );

		echo projax_array_serialize_for_autocomplete( $t_matching_entries );
	}
?>
