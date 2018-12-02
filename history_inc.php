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
	# $Id: history_inc.php,v 1.1.1.1 2009/01/08 07:30:19 chirag Exp $
	# --------------------------------------------------------
?>
<?php
	# This include file prints out the candidate history

	# $f_candidate_id must already be defined
?>
<?php
	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'history_api.php' );
?>

<?php
	$t_access_level_needed = config_get( 'view_history_threshold' );
	if ( !access_has_candidate_level( $t_access_level_needed, $f_candidate_id ) ) {
		return;
	}
?>

<a name="history" id="history" /><br />

<?php
	collapse_open( 'history' );
	$t_history = history_get_events_array( $f_candidate_id );
?>
<table class="width100" cellspacing="0">
<tr>
	<td class="form-title" colspan="4">
<?php
	collapse_icon( 'history' );
	echo lang_get( 'candidate_history' ) ?>
	</td>
</tr>
<tr class="row-category-history">
	<td class="small-caption">
		<?php echo lang_get( 'date_modified' ) ?>
	</td>
	<td class="small-caption">
		<?php echo lang_get( 'summary' ) ?> (<?php echo lang_get( 'access_levels' ) ?>)
	</td>
	<td class="small-caption">
		<?php echo lang_get( 'field' ) ?>
	</td>
	<td class="small-caption">
		<?php echo lang_get( 'change' ) ?>
	</td>
</tr>
<?php
	foreach ( $t_history as $t_item ) {
?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="small-caption">
		<?php echo $t_item['date'] ?>
	</td>
	<td class="small-caption">
		<?php echo user_get_realname( $t_item['userid']);?>
		(<?
		//updated by Chirag A to show user access level with user name
		echo  get_enum_element( 'access_levels', user_get_access_level($t_item['userid'],$t_candidate->project_id)); ?>)

	</td>
	<td class="small-caption">
		<?php echo string_display( $t_item['note'] ) ?>
	</td>
	<td class="small-caption">
		<?php echo string_display_line_links( $t_item['change'] ) ?>
	</td>
</tr>
<?php
	} # end for loop
?>
</table>
<?php
	collapse_closed( 'history' );
?>
<table class="width100" cellspacing="0">
<tr>
	<td class="form-title" colspan="4">
	<?php	collapse_icon( 'history' );
		echo lang_get( 'candidate_history' ) ?>
	</td>
</tr>
</table>

<?php
	collapse_end( 'history' );
?>
