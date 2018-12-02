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
	# $Id: candidate_set_sponsorship.php,v 1.5.14.1 2007-10-13 22:32:53 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'sponsorship_api.php' );

	# helper_ensure_post();

	if ( config_get( 'enable_sponsorship' ) == OFF ) {
		trigger_error( ERROR_SPONSORSHIP_NOT_ENABLED, ERROR );
	}

	# anonymous users are not allowed to sponsor issues
	if ( current_user_is_anonymous() ) {
		access_denied();
	}

	$f_candidate_id	= gpc_get_int( 'candidate_id' );
	$f_amount	= gpc_get_int( 'amount' );

	$t_candidate = candidate_get( $f_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

	access_ensure_candidate_level( config_get( 'sponsor_threshold' ), $f_candidate_id );

	helper_ensure_confirmed( 
		sprintf( lang_get( 'confirm_sponsorship' ), $f_candidate_id, sponsorship_format_amount( $f_amount ) ),
		lang_get( 'sponsor_issue' ) );
			
	if ( $f_amount == 0 ) {
		# if amount == 0, delete sponsorship by current user (if any)
		$t_sponsorship_id = sponsorship_get_id( $f_candidate_id );
		if ( $t_sponsorship_id !== false ) {
			sponsorship_delete( $t_sponsorship_id );
		}
	} else {
		# add sponsorship
		$t_user = auth_get_current_user_id();
		if ( is_blank( user_get_email( $t_user ) ) ) {
			trigger_error( ERROR_SPONSORSHIP_SPONSOR_NO_EMAIL, ERROR );
		} else {
			$sponsorship = new SponsorshipData;
			$sponsorship->candidate_id = $f_candidate_id;
			$sponsorship->user_id = $t_user;
			$sponsorship->amount = $f_amount;

			sponsorship_set( $sponsorship );
		}
	}

	print_header_redirect_view( $f_candidate_id );
?>
