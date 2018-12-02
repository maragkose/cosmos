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
	# $Id: candidate_file_upload_inc.php,v 1.39.2.1 2007-10-13 22:32:39 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	# This include file prints out the candidate file upload form
	# It POSTs to candidate_file_add.php

	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'file_api.php' );

	# check if we can allow the upload... bail out if we can't
	if ( ! file_allow_candidate_upload( $f_candidate_id ) ) {
		return false;
	}

	$t_max_file_size = (int)min( ini_get_number( 'upload_max_filesize' ), ini_get_number( 'post_max_size' ), config_get( 'max_file_size' ) );
?>
<br />

<?php
	collapse_open( 'upload_form' );
?>
<form method="post" enctype="multipart/form-data" action="candidate_file_add.php">
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
<?php
		collapse_icon( 'upload_form' );
		echo lang_get( 'upload_file' ) ?>
	</td>
</tr>
<tr class="row-1">
	<td class="category" width="15%">
		<?php echo lang_get( 'select_file' ) ?><br />
		<?php echo '<span class="small">(' . lang_get( 'max_file_size' ) . ': ' . number_format( $t_max_file_size/1000 ) . 'k)</span>'?>
	</td>
	<td width="85%">
		<input type="hidden" name="candidate_id" value="<?php echo $f_candidate_id ?>" />
		<input type="hidden" name="max_file_size" value="<?php echo $t_max_file_size ?>" />
		<input name="file" type="file" size="40" />
		<input type="submit" class="button" value="<?php echo lang_get( 'upload_file_button' ) ?>" />
	</td>
</tr>
</table>
</form>
<?php
	collapse_closed( 'upload_form' );
?>
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
		<?php
			collapse_icon( 'upload_form' );
			echo lang_get( 'upload_file' ) ?>
	</td>
</tr>
</table>

<?php
	collapse_end( 'upload_form' );
?>
