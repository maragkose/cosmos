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

?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'compress_api.php' );
	require_once( $t_core_path.'filter_api.php' );
	require_once( $t_core_path.'last_visited_api.php' );
?>
<?php auth_ensure_user_authenticated() ?>
<?php
	$f_page_number		= gpc_get_int( 'page_number', 1 );

	$t_per_page = null;
	$t_candidate_count = null;
	$t_page_count = null;

	$rows = filter_get_candidate_rows( $f_page_number, $t_per_page, $t_page_count, $t_candidate_count, null, null, null, true );
	if ( $rows === false ) {
		print_header_redirect( 'view_all_set.php?type=0' );
	}

	$t_candidateslist = Array();
	$t_row_count = sizeof( $rows );
	for($i=0; $i < $t_row_count; $i++) {
		array_push($t_candidateslist, $rows[$i]["id"] );
	}

	gpc_set_cookie( config_get( 'candidate_list_cookie' ), implode( ',', $t_candidateslist ) );

	compress_enable();

	html_page_top1( lang_get( 'view_candidates_link' ) );

	if ( current_user_get_pref( 'refresh_delay' ) > 0 ) {
		html_meta_redirect( 'view_all_candidate_page.php?page_number='.$f_page_number, current_user_get_pref( 'refresh_delay' )*60 );
	}

	html_page_top2();

	print_recently_visited();

	include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'view_all_inc.php' );

	html_page_bottom1( __FILE__ );
?>
