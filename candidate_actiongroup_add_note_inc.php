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
	# $Id: candidate_actiongroup_add_note_inc.php,v 1.3.2.1 2007-10-13 22:32:31 giallu Exp $
	# --------------------------------------------------------

	/**
	 * Prints the title for the custom action page.	 
	 */
	function action_add_note_print_title() {
        echo '<tr class="form-title">';
        echo '<td colspan="2">';
        echo lang_get( 'add_candidatenote_title' );
        echo '</td></tr>';		
	}

	/**
	 * Prints the field within the custom action form.  This has an entry for
	 * every field the user need to supply + the submit button.  The fields are
	 * added as rows in a table that is already created by the calling code.
	 * A row has two columns.         	 
	 */
	function action_add_note_print_fields() {
		echo '<tr class="row-1" valign="top"><td class="category">', lang_get( 'add_candidatenote_title' ), '</td><td><textarea name="candidatenote_text" cols="80" rows="10"></textarea></td></tr>';
	?>
	<!-- View Status -->
	<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
<?php
		if ( access_has_project_level( config_get( 'change_view_status_threshold' ) ) ) { ?>
			<select name="view_state">
				<?php print_enum_string_option_list( 'view_state', $t_candidate->view_state) ?>
			</select>
<?php
		} else {
			echo get_enum_element( 'view_state', $t_candidate->view_state );
			echo '<input type="hidden" name="view_state" value="', $t_candidate->view_state, '" />';
		}
?>
	</td>
	</tr>
	<?php
		echo '<tr><td colspan="2"><center><input type="submit" class="button" value="' . lang_get( 'add_candidatenote_button' ) . ' " /></center></td></tr>';
	}

	/**
	 * Validates the action on the specified candidate id.
	 * 
	 * @returns true    Action can be applied.
	 * @returns array( candidate_id => reason for failure )	 
	 */
	function action_add_note_validate( $p_candidate_id ) {
		$f_candidatenote_text = gpc_get_string( 'candidatenote_text' );

		if ( is_blank( $f_candidatenote_text ) ) {
			error_parameters( lang_get( 'candidatenote' ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}

		$t_failed_validation_ids = array();
		$t_add_candidatenote_threshold = config_get( 'add_candidatenote_threshold' );
		$t_candidate_id = $p_candidate_id;

		if ( candidate_is_readonly( $t_candidate_id ) ) {
			$t_failed_validation_ids[$t_candidate_id] = lang_get( 'actiongroup_error_issue_is_readonly' );
			return $t_failed_validation_ids;
		}

		if ( !access_has_candidate_level( $t_add_candidatenote_threshold, $t_candidate_id ) ) {
			$t_failed_validation_ids[$t_candidate_id] = lang_get( 'access_denied' );
			return $t_failed_validation_ids;
		}

		return true;
	}

	/**
	 * Executes the custom action on the specified candidate id.
	 * 
	 * @param $p_candidate_id  The candidate id to execute the custom action on.
	 * 
	 * @returns true   Action executed successfully.
	 * @returns array( candidate_id => reason for failure )               	 
	 */
	function action_add_note_process( $p_candidate_id ) {
		$f_candidatenote_text = gpc_get_string( 'candidatenote_text' );
		$f_view_state = gpc_get_int( 'view_state' );
		candidatenote_add ( $p_candidate_id, $f_candidatenote_text, '0:00', /* $p_private = */ $f_view_state != VS_PUBLIC  );
        return true;
    }
?>
