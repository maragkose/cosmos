<?php
#
# Created by Emmanouil Maragkos
#
	require_once( 'core.php' );
	require_once( 'csv_form_keys.php' );
	$t_core_path = config_get( 'core_path' );
	$TestResults_csv = config_get( 'TestResults_csv' );
	$MassImport_csv = config_get( 'MassImport_csv' );
	$NamesList_csv = config_get( 'NamesList_csv' );
	$ftpPath = config_get( 'ftp_path' );
	require_once( $t_core_path.'candidate_api.php' );
	
	access_ensure_global_level( config_get( 'import_threshold' ) );      
	
###########################################################################
function updateResults() {
###########################################################################
	$TestResults_csv = config_get( 'TestResults_csv' );
	$ftpPath = config_get( 'ftp_path' );
	$row = 1;
	$resultsFile = $ftpPath . $TestResults_csv;
	#$resultsFile = '/var/ftp/cosmos/TestResults.csv';
	if (($handle = fopen($resultsFile, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		$col = count($data);
		$candidate_name = $data[1] . ' ' . $data[0];
		$ccscore = $data[2];
		$unixscore = $data[3];
		$netscore = $data[4];
		$total_score = $data[5];
		$tool_result = $data[6];
		$profile = '|' . $data[7] . '|';
		$answeredCplus = $data[10];

		if ($answeredCplus == 'Yes'){
			$cscore = 0;
			$cplusscore = $ccscore;
		} else {
			$cscore = $ccscore;
			$cplusscore = 0; 
		}	
		if ($row == '1'){
			$row++;
			continue;
		}	
		$row++;
			set_custom_field("CplusplusTestResult", $candidate_name, $cplusscore);
			set_custom_field("CTestResult", $candidate_name, $cscore);
			set_custom_field("LinuxScriptingTestResult", $candidate_name, $unixscore);
			set_custom_field("NetworkingTestResult", $candidate_name, $netscore);
			set_custom_field("OverallTestScore", $candidate_name, $total_score);
			set_custom_field("Tool Suggestion", $candidate_name, $tool_result);
			set_custom_field("Test Profile", $candidate_name, $profile);
	    }

	fclose($handle);
	}

	$archive = "/opt/lampp/htdocs/cosmos/csv_archive/";
	$new_location = $archive . basename($TestResults_csv) . '.csvo';
	$res = rename ($resultsFile, $new_location);
	if ( !$res ) {
		trigger_error( ERROR_FILE_MOVE_FAILED, ERROR );
	}	
} # end function updateResult()
	
###########################################################################
function massImport() {
###########################################################################
	$csv_row = array();
	$csv_header = array();
	$row = 1;
	$MassImport_csv = config_get( 'MassImport_csv' );
	$ftpPath = config_get( 'ftp_path' );
	$MassImport_csv = $ftpPath . $MassImport_csv;
	if (($handle = fopen("$MassImport_csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
			$col = count($data);
			if ($row == 1) {
				for($i = 0;$i<=$col;$i++) {
			 		$csv_header[$i] = $data[$i];
			    	}
		    	} else {
				for($i = 0;$i<=$col;$i++) {
					$csv_row["$csv_header[$i]"] = $data[$i];
		    		}
				importRow($csv_row);
			}
			$row++;
		} # end while
	fclose($handle);
	} # end if
} # end massImport

###########################################################################
function namesImport() {
###########################################################################
	$csv_row = array();
	$csv_header = array();
	$row = 1;
	$NamesList_csv = config_get( 'NamesList_csv' );
	$ftpPath = config_get( 'ftp_path' );
	$NamesList_csv = $ftpPath . $NamesList_csv;
	if (($handle = fopen("$NamesList_csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 2000, ";")) !== FALSE) {
			importName($data);
		} # end while
	fclose($handle);
	} # end if


}
###########################################################################
function importName($import_r) {
###########################################################################
	echo $import_r[0];

	$candidate_data = new BugData;
	$candidate_data->category		= 'Not defined yet';		 			
	$candidate_data->view_state		= gpc_get_int( 'view_state', config_get( 'default_candidate_view_status' ) );
	$candidate_data->status			= 90;
	$candidate_data->summary		= $import_r[0];	
	$candidate_data->description		= 'Check CV';
	$candidate_data->project_id		= 1; 
	$candidate_data->reporter_id		= auth_get_current_user_id();
	$candidate_data->summary		= trim( $candidate_data->summary );
	$candidate_data->reproducibility	= getSNumber('Other');
	$candidate_data->severity		= getQNumber('Other');
	#$candidate_data->handler_id		= gpc_get_int( 'handler_id', 0 );
	#$f_file				= gpc_get_file( 'file', null ); #@@@ (thraxisp) Note that this always returns a structure

	#
	# Create the main report 
	#
	#var_dump($candidate_data);
	$t_new_candidate_id = candidate_create( $candidate_data, true );
	#
	# If duplicate candidate, do not stop just return and will move on to the next row
	#
	if ($t_new_candidate_id == 0){
		return;
	}

}
###########################################################################
function importRow($import_r) {
###########################################################################
	#var_dump($import_r);
	$import_r['Date of Birth'] = strtotime($import_r['Date of Birth']);
	$import_r['1st Interview Date'] = strtotime($import_r['1st Interview Date']);
	$import_r['2nd Interview Date'] = strtotime($import_r['2nd Interview Date']);
	$import_r['Hiring Date'] = strtotime($import_r['Hiring Date']);

	$notes = $import_r['Notes 1'] . ' ' . $import_r['Notes 2'] . ' ' .  $import_r['Notes 3'] . ' ' . $import_r['Notes 4'] . ' ' .  $import_r['Notes 5'];
	
	$candidate_data = new BugData;
	$candidate_data->category		= 'Not defined yet';		 			
	$candidate_data->build			= $import_r['Contact No'];		 			
	$candidate_data->view_state		= gpc_get_int( 'view_state', config_get( 'default_candidate_view_status' ) );
	$candidate_data->status			= 90;
	$candidate_data->reproducibility	= getSNumber('Other');
	$candidate_data->severity		= getQNumber('Other');
	$candidate_data->summary		= $import_r['Name'];	
	$candidate_data->resolution		= getFinalResultNumber($import_r['Final result']);	
	$candidate_data->description		= 'Check CV';
	$candidate_data->project_id		= 6; # Hardcoded job order for Join Us.. 
	$candidate_data->reporter_id		= auth_get_current_user_id();
	$candidate_data->summary		= trim( $candidate_data->summary );
	#
	# Create the main report 
	#
	$t_new_candidate_id = candidate_create( $candidate_data, true );
	#
	# If duplicate candidate, do not stop just update data and will move on to the next row
	#
	if ($t_new_candidate_id == 0){
		$c_id = get_id_from_name($candidate_data->summary);
		candidate_update( $c_id, $candidate_data ); 
	}
	$t_related_custom_field_ids = custom_field_get_linked_ids( $candidate_data->project_id );
	
	foreach( $t_related_custom_field_ids as $t_id ) {
		$t_def = custom_field_get_definition( $t_id );
		if (isset($import_r["$t_def[1]"])) {
			#echo  "$t_def[1]" . ' = ' . $import_r["$t_def[1]"] . '<br />';
			#
			# Set the custom field values to the created report. 
			# Get the custom field name from the position 1 of t_def custom structure
			#
			if( !custom_field_set_value( $t_id, $t_new_candidate_id, $import_r["$t_def[1]"] ) )  {
				error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
				trigger_error( ERROR_CUSTOM_FIELD_INVALID_VALUE, ERROR );
			}
		}
	}
	candidatenote_add ( $t_new_candidate_id, $notes, '0:00', false, 0, '', null, false);
} # END import_row

function evaluateAnswers() {

	#
	# Read CSV Answer to an array
	#
	$scores = array();
	$glob_str = config_get('answers_path') . '*.' . config_get('answers_ext'); 
	foreach (glob($glob_str) as $filename) {
		$csv_contents = array();
		$keys = array();
		$row = 0;
		if (($handle = fopen("$filename", "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, "~")) !== FALSE) {
				$num = count($data);
				for ($c=0; $c < $num; $c++) {
					if ($row==0){
						$keys[$c] = $data[$c];
					} else {
						$values[$c] = $data[$c];
					}
				}
				$row++;
			}
			$csv_contents = array_combine($keys, $values);
			$scores = calculateScore($csv_contents);
			fclose($handle);
		}
	}
	$candidate_id = $csv_contents['personal_data__ID'];
	$candidate_name = $csv_contents['personal_data__Name'];
	$candidate_surname = $csv_contents['personal_data__Surname'];
	echo $candidate_surname;
	echo $candidate_name;
	echo $candidate_id;
	$candidate = $candidate_surname . ' ' . $candidate_name;
	$db_id = get_id_from_name($candidate, true);
	if($db_id == $candidate_id){
		echo "ok";
		$total_score = $scores[0] + $scores[1] + $scores[2];
		#set_custom_field("CplusplusTestResult", $candidate, $cplusscore);
		set_custom_field("CTestResult", $candidate, $scores[0]);
		set_custom_field("LinuxScriptingTestResult", $candidate, $scores[1]);
		set_custom_field("NetworkingTestResult", $candidate, $scores[2]);
		set_custom_field("OverallTestScore", $candidate, $total_score);
		#set_custom_field("Tool Suggestion", $candidate, $tool_result);
		#set_custom_field("Test Profile", $candidate, $profile);
	}
}

function calculateScore($answers) {

	$csv_contents = array();
	$solutions = array();
	$answer_file_path = config_get('solutions_path') . $answers["test_name"] . ".csv";
	#echo $answer_file_path; die("234");
	$answer_file = fopen("$answer_file_path", "r");
	$row = 0;
	$score_set1 = 0;
	$score_set2 = 0;
	$score_set3 = 0;
	while (($data = fgetcsv($answer_file, 1000, ":")) !== FALSE) {
		$solutions[$data[0]] = $data[1];
	}
	foreach ($answers as $question => $value) {

		if (array_key_exists($question, $solutions)){
			
			$choice = explode(")", $value);
			$option_answered = $choice[0];

			if (!strncmp($question, 'set1', strlen('set1'))){
				if ($solutions[$question] == $option_answered){
					$score_set1++;
				}
			}
			if (!strncmp($question, 'set2', strlen('set2'))){
				if ($solutions[$question] == $option_answered){
					$score_set2++;
				}
			}
			if (!strncmp($question, 'set3', strlen('set3'))){
				if ($solutions[$question] == $option_answered){
					$score_set3++;
				}
			}
		}
	}
	$scores = array();
	$scores[0] = $score_set1;
	$scores[1] = $score_set2;
	$scores[2] = $score_set3;
	return $scores;
}
#======================================================================================================
# MAIN SECTION 
#======================================================================================================

	#$project_id = helper_get_current_project();
	#Manos todo need to find a dynamic way. it seems that only when cookie is set
	#the function returns current project. Need always the first one...	
        $project_id = 1; 
	$archive = "/opt/lampp/htdocs/cosmos/csv_archive/";
	$eval_delete_clicked = false;
	$eval_clicked = false;
	$csv_delete_clicked = false;
        $csv_import_all = false;
	$csv_import_sel = false;

	$csv_files = array();
	$eval_files = array();
	#
	# Iterate POST and store CSV filenames to csv_files array
	#
	foreach( $_POST as $posted_files ) {
	    if( is_array( $posted_files ) ) {
		    foreach( $posted_files as $csv_f ) {
			$ext = pathinfo($csv_f, PATHINFO_EXTENSION);
			if($ext == 'csv'){
				array_push($csv_files, "$csv_f");
			} else {
				array_push($eval_files, "$csv_f");
			}
	        }
	    }
	}
	if (empty($csv_files)) {
		$no_selection = true;
	}
	#
	# Delete button is clicked
	#
	if (isset($_POST['delete_button'])) {
		$csv_delete_clicked = true;

		foreach ($csv_files as $f){
			unlink($f);
		}
	} else if (isset($_POST['evaluate_delete_button'])) {
		$eval_delete_clicked = true;

		foreach ($eval_files as $f){
			unlink(config_get('answers_path') . $f);
		}

	#
	# Import All Button clicked
	#
	} else if (isset($_POST['import_all_button'])){
	    $csv_import_all = true;
	    $csv_files = array();
	    
	    foreach (glob("/var/ftp/cosmos/*.csv") as $filename) {
		
		    $check_file = basename ($filename);
		    if ( strcmp($check_file, $TestResults_csv ) == 0) {
			    updateResults();
		    		    
		    } else if ( strcmp($check_file, $MassImport_csv ) == 0) {
			massImport();	
		    } else if ( strcmp($check_file, $NamesList_csv ) == 0) {
			namesImport();	
		    } else {
			array_push($csv_files, "$filename");
		    }
	    }
	} else if (isset($_POST['evaluate_button'])){
		evaluateAnswers();
	#
	# Import selected Button clicked
	#
	} else if (isset($_POST['import_selected_button'])){
		$csv_import_sel = true;
	} else {
		$no_selection = true;
	}
        
	# 
	# Iterate csv_files and create a new application form for each csv
	# only when import buttons are clicked. 
	#
	if ( !$csv_delete_clicked) {
	    foreach ($csv_files as $f) {
		$csv_contents = array();
		$row = 1;
		if (($handle = fopen("$f", "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		            $col = count($data);
			    $row++;
			    $csv_contents["$data[0]"] = $data[1];
		    }
		fclose($handle);
		}
		#var_dump($csv_contents);
		$candidate_data = new BugData;
		$candidate_data->build			= $csv_contents[$key_Contact_Number];		 			
		$candidate_data->platform        	= $csv_contents[$key_City];	
		$candidate_data->os			= $csv_contents[$key_Address];
		$candidate_data->os_build		= $csv_contents[$key_Email];
		$candidate_data->view_state		= gpc_get_int( 'view_state', config_get( 'default_candidate_view_status' ) );
		$candidate_data->category		= $csv_contents[$key_Specialisation];	
		$candidate_data->reproducibility	= getSNumber($csv_contents[$key_Source]);
		$candidate_data->severity		= getQNumber($csv_contents[$key_Highest_Qualification]);
		$candidate_data->summary		= $csv_contents[$key_Name];	
		$candidate_data->description		= $csv_contents[$key_Degree_Title];
		$candidate_data->project_id		= $project_id;
		$candidate_data->reporter_id		= auth_get_current_user_id();
		$candidate_data->summary		= trim( $candidate_data->summary );
		$candidate_data->priority		= getExperienceLevel($csv_contents[$key_Work_Experience_Years]);
		#$candidate_data->version		= $csv_contents[$key_
		#$candidate_data->profile_id		= gpc_get_int( 'profile_id', 0 );
		#$candidate_data->handler_id		= gpc_get_int( 'handler_id', 0 );
		#$f_file				= gpc_get_file( 'file', null ); #@@@ (thraxisp) Note that this always returns a structure

		#echo  $map_source[$csv_contents[$key_Source]] . '<br />';
		#
		# Create the main report 
		#
		$t_new_candidate_id = candidate_create( $candidate_data, true );
		
		#
		# If duplicate candidate, do not stop trigger errorm try to update his form 
		#
		$t_new_candidate_id = get_id_from_name($candidate_data->summary);
		if ($t_new_candidate_id == 0){
			candidate_update($t_new_candidate_id, $candidate_data);
		}

		$t_related_custom_field_ids = custom_field_get_linked_ids( $candidate_data->project_id );
		
		foreach( $t_related_custom_field_ids as $t_id ) {
			$t_def = custom_field_get_definition( $t_id );
			if (isset($csv_contents["$t_def[1]"])) {
				#echo  "$t_def[1]" . ' = ' . $csv_contents["$t_def[1]"] . '<br />';
				#
				# Set the custom field values to the created report. 
				# Get the custom field name from the position 1 of t_def custom structure
				#
				if ($t_def[1] == 'Date of Birth') {
					$csv_contents["$t_def[1]"] = strtotime($csv_contents["$t_def[1]"]);
				}
				if( !custom_field_set_value( $t_id, $t_new_candidate_id, $csv_contents["$t_def[1]"] ) ) {
					error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
					trigger_error( ERROR_CUSTOM_FIELD_INVALID_VALUE, ERROR );
				}
			}
		}
		# 
		# Move imported csv to an archive folder
		#
		$csv_filename = basename ($f);
		$new_location = $archive . $csv_filename; 
		$res = rename ($f, $new_location);
	        if ( !$res ) {
			trigger_error( ERROR_FILE_MOVE_FAILED, ERROR );
		}	
	    } # end for loop csvs
	} # end if !csv_delete_clicked	
	#
	# Print redirect page
	#
        html_page_top1();
	html_page_top2();

	$t_redirect = 'import_menu_page.php';
	echo '<br /><div align="center">';
	if ( $csv_delete_clicked and !$no_selection  ) {
		echo lang_get( 'csv_delete_clicked' ) . '<br />';
	}
	if ( $csv_import_all and (!$no_selection)) {
		echo lang_get( 'csv_import_all' ) . '<br />';
	}
	if (( $csv_import_sel ) and (!$no_selection)) {
		echo lang_get( 'csv_import_sel' ) . '<br />';
	}
	if ( $no_selection ){
		echo lang_get( 'csv_import_no_sel' ) . '<br />';
	}

	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( $t_redirect, lang_get( 'proceed' ) );
	echo '</div>';

?>
