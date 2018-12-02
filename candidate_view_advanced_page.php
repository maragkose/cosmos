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
	# $Id: candidate_view_advanced_page.php,v 1.87.2.1 2007-10-13 22:32:59 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'candidate_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
	require_once( $t_core_path.'file_api.php' );
	require_once( $t_core_path.'compress_api.php' );
	require_once( $t_core_path.'date_api.php' );
	require_once( $t_core_path.'relationship_api.php' );
	require_once( $t_core_path.'last_visited_api.php' );
	require_once( $t_core_path.'tag_api.php' );

	$f_candidate_id		= gpc_get_int( 'candidate_id' );
	$f_history		= gpc_get_bool( 'history', config_get( 'history_default_visible' ) );

	candidate_ensure_exists( $f_candidate_id );

	access_ensure_candidate_level( VIEWER, $f_candidate_id );

	$t_candidate = candidate_prepare_display( candidate_get( $f_candidate_id, true ) );

	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	if ( SIMPLE_ONLY == config_get( 'show_view' ) ) {
		print_header_redirect ( 'candidate_view_page.php?candidate_id=' . $f_candidate_id );
	}

	compress_enable();

	html_page_top1( candidate_format_summary( $f_candidate_id, SUMMARY_CAPTION ) );
	html_page_top2();

	print_recently_visited();

	$t_access_level_needed = config_get( 'view_history_threshold' );
	$t_can_view_history = access_has_candidate_level( $t_access_level_needed, $f_candidate_id );

	$t_candidateslist = gpc_get_cookie( config_get( 'candidate_list_cookie' ), false );
?>
<br></br>
<table class="width100" cellspacing="1">
<!-- Title -->
<tr>
	<td class="document-form" colspan="<?php echo $t_candidateslist ? '3' : '4' ?>">
		<?php echo lang_get( 'viewing_candidate_advanced_details_title' ) ?>
	</td>
</tr>
<tr>
	<td class="document-form" colspan="<?php echo $t_candidateslist ? '3' : '4' ?>">
	<!-- Send Bug Reminder -->
	<?php
		if ( !current_user_is_anonymous() && !candidate_is_readonly( $f_candidate_id ) &&
			access_has_candidate_level( config_get( 'candidate_reminder_threshold' ), $f_candidate_id ) ) {
			html_button_candidate_update( $f_candidate_id, "button-small" );
	?>
		<span class="small">
			<?php print_button_link( 'candidate_reminder_page.php?candidate_id='.$f_candidate_id, lang_get( 'candidate_reminder' ), 'button-small' ) ?>
		</span>
		<!-- Jump to Bugnotes -->
		<span class="small"><?php print_button_link( "#candidatenotes", lang_get( 'jump_to_candidatenotes' ), 'button-small' ) ?></span>
	<?php
		}
		
		if ( wiki_is_enabled() ) {
	?>
		<span class="small">
			<?php print_bracket_link( 'wiki.php?id='.$f_candidate_id, lang_get( 'wiki' ) ) ?>
		</span>
	<?php
		}
	?>
	</td>

	<!-- prev/next links -->
	<?php if( $t_candidateslist ) { ?>
	<td class="center"><span class="small">
		<?php
			$t_candidateslist = explode( ',', $t_candidateslist );
			$t_index = array_search( $f_candidate_id, $t_candidateslist );
			if( false !== $t_index ) {
				if( isset( $t_candidateslist[$t_index-1] ) ) print_button_link( 'candidate_view_advanced_page.php?candidate_id='.$t_candidateslist[$t_index-1], '&lt;&lt;', 'button-small' );
				if( isset( $t_candidateslist[$t_index+1] ) ) print_button_link( 'candidate_view_advanced_page.php?candidate_id='.$t_candidateslist[$t_index+1], '&gt;&gt;', 'button-small');
			}
		?>
	</span></td>
	<?php } ?>

	<!-- Links -->
	<td class="right" colspan="2">

		<!-- Simple View (if enabled) -->
	<?php if ( BOTH == config_get( 'show_view' ) ) { ?>
			<span class="small"><?php print_bracket_link( 'candidate_view_page.php?candidate_id=' . $f_candidate_id, lang_get( 'view_simple_link' ) ) ?></span>
	<?php } ?>

	<?php if ( $t_can_view_history ) { ?>
		<!-- History -->
		<span class="small"><?php print_button_link( 'candidate_view_advanced_page.php?candidate_id=' . $f_candidate_id . '&amp;history=1#history', lang_get( 'candidate_history' ), 'button-small' ) ?></span>
	<?php } ?>

		<!-- Print Bug -->
		<span class="small"><?php print_button_link( 'print_candidate_page.php?candidate_id=' . $f_candidate_id, lang_get( 'print' ), 'button-small' ) ?></span>
	</td>

