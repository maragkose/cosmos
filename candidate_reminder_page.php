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
	# $Id: candidate_reminder_page.php,v 1.23.2.1 2007-10-13 22:32:50 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
?>
<?php
	$f_candidate_id = gpc_get_int( 'candidate_id' );

	if ( candidate_is_readonly( $f_candidate_id ) ) {
		error_parameters( $f_candidate_id );
		trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
	}

	access_ensure_candidate_level( config_get( 'candidate_reminder_threshold' ), $f_candidate_id );

	$t_candidate = candidate_get( $f_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

?>
<?php html_page_top1( candidate_format_summary( $f_candidate_id, SUMMARY_CAPTION ) ) ?>
<?php html_page_top2() ?>

<?php # Send reminder Form BEGIN ?>
<br />
<div align="center">
<table class="width75" cellspacing="1">
<form method="post" action="candidate_reminder.php">
<input type="hidden" name="candidate_id" value="<?php echo $f_candidate_id ?>">
<tr>
	<td class="form-title" colspan="2">
		<?php echo lang_get( 'candidate_reminder' ) ?>
	</td>
</tr>
<tr>
	<td class="category">
		<?php echo lang_get( 'to' ) ?>
	</td>
	<td class="category">
		<?php echo lang_get( 'reminder' ) ?>
	</td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
	<td>
		<select name="to[]" multiple="multiple" size="10">
			<?php echo print_project_user_option_list( candidate_get_field( $f_candidate_id, 'project_id' ) ) ?>
		</select>
	</td>
	<td class="center">
		<textarea name="body" cols="65" rows="10"></textarea>
	</td>
</tr>
<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo lang_get( 'candidate_send_button' ) ?>">
	</td>
</tr>
</form>
</table>
<br />
<table class="width75" cellspacing="1">
<tr>
	<td>
		<?php
			echo lang_get( 'reminder_explain' ) . ' ';
			if ( ON == config_get( 'reminder_recipents_monitor_candidate' ) ) {
				echo lang_get( 'reminder_monitor' ) . ' ';
			}
			if ( ON == config_get( 'store_reminders' ) ) {
				echo lang_get( 'reminder_store' );
			}
		?>
	</td>
</tr>
</table>
</div>

<br />
<?php include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'candidate_view_inc.php' ) ?>
<?php include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'candidatenote_view_inc.php' ) ?>

<?php html_page_bottom1( __FILE__ ) ?>
