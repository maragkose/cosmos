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
	# $Id: candidatenote_edit_page.php,v 1.54.2.1 2007-10-13 22:33:07 giallu Exp $
	# --------------------------------------------------------

	# CALLERS
	#	This page is submitted to by the following pages:
	#	- candidatenote_inc.php

	# EXPECTED BEHAVIOUR
	#	Allow the user to modify the text of a candidatenote, then submit to
	#	candidatenote_update.php with the new text

	# RESTRICTIONS & PERMISSIONS
	#	- none beyond API restrictions
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'candidatenote_api.php' );
	require_once( $t_core_path.'string_api.php' );
	require_once( $t_core_path.'current_user_api.php' );
?>
<?php
	$f_candidatenote_id = gpc_get_int( 'candidatenote_id' );
	$t_candidate_id = candidatenote_get_field( $f_candidatenote_id, 'candidate_id' );

	$t_candidate = candidate_get( $t_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	# Check if the current user is allowed to edit the candidatenote
	$t_user_id = auth_get_current_user_id();
	$t_reporter_id = candidatenote_get_field( $f_candidatenote_id, 'reporter_id' );

	if ( ( $t_user_id != $t_reporter_id ) ||
	 	( OFF == config_get( 'candidatenote_allow_user_edit_delete' ) ) ) {
		access_ensure_candidatenote_level( config_get( 'update_candidatenote_threshold' ), $f_candidatenote_id );
	}

	# Check if the candidate is readonly
	if ( candidate_is_readonly( $t_candidate_id ) ) {
		error_parameters( $t_candidate_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	$t_candidatenote_text = string_textarea( candidatenote_get_text( $f_candidatenote_id ) );

	# No need to gather the extra information if not used
	if ( config_get('time_tracking_enabled') &&
		access_has_candidate_level( config_get( 'time_tracking_edit_threshold' ), $t_candidate_id ) ) {
		$t_time_tracking = candidatenote_get_field( $f_candidatenote_id, "time_tracking" );
		$t_time_tracking = db_minutes_to_hhmm( $t_time_tracking );
	}

	# Determine which view page to redirect back to.
	$t_redirect_url = string_get_candidate_view_url( $t_candidate_id );
?>
<?php html_page_top1( candidate_format_summary( $t_candidate_id, SUMMARY_CAPTION ) ) ?>
<?php html_page_top2() ?>

<br />
<div align="center">
<form method="post" action="candidatenote_update.php">
<table class="width75" cellspacing="1">
<tr>
	<td class="form-title">
		<input type="hidden" name="candidatenote_id" value="<?php echo $f_candidatenote_id ?>" />
		<?php echo lang_get( 'edit_candidatenote_title' ) ?>
	</td>
	<td class="right">
		<?php print_bracket_link( $t_redirect_url, lang_get( 'go_back' ) ) ?>
	</td>
</tr>
<tr class="row-1">
	<td class="center" colspan="2">
		<textarea cols="80" rows="10" name="candidatenote_text"><?php echo $t_candidatenote_text ?></textarea>
	</td>
</tr>
<?php if ( config_get('time_tracking_enabled') ) { ?>
<?php if ( access_has_candidate_level( config_get( 'time_tracking_edit_threshold' ), $t_candidate_id ) ) { ?>
<tr class="row-2">
	<td class="center" colspan="2">
		<b><?php echo lang_get( 'time_tracking') ?> (HH:MM)</b><br />
		<input type="text" name="time_tracking" size="5" value="<?php echo $t_time_tracking ?>" />
	</td>
</tr>
<?php } ?>
<?php } ?>
<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo lang_get( 'update_information_button' ) ?>" />
	</td>
</tr>
</table>
</form>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
