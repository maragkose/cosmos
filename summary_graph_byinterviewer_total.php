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
	# $Id: summary_graph_byreporter.php,v 1.14.22.1 2007-10-13 22:34:34 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	class ArrayHelper {

		public function addArrays(Array &$to, Array $from) {
			foreach($from as $key => $value) {
				$to[$key] += $value;
		    	}
		}

	    	public function copyKeys(Array $from) {
			$to = array();
			foreach($from as $key => $value) {
				if(!array_key_exists($key, $to)){
					$to[$key] = 0;
			}
		}
		return $to;
		}

	}
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'graph_api.php' );

	access_ensure_project_level( config_get( 'view_summary_threshold' ) );

	$f_width = gpc_get_int( 'width', 300 );
	$t_ar = config_get( 'graph_bar_aspect' );

	$t_metrics1 = create_interviewer_summary();
	$t_metrics2 = create_second_interviewer_summary();
        #var_dump($t_metrics1); 	
        #var_dump($t_metrics2); 	
  	$t_metrics = array();
  	$sum = array();
	$t_metrics = array_merge($t_metrics1, $t_metrics2);
	$sum = ArrayHelper::copyKeys($t_metrics);
	ArrayHelper::addArrays($sum, $t_metrics1);
	ArrayHelper::addArrays($sum, $t_metrics2);
	#var_dump($sum); die("333");
	graph_bar( $sum, lang_get( 'by_total_interviews' ), $f_width, $f_width * $t_ar );
?>
