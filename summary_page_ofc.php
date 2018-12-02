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
	# $Id: summary_page_ofc.php,v 1.1.1.1 2009/01/08 07:30:19 chirag Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'summary_api_ofc.php' );
?>
<?php
	access_ensure_project_level( config_get( 'view_summary_threshold' ) );

	$f_project_id = gpc_get_int( 'project_id', helper_get_current_project() );

	# Override the current page to make sure we get the appropriate project-specific configuration
	$g_project_override = $f_project_id;

	$t_user_id = auth_get_current_user_id();

	# @@@ giallu: this block of code is duplicated from helper_project_specific_where
	# the only diff is the commented line below: can we do better than this ?
	if ( ALL_PROJECTS == $f_project_id ) {
		$t_topprojects = $t_project_ids = user_get_accessible_projects( $t_user_id );
		foreach ( $t_topprojects as $t_project ) {
			$t_project_ids = array_merge( $t_project_ids, user_get_all_accessible_subprojects( $t_user_id, $t_project ) );
		}

		$t_project_ids = array_unique( $t_project_ids );
	} else {
		# access_ensure_project_level( VIEWER, $p_project_id );
		$t_project_ids = user_get_all_accessible_subprojects( $t_user_id, $f_project_id );
		array_unshift( $t_project_ids, $f_project_id );
	}

	$t_project_ids = array_map( 'db_prepare_int', $t_project_ids );

	if ( 0 == count( $t_project_ids ) ) {
		$specific_where = ' 1 <> 1';
	} elseif ( 1 == count( $t_project_ids ) ) {
		$specific_where = ' project_id=' . $t_project_ids[0];
	} else {
		$specific_where = ' project_id IN (' . join( ',', $t_project_ids ) . ')';
	}
	# end @@@ block

	$t_candidate_table = config_get( 'cosmos_candidate_table' );
	$t_history_table = config_get( 'cosmos_candidate_history_table' );

	$t_resolved = config_get( 'candidate_resolved_status_threshold' );
	# the issue may have passed through the status we consider resolved
	#  (e.g., candidate is CLOSED, not RESOLVED). The linkage to the history field
	#  will look up the most recent 'resolved' status change and return it as well
	$query = "SELECT b.id, b.date_submitted, b.last_updated, MAX(h.date_modified) as hist_update, b.status 
        FROM $t_candidate_table b LEFT JOIN $t_history_table h 
            ON b.id = h.candidate_id  AND h.type=0 AND h.field_name='status' AND h.new_value='$t_resolved'  
            WHERE b.status >='$t_resolved' AND $specific_where
            GROUP BY b.id, b.status, b.date_submitted, b.last_updated 
            ORDER BY b.id ASC";
	$result = db_query( $query );
	$candidate_count = db_num_rows( $result );

	$t_candidate_id       = 0;
	$t_largest_diff = 0;
	$t_total_time   = 0;
	for ($i=0;$i<$candidate_count;$i++) {
		$row = db_fetch_array( $result );
		$t_date_submitted = db_unixtimestamp( $row['date_submitted'] );		
		$t_id = $row['id'];
		$t_status = $row['status'];
		if ( $row['hist_update'] !== NULL ) {
            $t_last_updated   = db_unixtimestamp( $row['hist_update'] );
        } else {
        	$t_last_updated   = db_unixtimestamp( $row['last_updated'] );
        }
		  
		if ($t_last_updated < $t_date_submitted) {
			$t_last_updated   = 0;
			$t_date_submitted = 0;
		}

		$t_diff = $t_last_updated - $t_date_submitted;
		$t_total_time = $t_total_time + $t_diff;
		if ( $t_diff > $t_largest_diff ) {
			$t_largest_diff = $t_diff;
			$t_candidate_id = $row['id'];
		}
	}
	if ( $candidate_count < 1 ) {
		$candidate_count = 1;
	}
	$t_average_time 	= $t_total_time / $candidate_count;

	$t_largest_diff 	= number_format( $t_largest_diff / 86400, 2 );
	$t_total_time		= number_format( $t_total_time / 86400, 2 );
	$t_average_time 	= number_format( $t_average_time / 86400, 2 );

	$t_orct_arr = preg_split( '/[\)\/\(]/', lang_get( 'orct' ), -1, PREG_SPLIT_NO_EMPTY );

	$t_orcttab = "";
	foreach ( $t_orct_arr as $t_orct_s ) {
		$t_orcttab .= '<td class="right">';
		$t_orcttab .= $t_orct_s;
		$t_orcttab .= '</td>';
	}
	
