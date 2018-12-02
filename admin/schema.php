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
	# $Id: schema.php,v 1.23.2.1 2007-10-13 22:34:56 giallu Exp $
	# --------------------------------------------------------
	
	# Each entry below defines the schema. The upgrade array consists of
	#  two elements
	# The first is the function to generate SQL statements (see adodb schema doc for more details)
	#  e.g., CreateTableSQL, DropTableSQL, ChangeTableSQL, RenameTableSQL, RenameColumnSQL,
	#  DropTableSQL, ChangeTableSQL, RenameTableSQL, RenameColumnSQL, AlterColumnSQL, DropColumnSQL
	#  A local function "InsertData" has been provided to add data to the db
	# The second parameter is an array of the parameters to be passed to the function.
	
	# An update identifier is inferred from the ordering of this table. ONLY ADD NEW CHANGES TO THE 
	#  END OF THE TABLE!!!
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_config_table'),"
			  config_id C(64) NOTNULL PRIMARY,
			  project_id I DEFAULT '0' PRIMARY,
			  user_id I DEFAULT '0' PRIMARY,
			  access_reqd I DEFAULT '0',
			  type I DEFAULT '90',
			  value XL NOTNULL",
Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_config',config_get('cosmos_config_table'),'config_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidate_file_table'),"
  id			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  candidate_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  title 		C(250) NOTNULL DEFAULT \" '' \",
  description 		C(250) NOTNULL DEFAULT \" '' \",
  diskfile 		C(250) NOTNULL DEFAULT \" '' \",
  filename 		C(250) NOTNULL DEFAULT \" '' \",
  folder 		C(250) NOTNULL DEFAULT \" '' \",
  filesize 		 I NOTNULL DEFAULT '0',
  file_type 		C(250) NOTNULL DEFAULT \" '' \",
  date_added 		T NOTNULL DEFAULT '1970-01-01 00:00:01',
  content 		B NOTNULL
  ",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_candidate_file_candidate_id',config_get('cosmos_candidate_file_table'),'candidate_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidate_history_table'),"
  id 			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  user_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  candidate_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  date_modified 	T NOTNULL DEFAULT '1970-01-01 00:00:01',
  field_name 		C(32) NOTNULL DEFAULT \" '' \",
  old_value 		C(128) NOTNULL DEFAULT \" '' \",
  new_value 		C(128) NOTNULL DEFAULT \" '' \",
  type 			I2 NOTNULL DEFAULT '0'
  ",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_candidate_history_candidate_id',config_get('cosmos_candidate_history_table'),'candidate_id'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_history_user_id',config_get('cosmos_candidate_history_table'),'user_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidate_monitor_table'),"
  user_id 		 I  UNSIGNED NOTNULL PRIMARY DEFAULT '0',
  candidate_id 		 I  UNSIGNED NOTNULL PRIMARY DEFAULT '0'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidate_relationship_table'),"
  id 			 I  UNSIGNED NOTNULL AUTOINCREMENT PRIMARY,
  source_candidate_id		 I  UNSIGNED NOTNULL DEFAULT '0',
  destination_candidate_id 	 I  UNSIGNED NOTNULL DEFAULT '0',
  relationship_type 	I2 NOTNULL DEFAULT '0'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_relationship_source',config_get('cosmos_candidate_relationship_table'),'source_candidate_id'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_relationship_destination',config_get('cosmos_candidate_relationship_table'),'destination_candidate_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidate_table'),"
  id 			 I  UNSIGNED PRIMARY NOTNULL AUTOINCREMENT,
  project_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  reporter_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  handler_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  duplicate_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  priority 		I2 NOTNULL DEFAULT '30',
  severity 		I2 NOTNULL DEFAULT '50',
  reproducibility 	I2 NOTNULL DEFAULT '10',
  status 		I2 NOTNULL DEFAULT '10',
  resolution 		I2 NOTNULL DEFAULT '10',
  projection 		I2 NOTNULL DEFAULT '10',
  category 		C(64) NOTNULL DEFAULT \" '' \",
  date_submitted 	T NOTNULL DEFAULT '1970-01-01 00:00:01',
  last_updated 		T NOTNULL DEFAULT '1970-01-01 00:00:01',
  eta 			I2 NOTNULL DEFAULT '10',
  candidate_text_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  os 			C(32) NOTNULL DEFAULT \" '' \",
  os_build 		C(32) NOTNULL DEFAULT \" '' \",
  platform 		C(32) NOTNULL DEFAULT \" '' \",
  version 		C(64) NOTNULL DEFAULT \" '' \",
  fixed_in_version 	C(64) NOTNULL DEFAULT \" '' \",
  build 		C(32) NOTNULL DEFAULT \" '' \",
  profile_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  view_state 		I2 NOTNULL DEFAULT '10',
  summary 		C(128) NOTNULL DEFAULT \" '' \",
  sponsorship_total 	 I  NOTNULL DEFAULT '0',
  sticky		L  NOTNULL DEFAULT '0'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_candidate_sponsorship_total',config_get('cosmos_candidate_table'),'sponsorship_total'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_candidate_fixed_in_version',config_get('cosmos_candidate_table'),'fixed_in_version'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_candidate_status',config_get('cosmos_candidate_table'),'status'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_project',config_get('cosmos_candidate_table'),'project_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidate_text_table'),"
  id 			 I  PRIMARY UNSIGNED NOTNULL AUTOINCREMENT,
  description 		XL NOTNULL,
  steps_to_reproduce 	XL NOTNULL,
  additional_information XL NOTNULL
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidatenote_table'),"
  id 			 I  UNSIGNED PRIMARY NOTNULL AUTOINCREMENT,
  candidate_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  reporter_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  candidatenote_text_id 	 I  UNSIGNED NOTNULL DEFAULT '0',
  view_state 		I2 NOTNULL DEFAULT '10',
  date_submitted 	T NOTNULL DEFAULT '1970-01-01 00:00:01',
  last_modified 	T NOTNULL DEFAULT '1970-01-01 00:00:01',
  note_type 		 I  DEFAULT '0',
  note_attr 		C(250) DEFAULT \" '' \"
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_candidate',config_get('cosmos_candidatenote_table'),'candidate_id'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_last_mod',config_get('cosmos_candidatenote_table'),'last_modified'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_candidatenote_text_table'),"
  id 			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  note 			XL NOTNULL
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_custom_field_project_table'),"
  field_id 		 I  NOTNULL PRIMARY DEFAULT '0',
  project_id 		 I  UNSIGNED PRIMARY NOTNULL DEFAULT '0',
  sequence 		I2 NOTNULL DEFAULT '0'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_custom_field_string_table'),"
  field_id 		 I  NOTNULL PRIMARY DEFAULT '0',
  candidate_id 		 I  NOTNULL PRIMARY DEFAULT '0',
  value 		C(255) NOTNULL DEFAULT \" '' \"
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_custom_field_candidate',config_get('cosmos_custom_field_string_table'),'candidate_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_custom_field_table'),"
  id 			 I  NOTNULL PRIMARY AUTOINCREMENT,
  name 			C(64) NOTNULL DEFAULT \" '' \",
  type 			I2 NOTNULL DEFAULT '0',
  possible_values 	C(255) NOTNULL DEFAULT \" '' \",
  default_value 	C(255) NOTNULL DEFAULT \" '' \",
  valid_regexp 		C(255) NOTNULL DEFAULT \" '' \",
  access_level_r 	I2 NOTNULL DEFAULT '0',
  access_level_rw 	I2 NOTNULL DEFAULT '0',
  length_min 		 I  NOTNULL DEFAULT '0',
  length_max 		 I  NOTNULL DEFAULT '0',
  advanced 		L NOTNULL DEFAULT '0',
  require_report 	L NOTNULL DEFAULT '0',
  require_update 	L NOTNULL DEFAULT '0',
  display_report 	L NOTNULL DEFAULT '1',
  display_update 	L NOTNULL DEFAULT '1',
  require_resolved 	L NOTNULL DEFAULT '0',
  display_resolved 	L NOTNULL DEFAULT '0',
  display_closed 	L NOTNULL DEFAULT '0',
  require_closed 	L NOTNULL DEFAULT '0'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
