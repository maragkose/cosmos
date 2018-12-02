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
	# $Id: candidate_actiongroup_ext.php,v 1.1.2.1 2007-10-13 22:32:32 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'candidate_api.php' );
	require_once( $t_core_path . 'candidate_group_action_api.php' );

	# helper_ensure_post();

	auth_ensure_user_authenticated();

	helper_begin_long_process();

	$f_action = gpc_get_string( 'action' );
	$f_candidate_arr	= gpc_get_int_array( 'candidate_arr', array() );

	$t_action_include_file = 'candidate_actiongroup_' . $f_action . '_inc.php';

	require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $t_action_include_file );

	# group candidates by project
	$t_projects_candidates = array();
	foreach( $f_candidate_arr as $t_candidate_id ) {
		candidate_ensure_exists( $t_candidate_id );
		$t_candidate = candidate_get( $t_candidate_id, true );
		
		if ( isset( $t_projects_candidates[$t_candidate->project_id] ) ) {
		  $t_projects_candidates[$t_candidate->project_id][] = $t_candidate_id;
        } else {
		  $t_projects_candidates[$t_candidate->project_id] = array( $t_candidate_id );
        }
    }
  
    $t_failed_ids = array();
    
    # validate all candidates before we start the processing, we may fail the whole action
    # group, or some of the candidates.
    foreach( $t_projects_candidates as $t_project_id => $t_candidate_ids ) {
        if ( $t_candidate->project_id != helper_get_current_project() ) {
            # in case the current project is not the same project of the candidate we are viewing...
            # ... override the current project. This to avoid problems with categories and handlers lists etc.
            $g_project_override = $t_candidate->project_id;
            # @@@ (thraxisp) the next line goes away if the cache was smarter and used project
            config_flush_cache(); # flush the config cache so that configs are refetched
        }

        foreach( $t_candidate_ids as $t_candidate_id ) {
            $t_result = candidate_group_action_validate( $f_action, $t_candidate_id );
            if ( $t_result !== true ) {
                foreach( $t_result as $t_key => $t_value ) {
                    $t_failed_ids[$t_key] = $t_value;
                }
            }
        }
    }

    # process candidates that are not already failed by validation.
    foreach( $t_projects_candidates as $t_project_id => $t_candidate_ids ) {
		if ( $t_candidate->project_id != helper_get_current_project() ) {
			# in case the current project is not the same project of the candidate we are viewing...
			# ... override the current project. This to avoid problems with categories and handlers lists etc.
			$g_project_override = $t_candidate->project_id;
			# @@@ (thraxisp) the next line goes away if the cache was smarter and used project
			config_flush_cache(); # flush the config cache so that configs are refetched
		}

        foreach( $t_candidate_ids as $t_candidate_id ) {
            # do not process this candidate if validation failed for it.
            if ( !isset( $t_failed_ids[$t_candidate_id] ) ) {
                $t_result = candidate_group_action_process( $f_action, $t_candidate_id );
                if ( $t_result !== true ) {
                    $t_failed_ids[] = $t_result;
                }
            }
        }
    }

	form_security_purge( $t_form_name );

	$t_redirect_url = 'view_all_candidate_page.php';

	if ( count( $t_failed_ids ) > 0 ) {
		html_page_top1();
		html_page_top2();

		echo '<div align="center">';
		foreach( $t_failed_ids as $t_id => $t_reason ) {
			printf("<p>%s: %s</p>\n", string_get_candidate_view_link( $t_id ), $t_reason );
		}

		print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
		echo '</div>';

		html_page_bottom1( __FILE__ );
	} else {
		print_header_redirect( $t_redirect_url );
	}
?>