</tr>
<!-- Labels -->
<tr class="row-category">
	<td width="20%">
		<?php echo lang_get( 'summary' ) ?>
	</td>
	<td width="20%">
		<?php echo lang_get( 'category' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'severity' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'reproducibility' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'date_submitted' ) ?>
	</td>
	<td width="15%">
		<?php echo lang_get( 'last_update' ) ?>
	</td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
	<!-- Name -->
	<td class="bold">
		<?php echo candidate_format_summary( $f_candidate_id, SUMMARY_FIELD_SIMPLE ) ?>
	</td>
	<!-- Category -->
	<td>
		<?php
			$t_project_name = string_display( project_get_field( $t_candidate->project_id, 'name' ) );
			echo "[$t_project_name] $t_candidate->category";
		?>
	</td>
	<!-- Severity -->
	<td>
		<?php echo get_enum_element( 'severity', $t_candidate->severity ) ?>
	</td>
	<!-- Source -->
	<td>
		<?php echo get_enum_element( 'reproducibility', $t_candidate->reproducibility ) ?>
	</td>
	<!-- Date Submitted -->
	<td>
		<?php print_date( config_get( 'normal_date_format' ), $t_candidate->date_submitted ) ?>
	</td>
	<!-- Date Updated -->
	<td>
		<?php print_date( config_get( 'normal_date_format' ), $t_candidate->last_updated ) ?>
	</td>
</tr>
<!-- spacer -->
<tr class="spacer">
	<td colspan="6"></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>

	<!-- Reporter -->
	<td class="category">
		<?php echo lang_get( 'reporter' ) ?>
	</td>
	<td>
		<?php print_user_with_subject( $t_candidate->reporter_id, $f_candidate_id ) ?>
	</td>

	<!-- View Status -->
	<td class="category">
		<?php echo lang_get( 'view_status' ) ?>
	</td>
	<td>
		<?php echo get_enum_element( 'project_view_state', $t_candidate->view_state ) ?>
	</td>

	<!-- spacer -->
	<td colspan="2">&nbsp;</td>

</tr>

<tr <?php echo helper_alternate_class() ?>>
	<!-- Handler -->
	<td class="category">
		<?php echo lang_get( 'assigned_to' ) ?>
	</td>
	<td colspan="1">
		<?php 
			if ( access_has_candidate_level( config_get( 'view_handler_threshold' ), $f_candidate_id ) ) {
				print_user_with_subject( $t_candidate->handler_id, $f_candidate_id );
			}
		?>
	</td>
	<!-- Status -->
	<td class="category">
		<?php echo lang_get( 'status' ) ?>
	</td>
	<td colspan="3" bgcolor="<?php echo get_status_color( $t_candidate->status ) ?>">
		<?php echo get_enum_element( 'status', $t_candidate->status ) ?>
	</td>
</tr>
<?php
			$t_show_version = ( ON == config_get( 'show_product_version' ) )
					|| ( ( AUTO == config_get( 'show_product_version' ) )
								&& ( count( version_get_all_rows( $t_candidate->project_id ) ) > 0 ) );
	if( $t_show_version ) {
?>
<tr <?php echo helper_alternate_class() ?>>
		<?php
			$t_show_version = ( ON == config_get( 'show_product_version' ) )
					|| ( ( AUTO == config_get( 'show_product_version' ) )
					&& ( count( version_get_all_rows( $t_candidate->project_id ) ) > 0 ) );
			if ( $t_show_version ) {
		?>
	<!-- First Interviewer -->
	<td class="category">
		<?php echo lang_get( 'fixed_in_version' ) ?>
	</td>
	<td>
		<?php echo user_get_realname($t_candidate->fixed_in_version) ?>
	</td>
		<?php
			} else {
		?>
	<td>
	</td>
	<td>
	</td>
		<?php
			}
		?>
	<!-- Second Interviewer -->
<?php
	if ( access_has_candidate_level( config_get( 'roadmap_view_threshold' ), $f_candidate_id ) ) {
?>
	<td class="category">
		<?php echo lang_get( 'target_version' ) ?>
	</td>
	<td>
		<?php echo user_get_realname($t_candidate->fixed_in_version) ?>
	</td>
<?php
	} else {
?>
	<!-- spacer -->
	<td colspan="4">&nbsp;</td>
<?php
	}
?>
	<!-- Third Interviewer -->
	<td class="category">
		<?php echo lang_get( 'steps_to_reproduce' ) ?>
	</td>
	<td colspan="1">
		<?php echo user_get_realname($t_candidate->steps_to_reproduce) ?>
	</td>
</tr>
<!-- horizontal line spacer -->
<tr class="spacer">
	<td colspan="6"></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
	<!-- Experience -->
	<td class="category">
		<?php echo lang_get( 'priority' ) ?>
	</td>
	<td>
		<?php echo get_enum_element( 'priority', $t_candidate->priority ) ?>
	</td>
	<!-- Final Result -->
	<td class="category">
		<?php echo lang_get( 'resolution' ) ?>
	</td>
	<td>
		<?php echo get_enum_element( 'resolution', $t_candidate->resolution ) ?>
	</td>
	<!-- spacer -->
	<td colspan="2">
	</td>
</tr>

<tr <?php echo helper_alternate_class() ?>>

	<!-- Contact No -->
	<td class="category_b">
		<?php echo lang_get( 'product_build' ); ?> 
	</td>
	<td>
		<?php echo $t_candidate->build;
		print_button_link("sip:$t_candidate->build", "call", "button-small"); ?>
	</td>
	<!-- email -->
	<td class="category">
		<?php echo lang_get( 'os_version' ) ?>
	</td>
	<td>
		<?php echo $t_candidate->os_build ?>
	</td>
	<!--spacer-->
	<td colspan="2">
	</td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
	<!-- City -->
	<td class="category">
		<?php echo lang_get( 'platform' ) ?>
	</td>
	<td>
		<?php echo $t_candidate->platform ?>
	</td>
	<!-- Address -->
	<td class="category">
		<?php echo lang_get( 'os' ) ?>
	</td>
	<td>
		<?php echo $t_candidate->os ?>
	</td>
	<!--spacer-->
	<td colspan="2">
	</td>
</tr>

<?php
	}
