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
	# $Id: db_table_names_inc.php,v 1.8.16.1 2007-10-13 22:34:54 giallu Exp $
	# --------------------------------------------------------

	# Load all the table names for use by the upgrade statements
	$t_candidate_file_table				= config_get_global( 'cosmos_candidate_file_table' );
	$t_candidate_history_table			= config_get_global( 'cosmos_candidate_history_table' );
	$t_candidate_monitor_table			= config_get_global( 'cosmos_candidate_monitor_table' );
	$t_candidate_relationship_table		= config_get_global( 'cosmos_candidate_relationship_table' );
	$t_candidate_table					= config_get_global( 'cosmos_candidate_table' );
	$t_candidate_text_table				= config_get_global( 'cosmos_candidate_text_table' );
	$t_candidatenote_table				= config_get_global( 'cosmos_candidatenote_table' );
	$t_candidatenote_text_table			= config_get_global( 'cosmos_candidatenote_text_table' );
	$t_news_table					= config_get_global( 'cosmos_news_table' );
	$t_project_category_table		= config_get_global( 'cosmos_project_category_table' );
	$t_project_file_table			= config_get_global( 'cosmos_project_file_table' );
	$t_project_table				= config_get_global( 'cosmos_project_table' );
	$t_project_user_list_table		= config_get_global( 'cosmos_project_user_list_table' );
	$t_project_version_table		= config_get_global( 'cosmos_project_version_table' );
	$t_user_table					= config_get_global( 'cosmos_user_table' );
	$t_user_profile_table			= config_get_global( 'cosmos_user_profile_table' );
	$t_user_pref_table				= config_get_global( 'cosmos_user_pref_table' );
	$t_user_print_pref_table		= config_get_global( 'cosmos_user_print_pref_table' );
	$t_custom_field_project_table	= config_get_global( 'cosmos_custom_field_project_table' );
	$t_custom_field_table      		= config_get_global( 'cosmos_custom_field_table' );
	$t_custom_field_string_table	= config_get_global( 'cosmos_custom_field_string_table' );
	$t_upgrade_table				= config_get_global( 'cosmos_upgrade_table' );
	$t_filters_table				= config_get_global( 'cosmos_filters_table' );
	$t_tokens_table					= config_get_global( 'cosmos_tokens_table' );
	$t_project_hierarchy_table		= config_get_global( 'cosmos_project_hierarchy_table' );
	$t_config_table					= config_get_global( 'cosmos_config_table' );
?>
