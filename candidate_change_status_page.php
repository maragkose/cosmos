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
	# $Id: candidate_change_status_page.php,v 1.28.2.1 2007-10-13 22:32:36 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	$g_allow_browser_cache = 1;
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );

	require_once( $t_core_path.'relationship_api.php' );
?>
<?php
	$f_candidate_id = gpc_get_int( 'candidate_id' );
	$t_candidate = candidate_get( $f_candidate_id );
	
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}
	
	$f_new_status = gpc_get_int( 'new_status' );
	$f_reopen_flag = gpc_get_int( 'reopen_flag', OFF );

	if ( ! ( ( access_has_candidate_level( access_get_status_threshold( $f_new_status, candidate_get_field( $f_candidate_id, 'project_id' ) ), $f_candidate_id ) ) ||
				( ( candidate_get_field( $f_candidate_id, 'reporter_id' ) == auth_get_current_user_id() ) &&
						( ( ON == config_get( 'allow_reporter_reopen' ) ) ||
								( ON == config_get( 'allow_reporter_close' ) ) ) ) ||
				( ( ON == $f_reopen_flag ) && ( access_has_candidate_level( config_get( 'reopen_candidate_threshold' ), $f_candidate_id ) ) )
			) ) {
		access_denied();
	}

	# get new issue handler if set, otherwise default to original handler
	$f_handler_id = gpc_get_int( 'handler_id', candidate_get_field( $f_candidate_id, 'handler_id' ) );

	if ( ASSIGNED == $f_new_status ) {
		$t_candidate_sponsored = sponsorship_get_amount( sponsorship_get_all_ids( $f_candidate_id ) ) > 0;
		if ( $t_candidate_sponsored ) {
			if ( !access_has_candidate_level( config_get( 'assign_sponsored_candidates_threshold' ), $f_candidate_id ) ) {
				trigger_error( ERROR_SPONSORSHIP_ASSIGNER_ACCESS_LEVEL_TOO_LOW, ERROR );
			}
		}

		if ( $f_handler_id != NO_USER ) {
            if ( !access_has_candidate_level( config_get( 'handle_candidate_threshold' ), $f_candidate_id, $f_handler_id ) ) {
				trigger_error( ERROR_HANDLER_ACCESS_TOO_LOW, ERROR );
			}

			if ( $t_candidate_sponsored ) {
				if ( !access_has_candidate_level( config_get( 'handle_sponsored_candidates_threshold' ), $f_candidate_id, $f_handler_id ) ) {
					trigger_error( ERROR_SPONSORSHIP_HANDLER_ACCESS_LEVEL_TOO_LOW, ERROR );
				}
			}
		}
	}

	$t_status_label = str_replace( " ", "_", get_enum_to_string( config_get( 'status_enum_string' ), $f_new_status ) );
	$t_resolved = config_get( 'candidate_resolved_status_threshold' );

	$t_candidate = candidate_get( $f_candidate_id );

	html_page_top1( candidate_format_summary( $f_candidate_id, SUMMARY_CAPTION ) );
	html_page_top2();

	print_recently_visited();
?>

<br />
<div align="center">
<form method="post" action="candidate_update.php">
<?php echo form_security_field( 'candidate_update' ) ?>
<table class="width75" cellspacing="1">


<!-- Title -->
<tr>
	<td class="document-form" colspan="2">
		<input type="hidden" name="candidate_id" value="<?php echo $f_candidate_id ?>" />
		<input type="hidden" name="status" value="<?php echo $f_new_status ?>" />
		<?php echo lang_get( $t_status_label . '_candidate_title' ) ?>
	</td>
</tr>

<?php
	# relationship warnings
	#
if ( ON == config_get( 'enable_relationship' ) ) {
	if ( $t_resolved <= $f_new_status ) {
		if ( relationship_can_resolve_candidate( $f_candidate_id ) == false ) {
			echo "<tr><td colspan=\"2\">" . lang_get( 'relationship_warning_blocking_candidates_not_resolved_2' ) . "</td></tr>";
		}
	}
}
?>

<?php
$t_current_resolution = $t_candidate->resolution;
$t_candidate_is_open = in_array( $t_current_resolution, array( OPEN, REOPENED ) );
if ( ( $t_resolved <= $f_new_status ) && ( ( CLOSED > $f_new_status ) || ( $t_candidate_is_open ) ) ) { ?>
<!-- Resolution -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'resolution' ) ?>
	</td>
	<td>
		<select name="resolution">
			<?php 
                $t_resolution = $t_candidate_is_open ? FIXED : $t_current_resolution;
                print_enum_string_option_list( "resolution", $t_resolution );
            ?>
		</select>
	</td>
</tr>
<?php } ?>

<?php
if ( ( $t_resolved <= $f_new_status ) && ( CLOSED > $f_new_status ) ) { ?>
<!-- Duplicate ID -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'duplicate_id' ) ?>
	</td>
	<td>
		<input type="text" name="duplicate_id" maxlength="7" />
	</td>
</tr>
<?php } ?>

<?php
if ( ( $t_resolved > $f_new_status ) &&
		access_has_candidate_level( config_get( 'update_candidate_assign_threshold', config_get( 'update_candidate_threshold')), $f_candidate_id) ) { ?>
<!-- Assigned To -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'assigned_to' ) ?>
	</td>
	<td>
		<select name="handler_id">
			<option value="0"></option>
			<?php print_assign_to_option_list( $t_candidate->handler_id, $t_candidate->project_id ) ?>
		</select>
	</td>
</tr>
<?php } ?>