//		Global $ChartData;

function getNext10($Number){
	if($Number<10){
	return 10;
	}else{
	$last_digit = $Number % 10;  
		if($last_digit>0){
		$Number=$Number+(10-$last_digit);
		return $Number;
		}else{
		//should be 10
		return $Number;
		}
	}
	
}

Global $ChartData,$PieChartData;
$PieChartData=array();
$PieChartData['labels']=array();
$PieChartData['links']=array();
$PieChartData['data']=array();
$ChartData=array();
$ChartData['label']=array();
$ChartData['open']=array();
$ChartData['resolved']=array();
$ChartData['closed']=array();
$ChartData['total']=array();
$ChratColorsArray=array('#cc3300','#ff8000','#006666','#3300cc','#996666','#ff6600','#cccc66','#66ccff','#ff6699','#6633cc','#cc0033','#cc6600','#9999ff','#ff3300','#cccc00');

?>
<?
include_once( 'ofc-library/open-flash-chart.php' );

function DrawPieChartsActive($ReportTitle="",$Data,$link,$labels){
Global $g,$ChratColorsArray;
//$PieChartData['Data'],$PieChartData['link'],$PieChartData['labels']
// generate some random data

$g->bg = '#E4F0DB';
$g->pie(60,'#E4F0DB','{font-size: 12px; color: #404040;}',false,1);
//
// pass in two arrays, one of data, the other data labels
//
$g->pie_values( $Data, $labels, $link );
//
// Colours for each slice, in this case some of the colours
// will be re-used (3 colurs for 5 slices means the last two
// slices will have colours colour[0] and colour[1]):
//
$g->pie_slice_colours($ChratColorsArray);

$g->set_tool_tip( 'Label: #x_label#<br>'.lang_get( 'score').': #val#' );

$g->title( $ReportTitle, '{font-size:18px; color: #d01f3c}' );
$g->bg_colour = '#ffffff';
return $g->render();

}

function DrawPieChartsLogetsOpen($ReportTitle="",$Data,$link,$labels){
Global $g,$ChratColorsArray;
//$PieChartData['Data'],$PieChartData['link'],$PieChartData['labels']
// generate some random data

$g->bg = '#E4F0DB';
$g->pie(60,'#E4F0DB','{font-size: 12px; color: #404040;}',false,1);
//
// pass in two arrays, one of data, the other data labels
//
$g->pie_values( $Data, $labels, $link );
//
// Colours for each slice, in this case some of the colours
// will be re-used (3 colurs for 5 slices means the last two
// slices will have colours colour[0] and colour[1]):
//
$g->pie_slice_colours($ChratColorsArray);
$g->set_tool_tip( 'Label: #x_label#<br>'.lang_get( 'days').': #val#' );

$g->title( lang_get( 'longest_open'), '{font-size:18px; color: #d01f3c}' );
//
$g->bg_colour = '#ffffff';
return $g->render();

}

