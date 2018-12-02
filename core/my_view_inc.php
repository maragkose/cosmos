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
	# $Id: my_view_inc.php,v 1.18.2.2 2007-10-13 22:35:34 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'current_user_api.php' );
	require_once( $t_core_path . 'candidate_api.php' );
	require_once( $t_core_path . 'string_api.php' );
	require_once( $t_core_path . 'date_api.php' );
	require_once( $t_core_path . 'icon_api.php' );
?>
<?php
	$t_filter = current_user_get_candidate_filter();

	if ( $t_filter === false ) {
		$t_filter = filter_get_default();
	}

	$t_sort = $t_filter['sort'];
	$t_dir = $t_filter['dir'];

	$t_checkboxes_exist = false;

	$t_icon_path = config_get( 'icon_path' );
	$t_update_candidate_threshold = config_get( 'update_candidate_threshold' );
	$t_candidate_resolved_status_threshold = config_get( 'candidate_resolved_status_threshold' );
	$t_hide_status_default = config_get( 'hide_status_default' );
	$t_default_show_changed = config_get( 'default_show_changed' );
?>

<?php
	$c_filter['assigned'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => META_FILTER_ANY ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => META_FILTER_ANY ),
		'handler_id'		=> Array ( '0' => $t_current_user_id ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_candidate_resolved_status_threshold ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY )
	);
	$url_link_parameters['assigned'] = 'handler_id=' . $t_current_user_id . '&amp;hide_status=' . $t_candidate_resolved_status_threshold;

	$c_filter['recent_mod'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => META_FILTER_ANY ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => META_FILTER_ANY ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => META_FILTER_NONE ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY )
	);
	$url_link_parameters['recent_mod'] = 'hide_status=none';

	$c_filter['reported'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => META_FILTER_ANY ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => $t_current_user_id ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'sort'			=> 'last_updated',
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY )
	);
	$url_link_parameters['reported'] = 'reporter_id=' . $t_current_user_id . '&amp;hide_status=' . $t_hide_status_default;

	$c_filter['resolved'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => $t_candidate_resolved_status_threshold ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => META_FILTER_ANY ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY )
	);
	$url_link_parameters['resolved'] = 'show_status=' . $t_candidate_resolved_status_threshold . '&amp;hide_status=' . $t_candidate_resolved_status_threshold;

	$c_filter['unassigned'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => META_FILTER_ANY ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => META_FILTER_ANY ),
		'handler_id'		=> Array ( '0' => META_FILTER_NONE ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY ),
		'custom_1st Interview Date'	=> Array ( '0' => META_FILTER_ANY )
	);
	$url_link_parameters['unassigned'] = 'handler_id=[none]' . '&amp;hide_status=' . $t_hide_status_default;

	$c_filter['monitored'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => META_FILTER_ANY ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => META_FILTER_ANY ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => $t_current_user_id )
	);
	$url_link_parameters['monitored'] = 'user_monitor=' . $t_current_user_id . '&amp;hide_status=' . $t_hide_status_default;


	$c_filter['feedback'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => FEEDBACK ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => $t_current_user_id ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY )
	);
	$url_link_parameters['feedback'] = 'reporter_id=' . $t_current_user_id . '&amp;show_status=' . FEEDBACK . '&amp;hide_status=' . $t_hide_status_default;

	$c_filter['verify'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => $t_candidate_resolved_status_threshold ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => $t_current_user_id ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY )
	);
	$url_link_parameters['verify'] = 'reporter_id=' . $t_current_user_id . '&amp;show_status=' . $t_candidate_resolved_status_threshold;
	
	$t_realname = current_user_get_field( 'realname' );
	$t_parts = explode(" ",$t_realname);
	$t_interviewer = current_user_get_interviewer_name( $t_parts[1] );
	$c_filter['myinterviews'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => CONFIRMED ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => META_FILTER_ANY ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY ),
		'fixed_in_version'	=> Array ( '0' => $t_interviewer)
	);
	$c_filter['mysecinterviews'] = array(
		'show_category'		=> Array ( '0' => META_FILTER_ANY ),
		'show_severity'		=> Array ( '0' => META_FILTER_ANY ),
		'show_status'		=> Array ( '0' => CONFIRMED ),
		'highlight_changed'	=> $t_default_show_changed,
		'reporter_id'		=> Array ( '0' => META_FILTER_ANY ),
		'handler_id'		=> Array ( '0' => META_FILTER_ANY ),
		'show_resolution'	=> Array ( '0' => META_FILTER_ANY ),
		'show_build'		=> Array ( '0' => META_FILTER_ANY ),
		'show_version'		=> Array ( '0' => META_FILTER_ANY ),
		'hide_status'		=> Array ( '0' => $t_hide_status_default ),
		'user_monitor'		=> Array ( '0' => META_FILTER_ANY ),
		'target_version'	=> Array ( '0' => $t_interviewer)
	);
	$url_link_parameters['myinterviews'] = 'show_status=' . CONFIRMED; 

        $rows1 = filter_get_candidate_rows ( $f_page_number, $t_per_page, $t_page_count, $t_candidate_count, $c_filter[$t_box_title]  );
		$t_filter = array_merge( $c_filter[$t_box_title], $t_filter );
        $rows2 = filter_get_candidate_rows ( $f_page_number, $t_per_page, $t_page_count, $t_candidate_count, $c_filter['mysecinterviews']  );
	$t_filter = array_merge( $c_filter['mysecinterviews'], $t_filter );
	$rows = array_merge($rows1, $rows2);
        $box_title = lang_get( 'my_view_title_' . $t_box_title );
