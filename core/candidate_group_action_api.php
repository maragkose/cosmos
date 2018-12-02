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
	# $Id: candidate_group_action_api.php,v 1.2.2.1 2007-10-13 22:35:14 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	/**
	 * Print the top part for the candidate action group page.
	 */
	function candidate_group_action_print_top() {
		html_page_top1();
		html_page_top2();
	}

	/**
	 * Print the bottom part for the candidate action group page.
	 */
	function candidate_group_action_print_bottom() {
		html_page_bottom1( __FILE__ );
	}

	/**
	 * Print the list of selected issues and the legend for the status colors.
	 *
	 * @param $p_candidate_ids_array   An array of issue ids.
	 */
	function candidate_group_action_print_candidate_list( $p_candidate_ids_array ) {
		$t_legend_position = config_get( 'status_legend_position' );

		if ( STATUS_LEGEND_POSITION_TOP == $t_legend_position ) {
			html_status_legend();
			echo '<br />';
		}

		echo '<div align="center">';
		echo '<table class="width75" cellspacing="1">';
		echo '<tr class="row-1">';
		echo '<td class="category" colspan="2">';
		echo lang_get( 'actiongroup_candidates' );
		echo '</td>';
		echo '</tr>';

		$t_i = 1;

		foreach( $p_candidate_ids_array as $t_candidate_id ) {
			$t_class = sprintf( "row-%d", ($t_i++ % 2) + 1 );
			echo sprintf( "<tr bgcolor=\"%s\"> <td>%s</td> <td>%s</td> </tr>\n",
				get_status_color( candidate_get_field( $t_candidate_id, 'status' ) ),
				string_get_candidate_view_link( $t_candidate_id ),
				string_attribute( candidate_get_field( $t_candidate_id, 'summary' ) )
		    );
		}

		echo '</table>';
		echo '</form>';
		echo '</div>';

		if ( STATUS_LEGEND_POSITION_BOTTOM == $t_legend_position ) {
			echo '<br />';
			html_status_legend();
		}
	}

	/**
	 * Print the array of issue ids via hidden fields in the form to be passed on to
	 * the candidate action group action page.
	 *
	 * @param $p_candidate_ids_array   An array of issue ids.
	 */
	function candidate_group_action_print_hidden_fields( $p_candidate_ids_array ) {
		foreach( $p_candidate_ids_array as $t_candidate_id ) {
			echo '<input type="hidden" name="candidate_arr[]" value="' . $t_candidate_id . '" />' . "\n";
		}
	}

	######
	# Call-Outs for EXT_* custom group actions
	######

	/**
	 * Prints the list of fields in the custom action form.  These are the user inputs
	 * and the submit button.  This ends up calling action_<action>_print_fields()
	 * from candidate_actiongroup_<action>_inc.php	 
	 *
	 * @param $p_action   The custom action name without the "EXT_" prefix.
	 */
	function candidate_group_action_print_action_fields( $p_action ) {
		require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'candidate_actiongroup_' . $p_action . '_inc.php' );
		$t_function_name = 'action_' . $p_action . '_print_fields';
		$t_function_name();
	}
  
	/**
	 * Prints some title text for the custom action page.  This ends up calling 
	 * action_<action>_print_title() from candidate_actiongroup_<action>_inc.php	 
	 *
	 * @param $p_action   The custom action name without the "EXT_" prefix.
	 */
	function candidate_group_action_print_title( $p_action ) {
		require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'candidate_actiongroup_' . $p_action . '_inc.php' );
		$t_function_name = 'action_' . $p_action . '_print_title';
		$t_function_name();
	}

	/**
	 * Validates the combination of an action and a candidate.  This ends up calling 
	 * action_<action>_validate() from candidate_actiongroup_<action>_inc.php	 
	 *
	 * @param $p_action   The custom action name without the "EXT_" prefix.
	 * @param $p_candidate_id   The id of the candidate to validate the action on.
	 * 
	 * @returns true      Action can be applied.
	 * @returns array( candidate_id => reason for failure to validate )         	 
	 */
	function candidate_group_action_validate( $p_action, $p_candidate_id ) {
		require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'candidate_actiongroup_' . $p_action . '_inc.php' );
		$t_function_name = 'action_' . $p_action . '_validate';
		return $t_function_name( $p_candidate_id );
	}

	/**
	 * Executes an action on a candidate.  This ends up calling 
	 * action_<action>_process() from candidate_actiongroup_<action>_inc.php	 
	 *
	 * @param $p_action   The custom action name without the "EXT_" prefix.
	 * @param $p_candidate_id   The id of the candidate to validate the action on.
	 * 
	 * @returns true      Action can be applied.
	 * @returns array( candidate_id => reason for failure to process )         	 
	 */
	function candidate_group_action_process( $p_action, $p_candidate_id ) {
		require_once( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'candidate_actiongroup_' . $p_action . '_inc.php' );
		$t_function_name = 'action_' . $p_action . '_process';
		return $t_function_name( $p_candidate_id );
	}
?>
