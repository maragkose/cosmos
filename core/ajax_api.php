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
	# $Id: ajax_api.php,v 1.1.2.1 2007-10-13 22:35:12 giallu Exp $
	# --------------------------------------------------------

	$t_core_dir = dirname( __FILE__ ).DIRECTORY_SEPARATOR;

	require_once( $t_core_dir . 'candidate_api.php' );

	### Ajax API ###

	function ajax_click_to_edit( $p_initial_string, $p_element_id_prefix, $p_query_string ) {
		$t_element_id_target = $p_element_id_prefix . '_target';
		$t_element_id_edit = $p_element_id_prefix . '_edit';
		$t_edit = lang_get( 'edit_link' );

		$t_return  = '<a id="' . $t_element_id_target . '">' . $p_initial_string . '</a> ';
		$t_return .= '<a id="' . $t_element_id_edit . '" onclick="';
		$t_return .= "AjaxLoad('$t_element_id_target', '$p_query_string', '$t_element_id_edit' )";
		$t_return .= '"><small>[' . $t_edit . ']</small></a>';
		
		return $t_return;
	}
?>