function DrawBarCharts($ReportType,$Charttype="bar"){
Global $t_orct_arr,$ChartData,$PieChartData,$ChratColorsArray,$g;

//Let us populate data based on report type
//let us create graph ob
	$g = new graph();
	switch($ReportType){
	case "summary":
	summary_print_by_project();
	$g->title(lang_get( 'summary_title'), '{font-size: 20px;}' );
	break;
	case "by_status":
	$g->title(lang_get( 'by_status'), '{font-size: 20px;}' );
	summary_print_by_enum( config_get( 'status_enum_string' ), 'status' );
	break;
	case "severity":
	$g->title(lang_get( 'by_severity'), '{font-size: 20px;}' );
	summary_print_by_enum( config_get( 'severity_enum_string' ), 'severity' ) ;
	break;
	case "by_resolution":
	$g->title(lang_get( 'by_resolution'), '{font-size: 20px;}' );
	summary_print_by_enum( config_get( 'resolution_enum_string' ), 'resolution' );
	break;
	case "by_priority":
	$g->title(lang_get( 'by_priority'), '{font-size: 20px;}' );
	summary_print_by_enum( config_get( 'priority_enum_string' ), 'priority' ) ;
	break;
	case "by_category":
	$g->title(lang_get('by_category'), '{font-size: 20px;}' );
	summary_print_by_category();
	break;
	break;
	case "by_developer":
	$g->title(lang_get('developer_stats'), '{font-size: 20px;}' );
	summary_print_by_developer();
	break;
	case "reporter_stats":
	$g->title(lang_get('reporter_stats'), '{font-size: 20px;}' );
	summary_print_by_reporter();
	break;

	case "most_active":
	summary_print_by_activity();
	echo DrawPieChartsActive(lang_get( 'most_active' ),$PieChartData['data'],$PieChartData['links'],$PieChartData['labels']);
	die();
	exit;
	break;
	case "longest_open":
	summary_print_by_age();
	echo DrawPieChartsLogetsOpen(lang_get( 'most_active' ),$PieChartData['data'],$PieChartData['links'],$PieChartData['labels']);
	die();
	exit;
	break;

	}		

	//Find out bar chart style
			
			//Create bars based on data we have
			$i=1;
			$bar_title="bar_";
					foreach ( $t_orct_arr as $t_orct_s ) {
					$bar_obj=$bar_title.$i;
					switch($Charttype){
					case "3dBar":
					$$bar_obj=new bar_3d (50, $ChratColorsArray[$i]);
					$g->set_x_axis_3d(2);
					break;
					case "bar":
					$$bar_obj=new bar (50, $ChratColorsArray[$i]);
					default:
					case "Glassbar":
					$$bar_obj=new bar_glass (50, $ChratColorsArray[$i], $ChratColorsArray[$i+1]);
					break;
					case "bar_fade":
					$$bar_obj=new bar_fade (50, $ChratColorsArray[$i]);
					break;
					case "bar_sketch":
					$$bar_obj=new bar_sketch (50,6,$ChratColorsArray[$i],'#000000');
					break;
					default:
					//default normal bar
					$$bar_obj=new bar (50, $ChratColorsArray[$i]);
					break;
					}
			$$bar_obj->key( $t_orct_s, 10 );
			$i++;
			}

//There is better way to do this, i know but in hurry :)
//Max
$MaxValue=0;
foreach($ChartData['open'] as  $OpenData){
$bar_1->data[] = $OpenData;
	if($MaxValue<$OpenData){
	$MaxValue=$OpenData;
	}
}
foreach($ChartData['resolved'] as  $OpenData){
//$OpenData=25;
$bar_2->data[] = $OpenData;
	if($MaxValue<$OpenData){
	$MaxValue=$OpenData;
	}
}
foreach($ChartData['closed'] as  $OpenData){
$bar_3->data[] = $OpenData;
	if($MaxValue<$OpenData){
	$MaxValue=$OpenData;
	}
}
foreach($ChartData['total'] as  $OpenData){
$bar_4->data[] = $OpenData;
	if($MaxValue<$OpenData){
	$MaxValue=$OpenData;
	}
}
//Suggest better way for above


$MaxValue=getNext10($MaxValue);
// add the 3 bar charts to it:
$g->data_sets[] = $bar_1;
$g->data_sets[] = $bar_2;
$g->data_sets[] = $bar_3;
$g->data_sets[] = $bar_4;
//
$g->set_x_labels( $ChartData['label']);
// set the X axis to show every 2nd label:
$g->set_x_label_style( 10, '#9933CC', 1, 1 );
// and tick every second value:
$g->set_x_axis_steps( 2 );
//

$g->set_y_max($MaxValue);
//$g->set_y_max('10');
$g->y_label_steps(10);
$g->bg_colour = '#ffffff';
$g->set_y_legend(lang_get( 'legend_total'), 12, '0x736AFF' );
return $g->render();


}
$ReportType=$_REQUEST['ReportType'];
$ChartType=$_REQUEST['ChartType'];
echo  DrawBarCharts($ReportType,$ChartType);
//


///Decandidate code to find out request 

$filename = 'requestlog.txt';
$somecontent =$_SERVER['REQUEST_URI']."
";
$somecontent .= $_SERVER['QUERY_STRING']."
";

// Let's make sure the file exists and is writable first.
if (is_writable($filename)) {
    if (!$handle = fopen($filename, 'a')) {
    }
    if (fwrite($handle, $somecontent) === FALSE) {
    }
    fclose($handle);

} else {
//    echo "The file $filename is not writable";
}

?>

