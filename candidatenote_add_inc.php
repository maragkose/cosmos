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
	# $Id: candidatenote_add_inc.php,v 1.34.2.1 2007-10-13 22:33:05 giallu Exp $
	# --------------------------------------------------------
?>
<?php if ( ( !candidate_is_readonly( $f_candidate_id ) ) &&
		( access_has_candidate_level( config_get( 'add_candidatenote_threshold' ), $f_candidate_id ) ) ) { ?>
<?php # Bugnote Add Form BEGIN ?>
<a name="addcandidatenote"></a> <br />

<?php
	collapse_open( 'candidatenote_add' );
?>
<form name="candidatenoteadd" method="post" action="candidatenote_add.php">
<input type="hidden" name="candidate_id" value="<?php echo $f_candidate_id ?>" />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
<?php
	collapse_icon( 'candidatenote_add' );
	echo lang_get( 'add_candidatenote_title' ) ?>
	</td>
</tr>
<tr class="row-2">
	<td class="category" width="25%">
		<?php echo lang_get( 'candidatenote' ) ?>
	</td>
	<td width="75%">
		<textarea name="candidatenote_text" cols="80" rows="10"></textarea>
	</td>
</tr>
<?php if ( access_has_candidate_level( config_get( 'private_candidatenote_threshold' ), $f_candidate_id ) ) { ?>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
<?php
		$t_default_candidatenote_view_status = config_get( 'default_candidatenote_view_status' );
		if ( access_has_candidate_level( config_get( 'set_view_status_threshold' ), $f_candidate_id ) ) {
?>
			<input type="checkbox" name="private" <?php check_checked( $t_default_candidatenote_view_status, VS_PRIVATE ); ?> />
<?php
			echo lang_get( 'private' );
		} else {
			echo get_enum_element( 'project_view_state', $t_default_candidatenote_view_status );
		}
?>
	</td>
</tr>
<?php } ?>

<?php if ( config_get('time_tracking_enabled') ) { ?>
<?php if ( access_has_candidate_level( config_get( 'time_tracking_edit_threshold' ), $f_candidate_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'time_tracking' ) ?>
	</td>
	<td>
		<?php if ( config_get('time_tracking_stopwatch') && ON == config_get( 'use_javascript' )) { ?>
		<script type="text/javascript" language="JavaScript" src="javascript/time_tracking_stopwatch.js"></script>
		<input type="text" name="time_tracking" size="5" value="00:00" />
		<input type="button" name="time_tracking_ssbutton" value="Start" onclick="time_tracking_swstartstop()" />
		<input type="button" name="time_tracking_reset" value="R" onclick="time_tracking_swreset()" />
		<?php } else { ?>
		<input type="text" name="time_tracking" size="5" value="00:00" />
		<?php } ?>
	</td>
</tr>
<?php } ?>
<?php } ?>

<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo lang_get( 'add_candidatenote_button' ) ?>" />
	</td>
</tr>
</table>
</form>
<?php
	collapse_closed( 'candidatenote_add' );
?>
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
	<?php	collapse_icon( 'candidatenote_add' );
		echo lang_get( 'add_candidatenote_title' ) ?>
	</td>
</tr>
</table>
<?php 
	collapse_end( 'candidatenote_add' );
?>

<?php # Bugnote Add Form END ?>
<?php } ?>