?>


<?php # -- ====================== BUG LIST ========================= -- ?>

<table class="width100" cellspacing="1">
<?php # -- Navigation header row -- ?>
<tr>
	<?php # -- Viewing range info -- ?>
	<td class="form-title" colspan="2">
		<?php
			echo '<a class="subtle" href="view_all_set.php?type=1&amp;temporary=y&amp;' . $url_link_parameters[$t_box_title] . '">';
			echo $box_title;
			if ($box_title == lang_get('my_view_title_assigned')) {
				echo ' (' . current_user_get_assigned_open_candidate_count() . ')';
			}
			echo '</a>';
			echo ' [';
			echo '<a class="subtle" href="view_all_set.php?type=1&amp;temporary=y&amp;' . $url_link_parameters[$t_box_title] . '" target="_blank">';
			echo '<img class="icon_link" src="images/view.png" alt="View Items"></img>';
			echo '</a>]';
			if ( strcmp($box_title,"Unassigned") == 0) {
				$arguments = array();
				$arguments['send_reminder'] = 'yes';
				print_button( 'http://davinci.emea.nsn-net.net/cosmos/send_reminder.php', lang_get('send_reminder'), $arguments); 
			}
		?>
		<?php
			if ( sizeof( $rows ) > 0 ) {
				$v_start = $t_filter['per_page'] * ($f_page_number-1) +1;
				$v_end   = $v_start + sizeof( $rows ) -1;
			} else {
				$v_start = 0;
				$v_end   = 0;
			}
			echo "($v_start - $v_end / $t_candidate_count)";
		?>
	</td>
</tr>

<?php mark_time( 'begin loop' ); ?>
<?php # -- Loop over candidate rows and create $v_* variables -- ?>
<?php
	for($i=0; $i < sizeof( $rows ); $i++) {
		# prefix candidate data with v_
		extract( $rows[$i], EXTR_PREFIX_ALL, 'v' );
                #var_dump($rows);
		$t_summary = string_attribute( $v_summary );
		$t_last_updated = date( config_get( 'normal_date_format' ), $v_last_updated );

		# choose color based on status
		$status_color = get_status_color( $v_status );

		# grab the candidatenote count
		# @@@ thraxisp - not used???
#		$candidatenote_info = candidate_get_candidatenote_stats( $v_id );

		# Check for attachments
		$t_attachment_count = 0;
		if (  ( file_can_view_candidate_attachments( $v_id ) ) ) {
		   $t_attachment_count = file_candidate_attachment_count( $v_id );
		}

		# grab the project name
		$project_name = project_get_field( $v_project_id, 'name' );
?>

<tr bgcolor="<?php echo $status_color ?>">
	<?php # -- Bug ID and details link + Pencil shortcut -- ?>
	<td class="center" valign="top" width ="0" nowrap="nowrap">
		<span class="small">
		<?php
			print_candidate_link_home( $v_id );

			echo '<br />';

			if ( !candidate_is_readonly( $v_id ) && access_has_candidate_level( $t_update_candidate_threshold, $v_id ) ) {
				echo '<a href="' . string_get_candidate_update_url( $v_id ) . '"><img border="0" width="24" height="24" src="' . $t_icon_path . 'update.png' . '" alt="' . lang_get( 'update_candidate_button' ) . '" /></a>';
			}

			if ( ON == config_get( 'show_priority_text' ) ) {
				print_formatted_priority_string( $v_status, $v_priority );
			} else {
				print_status_icon( $v_priority );
			}

			if ( 0 < $t_attachment_count ) {
				echo '<a href="' . string_get_candidate_view_url( $v_id ) . '#attachments">';
				echo '<img border="0" width="24" height="24" src="' . $t_icon_path . 'attachment.png' . '"';
				echo ' alt="' . lang_get( 'attachment_alt' ) . '"';
				echo ' title="' . $t_attachment_count . ' ' . lang_get( 'attachments' ) . '"';
				echo ' />';
				echo '</a>';
			}
			if ( VS_PRIVATE == $v_view_state ) {
				echo '<img src="' . $t_icon_path . 'protected.gif" width="8" height="15" alt="' . lang_get( 'private' ) . '" />';
			}
		?>
		</span>
	</td>

	<?php # -- Summary -- ?>
	<td class="left" valign="top" width="100%">
		<span class="small">
		<?php
			echo $t_summary;
		?>
		<br />
		<?php
			# type project name if viewing 'all projects' or candidate is in subproject
			if ( ON == config_get( 'show_candidate_project_links' ) &&
				helper_get_current_project() != $v_project_id ) {
				echo '[';
				print( $project_name );
				echo '] ';
			}
			echo string_display( $v_category );

			#if ( $v_last_updated > strtotime( '-'.$t_filter['highlight_changed'].' hours' ) ) {
			#	echo ' - <b>' . $t_last_updated . '</b>';
			#} else {
		#		echo ' - ' . $t_last_updated;
		#	}
		?>
		</span>
	</td>
</tr>
<?php # -- end of Repeating candidate row -- ?>
<?php
	}
?>
<?php # -- ====================== end of BUG LIST ========================= -- ?>

</table>

<?php mark_time( 'end loop' ); ?>
