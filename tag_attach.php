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
	# $Id: tag_attach.php,v 1.2.2.2 2007-10-18 15:33:22 nuclear_eclipse Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'tag_api.php' );

	form_security_validate( 'tag_attach' );

	$f_candidate_id = gpc_get_int( 'candidate_id' );
	$f_tag_select = gpc_get_int( 'tag_select' );
	$t_user_id = auth_get_current_user_id();

	access_ensure_candidate_level( config_get( 'tag_attach_threshold' ), $f_candidate_id, $t_user_id );

	$t_tags = tag_parse_string( gpc_get_string( 'tag_string' ) );
	$t_can_create = access_has_global_level( config_get( 'tag_create_threshold' ) );
	
	$t_tags_create = array();
	$t_tags_attach = array();
	$t_tags_failed = array();

	foreach ( $t_tags as $t_tag_row ) {
		if ( -1 == $t_tag_row['id'] ) {
			if ( $t_can_create ) {
				$t_tags_create[] = $t_tag_row;
			} else {
				$t_tags_failed[] = $t_tag_row;
			}
		} elseif ( -2 == $t_tag_row['id'] ) {
			$t_tags_failed[] = $t_tag_row;
		} else {
			$t_tags_attach[] = $t_tag_row;
		}
	}

	if ( 0 < $f_tag_select && tag_exists( $f_tag_select ) ) {
		$t_tags_attach[] = tag_get( $f_tag_select );
	}

	if ( count( $t_tags_failed ) > 0 ) {
		html_page_top1( lang_get( 'tag_attach_long' ).' '.candidate_format_summary( $f_candidate_id, SUMMARY_CAPTION ) );
		html_page_top2();
?>
<br/>
<table class="width75" align="center">
	<tr class="row-category">
	<td colspan="2"><?php echo lang_get( 'tag_attach_failed' ) ?></td>
	</tr>
	<tr class="spacer"><td colspan="2"></td></tr>
<?php		
		$t_tag_string = "";
		foreach( $t_tags_attach as $t_tag_row ) {
			if ( "" != $t_tag_string ) {
				$t_tag_string .= config_get( 'tag_separator' );
			}
			$t_tag_string .= $t_tag_row['name'];
		}

		foreach( $t_tags_failed as $t_tag_row ) {
			echo '<tr ',helper_alternate_class(),'>';
			if ( -1 == $t_tag_row['id'] ) {
				echo '<td class="category">',lang_get( 'tag_create_denied' ),'</td>';
			} elseif ( -2 == $t_tag_row['id'] ) {
				echo '<td class="category">',lang_get( 'tag_invalid_name' ),'</td>';
			}
			echo '<td>',string_html_specialchars( $t_tag_row['name'] ),'</td></tr>';
			
			if ( "" != $t_tag_string ) {
				$t_tag_string .= config_get( 'tag_separator' );
			}
			$t_tag_string .= $t_tag_row['name'];
		}
?>
	<tr class="spacer"><td colspan="2"></td></tr>
	<tr <?php echo helper_alternate_class() ?>>
	<td class="category"><?php echo lang_get( 'tag_attach_long' ) ?></td>
	<td>
<?php
		print_tag_input( $f_candidate_id, $t_tag_string );
?>	
	</td>
	</tr>
</table>
<?php
		html_page_bottom1(__FILE__);
	} else {
		foreach( $t_tags_create as $t_tag_row ) {
			$t_tag_row['id'] = tag_create( $t_tag_row['name'], $t_user_id );
			$t_tags_attach[] = $t_tag_row;
		}

		foreach( $t_tags_attach as $t_tag_row ) {
			if ( ! tag_candidate_is_attached( $t_tag_row['id'], $f_candidate_id ) ) {
				tag_candidate_attach( $t_tag_row['id'], $f_candidate_id, $t_user_id );
			}
		}

		form_security_purge( 'tag_attach' );

		print_successful_redirect_to_candidate( $f_candidate_id );
	}
