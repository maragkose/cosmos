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
	# $Id: config_defaults_inc.php,v 1.1.1.1 2009/01/08 07:30:19 chirag Exp $
	# --------------------------------------------------------


	###########################################################################
	# CONFIGURATION VARIABLES
	###########################################################################

	# config_defaults_inc.php

	# This file should not be changed. If you want to override any of the values
	# defined here, define them in a file called config_inc.php, which will
	# be loaded after this file.

	# In general a value of OFF means the feature is disabled and ON means the
	# feature is enabled.  Any other cases will have an explanation.

	# For more details see http://manual.cosmosbt.org/

	################################
	# COSMOS Database Settings
	################################

	# --- database variables ---------

	# set these values to match your setup

	# hostname should be either a hostname or connection string to supply to adodb.
	# For example, if you would like to connect to a mysql server on the local machine,
	# set hostname to 'localhost', and db_type to 'mysql'.
	# If you need to supply a port to connect to, set hostname as 'localhost:3306'.
	$g_hostname			= 'localhost';
	$g_db_username			= 'root';
	$g_db_password			= '';
	$g_database_name		= 'candidatetracker';
	$g_db_schema			= ''; // used in the case of db2

	# Supported types: 'mysql' or 'mysqli' for MySQL, 'pgsql' for PostgreSQL,
	# 'mssql' for MS SQL Server, 'oci8' for Oracle, and 'db2' for DB2.
	$g_db_type				= 'mysql';

	############################
	# COSMOS Path Settings
	############################

	# --- path variables --------------

	# path to your installation as seen from the web browser
	# requires trailing /
	if ( isset ( $_SERVER['PHP_SELF'] ) ) {
		$t_protocol = 'http';
		if ( isset( $_SERVER['HTTPS'] ) && ( strtolower( $_SERVER['HTTPS'] ) != 'off' ) ) {
			$t_protocol = 'https';
		}

		# $_SERVER['SERVER_PORT'] is not defined in case of php-cgi.exe
		if ( isset( $_SERVER['SERVER_PORT'] ) ) {
			$t_port = ':' . $_SERVER['SERVER_PORT'];
			if ( ( ':80' == $t_port && 'http' == $t_protocol )
			  || ( ':443' == $t_port && 'https' == $t_protocol )) {
				$t_port = '';
			}
		} else {
			$t_port = '';
		}

		if ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) { // Support ProxyPass
			$t_hosts = split( ',', $_SERVER['HTTP_X_FORWARDED_HOST'] );
			$t_host = $t_hosts[0];
		} else if ( isset( $_SERVER['HTTP_HOST'] ) ) {
			$t_host = $_SERVER['HTTP_HOST'];
		} else if ( isset( $_SERVER['SERVER_NAME'] ) ) {
			$t_host = $_SERVER['SERVER_NAME'] . $t_port;
		} else if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
			$t_host = $_SERVER['SERVER_ADDR'] . $t_port;
		} else {
			$t_host = 'www.example.com';
		}
		
		$t_path = dirname( strip_tags( $_SERVER['PHP_SELF'] ) );

		# Remove /api/soap/ from the path to handle the case where the config_defaults_inc.php is included from the
		# soap api.
		$t_soap_api_path = '/api/soap';
		$t_soap_api_path_pos = strpos( strtolower( $t_path ), $t_soap_api_path );
		if ( $t_soap_api_path_pos !== false ) {
			if ( $t_soap_api_path_pos == ( strlen( $t_path ) - strlen( $t_soap_api_path ) ) ) {
				$t_path = substr( $t_path, 0, $t_soap_api_path_pos );
			}
		}

		if ( '/' == $t_path || '\\' == $t_path ) {
			$t_path = '';
		}

		$g_path	= $t_protocol . '://' . $t_host . $t_path.'/';
	} else {
		$g_path	= 'http://www.example.com/cosmos/';
	}

	# path to your images directory (for icons)
	# requires trailing /
	$g_icon_path			= '%path%images/';

	# absolute path to your installation.  Requires trailing / or \
	# Symbolic links are allowed since release 0.17.3
	$g_absolute_path		= dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

	# absolute patch to your core files. The default is usually OK,
	# unless you moved the 'core' directory out of your webroot (recommended).
	$g_core_path			= $g_absolute_path . 'core' . DIRECTORY_SEPARATOR;

	# Used to link to manual for User Documentation.
	$g_manual_url = '';

	#############################
	# Web Server
	#############################

	# Using Microsoft Internet Information Server (IIS)
	if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) { # SERVER_SOFTWARE not defined in case of php-cgi.exe
		$g_use_iis = ( strstr( $_SERVER['SERVER_SOFTWARE'], 'IIS' ) !== false ) ? ON : OFF;
	} else {
		$g_use_iis = OFF;
	}

	# Session handler.  Possible values:
	#   'php' -> Default PHP filesystem sessions
	#   'adodb' -> Database storage sessions
	#   'memcached' -> Memcached storage sessions
	$g_session_handler = 'php';

	# Session save path.  If false, uses default value as set by session handler.
	$g_session_save_path = false;

	#############################
	# Configuration Settings
	#############################

	# The following list of variables should never be in the database.
	# These patterns will be concatenated and used as a regular expression
	# to bypass the database lookup and look here for appropriate global settings.
	$g_global_settings = array(
		'_table$', 'cookie', '^db_', 'hostname', 'database_name', 'session_handler',
		'_path$', 'use_iis', 'language', 'use_javascript', 'display_errors', 'stop_on_errors', 'login_method', '_file$',
		'anonymous', 'content_expire', 'html_valid_tags', 'custom_headers', 'rss_key_seed'
	);

	#############################
	# Signup and Lost Password
	#############################

	# --- signup ----------------------

	# allow users to signup for their own accounts.
	# Mail settings must be correctly configured in order for this to work
	$g_allow_signup			= ON;

	# Max. attempts to login using a wrong password before lock the account.
	# When locked, it's required to reset the password (lost password)
	# Value resets to zero at each successfully login
	# Set to OFF to disable this control
	$g_max_failed_login_count = OFF;

	# access level required to be notified when a new user has been created using the "signup form"
	$g_notify_new_user_created_threshold_min = ADMINISTRATOR;
	$g_able_to_edit_field = ADMINISTRATOR;

	# if ON users will be sent their password when reset.
	# if OFF the password will be set to blank. If set to ON, mail settings must be
	# correctly configured.
	$g_send_reset_password	= ON;

	# String used to generate the confirm_hash for the 'lost password' feature and captcha code for 'signup'
	# ATTENTION: CHANGE IT TO WHATEVER VALUE YOU PREFER
	$g_password_confirm_hash_magic_string = 'blowfish';

	# --- captcha image ---------------

	# use captcha image to validate subscription it requires GD library installed
	$g_signup_use_captcha	= ON;

	# absolute path (with trailing slash!) to folder which contains your TrueType-Font files
	# used to create the captcha image and since 0.19.3 for the Relationship Graphs
	#$g_system_font_folder	= 'c:/winnt/fonts/';
	$g_system_font_folder	= '/usr/share/X11/fonts/TTF';

	# font name used to create the captcha image. i.e. arial.ttf
	# (the font file has to exist in the system_font_folder)
	$g_font_per_captcha	= 'arial.ttf';

	# --- lost password -------------

	#  Setting to disable the 'lost your password' feature.
	$g_lost_password_feature = ON;

	# Max. simultaneous requests of 'lost password'
	# When this value is reached, it's no longer possible to request new password reset
	# Value resets to zero at each successfully login
	$g_max_lost_password_in_progress_count = 3;

	#############################
	# COSMOS Email Settings
	#############################

	# --- email variables -------------
	$g_administrator_email	= '';
	$g_webmaster_email		= 'youremail@gmail.com';

	# the sender email, part of 'From: ' header in emails
 	$g_from_email			= 'youremail@gmail.com';
	
	# the sender name, part of 'From: ' header in emails
	$g_from_name			= 'youremail@gmail.com';
	
	# following user will always get the invitations for all interviews 
	# scheduled
	$g_interview_organiser_email = 'youremail@gmail.com';

	# second interview duration in seconds. Used in format_end function
	# default is 3600 (1 hour)
  	$g_sec_interview_duration = 1800;	
	# the return address for bounced mail
	$g_return_path_email	= 'youremail@gmail.com';

	# allow email notification
	#  note that if this is disabled, sign-up and password reset messages will
	#  not be sent.
	$g_enable_email_notification	= ON;

	# The following two config options allow you to control who should get email
	# notifications on different actions/statuses.  The first option (default_notify_flags)
	# sets the default values for different user categories.  The user categories
	# are:
	#
	#      'reporter': the reporter of the candidate
	#       'handler': the handler of the candidate
	#       'monitor': users who are monitoring a candidate
	#      'candidatenotes': users who have added a candidatenote to the candidate
	# 'threshold_max': all users with access <= max
	# 'threshold_min': ..and with access >= min
	#
	# The second config option (notify_flags) sets overrides for specific actions/statuses.
	# If a user category is not listed for an action, the default from the config
	# option above is used.  The possible actions are:
	#
	#             'new': a new candidate has been added
 	#           'owner': a candidate has been assigned to a new owner
	#        'reopened': a candidate has been reopened
 	#         'deleted': a candidate has been deleted
	#         'updated': a candidate has been updated
	#         'candidatenote': a candidatenote has been added to a candidate
	#         'sponsor': sponsorship has changed on this candidate
	#        'relation': a relationship has changed on this candidate
	#        '<status>': eg: 'resolved', 'closed', 'feedback', 'acknowledged', ...etc.
	#                     this list corresponds to $g_status_enum_string

	#
	# If you wanted to have all developers get notified of new candidates you might add
	# the following lines to your config file:
	#
	# $g_notify_flags['new']['threshold_min'] = DEVELOPER;
	# $g_notify_flags['new']['threshold_max'] = DEVELOPER;
	#
	# You might want to do something similar so all managers are notified when a
	# candidate is closed.  If you didn't want reporters to be notified when a candidate is
	# closed (only when it is resolved) you would use:
	#
	$g_notify_flags['closed']['reporter'] = OFF;

	$g_default_notify_flags	= array('reporter'	=> ON,
									'handler'	=> ON,
									'monitor'	=> ON,
									'candidatenotes'	=> ON,
									'threshold_min'	=> NOBODY,
									'threshold_max' => NOBODY);

	# We don't need to send these notifications on new candidates
	# (see above for info on this config option)
	#@@@ (though I'm not sure they need to be turned off anymore
	#      - there just won't be anyone in those categories)
	#      I guess it serves as an example and a placeholder for this
	#      config option
	$g_notify_flags['new']	= array('candidatenotes'	=> OFF,
									'monitor'	=> OFF);

	# Whether user's should receive emails for their own actions
	$g_email_receive_own	= ON;

	# set to OFF to disable email check
	$g_validate_email		= ( substr( php_uname(), 0, 7 ) == 'Windows' ) ? OFF : ON;
	$g_check_mx_record		= OFF;	# Not supported under Windows.

	# if ON, allow the user to omit an email field
	# note if you allow users to create their own accounts, they
	#  must specify an email at that point, no matter what the value
	#  of this option is.  Otherwise they wouldn't get their passwords.
	$g_allow_blank_email	= OFF;

	# Only allow and send email to addresses in the given domain
	# For example:
	# $g_limit_email_domain		= 'users.sourceforge.net';
	$g_limit_email_domain	= OFF;

	# This specifies the access level that is needed to get the mailto: links.
	$g_show_user_email_threshold = NOBODY;

	# If use_x_priority is set to ON, what should the value be?
	# Urgent = 1, Not Urgent = 5, Disable = 0
	# Note: some MTAs interpret X-Priority = 0 to mean 'Very Urgent'
	$g_mail_priority		= 1;

	# select the method to mail by:
	# 0 - mail()
	# 1 - sendmail
	# 2 - SMTP
	$g_phpMailer_method		= 1;

	# This option allows you to use a remote SMTP host.  Must use the phpMailer script
	# One or more hosts, separated by a semicolon, can be listed. 
	# You can also specify a different port for each host by using this 
	# format: [hostname:port] (e.g. "smtp1.example.com:25;smtp2.example.com").
	# Hosts will be tried in order.
	$g_smtp_host			= 'localhost';

	# These options allow you to use SMTP Authentication when you use a remote
	# SMTP host with phpMailer.  If smtp_username is not '' then the username
	# and password will be used when logging in to the SMTP server.
	$g_smtp_username = '';
	$g_smtp_password = '';

	# It is recommended to use a cronjob or a scheduler task to send emails.  
	# The cronjob should typically run every 5 minutes.  If no cronjob is used,
	# then user will have to wait for emails to be sent after performing an action
	# which triggers notifications.  This slows user performance.
	$g_email_send_using_cronjob = ON;

	# Specify whether e-mails should be sent with the category set or not.  This is tested
	# with Microsoft Outlook.  More testing for this feature + other formats will be added
	# in the future.
	# OFF, EMAIL_CATEGORY_PROJECT_CATEGORY (format: [Project] Category)
	$g_email_set_category		= OFF;

	# --- email separator and padding ------------
	$g_email_separator1		= str_pad('', 70, '=');
	$g_email_separator2		= str_pad('', 70, '-');
	$g_email_padding_length	= 28;

	#############################
	# COSMOS Version String
	#############################

	# --- version variables -----------
	$g_show_version			= ON;

	################################
	# COSMOS Language Settings
	################################

	# --- language settings -----------

	# If the language is set to 'auto', the actual
	# language is determined by the user agent (web browser)
	# language preference.
	$g_default_language		= 'english';

	# list the choices that the users are allowed to choose
	$g_language_choices_arr	= array(
		'auto',
		'bulgarian',
		'catalan',
		'chinese_simplified',
		'chinese_traditional',
		'croatian',
		'czech',
		'danish',
		'dutch',
		'english',
		'estonian',
		'finnish',
		'french',
		'german',
		'german_eintrag',
		'greek',
		'hebrew',
		'hungarian',
		'icelandic',
		'italian',
		'japanese',
		'korean',
		'latvian',
		'lithuanian',
		'norwegian',
		'polish',
		'portuguese_brazil',
		'portuguese_standard',
		'romanian',
		'russian',
		'serbian',
		'slovak',
		'slovene',
		'spanish',
		'swedish',
		'turkish',
		'ukrainian',
		'urdu',
	);

	# Browser language mapping for 'auto' language selection
	$g_language_auto_map = array(
		'bg' => 'bulgarian',
		'ca' => 'catalan',
		'zh-cn, zh-sg, zh' => 'chinese_simplified',
		'zh-hk, zh-tw' => 'chinese_traditional',
		'cs' => 'czech',
		'da' => 'danish',
		'nl-be, nl' => 'dutch',
		'en-us, en-gb, en-au, en' => 'english',
		'et' => 'estonian',
		'fi' => 'finnish',
		'fr-ca, fr-be, fr-ch, fr' => 'french',
		'de-de, de-at, de-ch, de' => 'german',
		'he' => 'hebrew',
		'hu' => 'hungarian',
		'hr' => 'croatian',
		'is' => 'icelandic',
		'it-ch, it' => 'italian',
		'ja' => 'japanese',
		'ko' => 'korean',
		'lt' => 'lithuanian',
		'lv' => 'latvian',
		'no' => 'norwegian',
		'pl' => 'polish',
		'pt-br' => 'portugese_brazil',
		'pt' => 'portugese_standard',
		'ro-mo, ro' => 'romanian',
		'ru-mo, ru-ru, ru-ua, ru' => 'russian',
		'sr' => 'serbian',
		'sk' => 'slovak',
		'sl' => 'slovene',
		'es-mx, es-co, es-ar, es-cl, es-pr, es' => 'spanish',
		'sv-fi, sv' => 'swedish',
		'tr' => 'turkish',
		'uk' => 'ukrainian'
	);

	# Fallback for automatic language selection
	$g_fallback_language	= 'english';

	###############################
	# COSMOS Display Settings
	###############################

	# --- sitewide variables ----------
	$g_window_title			= 'COSMOS';	 # browser window title
	$g_page_title			= '';	 # title at top of html page (empty by default, since there is a logo now)

	# --- advanced views --------------
	# BOTH, SIMPLE_ONLY, ADVANCED_ONLY
	$g_show_report			= ADVANCED_ONLY;
	$g_show_update			= ADVANCED_ONLY;
	$g_show_view			= ADVANCED_ONLY;

	# --- top menu items --------------
	# Specifies whether to enable support for project documents or not.
	$g_enable_project_documentation	= ON;

	# --- footer menu -----------------
	# Display another instance of the menu at the bottom.  The top menu will still remain.
	$g_show_footer_menu		= OFF;

	# --- show extra menu bar with all available projects ---
	$g_show_project_menu_bar = OFF;

	# --- show extra dropdown for subprojects ---
	# Shows only top projects in the project dropdown and adds an extra dropdown for subprojects.
	$g_show_extended_project_browser = OFF;

	# --- show assigned to names ------
	# This is in the view all pages
	$g_show_assigned_names	= OFF;

	# --- show priority as icon ---
	# OFF: Shows priority as icon in view all candidates page
	# ON:  Shows priority as text in view all candidates page
	$g_show_priority_text	= OFF;

	# A configuration option that identifies the columns to be shown on the View Issues page.
	# In COSMOS 1.1, this option can be overriden using the Generic Configuration screen.
	# This configuration can be overriden dynamically by overriding the custom function "get_columns_to_view".
	# Some of the columns specified here can be removed automatically if they conflict with other configuration.
	# For example, sponsorship_total will be removed if sponsorships are disabled.
	# To include custom field 'xyz', include the column name as 'custom_xyz'.
	#
	# Standard Column Names (i.e. names to choose from):
	# selection, edit, id, project_id, reporter_id, handler_id, priority, reproducibility, projection, eta,
	# resolution, fixed_in_version, view_state, os, os_build, platform, version, date_submitted, attachment,
	# category, sponsorship_total, severity, status, last_updated, summary, candidatenotes_count
	$g_view_issues_page_columns = array ( 'selection', 'edit','build','os_build','summary','sponsorship_total', 'priority', 'candidatenotes_count', 'attachment', 'status','sticky',
						'custom_OverallTestScore', 'custom_CTestResult', 'custom_CplusplusTestResult', 
						'custom_LinuxScriptingTestResult', 'custom_NetworkingTestResult',
						'handler_id', 'fixed_in_version','target_version',  
						'custom_1st Interview Date', 'custom_1st Interview Time', 
						'custom_Interview Room', 'custom_2nd Interview Date', 
						'custom_2nd Interview Time', 'resolution');
	
	# A configuration option that identifies the columns to be show on the print issues page.
	# In COSMOS 1.1, this option can be overriden using the Generic Configuration screen.
	# This configuration can be overriden dynamically by overriding the custom function "get_columns_to_view".
	$g_print_issues_page_columns = array ( 'selection', 'priority', 'id', 'sponsorship_total', 'candidatenotes_count', 'attachment', 'category', 'severity', 'status', 'last_updated', 'summary' );

	# A configuration option that identifies the columns to be include in the CSV export.
	# In COSMOS 1.1, this option can be overriden using the Generic Configuration screen.
	# This configuration can be overriden dynamically by overriding the custom function "get_columns_to_view".
	$g_csv_columns = array ( 'id', 'project_id', 'reporter_id', 'handler_id', 'priority', 'severity', 'reproducibility', 'version', 'projection', 'category', 'date_submitted', 'eta', 'os', 'os_build', 'platform', 'view_state', 'last_updated', 'summary', 'status', 'resolution', 'fixed_in_version', 'duplicate_id' );
	
	/**
	 * The default columns to be included in the Excel export.
	 * This can be overriden using Manage -> Manage Configuration -> Manage Columns
	 * Also each user can configure their own columns using My Account -> Manage Columns
	 * @global array $g_excel_columns
	 */
	$g_excel_columns = array ( 'id', 'project_id', 'reporter_id', 'handler_id', 'priority', 'severity', 'reproducibility', 'version', 'projection', 'category', 'date_submitted', 'eta', 'os', 'os_build', 'platform', 'view_state', 'last_updated', 'summary', 'status', 'resolution', 'fixed_in_version' );

	# Make bold column value whose name contain the following
	# Also specify the css class to be used
 	$g_column_containing_word = 'Date';	
 	$g_column_containing_word_css = 'column_bold';	
	# --- show projects when in All Projects mode ---
	$g_show_candidate_project_links	= ON;

	# --- Position of the status colour legend, can be: STATUS_LEGEND_POSITION_*
	# --- see constant_inc.php. (*: TOP , BOTTOM , or BOTH)
	$g_status_legend_position	= STATUS_LEGEND_POSITION_TOP;

	# --- Show a legend with percentage of candidate status
	# --- x% of all candidates are new, y% of all candidates are assigned and so on.
	# --- If set to ON it will printed below the status colour legend.
	$g_status_percentage_legend = ON;
	$g_status_legend_filters = ON;

	# --- Position of the filter box, can be: FILTER_POSITION_*
	# FILTER_POSITION_TOP, FILTER_POSITION_BOTTOM, or 0 for none.
	$g_filter_position	= FILTER_POSITION_TOP;

	# --- show product versions in create, view and update screens
	#  ON forces display even if none are defined
	#  OFF suppresses display
	#  AUTO suppresses the display if there are no versions defined for the project
	$g_show_product_version = ON;

	# -- show users with their real name or not
	$g_show_realname = ON;
	$g_differentiate_duplicates = OFF;  # leave off for now

	# -- sorting for names in dropdown lists. If turned on, "Jane Doe" will be sorted with the "D"s
	$g_sort_by_last_name = OFF;

	# Show user avatar
	# the current implementation is based on http://www.gravatar.com
	# users will need to register there the same address used in 
	# this cosmos installation to have their avatar shown
	# Please note: upon registration or avatar change, it takes some time for
	# the updated gravatar images to show on sites
	$g_show_avatar = OFF;
	
	# Only users above this threshold will have their avatar shown
	$g_show_avatar_threshold = DEVELOPER;

        # Default avatar for users without a gravatar account
        $g_default_avatar = "%path%images/no_avatar.png";

	############################
	# COSMOS JPGRAPH Addon
	############################

	# --- jpgraph settings --- #
	# Initial Version from Duncan Lisset
	#
	# To use the Jpgraph addon you need the JPGRAPH package from
	# http://www.aditus.nu/jpgraph/index.php
	# You can place the package whereever you want, but you have
	# to set the var in jpgraph.php eg.
	# (DEFINE('DIR_BASE','/www/cosmosbt/jpgraph/');)

	$g_use_jpgraph			= ON;
	$g_jpgraph_path			= '.' . DIRECTORY_SEPARATOR . 'jpgraph' . DIRECTORY_SEPARATOR;   # dont forget the ending slash!

	# use antialiasing - Enabling anti-aliasing will greatly improve the visual apperance of certain graphs.
	# Note: Using anti-aliasing makes line drawing roughly 8 time slower than normal lines
	$g_jpgraph_antialias	= ON;

	# what truetype font will the graphs use. Allowed values are 'arial', 'verdana', 'courier', 'book', 'comic', 'times',
	#  'georgia', 'trebuche', 'vera', 'veramono', or 'veraserif'. Refer to the jpgraph manual for details.
	# NOTE: these fonts need to be installed in the TTF_DIR as specified to jpgraph
	$g_graph_font = '';

	# what width is used to scale the graphs.
	$g_graph_window_width = 800;
	# bar graph aspect ration (height / width)
	$g_graph_bar_aspect = 0.9;

	# how many graphs to put in each row in the advanced summary page
	$g_graph_summary_graphs_per_row = 2;
	
	# initial graph type selected on candidate_graph_page (see that page for possible values)
	# 0 asks user to select
	$g_default_graph_type = 0;
	
	# graph colours, once the list is exhausted it will repeat
	$g_graph_colors = array('coral', 'red', 'blue', 'black', 'green', 'orange', 'pink', 'brown', 'gray',
	        'blueviolet','chartreuse','magenta','purple3','teal','tan','olivedrab','magenta');

	############################
	# COSMOS Time Settings
	############################

	# time for 'permanent' cookie to live in seconds (1 year)
	$g_cookie_time_length	= 30000000;

	# minutes to wait before document is stale (in minutes)
	$g_content_expire		= 0;

	# The time (in seconds) to allow for page execution during long processes
	#  such as upgrading your database.
	# The default value of 0 indicates that the page should be allowed to
	#  execute until it is finished.
	$g_long_process_timeout	= 0;

	############################
	# COSMOS Date Settings
	############################

	# --- date format settings --------
	# date format strings defaults to ISO 8601 formatting
	# go to http://www.php.net/manual/en/function.date.php
	# for detailed instructions on date formatting
	#$g_short_date_format    	= 'D. d M. Y';
	$g_short_date_format    	= 'd-m-y';
	$g_short_date_format_jquery   	= 'D. d M. yy';
	$g_long_date_format    		= 'D. d M. Y';
	$g_normal_date_format   	= 'Y-m-d H:i';
	$g_complete_date_format 	= 'Y-m-d H:i T';

	############################
	# COSMOS News Settings
	############################

	# --- Limit News Items ------------
	# limit by entry count or date
	# BY_LIMIT - entry limit
	# BY_DATE - by date
	$g_news_limit_method	= BY_LIMIT;

	# limit by last X entries
	$g_news_view_limit		= 7;

	# limit by days
	$g_news_view_limit_days	= 30;

	# threshold for viewing private news
	$g_private_news_threshold	= DEVELOPER;

	##################################
	# COSMOS Default Preferences
	##################################

	# --- signup default ---------------
	# look in constant_inc.php for values
	$g_default_new_account_access_level	= REPORTER;

	# Default Bug View Status (VS_PUBLIC or VS_PRIVATE)
	$g_default_candidate_view_status = VS_PUBLIC;

	# Default value for steps to reproduce field.
	$g_default_candidate_steps_to_reproduce = '';

	# Default value for addition information field.
	$g_default_candidate_additional_info = '';

	# Default Bugnote View Status (VS_PUBLIC or VS_PRIVATE)
	$g_default_candidatenote_view_status = VS_PUBLIC;

	# Default candidate severity when reporting a new candidate
	$g_default_candidate_severity = MINOR;

	# Default candidate priority when reporting a new candidate
	$g_default_candidate_priority = NORMAL;
	
	# Default candidate reproducibility when reporting a new candidate
	$g_default_candidate_reproducibility = REPRODUCIBILITY_HAVENOTTRIED;

	# Default candidate category when reporting a new candidate
	$g_default_candidate_category = '';

	# --- viewing defaults ------------
	# site defaults for viewing preferences
	$g_default_limit_view	= 100;
	$g_default_show_changed	= 6;
	$g_hide_status_default 	= CLOSED;
	$g_show_sticky_issues   = 'on';

	# make sure people aren't refreshing too often
	$g_min_refresh_delay	= 10;    # in minutes

	# --- account pref defaults -------
	$g_default_advanced_report			= ON;
	$g_default_advanced_view			= ON;
	$g_default_advanced_update			= ON;
	$g_default_refresh_delay			= 30;    # in minutes
	$g_default_redirect_delay			= 1;     # in seconds
	$g_default_candidatenote_order			= 'ASC';
	$g_default_email_on_new				= ON;
	$g_default_email_on_assigned			= ON;
	$g_default_email_on_feedback			= ON;
	$g_default_email_on_resolved			= ON;
	$g_default_email_on_closed			= ON;
	$g_default_email_on_reopened			= ON;
	$g_default_email_on_candidatenote			= ON;
	$g_default_email_on_status			= 0; # @@@ Unused
	$g_default_email_on_priority			= 0; # @@@ Unused
	$g_default_email_on_new_minimum_severity	= OFF; # 'any'
	$g_default_email_on_assigned_minimum_severity	= OFF; # 'any'
	$g_default_email_on_feedback_minimum_severity	= OFF; # 'any'
	$g_default_email_on_resolved_minimum_severity	= OFF; # 'any'
	$g_default_email_on_closed_minimum_severity	= OFF; # 'any'
	$g_default_email_on_reopened_minimum_severity	= OFF; # 'any'
	$g_default_email_on_candidatenote_minimum_severity	= OFF; # 'any'
	$g_default_email_on_status_minimum_severity	= OFF; # 'any'
	$g_default_email_on_priority_minimum_severity	= OFF; # @@@ Unused
	$g_default_email_candidatenote_limit					= 0;
	# default_language - is set to site language

	###############################
	# COSMOS Summary Settings
	###############################

	# how many reporters to show
	# this is useful when there are hundreds of reporters
	$g_reporter_summary_limit	= 10;

	# --- summary date displays -------
	# date lengths to count candidates by (in days)
	$g_date_partitions			= array( 1, 2, 3, 7, 30, 60, 90, 180, 365);

	# shows project '[project] category' when 'All Projects' is selected
	# otherwise only 'category name'
	$g_summary_category_include_project	= OFF;

	# threshold for viewing summary reports
	$g_view_summary_threshold	= VIEWER;

	###############################
	# COSMOS Bugnote Settings
	###############################

	# --- candidatenote ordering ------------
	# change to ASC or DESC
	$g_candidatenote_order		= 'DESC';

	################################
	# COSMOS Bug History Settings
	################################

	# --- candidate history visible by default when you view a candidate ----
	# change to ON or OFF
	$g_history_default_visible	= ON;

	# --- candidate history ordering ----
	# change to ASC or DESC
	$g_history_order		= 'ASC';

	###############################
	# COSMOS Reminder Settings
	###############################

	# are reminders stored as candidatenotes
	$g_store_reminders		= ON;

	# Automatically add recipients of reminders to monitor list, if they are not
	# the handler or the reporter (since they automatically get notified, if required)
	# If recipients of the reminders are below the monitor threshold, they will not be added.
	$g_reminder_recipents_monitor_candidate = OFF;

	# Default Reminder View Status (VS_PUBLIC or VS_PRIVATE)
	$g_default_reminder_view_status = VS_PUBLIC;

	###################################
	# COSMOS Sponsorship Settings
	###################################

	# Whether to enable/disable the whole issue sponsorship feature
	$g_enable_sponsorship = OFF;

	# Currency used for all sponsorships.
	$g_sponsorship_currency = 'US$';

	# Access level threshold needed to view the total sponsorship for an issue by all users.
	$g_view_sponsorship_total_threshold = VIEWER;

	# Access level threshold needed to view the users sponsoring an issue and the sponsorship
	# amount for each.
	$g_view_sponsorship_details_threshold = VIEWER;

	# Access level threshold needed to allow user to sponsor issues.
	$g_sponsor_threshold = REPORTER;

	# Access level required to be able to handle sponsored issues.
	$g_handle_sponsored_candidates_threshold = DEVELOPER;

	# Access level required to be able to assign a sponsored issue to a user with access level
	# greater or equal to 'handle_sponsored_candidates_threshold'.
	$g_assign_sponsored_candidates_threshold = MANAGER;

	# Minimum sponsorship amount. If the user enters a value less than this, an error will be prompted.
	$g_minimum_sponsorship_amount = 5;

	###################################
	# COSMOS File Upload Settings
	###################################

	# --- file upload settings --------
	# This is the master setting to disable *all* file uploading functionality
	#
	# If you want to allow file uploads, you must also make sure that they are
	#  enabled in php.  You may need to add 'file_uploads = TRUE' to your php.ini
	#
	# See also: $g_upload_project_file_threshold, $g_upload_candidate_file_threshold,
	#   $g_allow_reporter_upload
	$g_allow_file_upload	= ON;

	# Upload destination: specify actual location in project settings
	# DISK, DATABASE, or FTP.
	$g_file_upload_method	= DATABASE;

	# When using FTP or DISK for storing uploaded files, this setting control 
	# the access permissions they will have on the web server: with the default
	# value (0400) files will be read-only, and accessible only by the user
	# running the apache process (probably "apache" in Linux and "Administrator"
	# in Windows).
	# For more details on unix style permissions:
	# http://www.perlfect.com/articles/chmod.shtml
	$g_attachments_file_permissions = 0400;

	#
	# direct download on attchment icon click
	# If ON this will download the FIRST available attchment directly when clicking the attachment icon. 
	# If OFF it will redirect you to the view page to see all available attachments. 
	#
	$g_direct_attachment_download = ON;
	
	# FTP settings, used if $g_file_upload_method = FTP
	$g_file_upload_ftp_server	= 'ftp.myserver.com';
	$g_file_upload_ftp_user		= 'readwriteuser';
	$g_file_upload_ftp_pass		= 'readwritepass';

	# Maximum file size that can be uploaded
	# Also check your PHP settings (default is usually 2MBs)
	$g_max_file_size		= 5000000; # 5 MB

	# Files that are allowed or not allowed.  Separate items by commas.
	# eg. 'php,html,java,exe,pl'
	# if $g_allowed_files is filled in NO other file types will be allowed.
	# $g_disallowed_files takes precedence over $g_allowed_files
	$g_allowed_files		= '';
	$g_disallowed_files		= '';

	# prefix to be used for the file system names of files uploaded to projects.
	# Eg: doc-001-myprojdoc.zip
	$g_document_files_prefix = 'doc';

	# absolute path to the default upload folder.  Requires trailing / or \
	$g_absolute_path_default_upload_folder = '';

	############################
	# COSMOS HTML Settings
	############################

	# --- html tags -------------------

	# Set this flag to automatically convert www URLs and
	# email adresses into clickable links
	$g_html_make_links		= ON;

	# These are the valid html tags for multi-line fields (e.g. description)
	# do NOT include href or img tags here
	# do NOT include tags that have parameters (eg. <font face="arial">)
	$g_html_valid_tags		= 'p, li, ul, ol, br, pre, i, b, u, em';

	# These are the valid html tags for single line fields (e.g. issue summary).
	# do NOT include href or img tags here
	# do NOT include tags that have parameters (eg. <font face="arial">)
	$g_html_valid_tags_single_line		= 'i, b, u, em';

	# maximum length of the description in a dropdown menu (for search)
	# set to 0 to disable truncations
	$g_max_dropdown_length = 40;

	# This flag conntrolls whether pre-formatted text (delimited by <pre> tags
	#  is wrapped to a maximum linelength (defaults to 100 chars in strings_api)
	#  If turned off, the display may be wide when viewing the text
	$g_wrap_in_preformatted_text = ON;

	##########################
	# COSMOS HR Settings
	##########################

	# --- hr --------------------------
	$g_hr_size				= 1;
	$g_hr_width				= 50;

	#############################
	# COSMOS LDAP Settings
	#############################

	# look in README.LDAP for details

	# --- using openldap -------------
	$g_ldap_server			= 'ldaps://ldap.example.com.au/';
	$g_ldap_port			= '636';
	$g_ldap_root_dn			= 'dc=example,dc=com,dc=au';
	$g_ldap_organization		= '';    # e.g. '(organizationname=*Traffic)'
	$g_ldap_uid_field		= 'uid'; # Use 'sAMAccountName' for Active Directory
	$g_ldap_bind_dn			= '';
	$g_ldap_bind_passwd		= '';
	$g_use_ldap_email		= OFF; # Should we send to the LDAP email address or what MySql tells us
	
	# The LDAP Protocol Version, if 0, then the protocol version is not set.
	$g_ldap_protocol_version = 0;

	############################
	# Status Settings
	############################

	# Status to assign to the candidate when submitted.
	$g_candidate_submit_status = NEW_;

	# Status to assign to the candidate when assigned.
	$g_candidate_assigned_status = ''; 

	# Status to assign to the candidate when reopened.
	$g_candidate_reopen_status = FEEDBACK;

	# Resolution to assign to the candidate when reopened.
	$g_candidate_reopen_resolution = REOPENED;

	# --- status thresholds (*_status_threshold) ---

	# Bug becomes readonly if its status is >= this status.  The candidate becomes read/write again if re-opened and its
	# status becomes less than this threshold.
	$g_candidate_readonly_status_threshold = RESOLVED;

	# Bug is resolved, ready to be closed or reopened.  In some custom installations a candidate
	# maybe considered as resolved when it is moved to a custom (FIXED OR TESTED) status.
	$g_candidate_resolved_status_threshold = RESOLVED;

	# Automatically set status to ASSIGNED whenever a candidate is assigned to a person.
	# This is useful for installations where assigned status is to be used when
	# the candidate is in progress, rather than just put in a person's queue.
	$g_auto_set_status_to_assigned	= OFF;

	# 'status_enum_workflow' defines the workflow, and reflects a simple
	#  2-dimensional matrix. For each existing status, you define which
	#  statuses you can go to from that status, e.g. from NEW_ you might list statuses
	#  '10:new,20:feedback,30:acknowledged' but not higher ones.
	# The following example can be transferred to config_inc.php
	# $g_status_enum_workflow[NEW_]='20:feedback,30:acknowledged,40:confirmed,50:assigned,80:resolved';
	# $g_status_enum_workflow[FEEDBACK] ='10:new,30:acknowledged,40:confirmed,50:assigned,80:resolved';
	# $g_status_enum_workflow[ACKNOWLEDGED] ='20:feedback,40:confirmed,50:assigned,80:resolved';
	# $g_status_enum_workflow[CONFIRMED] ='20:feedback,50:assigned,80:resolved';
	# $g_status_enum_workflow[ASSIGNED] ='20:feedback,80:resolved,90:closed';
	# $g_status_enum_workflow[RESOLVED] ='50:assigned,90:closed';
	# $g_status_enum_workflow[CLOSED] ='50:assigned';
	$g_status_enum_workflow = array();

	############################
	# Bug Attachments Settings
	############################

	# Specifies the maximum size below which an attachment is previewed in the candidate
	# view pages.  To disable this feature, set max size to 0.
	# This feature applies to: bmp, png, gif, jpg
	$g_preview_attachments_inline_max_size = 0;

	# Extenstions for text files that can be expanded inline.
	$g_preview_text_extensions = array( 'txt', 'diff', 'patch' );

	# Extensions for images that can be expanded inline.
	$g_preview_image_extensions = array( 'bmp', 'png', 'gif', 'jpg', 'jpeg' );

	# Specifies the maximum width for the auto-preview feature.  If no maximum width should be imposed
	# then it should be set to 0.
	$g_preview_max_width = 0;

	# Specifies the maximum height for the auto-preview feature.  If no maximum height should be imposed
	# then it should be set to 0.
	$g_preview_max_height = 250;

	# --- Show an attachment indicator on candidate list ---
	# Show a clickable attachment indicator on the candidate
	# list page if the candidate has one or more files attached.
	# Note: This option is disabled by default since it adds
	# 1 database query per candidate listed and thus might slow
	# down the page display.
	$g_show_attachment_indicator = ON;

	# access level needed to view candidates attachments.  View means to see the file names
	# sizes, and timestamps of the attachments.
	$g_view_attachments_threshold	= VIEWER;

	# list of filetypes to view inline. This is a string of extentions separated by commas
	# This is used when downloading an attachment.  Rather than downloading, the attachment
	# is viewed in the browser.
	$g_inline_file_exts = 'bmp,png,gif,jpg,jpeg';

	# access level needed to download candidate attachments
	$g_download_attachments_threshold	= VIEWER;

	# access level needed to delete candidate attachments
	$g_delete_attachments_threshold	= DEVELOPER;

	# allow users to view attachments uploaded by themselves even if their access
	# level is below view_attachments_threshold.
	$g_allow_view_own_attachments = ON;

	# allow users to download attachments uploaded by themselves even if their access
	# level is below download_attachments_threshold.
	$g_allow_download_own_attachments = ON;

	# allow users to delete attachments uploaded by themselves even if their access
	# level is below delete_attachments_threshold.
	$g_allow_delete_own_attachments = OFF;

	############################
	# COSMOS Misc Settings
	############################

	# --- access level thresholds (*_threshold) ---

	# access level needed to report a candidate
	$g_report_candidate_threshold			= REPORTER;

	# access level needed to update candidates (i.e., the update_candidate_page)
	#  This controls whether the user sees the "Update Bug" button in candidate_view*_page
	#  and the pencil icon in view_all_candidate_page
	$g_update_candidate_threshold			= UPDATER;

	# access level needed to monitor candidates
	# Look in the constant_inc.php file if you want to set a different value
	$g_monitor_candidate_threshold		= UPDATER;

	# access level needed to view private candidates
	# Look in the constant_inc.php file if you want to set a different value
	$g_private_candidate_threshold		= MANAGER;

	# access level needed to be able to be listed in the assign to field.
	$g_handle_candidate_threshold			= DEVELOPER;
	# access level needed to show the Assign To: button candidate_view*_page or
	#  the Assigned list in candidate_update*_page.
	#  This allows control over who can route candidates
	# This defaults to $g_handle_candidate_threshold
	$g_update_candidate_assign_threshold			= UPDATER;

	# access level needed to view private candidatenotes
	# Look in the constant_inc.php file if you want to set a different value
	$g_private_candidatenote_threshold	= MANAGER;

	# access level needed to view handler in candidate reports and notification email
	# nt
	#
	# @@@ yarick123: now it is implemented for notification email only
	$g_view_handler_threshold		= DEVELOPER;

	# access level needed to view history in candidate reports and notification email
	# @@@ yarick123: now it is implemented for notification email only
	$g_view_history_threshold		= DEVELOPER;

	# access level needed to send a reminder from the candidate view pages
	# set to NOBODY to disable the feature
	$g_candidate_reminder_threshold		= MANAGER;

	# access level needed to upload files to the project documentation section
	# You can set this to NOBODY to prevent uploads to projects
	# See also: $g_upload_candidate_file_threshold, $g_allow_file_upload
	$g_upload_project_file_threshold = MANAGER;

	# access level needed to upload files to attach to a candidate
	# You can set this to NOBODY to prevent uploads to candidates but note that
	#  the reporter of the candidate will still be able to upload unless you set
	#  $g_allow_reporter_upload or $g_allow_file_upload to OFF
	# See also: $g_upload_project_file_threshold, $g_allow_file_upload,
	#			$g_allow_reporter_upload
	$g_upload_candidate_file_threshold	= REPORTER;

	# Add candidatenote threshold
	$g_add_candidatenote_threshold = REPORTER;

	# Update candidatenote threshold (if the candidatenote is not your own)
	$g_update_candidatenote_threshold = DEVELOPER;

	# Threshold needed to view project documentation
	$g_view_proj_doc_threshold = DEVELOPER;

	# Threshold needed to manage a project: edit project
	# details (not to add/delete projects), upload documentation, ...etc.
	$g_manage_project_threshold = MANAGER;

	# Threshold needed to add/delete/modify news
	$g_manage_news_threshold = MANAGER;
	
	# Threshold needed to import CSV files
	$g_manage_import_threshold = ADMINISTRATOR;
	
	# Threshold needed to activate tests 
	$g_manage_activate_threshold = ADMINISTRATOR;
	
	# Threshold needed to create tests 
	$g_manage_create_threshold = ADMINISTRATOR;

	# Threshold required to delete a project
	$g_delete_project_threshold = ADMINISTRATOR;

	# Threshold needed to create a new project
	$g_create_project_threshold = ADMINISTRATOR;

	# Threshold needed to be automatically included in private projects
	$g_private_project_threshold = ADMINISTRATOR;

	# Threshold needed to manage user access to a project
	$g_project_user_threshold = MANAGER;

	# Threshold needed to manage user accounts
	$g_manage_user_threshold = ADMINISTRATOR;
	
	# Threshold needed to manage tests 
	$g_manage_tests_threshold = ADMINISTRATOR;
	
	# Threshold needed to import csvs 
	$g_import_threshold = ADMINISTRATOR;

	# Delete candidate threshold
	$g_delete_candidate_threshold = DEVELOPER;

	# Delete candidatenote threshold
	$g_delete_candidatenote_threshold = '%delete_candidate_threshold%';

	# Are users allowed to change and delete their own candidatenotes?
	$g_candidatenote_allow_user_edit_delete = ON;

	# Move candidate threshold
	$g_move_candidate_threshold = DEVELOPER;

	# Threshold needed to set the view status while reporting a candidate or a candidate note.
	$g_set_view_status_threshold = REPORTER;

	# Threshold needed to update the view status while updating a candidate or a candidate note.
	# This threshold should be greater or equal to $g_set_view_status_threshold.
	$g_change_view_status_threshold = UPDATER;

	# --- Threshold needed to show the list of users montoring a candidate on the candidate view pages.
	$g_show_monitor_list_threshold = DEVELOPER;

	# Threshold needed to be able to use stored queries
	$g_stored_query_use_threshold = DEVELOPER;

	# Threshold needed to be able to create stored queries
	$g_stored_query_create_threshold = DEVELOPER;

	# Threshold needed to be able to create shared stored queries
	$g_stored_query_create_shared_threshold = MANAGER;

	# Threshold needed to update readonly candidates.  Readonly candidates are identified via
	# $g_candidate_readonly_status_threshold.
	$g_update_readonly_candidate_threshold = MANAGER;

	# threshold for viewing changelog
	$g_view_changelog_threshold = DEVELOPER;

	# threshold for viewing roadmap
	$g_roadmap_view_threshold = VIEWER;
	
	# threshold for updating roadmap, target_version, etc
	$g_roadmap_update_threshold = DEVELOPER;
	
	# status change thresholds
	$g_update_candidate_status_threshold = DEVELOPER;

	# access level needed to re-open candidates
	$g_reopen_candidate_threshold			= DEVELOPER;

	# access level needed to set a candidate sticky
	$g_set_candidate_sticky_threshold			= MANAGER;
	
	# The minimum access level for someone to be a member of the development team
	# and appear on the project information page.
	$g_development_team_threshold = DEVELOPER;

	# this array sets the access thresholds needed to enter each status listed.
	# if a status is not listed, it falls back to $g_update_candidate_status_threshold
	# example: $g_set_status_threshold = array( ACKNOWLEDGED => MANAGER, CONFIRMED => DEVELOPER, CLOSED => MANAGER );
	$g_set_status_threshold = array();

	# --- login method ----------------
	# CRYPT or PLAIN or MD5 or LDAP or BASIC_AUTH
	# You can simply change this at will. COSMOS will try to figure out how the passwords were encrypted.
	$g_login_method				= MD5;

	# --- limit reporters -------------
	# Set to ON if you wish to limit reporters to only viewing candidates that they report.
	$g_limit_reporters			= ON;

	# --- close immediately -----------
	# Allow developers and above to close candidates immediately when resolving candidates
	$g_allow_close_immediately	= OFF;

	# --- reporter can close ----------
	# Allow reporters to close the candidates they reported, after they're marked resolved.
	$g_allow_reporter_close		= OFF;

	# --- reporter can reopen ---------
	# Allow reporters to reopen the candidates they reported, after they're marked resolved.
	$g_allow_reporter_reopen	= OFF;

	# --- reporter can upload ---------
	# Allow reporters to upload attachments to candidates they reported.
	$g_allow_reporter_upload	= ON;

	# --- account delete -----------
	# Allow users to delete their own accounts
	$g_allow_account_delete		= OFF;

	# --- anonymous login -----------
	# Allow anonymous login
	$g_allow_anonymous_login	= OFF;
	$g_anonymous_account		= '';

	# --- CVS linking ---------------
	# insert the URL to your CVSweb or ViewCVS
	# eg: http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/cosmosbt/cosmosbt/
	$g_cvs_web				= '';

	# --- Source Control Integration ------

	# For open source projects it is expected that the notes be public, however,
	# for non-open source it will probably be VS_PRIVATE.
	$g_source_control_notes_view_status = VS_PRIVATE;

	# Account to be used by the source control script.  The account must be enabled
	# and must have the appropriate access level to add notes to all issues even
	# private ones (DEVELOPER access recommended).
	$g_source_control_account           = '';

	# If set to a status, then after a checkin with a log message that matches the regular expression in
	# $g_source_control_fixed_regexp, the issue status is set to the specified status.  If set to OFF, the
	# issue status is not changed.
	$g_source_control_set_status_to     = OFF;

	# Whenever an issue status is set to $g_source_control_set_status_to, the issue resolution is set to
	# the value specified for this configuration.
	$g_source_control_set_resolution_to = FIXED;

	# Regular expression used to detect issue ids within checkin comments.
	# see preg_match_all() documentation at
	# http://www.php.net/manual/en/function.preg-match-all.php
	$g_source_control_regexp = "/\bissue [#]{0,1}(\d+)\b/i";

	# Regular expression used to detect the fact that an issue is fixed and extracts
	# its issue id.  If there is a match to this regular expression, then the issue
	# will be marked as resolved and the resolution will be set to fixed.
	$g_source_control_fixed_regexp = "%source_control_regexp%";

	# --- Bug Linking ---------------
	# if a number follows this tag it will create a link to a candidate.
	# eg. for # a link would be #45
	# eg. for candidate: a link would be candidate:98
	$g_candidate_link_tag			= '#';

	# --- Bugnote Linking ---------------
	# if a number follows this tag it will create a link to a candidatenote.
	# eg. for ~ a link would be ~45
	# eg. for candidatenote: a link would be candidatenote:98
	$g_candidatenote_link_tag			= '~';

	# --- Bug Count Linking ----------
	# this is the prefix to use when creating links to candidate views from candidate counts (eg. on the main
	# page and the summary page).
	# $g_candidate_count_hyperlink_prefix = 'view_all_set.php?type=1';				# permanently change the filter
	$g_candidate_count_hyperlink_prefix = 'view_all_set.php?type=1&amp;temporary=y';	# only change the filter this time

	# The regular expression to use when validating new user login names
	# The default regular expression allows a-z, A-z, 0-9, as well as space and
	#  underscore.  If you change this, you may want to update the
	#  ERROR_USER_NAME_INVALID string in the language files to explain
	#  the rules you are using on your site
	$g_user_login_valid_regex = '/^[\w \-]+$/';

	# Default user name prefix used to filter the list of users in
	# manage_user_page.php.  Change this to 'A' (or any other
	# letter) if you have a lot of users in the system and loading
	# the manage users page takes a long time.
	$g_default_manage_user_prefix = 'ALL';

	# --- CSV Export ---------------
	# Set the csv separator
	$g_csv_separator = ',';

	# threshold for users to view the system configurations
	$g_view_configuration_threshold = DEVELOPER;

	# threshold for users to set the system configurations generically via COSMOS web interface.
	# WARNING: Users who have access to set configuration via the interface MUST be trusted.  This is due
	# to the fact that such users can set configurations to PHP code and hence there can be a security
	# risk if such users are not trusted.
	$g_set_configuration_threshold = ADMINISTRATOR;

	################################
	# COSMOS Look and Feel Variables
	################################

	# --- status color codes ----------
	#
	$g_status_colors		= array( 'new'			=> '#ffad33', # ff9900
									   'feedback'		=> '#cc99cc', #660066 
									   'acknowledged'	=> '#66ff66 ', # green 
									   'confirmed'		=> '#ffff33', # yellow ffcc00 
									   'assigned'		=> '#AFcaff  ', # nsn grey 
									   'resolved'		=> '#a1caff ', # buish-green
									   'completed'		=> '#FFFED9', # FFFF99open yellow 
									   'closed'		=> '#999999'); # light gray

	# The padding level when displaying project ids
	#  The candidate id will be padded with 0's up to the size given
	$g_display_project_padding	= 3;

	# The padding level when displaying candidate ids
	#  The candidate id will be padded with 0's up to the size given
	$g_display_candidate_padding		= 7;

	# The padding level when displaying candidatenote ids
	#  The candidatenote id will be padded with 0's up to the size given
	$g_display_candidatenote_padding	= 7;

	# colours for configuration display
	$g_colour_project = 'LightGreen';
	$g_colour_global = 'LightBlue';

	###############################
	# COSMOS Cookie Variables
	###############################

	# --- cookie path ---------------
	# set this to something more restrictive if needed
	# http://www.php.net/manual/en/function.setcookie.php
	$g_cookie_path			= '/';
	$g_cookie_domain		= '';
	# cookie version for view_all_page
	$g_cookie_version		= 'v8';

	# --- cookie prefix ---------------
	# set this to a unique identifier.  No spaces.
	$g_cookie_prefix		= 'MANTIS';

	# --- cookie names ----------------
	$g_string_cookie		= '%cookie_prefix%_STRING_COOKIE';
	$g_project_cookie		= '%cookie_prefix%_PROJECT_COOKIE';
	$g_view_all_cookie		= '%cookie_prefix%_VIEW_ALL_COOKIE';
	$g_manage_cookie		= '%cookie_prefix%_MANAGE_COOKIE';
	$g_logout_cookie		= '%cookie_prefix%_LOGOUT_COOKIE';
	$g_candidate_list_cookie		= '%cookie_prefix%_BUG_LIST_COOKIE';

	#######################################
	# COSMOS Filter Variables
	#######################################
	$g_filter_by_custom_fields = ON;
	$g_filter_custom_fields_per_row = 8;
	$g_view_filters = SIMPLE_DEFAULT;

	# This switch enables the use of xmlhttprequest protocol to speed up the filter display.
	# Rather than launching a separate page, the filters are updated in-line in the
	# view_all_candidates_page.
	$g_dhtml_filters = ON;
	
	# The service to use to create a short URL.  The %s will be replaced by the long URL.
	$g_create_short_url = 'http://tinyurl.com/create.php?url=%s';

	#######################################
	# COSMOS Database Table Variables
	#######################################

	# --- table prefix ----------------
	$g_db_table_prefix		= 'cosmos';
	$g_db_table_suffix		= '_table';

	# --- table names -----------------
	$g_cosmos_candidate_file_table			= '%db_table_prefix%_candidate_file%db_table_suffix%';
	$g_cosmos_candidate_history_table			= '%db_table_prefix%_candidate_history%db_table_suffix%';
	$g_cosmos_candidate_monitor_table			= '%db_table_prefix%_candidate_monitor%db_table_suffix%';
	$g_cosmos_candidate_relationship_table		= '%db_table_prefix%_candidate_relationship%db_table_suffix%';
	$g_cosmos_candidate_table				= '%db_table_prefix%_candidate%db_table_suffix%';
	$g_cosmos_candidate_tag_table				= '%db_table_prefix%_candidate_tag%db_table_suffix%';
	$g_cosmos_candidate_text_table			= '%db_table_prefix%_candidate_text%db_table_suffix%';
	$g_cosmos_candidatenote_table				= '%db_table_prefix%_candidatenote%db_table_suffix%';
	$g_cosmos_candidatenote_text_table			= '%db_table_prefix%_candidatenote_text%db_table_suffix%';
	$g_cosmos_news_table				= '%db_table_prefix%_news%db_table_suffix%';
	$g_cosmos_project_category_table		= '%db_table_prefix%_project_category%db_table_suffix%';
	$g_cosmos_project_file_table			= '%db_table_prefix%_project_file%db_table_suffix%';
	$g_cosmos_project_table				= '%db_table_prefix%_project%db_table_suffix%';
	$g_cosmos_project_user_list_table		= '%db_table_prefix%_project_user_list%db_table_suffix%';
	$g_cosmos_project_version_table			= '%db_table_prefix%_project_version%db_table_suffix%';
	$g_cosmos_tag_table				= '%db_table_prefix%_tag%db_table_suffix%';
	$g_cosmos_user_table				= '%db_table_prefix%_user%db_table_suffix%';
	$g_cosmos_user_profile_table			= '%db_table_prefix%_user_profile%db_table_suffix%';
	$g_cosmos_user_pref_table			= '%db_table_prefix%_user_pref%db_table_suffix%';
	$g_cosmos_user_print_pref_table			= '%db_table_prefix%_user_print_pref%db_table_suffix%';
	$g_cosmos_custom_field_project_table		= '%db_table_prefix%_custom_field_project%db_table_suffix%';
	$g_cosmos_custom_field_table      		= '%db_table_prefix%_custom_field%db_table_suffix%';
	$g_cosmos_custom_field_string_table     	= '%db_table_prefix%_custom_field_string%db_table_suffix%';
	$g_cosmos_upgrade_table				= '%db_table_prefix%_upgrade%db_table_suffix%';
	$g_cosmos_filters_table				= '%db_table_prefix%_filters%db_table_suffix%';
	$g_cosmos_sponsorship_table			= '%db_table_prefix%_sponsorship%db_table_suffix%';
	$g_cosmos_tokens_table				= '%db_table_prefix%_tokens%db_table_suffix%';
	$g_cosmos_project_hierarchy_table		= '%db_table_prefix%_project_hierarchy%db_table_suffix%';
	$g_cosmos_config_table				= '%db_table_prefix%_config%db_table_suffix%';
	$g_cosmos_database_table			= '%db_table_prefix%_database%db_table_suffix%';
	$g_cosmos_email_table				= '%db_table_prefix%_email%db_table_suffix%';

	###########################
	# COSMOS Enum Strings
	###########################

	# --- enum strings ----------------
	# status from $g_status_index-1 to 79 are used for the onboard customization (if enabled)
	# directly use COSMOS to edit them.
	$g_access_levels_enum_string		= '10:viewer,25:reporter,40:updater,55:developer,70:manager,90:administrator';
	$g_project_status_enum_string		= '10:development,30:release,50:stable,70:obsolete';
	$g_project_view_state_enum_string	= '10:public,50:private';
	$g_view_state_enum_string		= '10:public,50:private';

	$g_priority_enum_string			= '10:none,20:low,30:normal,40:high,50:urgent,60:immediate';
	$g_severity_enum_string			= '10:feature,20:trivial,30:text,40:tweak,50:minor,60:major,70:crash,80:block';
	$g_reproducibility_enum_string		= '10:always,30:sometimes,50:random,70:have not tried,90:unable to duplicate,100:N/A';
	$g_status_enum_string			= '10:new,20:feedback,30:acknowledged,40:confirmed,50:assigned,60:completed,80:resolved,90:closed';
	#$g_status_enum_string			= '10:new,20:feedback,30:acknowledged,40:confirmed,50:assigned,60:completed,80:resolved,90:closed';
        #$s_status_enum_string = '10:New,20:Under Assessment,30:Assessment Passed,40:1st Interview (Scheduled), 50: 1st Interview (Completed),60: 2nd Interview (Scheduled), 80: 2nd Interview (Completed), 90:Closed';
	#$g_status_enum_string			= '10:new,20:feedback,30:acknowledged,40:confirmed,50:assigned,80:resolved,90:closed';
	  # @@@ for documentation, the values in this list are also used to define variables in the language files
	  #  (e.g., $s_new_candidate_title referenced in candidate_change_status_page.php )
	  # Embedded spaces are converted to underscores (e.g., "working on" references $s_working_on_candidate_title).
	  # they are also expected to be english names for the states
	$g_resolution_enum_string		= '10:open,20:fixed,30:reopened,40:unable to duplicate,50:not fixable,60:duplicate,70:not a candidate,80:suspended,90:wont fix';
	$g_projection_enum_string		= '10:none,30:tweak,50:minor fix,70:major rework,90:redesign';
	$g_eta_enum_string			= '10:none,20:< 1 day,30:2-3 days,40:< 1 week,50:< 1 month,60:> 1 month';
	$g_sponsorship_enum_string 		= '0:Unpaid,1:Requested,2:Paid';

	$g_custom_field_type_enum_string  	= '0:string,1:numeric,2:float,3:enum,4:email,5:checkbox,6:list,7:multiselection list,8:date';

	#############################
	# COSMOS Javascript Variables
	#############################

	# allow the use of Javascript?
	$g_use_javascript		= ON;

	###########################
	# COSMOS Speed Optimisation
	###########################

	# Use compression of generated html if browser supports it
	# If you already have compression enabled in your php.ini file
	#  (either with zlib.output_compression or
	#  output_handler=ob_gzhandler) this option will be ignored.
	#
	# If you do not have zlib enabled in your PHP installation
	#  this option will also be ignored.  PHP 4.3.0 and later have
	#  zlib included by default.  Windows users should uncomment
	#  the appropriate line in their php.ini files to load
	#  the zlib DLL.  You can check what extensions are loaded
	#  by running "php -m" at the command line (look for 'zlib')
	$g_compress_html		= ON;

	# Use persistent database connections
	$g_use_persistent_connections	= OFF;

	###########################
	# Include files
	###########################

	# Specify your top/bottom include file (logos, banners, etc)
	# if a top file is supplied, the default COSMOS logo at the top will be hidden
	$g_bottom_include_page			= '%absolute_path%';
	$g_top_include_page			= '%absolute_path%';
	# CSS file
	$g_css_include_file			= '%path%css/default.css';
	$g_css_ie_include_file			= '%path%css/default_ie.css';
	# meta tags
	$g_meta_include_file			= '%absolute_path%meta_inc.php';

	###########################
	# Redirections
	###########################

	# Specify where the user should be sent after logging out.
	$g_logout_redirect_page			= '%path%login_page.php';

	###########################
	# Headers
	###########################

	# An array of headers to be sent with each page.
	# For example, to allow your cosmos installation to be viewed in a frame in IE 6
	#  when the frameset is not at the same hostname as the cosmos install, you need
	#  to add a P3P header.  You could try something like 'P3P: CP="CUR ADM"' in your
	#  config file, but make sure to check that the your policy actually matches with
	#  what you are promising. See
	#  http://msdn.microsoft.com/library/default.asp?url=/library/en-us/dnpriv/html/ie6privacyfeature.asp
	#  for more information.

	$g_custom_headers			= array();
	#$g_custom_headers[]			= 'P3P: CP="CUR ADM"';

	# Browser Caching Control
	# By default, we try to prevent the browser from caching anything. These two settings
	# will defeat this for some cases.
	#
	# Browser Page caching - This will allow the browser to cache all pages. The upside will
	#  be better performance, but there may be cases where obsolete information is displayed.
	#  Note that this will be bypassed (and caching is allowed) for the candidate report pages.
	# $g_allow_browser_cache = ON;
	#
	# File caching - This will allow the browser to cache downloaded files. Without this set,
	# there may be issues with IE receiving files, and launching support programs.
	# $g_allow_file_cache = ON;

	###########################
	# Decandidateging
	###########################

	# --- Timer ----------------------
	# Time page loads.  Shows at the bottom of the page.
	$g_show_timer			= OFF;

	# used for development only.  Leave OFF
	$g_debug_timer			= OFF;

	# Used for debugging e-mail feature, when set to OFF the emails work as normal.
	# when set to e-mail address, all e-mails are sent to this address with the
	# original To, Cc, Bcc included in the message body.
	#$g_debug_email			= 'somemail@gmail.comm';
	
	$g_debug_email=OFF;

	# --- Queries --------------------
	# Shows the total number/unique number of queries executed to serve the page.
	$g_show_queries_count	= OFF;

	# Indicates the access level required for a user to see the queries count / list.
	# This only has an effect if $g_show_queries_count is ON.  Note that this threshold
	# is compared against the user's default global access level rather than the
	# threshold based on the current active project.
	$g_show_queries_threshold = ADMINISTRATOR;

	# Shows the list of all queries that are executed in chronological order from top
	# to bottom.  This option is only effective when $g_show_queries_count is ON.
	# WARNING: Potential security hazard.  Only turn this on when you really
	# need it (for debugging/profiling)
	$g_show_queries_list	= OFF;
	
	# --- detailed error messages -----
	# Shows a list of variables and their values when an error is triggered
	# Only applies to error types configured to 'halt' in $g_display_errors, below
	# WARNING: Potential security hazard.  Only turn this on when you really
	# need it for debugging
	$g_show_detailed_errors	= OFF;

	# --- error display ---
	# what errors are displayed and how?
	# The options for display are:
	#  'halt' - stop and display traceback
	#  'inline' - display 1 line error and continue
	#  'none' - no error displayed
	# A developer might set this in config_inc.php as:
	#	$g_display_errors = array(
	#		E_WARNING => 'halt',
	#		E_NOTICE => 'halt',
	#		E_USER_ERROR => 'halt',
	#		E_USER_WARNING => 'none',
	#		E_USER_NOTICE => 'none'
	#	);

	$g_display_errors = array(
		E_WARNING => 'inline',
		E_NOTICE => 'none',
		E_USER_ERROR => 'halt',
		E_USER_WARNING => 'inline',
		E_USER_NOTICE => 'none'
	);

	# --- debug messages ---
	# If this option is turned OFF (default) page redirects will continue to
	#  function even if a non-fatal error occurs.  For debugging purposes, you
	#  can set this to ON so that any non-fatal error will prevent page redirection,
	#  allowing you to see the errors.
	# Only turn this option on for debugging
	$g_stop_on_errors		= OFF;

	# --- system logging ---
	# This controls the logging of information to a separate file for debug or audit
	# $g_log_level controls what information is logged
	#  see constant_inc.php for details on the log channels available
	#  e.g., $g_log_level = LOG_EMAIL | LOG_EMAIL_RECIPIENT | LOG_FILTERING | LOG_AJAX;
	#
	# $g_log_destination specifies the file where the data goes
	#   right now, only "file:<file path>" is supported
	#   e.g. (Linux), $g_log_destination = 'file:/tmp/cosmos.log';
	#   e.g. (Windows), $g_log_destination = 'file:c:/temp/cosmos.log';
	#   see http://www.php.net/error_log for details
	$g_log_level = LOG_EMAIL;
	$g_log_destination = 'file:/tmp/cosmos.log';

	##################
	# Custom Fields
	##################

	# Threshold needed to manage custom fields
	$g_manage_custom_fields_threshold = ADMINISTRATOR;

	# Threshold needed to link/unlink custom field to/from a project
	$g_custom_field_link_threshold = MANAGER;

	# Whether to start editng a custom field immediately after creating it
	$g_custom_field_edit_after_create = ON;


	#################
	# Custom Menus
	#################

	# Add custom options to the main menu.  For example:
	# $g_main_menu_custom_options = array(	array( "My Link",  MANAGER,       'my_link.php' ),
	#					array( "My Link2", ADMINISTRATOR, 'my_link2.php' ) );
	# Note that if the caption is found in custom_strings_inc.php, then it will be replaced by the
	# translated string.  Options will only be added to the menu if the current logged in user has
	# the appropriate access level.
	$g_main_menu_custom_options = array ();

	##########
	# Icons
	##########

	# Maps a file extension to a file type icon.  These icons are printed
	# next to project documents and candidate attachments.
	# Note:
	# - Extensions must be in lower case
	# - All icons will be displayed as 16x16 pixels.
	$g_file_type_icons = array(	
		'7z'	=> 'zip.gif',
		'ace'	=> 'zip.gif',
		'arj'	=> 'zip.gif',
		'bz2'	=> 'zip.gif',
		'c'		=> 'cpp.gif',
		'chm'	=> 'chm.gif',
		'cpp'	=> 'cpp.gif',
		'css'	=> 'css.gif',
		'csv'	=> 'csv.gif',
		'cxx'	=> 'cpp.gif',
		'doc'	=> 'doc.gif',
		'dot'	=> 'doc.gif',
		'eml'	=> 'eml.gif',
		'htm'	=> 'html.gif',
		'html'	=> 'html.gif',
		'gif'	=> 'gif.gif',
		'gz'	=> 'zip.gif',
		'jpe'	=> 'jpg.gif',
		'jpg'	=> 'jpg.gif',
		'jpeg'	=> 'jpg.gif',
		'log'	=> 'text.gif',
		'lzh'	=> 'zip.gif',
		'mhtml'	=> 'html.gif',
		'mid'	=> 'mid.gif',
		'midi'	=> 'mid.gif',
		'mov'	=> 'mov.gif',
		'msg'	=> 'eml.gif',
		'one'	=> 'one.gif',
		'pcx'	=> 'pcx.gif',
		'pdf'	=> 'pdf.gif',
		'png'	=> 'png.gif',
		'pot'	=> 'pot.gif',
		'pps'	=> 'pps.gif',
		'ppt'	=> 'ppt.gif',
		'pub'	=> 'pub.gif',
		'rar'	=> 'zip.gif',
		'reg'	=> 'reg.gif',
		'rtf'	=> 'doc.gif',
		'tar'	=> 'zip.gif',
		'tgz'	=> 'zip.gif',
		'txt'	=> 'text.gif',
		'uc2'	=> 'zip.gif',
		'vsd'	=> 'vsd.gif',
		'vsl'	=> 'vsl.gif',
		'vss'	=> 'vsd.gif',
		'vst'	=> 'vst.gif',
		'vsu'	=> 'vsd.gif',
		'vsw'	=> 'vsd.gif',
		'vsx'	=> 'vsd.gif',
		'vtx'	=> 'vst.gif',
		'wav'	=> 'wav.gif',
		'wbk'	=> 'wbk.gif',
		'wma'	=> 'wav.gif',
		'wmv'	=> 'mov.gif',
		'wri'	=> 'wri.gif',
		'xlk'	=> 'xls.gif',
		'xls'	=> 'xls.gif',
		'xlt'	=> 'xlt.gif',
		'xml'	=> 'xml.gif',
		'zip'	=> 'zip.gif',
		'?'		=> 'generic.gif' );

	# Icon associative arrays
	# Status to icon mapping
	$g_status_icon_arr = array (
		NONE      => 'priority_low_1.gif',
		LOW       => 'priority_low_2.gif',
		NORMAL    => 'priority_low_3.gif',
		HIGH      => 'priority_1.gif',
		URGENT    => 'priority_2.gif',
		IMMEDIATE => 'priority_3.gif'
	);
	# --------------------
	# Sort direction to icon mapping
	$g_sort_icon_arr = array (
		ASCENDING  => 'up.gif',
		DESCENDING => 'down.gif'
	);
	# --------------------
	# Read status to icon mapping
	$g_unread_icon_arr = array (
		READ         => 'cosmos_space.gif',
		UNREAD       => 'unread.gif'
	);
	# --------------------

	##################
	# My View Settings
	##################

	# Number of candidates shown in each box
	$g_my_view_candidate_count = 15;

	# Boxes to be shown and their order
	# A box that is not to be shown can have its value set to 0
	$g_my_view_boxes = array (
		'assigned'      => '2',
		'unassigned'    => '4',
		'reported'      => '5',
		'resolved'      => '6',
		'recent_mod'	=> '7',
		'monitored'	=> '3',
		'feedback'	=> '8',
		'verify'	=> '0',
		'myinterviews'	=> '1'
	);

	# Toggle whether 'My View' boxes are shown in a fixed position (i.e. adjacent boxes start at the same vertical position)
	$g_my_view_boxes_fixed_position = OFF;

	# Default page after Login or Set Project
	$g_default_home_page = 'my_view_page.php';

	######################
	# RSS Feeds
	######################

	# This flag enables or disables RSS syndication.  In the case where RSS syndication is not used,
	# it is recommended to set it to OFF.
	$g_rss_enabled = ON;

	# This seed is used as part of the inputs for calculating the authentication key for the RSS feeds.
	# If this seed changes, all the existing keys for the RSS feeds will become invalid.  This is 
	# defaulted to the database user name, but it is recommended to overwrite it with a specific value
	# on installation.
	$g_rss_key_seed = '%db_username%';

	######################
	# Bug Relationships
	######################

	# Enable support for candidate relationships where a candidate can be a related, dependent on, or duplicate of another.
	# See relationship_api.php for more details.
	$g_enable_relationship = OFF;

	# --- Relationship Graphs -----------
	# Show issue relationships using graphs.
	#
	# In order to use this feature, you must first install either GraphViz
	# (all OSs except Windows) or WinGraphviz (only Windows).
	#
	# Graphviz homepage:    http://www.research.att.com/sw/tools/graphviz/
	# WinGraphviz homepage: http://home.so-net.net.tw/oodtsen/wingraphviz/
	#
	# Refer to the notes near the top of core/graphviz_api.php and
	# core/relationship_graph_api.php for more information.

	# Enable relationship graphs support.
	$g_relationship_graph_enable		= ON;

	# Font name and size, as required by Graphviz. If Graphviz fails to run
	# for you, you are probably using a font name that gd can't find. On
	# Linux, try the name of the font file without the extension.
	$g_relationship_graph_fontname		= 'Arial';
	$g_relationship_graph_fontsize		= 8;

	# Local path where the above font is found on your system for Relationship Graphs
	# You shouldn't care about this on Windows since there is only one system
	# folder where fonts are installed and Graphviz already knows where it
	# is. On Linux and other unices, the default font search path is defined
	# during Graphviz compilation. If you are using a pre-compiled Graphviz
	# package provided by your distribution, probably the font search path was
	# already configured by the packager.
	#
	# If for any reason, the font file you want to use is not in any directory
	# listed on the default font search path list, you can either: (1) export
	# the DOTFONTPATH environment variable in your webserver startup script
	# or (2) use this config option conveniently available here. If you need
	# to list more than one directory, use colons to separate them.

	# Since 0.19.3 we use the $g_system_font_folder variable to define the font folder

	# Default dependency orientation. If you have issues with lots of childs
	# or parents, leave as 'horizontal', otherwise, if you have lots of
	# "chained" issue dependencies, change to 'vertical'.
	$g_relationship_graph_orientation	= 'horizontal';

	# Max depth for relation graphs. This only affects relation graphs,
	# dependency graphs are drawn to the full depth. A value of 3 is already
	# enough to show issues really unrelated to the one you are currently
	# viewing.
	$g_relationship_graph_max_depth		= 2;

	# If set to ON, clicking on an issue on the relationship graph will open
	# the candidate view page for that issue, otherwise, will navigate to the
	# relationship graph for that issue.
	$g_relationship_graph_view_on_click	= OFF;

	# Complete path to dot and neato tools. Your webserver must have execute
	# permission to these programs in order to generate relationship graphs.
	# NOTE: These are meaningless under Windows! Just ignore them!
	$g_dot_tool							= '/usr/bin/dot';
	$g_neato_tool						= '/usr/bin/neato';

	# Number of years in the future that custom date fields will display in
	# drop down boxes.
	$g_forward_year_count 				= 5 ;

	# Custom Group Actions
	#
	# This extensibility model allows developing new group custom actions.  This
	# can be implemented with a totally custom form and action pages or with a 
	# pre-implemented form and action page and call-outs to some functions.  These
	# functions are to be implemented in a predefined file whose name is based on
	# the action name.  For example, for an action to add a note, the action would
	# be EXT_ADD_NOTE and the file implementing it would be candidate_actiongroup_add_note_inc.php.
	# See implementation of this file for details.
	#
	# Sample:
	#
	# array(
	#	array(	'action' => 'my_custom_action',
	#			'label' => 'my_label',   // string to be passed to lang_get_defaulted()
	#			'form_page' => 'my_custom_action_page.php',
	#			'action_page' => 'my_custom_action.php'
	#   )
	#	array(	'action' => 'my_custom_action2',
	#			'form_page' => 'my_custom_action2_page.php',
	#			'action_page' => 'my_custom_action2.php'
	#   )
	#	array(	'action' => 'EXT_ADD_NOTE',  // you need to implement candidate_actiongroup_<action_without_'EXT_')_inc.php
	#		'label' => 'actiongroup_menu_add_note' // see strings_english.txt for this label
	#   )
	# );
	$g_custom_group_actions = array();

	#####################
	# Wiki Integration
	#####################
 
	# Wiki Integration Enabled?
	$g_wiki_enable = OFF;

	# Wiki Engine (supported engines: 'dokuwiki', 'mediawiki', 'xwiki')
	$g_wiki_engine = 'dokuwiki';
 
	# Wiki namespace to be used as root for all pages relating to this cosmos installation.
	$g_wiki_root_namespace = 'cosmos';
 
	# URL under which the wiki engine is hosted.  Must be on the same server.
	$g_wiki_engine_url = $t_protocol . '://' . $t_host . '/%wiki_engine%/';
	
	#####################
	# Recently Visited
	#####################

	# Whether to show the most recently visited issues or not.  At the moment we always track them even if this flag is off.
	$g_recently_visited = OFF;
	
	# The maximum number of issues to keep in the recently visited list.
	$g_recently_visited_count = 5;

	#####################
	# Bug Tagging
	#####################

	# String that will separate tags as entered for input
	$g_tag_separator = ',';

	# Access level required to view tags attached to a candidate
	$g_tag_view_threshold = DEVELOPER;

	# Access level required to attach tags to a candidate
	$g_tag_attach_threshold = DEVELOPER;

	# Access level required to detach tags from a candidate
	$g_tag_detach_threshold = DEVELOPER;

	# Access level required to detach tags attached by the same user
	$g_tag_detach_own_threshold = DEVELOPER;

	# Access level required to create new tags
	$g_tag_create_threshold = DEVELOPER;

	# Access level required to edit tag names and descriptions
	$g_tag_edit_threshold = DEVELOPER;

	# Access level required to edit descriptions by the creating user
	$g_tag_edit_own_threshold = REPORTER;

	#####################
	# Time tracking
	#####################

	# Turn on Time Tracking accounting
	$g_time_tracking_enabled = OFF;

	# A billing sums
	$g_time_tracking_with_billing = OFF;

	# Stop watch to build time tracking field
	$g_time_tracking_stopwatch = OFF;

	# access level required to view time tracking information
	$g_time_tracking_view_threshold = DEVELOPER;

	# access level required to add/edit time tracking information
	$g_time_tracking_edit_threshold = DEVELOPER;

	# access level required to run reports
	$g_time_tracking_reporting_threshold = DEVELOPER;

	#allow time tracking to be recorded without a candidatenote
	$g_time_tracking_without_note = ON;

	#############################
	# Profile Related Settings
	#############################

	# Add profile threshold
	$g_add_profile_threshold = MANAGER;

	# Threshold needed to be able to create and modify global profiles
	$g_manage_global_profile_threshold = MANAGER;

	# Allows the users to enter free text when reporting/updating issues 
	# for the profile related fields (i.e. platform, os, os build)
	$g_allow_freetext_in_profile_fields = ON;

	#############################
	# Twitter Settings
	#############################

	# The integration with twitter allows for a COSMOS installation to post
	# updates to a twitter account.  This feature will be disabled if username
	# is empty or if the curl extension is not enabled.

	# The twitter account user name.
	$g_twitter_username = '';
	
	$p = pathinfo( $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
	#$actual_link = "$_SERVER[HTTP_HOST]";
	#$a = "$_SERVER[PHP_SELF]";
	#$ar = explode ('/',$a);
	#$g_p = $actual_link . '/' . $ar[1];
	
	# The twitter account password.
	$g_twitter_password  = '';
	$g_TestResults_csv   = 'TestResults.csv';
	$g_MassImport_csv    = 'MassImport.csv';
	$g_NamesList_csv     = 'NamesList.csv';
	$g_ftp_path          = '/var/ftp/cosmos/';
	$g_answers_path      = $p['dirname'] . '/answers/';
	$g_solutions_path    = $p['dirname'] . '/tests_solutions/';
	$g_tests_pool_path   = $p['dirname'] . '/tests_pool/';
	$g_tests_path        = $p['dirname'] . '/tests/';
	$g_active_test       = 'active.yml';
	$g_answers_ext       = 'ans';

	$g_lower_bottom_button_link = 'http://www.google.com';
	
	/**
	 * Enable or disable usage of the ETA field.
	 * @global int $g_enable_eta
	 */
	$g_enable_eta = OFF;
	$g_enable_profiles = OFF;

	/**
	 * Enable or disable usage of the Projection field.
	 * @global int $g_enable_projection
	 */
	$g_enable_projection = OFF;

	/**
	 * Enable or disable usage of the Product Build field.
	 * @global int $g_enable_product_build
	 */
	$g_enable_product_build = OFF;