?>
<!-- Description -->
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<?php echo lang_get( 'description' ) ?>
	</td>
	<td colspan="5">
		<?php echo $t_candidate->description ?>
	</td>
</tr>
<!-- Manos Removed Additional Information -->
<!-- Tagging -->
<?php if ( access_has_global_level( config_get( 'tag_view_threshold' ) ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category"><?php echo lang_get( 'tags' ) ?></td>
	<td colspan="5">
<?php
	tag_display_attached( $f_candidate_id );
?>
	</td>
</tr>
<?php } # has tag_view access ?>

<?php if ( access_has_candidate_level( config_get( 'tag_attach_threshold' ), $f_candidate_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category"><?php echo lang_get( 'tag_attach_long' ) ?></td>
	<td colspan="5">
<?php
	print_tag_attach_form( $f_candidate_id );
?>
	</td>
</tr>
<?php } # has tag attach access ?>

<!-- spacer -->
<tr class="spacer">
	<td colspan="6"></td>
</tr>
<!-- Custom Fields -->
<?php
	$t_custom_fields_found = false;
	$t_related_custom_field_ids = custom_field_get_linked_ids( $t_candidate->project_id );
	foreach( $t_related_custom_field_ids as $t_id ) {
		if ( !custom_field_has_read_access( $t_id, $f_candidate_id ) ) {
			continue;
		} # has read access

		$t_custom_fields_found = true;
		$t_def = custom_field_get_definition( $t_id );
?>
	<tr <?php echo helper_alternate_class() ?>>
		<td class="category">
			<?php echo string_display( lang_get_defaulted( $t_def['name'] ) ) ?>
		</td>
		<td colspan="5">
		<?php print_custom_field_value( $t_def, $t_id, $f_candidate_id ); ?>
		</td>
	</tr>
<?php
	} # foreach
?>

<?php if ( $t_custom_fields_found ) { ?>
<!-- spacer -->
<tr class="spacer">
	<td colspan="6"></td>
</tr>
<?php } # custom fields found ?>


<!-- Attachments -->
<?php
	$t_show_attachments = ( $t_candidate->reporter_id == auth_get_current_user_id() ) || access_has_candidate_level( config_get( 'view_attachments_threshold' ), $f_candidate_id );

	if ( $t_show_attachments ) {
?>
<tr <?php echo helper_alternate_class() ?>>
	<td class="category">
		<a name="attachments" id="attachments" />
		<?php echo lang_get( 'attached_files' ) ?>
	</td>
	<td colspan="5">
		<?php file_list_attachments ( $f_candidate_id ); ?>
	</td>
</tr>
<?php
	}
?>
<!-- Buttons -->
<tr align="center">
	<td align="center" colspan="6">
<?php
	html_buttons_view_candidate_page( $f_candidate_id );
?>
	</td>
</tr>
</table>

<?php
	$t_cosmos_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;

	# User list sponsoring the candidate
	if ( ON == config_get( 'enable_sponsorship' ) ) {
		include( $t_cosmos_dir . 'candidate_sponsorship_list_view_inc.php' );
	}
	# Bug Relationships
	# MASC RELATIONSHIP
	if ( ON == config_get( 'enable_relationship' ) ) {
		relationship_view_box ( $f_candidate_id );
	}
	# MASC RELATIONSHIP

	# File upload box
	if ( !candidate_is_readonly( $f_candidate_id ) ) {
		include( $t_cosmos_dir . 'candidate_file_upload_inc.php' );
	}

	# User list monitoring the candidate
	include( $t_cosmos_dir . 'candidate_monitor_list_view_inc.php' );

	# Bugnotes and "Add Note" box
	if ( 'ASC' == current_user_get_pref( 'candidatenote_order' ) ) {
		include( $t_cosmos_dir . 'candidatenote_view_inc.php' );
		include( $t_cosmos_dir . 'candidatenote_add_inc.php' );
	} else {
		include( $t_cosmos_dir . 'candidatenote_add_inc.php' );
		include( $t_cosmos_dir . 'candidatenote_view_inc.php' );
	}

	# History
	if ( $f_history ) {
		include( $t_cosmos_dir . 'history_inc.php' );
	}

	html_page_bottom1( __FILE__ );
	last_visited_issue( $f_candidate_id );
?>
