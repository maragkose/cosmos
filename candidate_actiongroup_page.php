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
	# $Id: candidate_actiongroup_page.php,v 1.55.2.1 2007-10-13 22:32:34 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	# This page allows actions to be performed on an array of candidates

	require_once( 'core.php' );

	require_once( $t_core_path.'candidate_group_action_api.php' );

	auth_ensure_user_authenticated();

	$f_action = gpc_get_string( 'action', '' );
	$f_candidate_arr = gpc_get_int_array( 'candidate_arr', array() );

	# redirects to all_candidate_page if nothing is selected
	if ( is_blank( $f_action ) || ( 0 == sizeof( $f_candidate_arr ) ) ) {
		print_header_redirect( 'view_all_candidate_page.php' );
	}

	# run through the issues to see if they are all from one project
	$t_project_id = ALL_PROJECTS;
	$t_multiple_projects = false;
	foreach( $f_candidate_arr as $t_candidate_id ) {
		$t_candidate = candidate_get( $t_candidate_id );
		if ( $t_project_id != $t_candidate->project_id ) {
			if ( ( $t_project_id != ALL_PROJECTS ) && !$t_multiple_projects ) {
				$t_multiple_projects = true;
			} else {
				$t_project_id = $t_candidate->project_id;
			}
		}
	}
	if ( $t_multiple_projects ) {
		$t_project_id = ALL_PROJECTS;
	}
	# override the project if necessary
	if( $t_project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_project_id;
	}

	$t_finished = false;
	$t_request = '';

	$t_external_action_prefix = 'EXT_';
	if ( strpos( $f_action, $t_external_action_prefix ) === 0 ) {
		$t_form_page = 'candidate_actiongroup_ext_page.php';
		require_once( $t_form_page );
		exit;
	}

	$t_custom_group_actions = config_get( 'custom_group_actions' );
	
	foreach( $t_custom_group_actions as $t_custom_group_action ) {
		if ( $f_action == $t_custom_group_action['action'] ) {
			require_once( $t_custom_group_action['form_page'] );
			exit;
		}
	}

	# Check if user selected to update a custom field.
	$t_custom_fields_prefix = 'custom_field_';
	if ( strpos( $f_action, $t_custom_fields_prefix ) === 0 ) {
		$t_custom_field_id = (int)substr( $f_action, strlen( $t_custom_fields_prefix ) );
		$f_action = 'CUSTOM';
	}

	# Form name
	$t_form_name = 'candidate_actiongroup_' . $f_action;

	switch ( $f_action )  {
		# Use a simple confirmation page, if close or delete...
		case 'CLOSE' :
			$t_finished 			= true;
			$t_question_title 		= lang_get( 'close_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'close_group_candidates_button' );
			break;

		case 'DELETE' :
			$t_finished 			= true;
			$t_question_title		= lang_get( 'delete_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'delete_group_candidates_button' );
			break;

		case 'SET_STICKY' :
			$t_finished 			= true;
			$t_question_title		= lang_get( 'set_sticky_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'set_sticky_group_candidates_button' );
			break;

		# ...else we define the variables used in the form
		case 'MOVE' :
			$t_question_title 		= lang_get( 'move_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'move_group_candidates_button' );
			$t_form					= 'project_id';
			break;

		case 'COPY' :
			$t_question_title 		= lang_get( 'copy_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'copy_group_candidates_button' );
			$t_form					= 'project_id';
			break;

		case 'ASSIGN' :
			$t_question_title 		= lang_get( 'assign_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'assign_group_candidates_button' );
			$t_form 				= 'assign';
			break;

		case 'RESOLVE' :
			$t_question_title 		= lang_get( 'resolve_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'resolve_group_candidates_button' );
			$t_form 				= 'resolution';
			$t_request 				= 'resolution'; # the "request" vars allow to display the adequate list
			break;

		case 'UP_PRIOR' :
			$t_question_title 		= lang_get( 'priority_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'priority_group_candidates_button' );
			$t_form 				= 'priority';
			$t_request 				= 'priority';
			break;

		case 'UP_STATUS' :
			$t_question_title 		= lang_get( 'status_candidates_conf_msg' );
			$t_button_title 		= lang_get( 'status_group_candidates_button' );
			$t_form 				= 'status';
			$t_request 				= 'status';
			break;

		case 'UP_CATEGORY' :
			$t_question_title		= lang_get( 'category_candidates_conf_msg' );
			$t_button_title			= lang_get( 'category_group_candidates_button' );
			$t_form					= 'category';
			break;

		case 'VIEW_STATUS' :
			$t_question_title		= lang_get( 'view_status_candidates_conf_msg' );
			$t_button_title			= lang_get( 'view_status_group_candidates_button' );
			$t_form					= 'view_status';
			break;
		
		case 'UP_FIXED_IN_VERSION':
			$t_question_title		= lang_get( 'fixed_in_version_candidates_conf_msg' );
			$t_button_title			= lang_get( 'fixed_in_version_group_candidates_button' );
			$t_form					= 'fixed_in_version';
			break;

		case 'UP_TARGET_VERSION':
			$t_question_title		= lang_get( 'target_version_candidates_conf_msg' );
			$t_button_title			= lang_get( 'target_version_group_candidates_button' );
			$t_form					= 'target_version';
			break;

		case 'CUSTOM' :
			$t_custom_field_def = custom_field_get_definition( $t_custom_field_id );
			$t_question_title = sprintf( lang_get( 'actiongroup_menu_update_field' ), lang_get_defaulted( $t_custom_field_def['name'] ) );
			$t_button_title = $t_question_title;
			$t_form = "custom_field_$t_custom_field_id";
			break;

		default:
			trigger_error( ERROR_GENERIC, ERROR );
	}

	candidate_group_action_print_top();
	
	if ( $t_multiple_projects ) {
		echo '<p class="bold">' . lang_get( 'multiple_projects' ) . '</p>';
	}
?>

<br />

<div align="center">
<form method="post" action="candidate_actiongroup.php">
<?php
if ( !is_blank( $t_form_name ) ) {
	echo form_security_field( $t_form_name );
}
?>
<input type="hidden" name="action" value="<?php echo string_attribute( $f_action ) ?>" />
<?php
	candidate_group_action_print_hidden_fields( $f_candidate_arr );

	if ( $f_action === 'CUSTOM' ) {
		echo "<input type=\"hidden\" name=\"custom_field_id\" value=\"$t_custom_field_id\" />";
	}
?>
<table class="width75" cellspacing="1">
<?php
if ( !$t_finished ) {
?>
<tr class="row-1">
	<td class="category">
		<?php echo $t_question_title ?>
	</td>
	<td>
	<?php
		if ( $f_action === 'CUSTOM' ) {
			$t_custom_field_def = custom_field_get_definition( $t_custom_field_id );

			$t_candidate_id = null;

			# if there is only one issue, use its current value as default, otherwise,
			# use the default value specified in custom field definition.
			if ( sizeof( $f_candidate_arr ) == 1 ) {
				$t_candidate_id = $f_candidate_arr[0];
			}

			print_custom_field_input( $t_custom_field_def, $t_candidate_id );
		} else {
			echo "<select name=\"$t_form\">";

			switch ( $f_action ) {
				case 'COPY':
				case 'MOVE':
					print_project_option_list( null, false );
					break;
				case 'ASSIGN':
					print_assign_to_option_list( 0, $t_project_id );
					break;
				case 'VIEW_STATUS':
					print_enum_string_option_list( 'view_state', config_get( 'default_candidate_view_status' ) );
					break;
				case 'UP_CATEGORY':
					print_category_option_list();
					break;
				case 'UP_TARGET_VERSION':
				case 'UP_FIXED_IN_VERSION':
					print_version_option_list( '', $t_project_id, VERSION_ALL );
					break;
			}

			# other forms use the same function to display the list
			if ( $t_request > '' ) {
				print_enum_string_option_list( $t_request, FIXED );
			}

			echo '</select>';
		}
		?>
	</td>
</tr>
	<?php
	if ( isset( $t_question_title2 ) ) {
		switch ( $f_action ) {
			case 'RESOLVE':
				$t_show_version = ( ON == config_get( 'show_product_version' ) )
					|| ( ( AUTO == config_get( 'show_product_version' ) )
								&& ( count( version_get_all_rows( $t_project_id ) ) > 0 ) );
				if ( $t_show_version ) {
	?>
		<tr class="row-2">
			<td class="category">
				<?php echo $t_question_title2 ?>
			</td>
			<td>
				<select name="<?php echo $t_form2 ?>">
					<?php print_version_option_list( '', null, VERSION_ALL );?>
				</select>
			</td>
		</tr>
	<?php
				}
				break;
		}
	}
	?>
<?php
} else {
?>

<tr class="row-1">
	<td class="category" colspan="2">
		<?php echo $t_question_title; ?>
	</td>
</tr>
<?php
}
?>

<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo $t_button_title ?>" />
	</td>
</tr>
</table>
<br />

<?php
	candidate_group_action_print_candidate_list( $f_candidate_arr );
?>
</form>
</div>

<?php
	candidate_group_action_print_bottom();
?>
