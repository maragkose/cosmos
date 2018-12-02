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
        # $Id: candidate_update_advanced_page.php,v 1.1.1.1 2009/01/08 07:30:19 chirag Exp $
        # --------------------------------------------------------

        $g_allow_browser_cache = 1;
        require_once( 'core.php' );

        $t_core_path = config_get( 'core_path' );

        require_once( $t_core_path.'ajax_api.php' );
        require_once( $t_core_path.'candidate_api.php' );
        require_once( $t_core_path.'custom_field_api.php' );
        require_once( $t_core_path.'date_api.php' );
        require_once( $t_core_path.'last_visited_api.php' );
        require_once( $t_core_path.'projax_api.php' );
?>
<?php
        $f_candidate_id = gpc_get_int( 'candidate_id' );

        $t_candidate = candidate_prepare_edit( candidate_get( $f_candidate_id, true ) );

        if( $t_candidate->project_id != helper_get_current_project() ) {
                # in case the current project is not the same project of the candidate we are viewing...
                # ... override the current project. This to avoid problems with categories and handlers lists etc.
                $g_project_override = $t_candidate->project_id;
                $t_changed_project = true;
        } else {
                $t_changed_project = false;
        }

        if ( SIMPLE_ONLY == config_get( 'show_update' ) ) {
                print_header_redirect ( 'candidate_update_page.php?candidate_id=' . $f_candidate_id );
        }

        if ( candidate_is_readonly( $f_candidate_id ) ) {
                error_parameters( $f_candidate_id );
                trigger_error( ERROR_BUG_READ_ONLY_ACTION_DENIED, ERROR );
        }

        access_ensure_candidate_level( config_get( 'update_candidate_threshold' ), $f_candidate_id );

        #html_page_top1( candidate_format_summary( $f_candidate_id, SUMMARY_CAPTION ) );
        html_page_top1();
        html_page_top2();

        print_recently_visited();
?>

<br></br>
<form method="post" action="candidate_update.php">
<?php echo form_security_field( 'candidate_update' ) ?>
<table class="width100" cellspacing="1">
<tr>
        <td class="document-form" colspan="3">
                <input type="hidden" name="candidate_id" value="<?php echo $f_candidate_id ?>" />
                <input type="hidden" name="update_mode"                 value="1" />
                <?php echo lang_get( 'updating_candidate_advanced_title' ) ?>
        </td>
</tr>

<!-- Submit Button -->
<tr>
        <td class="left" colspan="0">
                <input <?php echo helper_get_tab_index() ?> type="submit" class="button" value="<?php echo lang_get( 'update_information_button' ) ?>" />
        </td>
        <td class="left" colspan="0">
<?php
        print_button_link( string_get_candidate_view_url( $f_candidate_id ), lang_get( 'back_to_candidate_link' ), 'button' );

        if ( BOTH == config_get( 'show_update' ) ) {
                print_bracket_link( 'candidate_update_page.php?candidate_id=' . $f_candidate_id, lang_get( 'update_simple_link' ) );
        }
?>
        </td>
</tr>