//updated by Chirag A to pre-filled some values
$upgrade[] = Array('InsertData', Array( config_get('cosmos_custom_field_table'), 
    "(`id`, `name`, `type`, `possible_values`, `default_value`, `valid_regexp`, `access_level_r`, `access_level_rw`, `length_min`, `length_max`, `advanced`, `require_report`, `require_update`, `display_report`, `display_update`, `require_resolved`, `display_resolved`, `display_closed`, `require_closed`) 
VALUES (2, 'Address', 0, '', '', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(3, 'Best Time to Call', 0, '', '', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(4, 'Current Employer', 0, '', '', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(5, 'Date Available', 8, '', '', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(6, 'Current Pay', 1, '', '', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(7, 'Desired Pay', 1, '', '', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(8, 'Willing to Relocate', 6, 'Yes|No', 'Yes', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(9, 'Gender', 6, 'Male|Female', 'Male', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0),
(10, 'Rating', 1, '1|2|3|4|5|6|7|8|9|10', '1', '', 10, 10, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0),
(11, 'Industry Certifications', 6, 'Microsoft ISA Certification|MCSD||CISSP|MCSE', '', '', 10, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0);
" ) );			 
			 
$upgrade[] = Array('CreateIndexSQL',Array('idx_custom_field_name',config_get('cosmos_custom_field_table'),'name'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_filters_table'),"
  id 			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  user_id 		 I  NOTNULL DEFAULT '0',
  project_id 		 I  NOTNULL DEFAULT '0',
  is_public 		L DEFAULT NULL,
  name 			C(64) NOTNULL DEFAULT \" '' \",
  filter_string 	XL NOTNULL
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_news_table'),"
  id 			 I  UNSIGNED PRIMARY NOTNULL AUTOINCREMENT,
  project_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  poster_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  date_posted 		T NOTNULL DEFAULT '1970-01-01 00:00:01',
  last_modified 	T NOTNULL DEFAULT '1970-01-01 00:00:01',
  view_state 		I2 NOTNULL DEFAULT '10',
  announcement 		L NOTNULL DEFAULT '0',
  headline 		C(64) NOTNULL DEFAULT \" '' \",
  body 			XL NOTNULL
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_project_category_table'),"
  project_id 		 I  UNSIGNED NOTNULL PRIMARY DEFAULT '0',
  category 		C(64) NOTNULL PRIMARY DEFAULT \" '' \",
  user_id 		 I  UNSIGNED NOTNULL DEFAULT '0'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_project_file_table'),"
  id 			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  project_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  title 		C(250) NOTNULL DEFAULT \" '' \",
  description 		C(250) NOTNULL DEFAULT \" '' \",
  diskfile 		C(250) NOTNULL DEFAULT \" '' \",
  filename 		C(250) NOTNULL DEFAULT \" '' \",
  folder 		C(250) NOTNULL DEFAULT \" '' \",
  filesize 		 I NOTNULL DEFAULT '0',
  file_type 		C(250) NOTNULL DEFAULT \" '' \",
  date_added 		T NOTNULL DEFAULT '1970-01-01 00:00:01',
  content 		B NOTNULL
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_project_hierarchy_table'),"
			  child_id I UNSIGNED NOTNULL,
			  parent_id I UNSIGNED NOTNULL",
Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_project_table'),"
  id 			 I  UNSIGNED PRIMARY NOTNULL AUTOINCREMENT,
  name 			C(128) NOTNULL DEFAULT \" '' \",
  status 		I2 NOTNULL DEFAULT '10',
  enabled 		L NOTNULL DEFAULT '1',
  view_state 		I2 NOTNULL DEFAULT '10',
  access_min 		I2 NOTNULL DEFAULT '10',
  file_path 		C(250) NOTNULL DEFAULT \" '' \",
  description 		XL NOTNULL
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_project_id',config_get('cosmos_project_table'),'id'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_project_name',config_get('cosmos_project_table'),'name',Array('UNIQUE')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_project_view',config_get('cosmos_project_table'),'view_state'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_project_user_list_table'),"
  project_id 		 I  UNSIGNED PRIMARY NOTNULL DEFAULT '0',
  user_id 		 I  UNSIGNED PRIMARY NOTNULL DEFAULT '0',
  access_level 		I2 NOTNULL DEFAULT '10'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array( 'CreateIndexSQL',Array('idx_project_user',config_get('cosmos_project_user_list_table'),'user_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_project_version_table'),"
  id 			 I  NOTNULL PRIMARY AUTOINCREMENT,
  project_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  version 		C(64) NOTNULL DEFAULT \" '' \",
  date_order 		T NOTNULL DEFAULT '1970-01-01 00:00:01',
  description 		XL NOTNULL,
  released 		L NOTNULL DEFAULT '1'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_project_version',config_get('cosmos_project_version_table'),'project_id,version',Array('UNIQUE')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_sponsorship_table'),"
  id 			 I  NOTNULL PRIMARY AUTOINCREMENT,
  candidate_id 		 I  NOTNULL DEFAULT '0',
  user_id 		 I  NOTNULL DEFAULT '0',
  amount 		 I  NOTNULL DEFAULT '0',
  logo 			C(128) NOTNULL DEFAULT \" '' \",
  url 			C(128) NOTNULL DEFAULT \" '' \",
  paid 			L NOTNULL DEFAULT '0',
  date_submitted 	T NOTNULL DEFAULT '1970-01-01 00:00:01',
  last_updated 		T NOTNULL DEFAULT '1970-01-01 00:00:01'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_sponsorship_candidate_id',config_get('cosmos_sponsorship_table'),'candidate_id'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_sponsorship_user_id',config_get('cosmos_sponsorship_table'),'user_id'));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_tokens_table'),"
			  id I NOTNULL PRIMARY AUTOINCREMENT,
			  owner I NOTNULL,
			  type I NOTNULL,
			  timestamp T NOTNULL,
			  expiry T,
			  value XL NOTNULL",
Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_user_pref_table'),"
  id 			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  user_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  project_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  default_profile 	 I  UNSIGNED NOTNULL DEFAULT '0',
  default_project 	 I  UNSIGNED NOTNULL DEFAULT '0',
  advanced_report 	L NOTNULL DEFAULT '0',
  advanced_view 	L NOTNULL DEFAULT '0',
  advanced_update 	L NOTNULL DEFAULT '0',
  refresh_delay 	 I  NOTNULL DEFAULT '0',
  redirect_delay 	L NOTNULL DEFAULT '0',
  candidatenote_order 	C(4) NOTNULL DEFAULT 'ASC',
  email_on_new 		L NOTNULL DEFAULT '0',
  email_on_assigned 	L NOTNULL DEFAULT '0',
  email_on_feedback 	L NOTNULL DEFAULT '0',
  email_on_resolved	L NOTNULL DEFAULT '0',
  email_on_closed 	L NOTNULL DEFAULT '0',
  email_on_reopened 	L NOTNULL DEFAULT '0',
  email_on_candidatenote 	L NOTNULL DEFAULT '0',
  email_on_status 	L NOTNULL DEFAULT '0',
  email_on_priority 	L NOTNULL DEFAULT '0',
  email_on_priority_min_severity 	I2 NOTNULL DEFAULT '10',
  email_on_status_min_severity 	I2 NOTNULL DEFAULT '10',
  email_on_candidatenote_min_severity 	I2 NOTNULL DEFAULT '10',
  email_on_reopened_min_severity 	I2 NOTNULL DEFAULT '10',
  email_on_closed_min_severity 	I2 NOTNULL DEFAULT '10',
  email_on_resolved_min_severity 	I2 NOTNULL DEFAULT '10',
  email_on_feedback_min_severity	I2 NOTNULL DEFAULT '10',
  email_on_assigned_min_severity 	I2 NOTNULL DEFAULT '10',
  email_on_new_min_severity 	I2 NOTNULL DEFAULT '10',
  email_candidatenote_limit 	I2 NOTNULL DEFAULT '0',
  language 		C(32) NOTNULL DEFAULT 'english'
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_user_print_pref_table'),"
  user_id 		 I  UNSIGNED NOTNULL PRIMARY DEFAULT '0',
  print_pref 		C(27) NOTNULL DEFAULT \" '' \"
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_user_profile_table'),"
  id 			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  user_id 		 I  UNSIGNED NOTNULL DEFAULT '0',
  platform 		C(32) NOTNULL DEFAULT \" '' \",
  os 			C(32) NOTNULL DEFAULT \" '' \",
  os_build 		C(32) NOTNULL DEFAULT \" '' \",
  description 		XL NOTNULL
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_user_table'),"
  id 			 I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  username 		C(32) NOTNULL DEFAULT \" '' \",
  realname 		C(64) NOTNULL DEFAULT \" '' \",
  email 		C(64) NOTNULL DEFAULT \" '' \",
  password 		C(32) NOTNULL DEFAULT \" '' \",
  date_created 		T NOTNULL DEFAULT '1970-01-01 00:00:01',
  last_visit 		T NOTNULL DEFAULT '1970-01-01 00:00:01',
  enabled		L NOTNULL DEFAULT '1',
  protected 		L NOTNULL DEFAULT '0',
  access_level 		I2 NOTNULL DEFAULT '10',
  login_count 		 I  NOTNULL DEFAULT '0',
  lost_password_request_count 	I2 NOTNULL DEFAULT '0',
  failed_login_count 	I2 NOTNULL DEFAULT '0',
  cookie_string 	C(64) NOTNULL DEFAULT \" '' \"
",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_user_cookie_string',config_get('cosmos_user_table'),'cookie_string',Array('UNIQUE')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_user_username',config_get('cosmos_user_table'),'username',Array('UNIQUE')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_enable',config_get('cosmos_user_table'),'enabled'));
$upgrade[] = Array('CreateIndexSQL',Array('idx_access',config_get('cosmos_user_table'),'access_level'));
$upgrade[] = Array('InsertData', Array( config_get('cosmos_user_table'), 
    "(username, realname, email, password, date_created, last_visit, enabled, protected, access_level, login_count, lost_password_request_count, failed_login_count, cookie_string) VALUES 
        ('administrator', '', 'root@localhost', '63a9f0ea7bb98050796b649e85481845', " . db_now() . ", " . db_now() . ", 1, 0, 90, 3, 0, 0, '" . 
             md5( mt_rand( 0, mt_getrandmax() ) + mt_rand( 0, mt_getrandmax() ) ) . md5( time() ) . "')" ) );
$upgrade[] = Array('AlterColumnSQL', Array( config_get( 'cosmos_candidate_history_table' ), "old_value C(255) NOTNULL" ) );
$upgrade[] = Array('AlterColumnSQL', Array( config_get( 'cosmos_candidate_history_table' ), "new_value C(255) NOTNULL" ) );

$upgrade[] = Array('CreateTableSQL',Array(config_get('cosmos_email_table'),"
  email_id 		I  UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
  email		 	C(64) NOTNULL DEFAULT \" '' \",
  subject		C(250) NOTNULL DEFAULT \" '' \",
  submitted 	T NOTNULL DEFAULT '1970-01-01 00:00:01',
  metadata 		XL NOTNULL,
  body 			XL NOTNULL
  ",Array('mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS')));
$upgrade[] = Array('CreateIndexSQL',Array('idx_email_id',config_get('cosmos_email_table'),'email_id'));
$upgrade[] = Array('AddColumnSQL',Array(config_get('cosmos_candidate_table'), "target_version C(64) NOTNULL DEFAULT \" '' \""));
$upgrade[] = Array('AddColumnSQL',Array(config_get('cosmos_candidatenote_table'), "time_tracking I UNSIGNED NOTNULL DEFAULT \" 0 \""));
$upgrade[] = Array('CreateIndexSQL',Array('idx_diskfile',config_get('cosmos_candidate_file_table'),'diskfile'));
$upgrade[] = Array('AlterColumnSQL', Array( config_get( 'cosmos_user_print_pref_table' ), "print_pref C(64) NOTNULL" ) );
$upgrade[] = Array('AlterColumnSQL', Array( config_get( 'cosmos_candidate_history_table' ), "field_name C(64) NOTNULL" ) );

# Release marker: 1.1.0a4

$upgrade[] = Array('CreateTableSQL', Array( config_get( 'cosmos_tag_table' ), "
	id				I		UNSIGNED NOTNULL PRIMARY AUTOINCREMENT,
	user_id			I		UNSIGNED NOTNULL DEFAULT '0',
	name			C(100)	NOTNULL PRIMARY DEFAULT \" '' \",
	description		XL		NOTNULL,
	date_created	T		NOTNULL DEFAULT '1970-01-01 00:00:01',
	date_updated	T		NOTNULL DEFAULT '1970-01-01 00:00:01'
	", Array( 'mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS' ) ) );
$upgrade[] = Array('CreateTableSQL', Array( config_get( 'cosmos_candidate_tag_table' ), "
	candidate_id			I	UNSIGNED NOTNULL PRIMARY DEFAULT '0',
	tag_id			I	UNSIGNED NOTNULL PRIMARY DEFAULT '0',
	user_id			I	UNSIGNED NOTNULL DEFAULT '0',
	date_attached	T	NOTNULL DEFAULT '1970-01-01 00:00:01'
	", Array( 'mysql' => 'ENGINE=MyISAM', 'pgsql' => 'WITHOUT OIDS' ) ) );

$upgrade[] = Array('CreateIndexSQL', Array( 'idx_typeowner', config_get( 'cosmos_tokens_table' ), 'type, owner' ) );
?>
