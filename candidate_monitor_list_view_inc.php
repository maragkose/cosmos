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
	# $Id: candidate_monitor_list_view_inc.php,v 1.16.2.1 2007-10-13 22:32:43 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	# This include file prints out the list of users monitoring the current
	# candidate.	$f_candidate_id must be set and be set to the candidate id
?>
<?php	if ( access_has_candidate_level( config_get( 'show_monitor_list_threshold' ), $f_candidate_id ) ) { ?>
<?php
	$c_candidate_id = db_prepare_int( $f_candidate_id );
	$t_candidate_monitor_table = config_get( 'cosmos_candidate_monitor_table' );
	$t_user_table = config_get( 'cosmos_user_table' );

	# get the candidatenote data
	$query = "SELECT user_id, enabled
			FROM $t_candidate_monitor_table m, $t_user_table u
			WHERE m.candidate_id=$c_candidate_id AND m.user_id = u.id
			ORDER BY u.realname, u.username";
	$result = db_query($query);
	$num_users = db_num_rows($result);

	echo '<a name="monitors" id="monitors" /><br />';
?>

<?php
	collapse_open( 'monitoring' );
?>
<table class="width100" cellspacing="1">
<?php 	if ( 0 == $num_users ) { ?>
<tr>
	<td class="center">
		<?php echo lang_get( 'no_users_monitoring_candidate' ); ?>
	</td>
</tr>
<?php	} else { ?>
<tr>
	<td class="form-title" colspan="2">
<?php
	collapse_icon( 'monitoring' );
 ?>
		<?php echo lang_get( 'users_monitoring_candidate' ); ?>
	</td>
</tr>
<tr class="row-1">
	<td class="category" width="15%">
		<?php echo lang_get( 'monitoring_user_list' ); ?>
	</td>
	<td>
<?php
 		for ( $i = 0; $i < $num_users; $i++ ) {
 			$row = db_fetch_array( $result );
			echo ($i > 0) ? ', ' : '';
			echo print_user( $row['user_id'] );
 		}
?>
	</td>
</tr>
<?php 	} ?>
</table>
<?php
	collapse_closed( 'monitoring' ); 
?>
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2"><?php collapse_icon( 'monitoring' ); ?>
		<?php echo lang_get( 'users_monitoring_candidate' ); ?>
	</td>
</tr>
</table>
<?php
	collapse_end( 'monitoring' );
?>

<?php } # show monitor list ?>
