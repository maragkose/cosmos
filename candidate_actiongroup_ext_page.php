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
	# $Id: candidate_actiongroup_ext_page.php,v 1.2.2.1 2007-10-13 22:32:33 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	require_once( $t_core_path.'candidate_group_action_api.php' );

	auth_ensure_user_authenticated();

	$f_action = gpc_get_string( 'action' );
	$f_candidate_arr = gpc_get_int_array( 'candidate_arr', array() );

	# redirect to view issues if nothing is selected
	if ( is_blank( $f_action ) || ( 0 == sizeof( $f_candidate_arr ) ) ) {
		print_header_redirect( 'view_all_candidate_page.php' );
	}

  # redirect to view issues page if action doesn't have ext_* prefix.
  # This should only occur if this page is called directly.
	$t_external_action_prefix = 'EXT_';
	if ( strpos( $f_action, $t_external_action_prefix ) !== 0 ) {
		print_header_redirect( 'view_all_candidate_page.php' );
  }

	$t_external_action = strtolower( substr( $f_action, strlen( $t_external_action_prefix ) ) );
	$t_form_fields_page = 'candidate_actiongroup_' . $t_external_action . '_inc.php';

	candidate_group_action_print_top();
?>

	<br />

	<div align="center">
	<form method="post" action="candidate_actiongroup_ext.php">
		<input type="hidden" name="action" value="<?php echo string_attribute( $t_external_action ) ?>" />
		<input type="hidden" name="action" value="<?php echo string_attribute( $t_external_action ) ?>" />
<table class="width75" cellspacing="1">
	<?php
		candidate_group_action_print_title( $t_external_action );
		candidate_group_action_print_hidden_fields( $f_candidate_arr );
		candidate_group_action_print_action_fields( $t_external_action );
	?>
</table>
	</form>
	</div>

	<br />

<?php
	candidate_group_action_print_candidate_list( $f_candidate_arr );
	candidate_group_action_print_bottom();
?>