<!-- Custom Fields -->
<?php
# @@@ thraxisp - I undid part of the change for #5068 for #5527
#  We really need to say what fields are shown in which statusses. For now,
#  this page will show required custom fields in update mode, or 
#  display or required fields on resolve or close
$t_custom_status_label = "update"; # Don't show custom fields by default
if ( ( $f_new_status == $t_resolved ) && ( CLOSED > $f_new_status ) ) {
	$t_custom_status_label = "resolved";
}
if ( CLOSED == $f_new_status ) {
	$t_custom_status_label = "closed";
}

$t_related_custom_field_ids = custom_field_get_linked_ids( candidate_get_field( $f_candidate_id, 'project_id' ) );

foreach( $t_related_custom_field_ids as $t_id ) {
	$t_def = custom_field_get_definition( $t_id );
	$t_display = $t_def['display_' . $t_custom_status_label];
	$t_require = $t_def['require_' . $t_custom_status_label];
	
	#MANOS
	#echo custom_field_get_status_from_id($t_id);
	#echo "=";
	#echo $f_new_status;
	#echo "|";

        #if ( (custom_field_get_status_from_id($t_id) != $f_new_status) ) {
        #   continue;
	#}
	if ( ( "update" == $t_custom_status_label ) && ( ! $t_require ) ) {
        continue;
	}
	
	if ( in_array( $t_custom_status_label, array( "resolved", "closed"  ) ) && ! ( $t_display || $t_require ) ) {
	$g_status_enum_string				= '10:new,20:feedback,30:acknowledged,40:confirmed,50:assigned,60:completed,80:resolved,90:closed';
        continue;
	}
	if ( custom_field_has_write_access( $t_id, $f_candidate_id ) ) {
?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php if ( $t_require ) {?><span class="required">*</span><?php } ?><?php echo lang_get_defaulted( $t_def['name'] ) ?>
	</td>
	<td>
		<?php
			print_custom_field_input( $t_def, $f_candidate_id );
		?>
	</td>
</tr>
<?php
	} #  custom_field_has_write_access( $t_id, $f_candidate_id ) )
	else if ( custom_field_has_read_access( $t_id, $f_candidate_id ) ) {
?>
	<tr <?php echo helper_alternate_class() ?>>
		<td class="category">
			<?php echo lang_get_defaulted( $t_def['name'] ) ?>
		</td>
		<td>
			<?php print_custom_field_value( $t_def, $t_id, $f_candidate_id );			?>
		</td>
	</tr>
<?php
	} # custom_field_has_read_access( $t_id, $f_candidate_id ) )
} # foreach( $t_related_custom_field_ids as $t_id )
?>

<?php
if ( ( $f_new_status >= $t_resolved ) && access_has_candidate_level( config_get( 'handle_candidate_threshold' ), $f_candidate_id ) ) {
	$t_show_version = ( ON == config_get( 'show_product_version' ) )
		|| ( ( AUTO == config_get( 'show_product_version' ) )
					&& ( count( version_get_all_rows( $t_candidate->project_id ) ) > 0 ) );
	if ( $t_show_version ) {
?>
<!-- Fixed in Version -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'fixed_in_version' ) ?>
	</td>
	<td>
		<select name="fixed_in_version">
			<?php print_version_option_list( candidate_get_field( $f_candidate_id, 'fixed_in_version' ),
							candidate_get_field( $f_candidate_id, 'project_id' ), VERSION_ALL ) ?>
		</select>
	</td>
</tr>
<?php }
	} ?>

<?php
if ( ( $f_new_status >= $t_resolved ) && ( CLOSED > $f_new_status ) ) { ?>
<!-- Close Immediately (if enabled) -->
<?php if ( ( ON == config_get( 'allow_close_immediately' ) )
				&& ( access_has_candidate_level( access_get_status_threshold( CLOSED ), $f_candidate_id ) ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'close_immediately' ) ?>
	</td>
	<td>
		<input type="checkbox" name="close_now" />
	</td>
</tr>
<?php } ?>
<?php } ?>

<?php
	if ( ON == $f_reopen_flag ) {
		# candidate was re-opened
		printf("	<input type=\"hidden\" name=\"resolution\" value=\"%s\" />\n",  config_get( 'candidate_reopen_resolution' ) );
	}
?>

<!-- Bugnote -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'add_candidatenote_title' ) ?>
	</td>
	<td class="center">
		<textarea name="candidatenote_text" cols="80" rows="10"></textarea>
	</td>
</tr>
<?php if ( access_has_candidate_level( config_get( 'private_candidatenote_threshold' ), $f_candidate_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
<?php
		$t_default_candidatenote_view_status = config_get( 'default_candidatenote_view_status' );
		if ( access_has_candidate_level( config_get( 'set_view_status_threshold' ), $f_candidate_id ) ) {
?>
			<input type="checkbox" name="private" <?php check_checked( $t_default_candidatenote_view_status, VS_PUBLIC ); ?> />
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
<?php if ( access_has_candidate_level( config_get( 'private_candidatenote_threshold' ), $f_candidate_id ) ) { ?>
<?php if ( access_has_candidate_level( config_get( 'time_tracking_edit_threshold' ), $f_candidate_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'time_tracking' ) ?>
	</td>
	<td>
		<input type="text" name="time_tracking" size="5" value="0:00" />
	</td>
</tr>
<?php } ?>
<?php } ?>
<?php } ?>

<!-- Submit Button -->
<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo lang_get( $t_status_label . '_candidate_button' ) ?>" />
	</td>
</tr>


</table>
</form>
</div>

<br />
<?php
	include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'candidate_view_inc.php' );
	include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'candidatenote_view_inc.php' );
?>

<?php html_page_bottom1( __FILE__ ) ?>
