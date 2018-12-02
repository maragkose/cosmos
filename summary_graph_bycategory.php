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
	# $Id: summary_graph_bycategory.php,v 1.1.1.1 2009/01/08 07:30:19 chirag Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'graph_api.php' );

	access_ensure_project_level( config_get( 'view_summary_threshold' ) );

	$f_width = gpc_get_int( 'width', 300 );
	$t_ar = config_get( 'graph_bar_aspect' );

	$t_token = token_get_value( TOKEN_GRAPH );
	if ( $t_token == null ) {
		$t_metrics = create_category_summary();
	} else {
		$t_metrics = unserialize( $t_token );
	}
	$TotalCat=count($t_metrics);
	if($TotalCat>20){
		$NewWidth=count($t_metrics)*15;
		if($NewWidth>0 and $NewWidth<=1500){
		$f_width=$NewWidth;
		}
	}
	
	graph_bar( $t_metrics, lang_get( 'by_category' ), $f_width, $f_width * $t_ar );
?>
