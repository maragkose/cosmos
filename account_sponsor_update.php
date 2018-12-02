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
	# $Id: account_sponsor_update.php,v 1.2.14.1 2007-10-13 22:32:22 giallu Exp $
	# --------------------------------------------------------

	# This page updates a user's sponsorships
	# If an account is protected then changes are forbidden
	# The page gets redirected back to account_page.php

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'email_api.php' );

	# helper_ensure_post();

	auth_ensure_user_authenticated();

	$f_candidate_list = gpc_get_string( 'candidatelist', '' );
	$t_candidate_list = explode( ',', $f_candidate_list );
	
	foreach ( $t_candidate_list as $t_candidate ) {
		list( $t_candidate_id, $t_sponsor_id ) = explode( ':', $t_candidate );
		$c_candidate_id = (int) $t_candidate_id;
		
		candidate_ensure_exists( $c_candidate_id ); # dies if candidate doesn't exist
		
		access_ensure_candidate_level( config_get( 'handle_sponsored_candidates_threshold' ), $c_candidate_id ); # dies if user can't handle candidate
		
		$t_candidate = candidate_get( $c_candidate_id );
		$t_sponsor = sponsorship_get( (int) $t_sponsor_id );
		
		$t_new_payment = gpc_get_int( 'sponsor_' . $c_candidate_id . '_' . $t_sponsor->id, $t_sponsor->paid );
		if ( $t_new_payment != $t_sponsor->paid ) {
			sponsorship_update_paid( $t_sponsor_id, $t_new_payment );
		}
	}
		
	$t_redirect = 'account_sponsor_page.php';
	html_page_top1();
	html_meta_redirect( $t_redirect );
	html_page_top2();

	echo '<br /><div align="center">';

	echo lang_get( 'payment_updated' ) . '<br />';

	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( $t_redirect, lang_get( 'proceed' ) );
	echo '</div>';
	html_page_bottom1( __FILE__ );
?>
