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
	# $Id: summary_ofc_page.php,v 1.1.1.1 2009/01/08 07:30:19 chirag Exp $
	# --------------------------------------------------------
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'summary_api.php' );
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

Global $ChartData;
$ChartData=array();
$ChartData['label']=array();
$ChartData['open']=array();
$ChartData['resolved']=array();
$ChartData['closed']=array();
$ChartData['total']=array();


?>
<?php html_page_top1( lang_get( 'summary_link' ) ) ?>
<?php html_page_top2() ?>

<br />
<?php print_summary_menu( 'summary_ofc_page.php' ) ?>
<script type="text/javascript" src="javascript/jquery.clickmenu.js"></script>

<script language="JavaScript">
var ReportType='summary';
var chart='bar';
function ChangeReportyType(Report){

	ReportType=Report;
	reload(chart);
}

function reload(chartype)
{

/*
*/
  tmp = findSWF("chart");
  //
  // reload the data:
  //
  x = tmp.reload();
  //
  	if(chartype==""){
	chart='bar';
	}else{
	chart=chartype;
	}
  	if(ReportType==""){
	ReportType='summary';
	}
	ReloadString='summary_page_ofc.php?ChartType='+chart+'&ReportType='+ReportType;
  // to load from a specific URL:
  // you may need to 'escape' (URL escape, i.e. percent escape) your URL if it has & in it
  //
		x = tmp.reload(ReloadString);
			x = tmp.reload(ReloadString);
	  	return false;
  //
  // do NOT show the 'loading...' message:
  //x = tmp.reload("gallery-data-32.php?beer=1", false);
}

function findSWF(movieName) {
  if (navigator.appName.indexOf("Microsoft")!= -1) {
    return window["ie_" + movieName];
  } else {
    return document[movieName];
  }
}
</script>

<table width="600" border="0" cellspacing="1" class="width100">
<TR>
<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=summary', false );
		?>
</TD>

<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=by_category', false );
		?>
</TD>

<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=most_active', false );
		?>
</TD>
</TR>
<TR>

<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=severity', false );
		?>
</TD>
<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=by_resolution', false );
		?>
</TD>


<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=by_priority', false );
		?>
</TD>
</TR>
<TR>
<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=reporter_stats', false );
		?>
</TD>
<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=longest_open', false );
		?>
</TD>

<TD style="height:500px">
  	<?
		include_once 'ofc-library/open_flash_chart_object.php';
		open_flash_chart_object('400', '600', 'summary_page_ofc.php?ChartType=3dBar&ReportType=by_developer', false );
		?>
</TD>
</TR>
</table>


<?php html_page_bottom1( __FILE__ ) ?>

