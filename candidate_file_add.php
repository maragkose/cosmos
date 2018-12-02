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
	# $Id: candidate_file_add.php,v 1.49.2.1 2007-10-13 22:32:37 giallu Exp $
	# --------------------------------------------------------

	# Add file to a candidate and then view the candidate

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'file_api.php' );

	# helper_ensure_post();

	$f_candidate_id	= gpc_get_int( 'candidate_id', -1 );
	$f_file		= gpc_get_file( 'file', -1 );

	if ( $f_candidate_id == -1 && $f_file	== -1 ) {
		# _POST/_FILES does not seem to get populated if you exceed size limit so check if candidate_id is -1
		trigger_error( ERROR_FILE_TOO_BIG, ERROR );
	}
	
	if ( ! file_allow_candidate_upload( $f_candidate_id ) ) {
		access_denied();
	}

	access_ensure_candidate_level( config_get( 'upload_candidate_file_threshold' ), $f_candidate_id );

	$t_candidate = candidate_get( $f_candidate_id, true );
	if( $t_candidate->project_id != helper_get_current_project() ) {
		# in case the current project is not the same project of the candidate we are viewing...
		# ... override the current project. This to avoid problems with categories and handlers lists etc.
		$g_project_override = $t_candidate->project_id;
	}

    $f_file_error =  ( isset( $f_file['error'] ) ) ? $f_file['error'] : 0;
	file_add( $f_candidate_id, $f_file['tmp_name'], $f_file['name'], $f_file['type'], 'candidate', $f_file_error );

	# Determine which view page to redirect back to.
	$t_redirect_url = string_get_candidate_view_url( $f_candidate_id );

	html_page_top1();
	html_meta_redirect( $t_redirect_url );
	html_page_top2();
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
