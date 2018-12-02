<?php
#
# Created by Emmanouil Maragkos
#
	require_once( 'core.php' );
	require_once( 'csv_form_keys.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'yml_api.php' );
	
	access_ensure_global_level( config_get( 'manage_activate_threshold' ) );      
	
#======================================================================================================
# MAIN SECTION 
#======================================================================================================

	$test = array();
	if (gpc_isset('test_name')){
		array_push($test, gpc_get_string('test_name'));
	}else {
		foreach( $_POST as $posted_files ) {
		    if( is_array( $posted_files ) ) {
			    foreach( $posted_files as $f ) {
				array_push($test, "$f");
			}
		    }
		}
	}
	$base_test = basename($test[0]);
	#
	# Print redirect page
	#
        html_page_top1();
	html_page_top2();
	
	if (gpc_isset('activate_test_button')){
		# 
		# Copy selected test to active folder 
		#
		$active = $test[0];
		$to = config_get('tests_path') . config_get('active_test');
		$res = copy ($active, $to);
		if ( !$res ) {
			trigger_error( ERROR_FILE_MOVE_FAILED, ERROR );
		}	
		$t_redirect = 'manage_tests_page.php';
		echo '<br /><div align="center">';
		if(empty($test)){
			echo 'No Tests were selected!' . '<br />';
		} else {
			echo lang_get( 'operation_successful' ) . '<br />';
		}
		html_meta_redirect( $t_redirect);
		echo '</div>';
	} else if ((gpc_isset('delete_test_button'))) {
		unlink($test[0]);
		$t_redirect = 'manage_tests_page.php';
		echo '<br /><div align="center">';
		if(empty($test)){
			echo 'No Tests were selected!' . '<br />';
		} else {
			echo lang_get( 'operation_successful' ) . '<br />';
		}
		html_meta_redirect( $t_redirect);
	} else if ((gpc_isset('view_test_button'))) {
		$oYML = new yml($test[0]);
		echo $oYML->view();
	} else {
?>		
		<div align="center">
		<form method="post" action="modify_test.php">
		<?php echo form_security_field( 'manage_test' ); ?>
		<table class="width100_space" cellspacing="1">
		<tr>
		  <td class="document-form" colspan="1">
			<?php echo lang_get( 'manage_tests_edit' ) .  " \"$base_test\""  ?>
		  </td>
		  <td class="document-form" colspan="1">
			<?php echo "Answer"  ?>
		  </td>
		  <td class="document-form" colspan="1">
			<?php echo "Options"  ?>
		  </td>
		</tr>
		<tr class="row-2">
		  <td class="top">
		    <textarea id="elm1" name="new_question" rows="20" cols="60"></textarea>
		  </td>
		  <td class="top">
		    <label class="bold">a)</label> 
		    <input name="solution" type="radio" value="a" checked></input><br \><br \><br \><br \><br ><br \><br \><br \><br \>
		    <label class="bold">b)</label> 
		    <input name="solution" type="radio" value="b"></input><br \><br \><br \><br \><br ><br \><br \><br \><br \>
		    <label class="bold">c)</label> 
		    <input name="solution" type="radio" value="c"></input><br \><br \><br \><br \><br ><br \><br \><br \><br \>
		    <label class="bold">d)</label> 
		    <input name="solution" type="radio" value="d" ></input><br \><br \><br \><br \><br ><br \><br \><br \><br \>
		  </td>
		  <td class="top" >
		    <textarea id="t1" name="o1" size="35"></textarea><br \>
		    <textarea id="t2" name="o2" size="35"></textarea><br \>
		    <textarea id="t3" name="o3" size="35"></textarea><br \>
		    <textarea id="t4" name="o4" size="35"></textarea><br \>
		<?php
		echo "<input name=\"test_name\" type=\"hidden\" value=\"$test[0]\"></input><br \\\>";
		?>	
		  </td>
		</tr>
		<tr class="row-2">
		  <td class="left" colspan="3">   
		    <label class="bold"> | <?php echo lang_get('C_Section')?></label> 
		    <input name="section" type="radio" value="set1"checked/>
		    <label class="bold"> | <?php echo lang_get('Linux_Section')?></label> 
		    <input name="section" type="radio" value="set2"/>
		    <label class="bold"> | <?php echo lang_get('IP_Section')?></label> 
		    <input name="section" type="radio" value="set3"/>
		    <label class="bold"> |</label> 
		  </td>
		</tr>
		<tr>
		  <td class="left">    
		     <input name="add_question" type="submit" class="button" value="<?php echo lang_get( 'post_add_question_button' ) ?>" />
		     <input name="edit_question" type="submit" class="button" value="<?php echo lang_get( 'post_edit_question_button' ) ?>" />
		  </td>
		</tr>
		</table>
<p>Date: <input type="text" id="datepicker"></p>
		</form>
	</div>
	<?php
	}
?>
