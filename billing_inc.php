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
	# $Id: billing_inc.php,v 1.14.2.1 2007-10-13 22:32:29 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	# This include file prints out the candidate candidatenote_stats

	# $f_candidate_id must already be defined
?>
<?php
	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidatenote_api.php' );
?>
<?php
	if ( ! config_get('time_tracking_enabled') )
		return;
?>

<a name="candidatenotestats" id="candidatenotestats" /><br />

<?php 
	collapse_open( 'candidatenotestats' );

	$t_today = date( "d:m:Y" );
	$t_date_submitted = isset( $t_candidate ) ? date( "d:m:Y", $t_candidate->date_submitted ) : $t_today;

	$t_candidatenote_stats_from_def = $t_date_submitted;
	$t_candidatenote_stats_from_def_ar = explode ( ":", $t_candidatenote_stats_from_def );
	$t_candidatenote_stats_from_def_d = $t_candidatenote_stats_from_def_ar[0];
	$t_candidatenote_stats_from_def_m = $t_candidatenote_stats_from_def_ar[1];
	$t_candidatenote_stats_from_def_y = $t_candidatenote_stats_from_def_ar[2];

	$t_candidatenote_stats_from_d = gpc_get_int('start_day', $t_candidatenote_stats_from_def_d);
	$t_candidatenote_stats_from_m = gpc_get_int('start_month', $t_candidatenote_stats_from_def_m);
	$t_candidatenote_stats_from_y = gpc_get_int('start_year', $t_candidatenote_stats_from_def_y);

	$t_candidatenote_stats_to_def = $t_today;
	$t_candidatenote_stats_to_def_ar = explode ( ":", $t_candidatenote_stats_to_def );
	$t_candidatenote_stats_to_def_d = $t_candidatenote_stats_to_def_ar[0];
	$t_candidatenote_stats_to_def_m = $t_candidatenote_stats_to_def_ar[1];
	$t_candidatenote_stats_to_def_y = $t_candidatenote_stats_to_def_ar[2];

	$t_candidatenote_stats_to_d = gpc_get_int('end_day', $t_candidatenote_stats_to_def_d);
	$t_candidatenote_stats_to_m = gpc_get_int('end_month', $t_candidatenote_stats_to_def_m);
	$t_candidatenote_stats_to_y = gpc_get_int('end_year', $t_candidatenote_stats_to_def_y);

	$f_get_candidatenote_stats_button = gpc_get_string('get_candidatenote_stats_button', '');
	$f_candidatenote_cost = gpc_get_int( 'candidatenote_cost', '' );
	$f_project_id = helper_get_current_project();

	if ( ON == config_get( 'time_tracking_with_billing' ) ) {
		$t_cost_col = true;
	} else {
		$t_cost_col = false;
	}

?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="id" value="<?php echo isset( $f_candidate_id ) ? $f_candidate_id : 0 ?>" />
<table border="0" class="width100" cellspacing="0">
<tr>
	<td class="form-title" colspan="4">
<?php
		collapse_icon( 'candidatenotestats' );
?>
		<?php echo lang_get( 'time_tracking' ) ?>
	</td>
</tr>
<tr class="row-2">
        <td class="category" width="25%">
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
<?php if ( $t_cost_col ) { ?>
<tr class="row-1">
	<td>
		<?php echo lang_get( 'time_tracking_cost' ) ?>:
		<input type="text" name="candidatenote_cost" value="<?php echo $f_candidatenote_cost ?>" />
	</td>
</tr>
<?php } ?>
<tr>
        <td class="center" colspan="2">
                <input type="submit" class="button" name="get_candidatenote_stats_button" value="<?php echo lang_get( 'time_tracking_get_info_button' ) ?>" />
        </td>
</tr>

</table>
</form>
<?php
if ( !is_blank( $f_get_candidatenote_stats_button ) ) {
	$t_from = "$t_candidatenote_stats_from_y-$t_candidatenote_stats_from_m-$t_candidatenote_stats_from_d";
	$t_to = "$t_candidatenote_stats_to_y-$t_candidatenote_stats_to_m-$t_candidatenote_stats_to_d";
	$t_candidatenote_stats = candidatenote_stats_get_project_array( $f_project_id, $t_from, $t_to, $f_candidatenote_cost );

	if ( is_blank( $f_candidatenote_cost ) || ( (double)$f_candidatenote_cost == 0 ) ) {
		$t_cost_col = false;
    }

	$t_prev_id = -1;
?>
<br />
<table border="0" class="width100" cellspacing="0">
<tr class="row-category-history">
	<td class="small-caption">
		<?php echo lang_get( 'username' ) ?>
	</td>
	<td class="small-caption">
		<?php echo lang_get( 'time_tracking' ) ?>
	</td>
<?php if ( $t_cost_col) { ?>
	<td class="small-caption">
		<?php echo lang_get( 'time_tracking_cost' ) ?>
	</td>
<?php } ?>

</tr>
<?php
	$t_sum_in_minutes = 0;

	foreach ( $t_candidatenote_stats as $t_item ) {
		$t_sum_in_minutes += $t_item['sum_time_tracking'];

		$t_item['sum_time_tracking'] = db_minutes_to_hhmm( $t_item['sum_time_tracking'] );
		if ( $t_item['candidate_id'] != $t_prev_id) {
			$t_link = string_get_candidate_view_link( $t_item['candidate_id'] ) . ": " . string_display( $t_item['summary'] );
			echo '<tr class="row-category-history"><td colspan="4">' . $t_link . "</td></tr>";
			$t_prev_id = $t_item['candidate_id'];
		}
?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="small-caption">
		<?php echo $t_item['username'] ?>
	</td>
	<td class="small-caption">
		<?php echo $t_item['sum_time_tracking'] ?>
	</td>
<?php if ($t_cost_col) { ?>
	<td>
		<?php echo string_attribute( number_format( $t_item['cost'], 2 ) ); ?>
	</td>
<?php } ?>
</tr>
<?php } # end for loop 
?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="small-caption">
		<?php echo lang_get( 'total_time' ); ?>
	</td>
	<td class="small-caption">
		<?php echo db_minutes_to_hhmm( $t_sum_in_minutes ); ?>
	</td>
<?php if ($t_cost_col) { ?>
	<td>
		<?php echo string_attribute( number_format( $t_sum_in_minutes * $f_candidatenote_cost / 60, 2 ) ); ?>
	</td>
<?php } ?>
</tr>
</table>
<?php } # end if
	collapse_closed( 'candidatenotestats' );
?>
<table class="width100" cellspacing="0">
<tr>
	<td class="form-title" colspan="4">
		<?php collapse_icon( 'candidatenotestats' );
		echo lang_get( 'time_tracking' ) ?>
	</td>
</tr>
</table>
<?php
	collapse_end( 'candidatenotestats' );
?>