<tr class="row-category">
        <td width="15%">
                <?php echo lang_get( 'summary' ) ?>
        </td>
        <td width="20%">
                <?php echo lang_get( 'category' ) ?>
        </td>
        <td width="15%">
                <?php echo lang_get( 'severity' ) ?>
        </td>
        <td width="20%">
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
        <td colspan="0">
                <input <?php echo helper_get_tab_index() ?> type="text" name="summary"size="24" maxlength="128" value="<?php echo $t_candidate->summary ?>" />
        </td>

        <!-- Category -->
        <td>
                <?php if ( $t_changed_project ) {
                        echo "[" . project_get_field( $t_candidate->project_id, 'name' ) . "] ";
                } ?>
                <select <?php echo helper_get_tab_index() ?> name="category">
                <?php print_category_option_list( $t_candidate->category, $t_candidate->project_id ) ?>
                </select>
        </td>

        <!-- Severity -->
        <td>
                <select <?php echo helper_get_tab_index() ?> name="severity">
                        <?php print_enum_string_option_list( 'severity', $t_candidate->severity ) ?>
                </select>
        </td>

        <!-- Reproducibility -->
        <td>
                <select <?php echo helper_get_tab_index() ?> name="reproducibility" <?=$Disabledme;?>>
                        <?php print_enum_string_option_list( 'reproducibility', $t_candidate->reproducibility ) ?>
                </select>
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
                <?php
                        if ( ON == config_get( 'use_javascript' ) ) {
                                $t_username = prepare_user_name( $t_candidate->reporter_id );
                                echo ajax_click_to_edit( $t_username, 'reporter_id', 'entrypoint=issue_reporter_combobox&amp;issue_id=' . $f_candidate_id );
                        } else {
                                echo '<select <?php echo helper_get_tab_index() ?> name="reporter_id">';
                                print_reporter_option_list( $t_candidate->reporter_id, $t_candidate->project_id );
                                echo '</select>';
                        }
                ?>
        </td>

        <!-- View Status -->
        <td class="category">
                <?php echo lang_get( 'view_status' ) ?>
        </td>
        <td>
<?php
                if ( access_has_project_level( config_get( 'change_view_status_threshold' ) ) ) { ?>
                        <select <?php echo helper_get_tab_index() ?> name="view_state">
                                <?php print_enum_string_option_list( 'view_state', $t_candidate->view_state) ?>
                        </select>
<?php
                } else {
                        echo get_enum_element( 'view_state', $t_candidate->view_state );
                }
?>
        </td>

        <!-- spacer -->
        <td colspan="2">&nbsp;</td>
</tr>


<tr <?php echo helper_alternate_class() ?>>

        <!-- Assigned To -->
        <td class="category">
                <?php echo lang_get( 'assigned_to' ) ?>
        </td>
        <td colspan="1">
        <?php if ( access_has_project_level( config_get( 'update_candidate_assign_threshold', config_get( 'update_candidate_threshold' ) ) ) ) {
        ?>
                <select <?php echo helper_get_tab_index() ?> name="handler_id" <?=$Disabledme;?>>
                        <option value="0"></option>
                        <?php print_assign_to_option_list( $t_candidate->handler_id, $t_candidate->project_id ) ?>
                </select>
        <?php
                } else {
                        echo user_get_name( $t_candidate->handler_id );
                }
        ?>
        </td>
        <!-- Status -->
        <td class="category">
                <?php echo lang_get( 'status' ) ?>
        </td>
        <td colspan="3" bgcolor="<?php echo get_status_color( $t_candidate->status ) ?>">
                <?php
                #
                # Does not allow to update to reporter 
                if($Disabledme==""){
                print_status_option_list( 'status', $t_candidate->status,
                                        ( $t_candidate->reporter_id == auth_get_current_user_id() &&
                                        ( ON == config_get( 'allow_reporter_close' ) ) ), $t_candidate->project_id ); 
                } else {
                        echo "";
                }
                                        
                ?>
        </td>

