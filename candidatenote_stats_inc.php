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
	# $Id: candidatenote_stats_inc.php,v 1.11.2.2 2007-10-13 22:33:09 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	# This include file prints out the candidate candidatenote_stats

	# $f_candidate_id must already be defined

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidatenote_api.php' );

	if ( ON != config_get('time_tracking_enabled') ) {
		return;
	}
?>

<a name="candidatenotestats" id="candidatenotestats" /><br />

<?php 
	collapse_open( 'candidatenotestats' );

	$t_candidatenote_stats_from_def = date( "d:m:Y", $t_candidate->date_submitted );
	$t_candidatenote_stats_from_def_ar = explode ( ":", $t_candidatenote_stats_from_def );
	$t_candidatenote_stats_from_def_d = $t_candidatenote_stats_from_def_ar[0];
	$t_candidatenote_stats_from_def_m = $t_candidatenote_stats_from_def_ar[1];
	$t_candidatenote_stats_from_def_y = $t_candidatenote_stats_from_def_ar[2];

	$t_candidatenote_stats_from_d = gpc_get_string('start_day', $t_candidatenote_stats_from_def_d);
	$t_candidatenote_stats_from_m = gpc_get_string('start_month', $t_candidatenote_stats_from_def_m);
	$t_candidatenote_stats_from_y = gpc_get_string('start_year', $t_candidatenote_stats_from_def_y);

	$t_candidatenote_stats_to_def = date( "d:m:Y" );
	$t_candidatenote_stats_to_def_ar = explode ( ":", $t_candidatenote_stats_to_def );
	$t_candidatenote_stats_to_def_d = $t_candidatenote_stats_to_def_ar[0];
	$t_candidatenote_stats_to_def_m = $t_candidatenote_stats_to_def_ar[1];
	$t_candidatenote_stats_to_def_y = $t_candidatenote_stats_to_def_ar[2];

	$t_candidatenote_stats_to_d = gpc_get_string('end_day', $t_candidatenote_stats_to_def_d);
	$t_candidatenote_stats_to_m = gpc_get_string('end_month', $t_candidatenote_stats_to_def_m);
	$t_candidatenote_stats_to_y = gpc_get_string('end_year', $t_candidatenote_stats_to_def_y);

	$f_get_candidatenote_stats_button = gpc_get_string('get_candidatenote_stats_button', '');
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<input type="hidden" name="id" value="<?php echo $f_candidate_id ?>" />
<table border=0 class="width100" cellspacing="0">
<tr>
	<td class="form-title" colspan="4">
<?php
		collapse_icon( 'candidatenotestats' );
		echo lang_get( 'time_tracking' ) ?>
	</td>
</tr>
<tr class="row-2">
        <td class="category" colspan="2">
                <?php
		$t_filter = array();
		$t_filter['do_filter_by_date'] = 'on';
		$t_filter['start_day'] = $t_candidatenote_stats_from_d;
		$t_filter['start_month'] = $t_candidatenote_stats_from_m;
		$t_filter['start_year'] = $t_candidatenote_stats_from_y;
		$t_filter['end_day'] = $t_candidatenote_stats_to_d;
		$t_filter['end_month'] = $t_candidatenote_stats_to_m;
		$t_filter['end_year'] = $t_candidatenote_stats_to_y;
		print_filter_do_filter_by_date(true);
		?>
        </td>
</tr>
<tr>
        <td class="center" colspan="2">
                <input type="submit" class="button" name="get_candidatenote_stats_button" value="<?php echo lang_get( 'time_tracking_get_info_button' ) ?>" />
        </td>
</tr>

<?php
if ( !is_blank( $f_get_candidatenote_stats_button ) ) {
	$t_from = "$t_candidatenote_stats_from_y-$t_candidatenote_stats_from_m-$t_candidatenote_stats_from_d";
	$t_to = "$t_candidatenote_stats_to_y-$t_candidatenote_stats_to_m-$t_candidatenote_stats_to_d";
	$t_candidatenote_stats = candidatenote_stats_get_events_array( $f_candidate_id, $t_from, $t_to );
?>
<br />
<tr class="row-category-history">
	<td class="small-caption">
		<?php echo lang_get( 'username' ) ?>
	</td>
	<td class="small-caption">
		<?php echo lang_get( 'time_tracking' ) ?>
	</td>
</tr>
<?php
	$t_sum_in_minutes = 0;
	foreach ( $t_candidatenote_stats as $t_item ) {
		$t_sum_in_minutes += $t_item['sum_time_tracking'];
		$t_item['sum_time_tracking'] = db_minutes_to_hhmm ( $t_item['sum_time_tracking'] );
?>

<tr <?php echo helper_alternate_class() ?>>
	<td class="small-caption">
		<?php echo $t_item['username'] ?>
	</td>
	<td class="small-caption">
		<?php echo $t_item['sum_time_tracking'] ?>
	</td>
</tr>
<?php } # end for loop ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="small-caption">
		<?php echo lang_get( 'total_time' ) ?>
	</td>
	<td class="small-caption">
		<?php echo db_minutes_to_hhmm ( $t_sum_in_minutes ) ?>
	</td>
</tr>
<?php } # end if ?>
</table>
</form>
<?php
	collapse_closed( 'candidatenotestats' );
?>
<table class="width100" cellspacing="0">
<tr>
	<td class="form-title" colspan="4">
		<?php
			collapse_icon( 'candidatenotestats' );
			echo lang_get( 'time_tracking' ) ?>
	</td>
</tr>
</table>
<?php
	collapse_end( 'candidatenotestats' );
?>