</tr>
        <?php
                $t_show_version = ( ON == config_get( 'show_product_version' ) )
                                        || ( ( AUTO == config_get( 'show_product_version' ) )
                                        && ( count( version_get_all_rows( $t_candidate->project_id ) ) > 0 ) );
                if ( $t_show_version ) {
        ?>

<tr <?php echo helper_alternate_class() ?>>
        <td class="category">
                <?php
                        $t_show_version = ( ON == config_get( 'show_product_version' ) )
                                        || ( ( AUTO == config_get( 'show_product_version' ) )
                                        && ( count( version_get_all_rows( $t_candidate->project_id ) ) > 0 ) );
                        if ( $t_show_version ) {
                                echo lang_get( 'fixed_in_version' );
                        }
                ?>
        </td>
        <!-- First Interviewer -->
        <td>
                <?php
                        if ( $t_show_version ) {
                ?>
                <?php if ( !access_has_project_level( config_get( 'able_to_edit_field' ) ) ) {
                        $disabled='disabled';
                } else {
                        $disabled='';
                }
                ?>
                        <select <?php echo helper_get_tab_index() ?> name="fixed_in_version" <?php echo $disabled ?> >
                        <?php print_assign_to_option_list( $t_candidate->handler_id, $t_candidate->project_id ) ?>
                </select>
                <?php
                        }
                ?>
        </td>

<?php
        if ( access_has_candidate_level( config_get( 'roadmap_update_threshold' ), $f_candidate_id ) ) {
?>
        <!-- Second Interviewer -->
        <td class="category">
                <?php
                        echo lang_get( 'target_version' );
                ?>
        </td>
        <td>
        <select <?php echo helper_get_tab_index() ?> name="target_version">
                        <?php print_assign_to_option_list( $t_candidate->handler_id, $t_candidate->project_id ) ?>
        </select>
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
        <td colspan="0">
                <select <?php echo helper_get_tab_index() ?> name="steps_to_reproduce">
			<option value="0" selected="selected"></option>
                        <?php print_assign_to_option_list( $t_candidate->handler_id, $t_candidate->project_id ) ?>
                </select>
        </td>
</tr>
<!-- spacer -->
<tr class="spacer">
        <td colspan="6"></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
        <!-- Experience-->
        <td class="category">
                <?php echo lang_get( 'priority' ) ?>
        </td>
        <td align="left">
                <select <?php echo helper_get_tab_index() ?> name="priority" <?=$Disabledme;?>>
                        <?php print_enum_string_option_list( 'priority', $t_candidate->priority ) ?>
                </select>
        </td>

        <!-- Final Result -->
        <td class="category">
                <?php echo lang_get( 'resolution' ) ?>
        </td>
        <td colspan="3">
                <select <?php echo helper_get_tab_index() ?> name="resolution" <?=$Disabledme;?>>
                        <?php print_enum_string_option_list( "resolution", $t_candidate->resolution ) ?>
                </select>
        </td>


</tr>

<tr <?php echo helper_alternate_class() ?>>

        <!-- Contact No -->
        <td class="category">
                <?php echo lang_get( 'build' ) ?>
        </td>
        <td>
                <input <?php echo helper_get_tab_index() ?> type="text" name="build" size="16" maxlength="32" value="<?php echo $t_candidate->build ?>" />
        </td>
        <!-- email -->
        <td class="category">
                <?php echo lang_get( 'os_version' ) ?>
        </td>
        <td>
                <?php
                        if ( config_get( 'allow_freetext_in_profile_fields' ) == OFF ) {
                ?>
                                <select name="os_build">
                                        <option value=""></option>
                                <?php
                                                print_os_build_option_list( $t_candidate->os_build );
                                ?>
                                </select>
                <?php
                        } else {
                                projax_autocomplete( 'os_build_get_with_prefix', 'os_build', array( 'value' => $t_candidate->os_build, 'size' => '32', 'maxlength' => '255', 'tabindex' => helper_get_tab_index_value() ) );
                        }
                ?>
        </td>
        <td colspan="2">&nbsp;</td>

</tr>

<tr <?php echo helper_alternate_class() ?>>
        <!-- City -->
        <td class="category">
                <?php echo lang_get( 'platform' ) ?>
        </td>
        <td>
                <?php
                        if ( config_get( 'allow_freetext_in_profile_fields' ) == OFF ) {
                ?>
                                <select name="platform" <?=$Disabledme;?> >
                                        <option value=""></option>
                                <?php
                                                print_platform_option_list( $t_candidate->platform );
                                ?>
                                </select>
                <?php
                        } else {
                                projax_autocomplete( 'platform_get_with_prefix', 'platform', array( 'value' => $t_candidate->platform, 'size' => '16', 'maxlength' => '32', 'tabindex' => helper_get_tab_index_value() ) );
                        }
                ?>
        </td>
        <!-- Address -->
        <td class="category">
                <?php echo lang_get( 'os' ) ?>
        </td>
        <td colspan="3">
                <?php
                        if ( config_get( 'allow_freetext_in_profile_fields' ) == OFF ) {
                ?>
                                <select name="os">
                                        <option value=""></option>
                                <?php
                                                print_os_option_list( $t_candidate->os );
                                ?>
                                </select>
                <?php
                        } else {
                                projax_autocomplete( 'os_get_with_prefix', 'os', array( 'value' => $t_candidate->os, 'size' => '32', 'maxlength' => '255', 'tabindex' => helper_get_tab_index_value() ) );
                        }
                ?>
        </td>
</tr>

<?php
        }
?>
<!-- Degree Title -->
<tr <?php echo helper_alternate_class() ?>>
        <td class="category">
                <?php echo lang_get( 'description' ) ?>
        </td>
        <td colspan="5">
                <textarea <?php echo helper_get_tab_index() ?> cols="80" rows="2" name="description"><?php echo $t_candidate->description ?></textarea>
        </td>
</tr>
<!-- Custom Fields -->
<?php
        $t_custom_fields_found = false;
        $t_related_custom_field_ids = custom_field_get_linked_ids( $t_candidate->project_id );
        foreach( $t_related_custom_field_ids as $t_id ) {
                $t_def = custom_field_get_definition( $t_id );
                if( ( $t_def['display_update'] || $t_def['require_update']) && custom_field_has_write_access( $t_id, $f_candidate_id ) ) {
                        $t_custom_fields_found = true;
?>
<tr <?php echo helper_alternate_class() ?>>
        <td class="category">
                <?php if($t_def['require_update']) {?><span class="required">*</span><?php } ?><?php echo string_display( lang_get_defaulted( $t_def['name'] ) ) ?>
        </td>
        <td colspan="5">
                <?php
                        print_custom_field_input( $t_def, $f_candidate_id );
                ?>
        </td>
</tr>
<?php
                }
        } # foreach( $t_related_custom_field_ids as $t_id )
?>

<?php if ( $t_custom_fields_found ) { ?>
<!-- spacer -->
<tr class="spacer">
        <td colspan="6"></td>
</tr>
<?php } # custom fields found ?>

<!-- Bugnote Text Box -->
<tr <?php echo helper_alternate_class() ?>>
        <td class="category">
                <?php echo lang_get( 'add_candidatenote_title' ) ?>
        </td>
        <td colspan="5">
                <textarea <?php echo helper_get_tab_index() ?> name="candidatenote_text" cols="80" rows="10"></textarea>
        </td>
</tr>

<!-- Bugnote Private Checkbox (if permitted) -->
<?php if ( access_has_candidate_level( config_get( 'private_candidatenote_threshold' ), $f_candidate_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
        <td class="category">
                <?php echo lang_get( 'private' ) ?>
        </td>
        <td colspan="5">
<?php
                $t_default_candidatenote_view_status = config_get( 'default_candidatenote_view_status' );
                if ( access_has_candidate_level( config_get( 'set_view_status_threshold' ), $f_candidate_id ) ) {
?>
                        <input <?php echo helper_get_tab_index() ?> type="checkbox" name="private" <?php check_checked( config_get( 'default_candidatenote_view_status' ), VS_PRIVATE ); ?> />
<?php
                        echo lang_get( 'private' );
                } else {
                        echo get_enum_element( 'view_state', $t_default_candidatenote_view_status );
                }
?>
        </td>
</tr>
<?php } ?>

<!-- Time Tracking (if permitted) -->
<?php if ( config_get('time_tracking_enabled') ) { ?>
<?php if ( access_has_candidate_level( config_get( 'time_tracking_edit_threshold' ), $f_candidate_id ) ) { ?>
<tr <?php echo helper_alternate_class() ?>>
        <td class="category">
                <?php echo lang_get( 'time_tracking' ) ?>
        </td>
        <td colspan="5">
                <input type="text" name="time_tracking" size="5" value="0:00" />
        </td>
</tr>
<?php } ?>
<?php } ?>

<!-- Submit Button -->
<tr>
        <td class="right" colspan="6">
                <input <?php echo helper_get_tab_index() ?> type="submit" class="button" value="<?php echo lang_get( 'update_information_button' ) ?>" />
        </td>
</tr>

</table>
</form>
<?php
        include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'candidatenote_view_inc.php' );
        html_page_bottom1( __FILE__ );

        last_visited_issue( $f_candidate_id );
?>
