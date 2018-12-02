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
	# $Id: login_select_proj_page.php,v 1.3 2009/01/12 12:26:04 jigar Exp $
	# --------------------------------------------------------

	# Allows the user to select a project that is visible to him

	require_once( 'core.php' );

	auth_ensure_user_authenticated();

	$f_ref = gpc_get_string( 'ref', '' );

	html_page_top1( lang_get( 'select_project_button' ) );
	html_page_top2();
	
//Being modfied by Chirag A to make this a list page 

	# List projects that the current user has access to
	function print_project_option_list_ats( $p_project_id = null, $p_include_all_projects = true, $p_filter_project_id = null, $p_trace = false ) {
		project_cache_all();
		$t_project_ids = current_user_get_accessible_projects();
		$t_project_count = count( $t_project_ids );
		for ($i=0;$i<$t_project_count;$i++) {
			$t_id = $t_project_ids[$i];
			if ( $t_id != $p_filter_project_id ) {
				echo "<TR ".helper_alternate_class()." id='alt_".$t_id."'>";
				//PRINT "<option value=\"$t_id\"";
				check_selected( $p_project_id, $t_id );
				PRINT '<td width="1%"><img src="images/plus.png" id="img_'.$t_id.'" alt="" /></td>';
				PRINT '<TD valign="top" width="90%"><a href="set_project.php?ref=candidate_report_advanced_page.php&project_id='.$t_id.'">' . string_display( project_get_field( $t_id, 'name' ) ) . '' . "</a></TD>";
			//	PRINT '<TD valign="top" width="60%">' . project_get_field( $t_id, 'description' ). '' . "</TD>";
				print_subproject_option_list_ats( $t_id, $p_project_id, $p_filter_project_id, $p_trace );
				echo "</TR>";
				echo "<TR id='tr_".$t_id."' style='display:none'><td valign='top' colspan='2'>".project_get_field( $t_id, 'description' )."</td></TR>";
			}
		}
	}
	# --------------------
	# List projects that the current user has access to
	function print_subproject_option_list_ats( $p_parent_id, $p_project_id = null, $p_filter_project_id = null, $p_trace = false, $p_parents = Array() ) {
		array_push( $p_parents, $p_parent_id );
		$t_project_ids = current_user_get_accessible_subprojects( $p_parent_id );
		$t_project_count = count( $t_project_ids );
		for ($i=0;$i<$t_project_count;$i++) {
			$t_full_id = $t_id = $t_project_ids[$i];
			if ( $t_id != $p_filter_project_id ) {
	
				if ( $p_trace ) {
				  $t_full_id = join( $p_parents, ";") . ';' . $t_id;
				}
				//PRINT "$t_full_id\"";
				check_selected( $p_project_id, $t_full_id );
				
				PRINT '<tr><TD width="40%" valign="top"><a href="set_project.php?ref=candidate_report_advanced_page.php&project_id='.$t_full_id.'">'.str_repeat( '&nbsp;', count( $p_parents ) ) . str_repeat( '&raquo;', count( $p_parents ) ) . ' ' . string_display( project_get_field( $t_id, 'name' ) ) . "</a></TD>";
				PRINT '<TD valign="top" width="60%">'. project_get_field( $t_id, 'description' ). "</TD>";

				print_subproject_option_list_ats( $t_id, $p_project_id, $p_filter_project_id, $p_trace, $p_parents );
			}
		}
	}
?>

<!-- Project Select Form BEGIN -->
<br />
<div align="center">
<form method="post" action="set_project.php">
<table class="width100" cellspacing="1" >
<tr>
<td>
	<table>
		<?php print_project_option_list_ats( ALL_PROJECTS, false, null, true ) ?>
		</table>
</td>
</tr>	
</table>
</form>
</div>
<script type="text/javascript" src="js/jquery/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $("img[@src*=images/plus.png]").click(function(){
	  	arr=this.id.split("_");
		CancelBubbleEve(this.event);
		showHide("tr_"+arr[1],this.id);
	  }
    );
	$(".row-1").click(function(){
		arr=this.id.split("_");
		showHide("tr_"+arr[1],"img_"+arr[1]);
	});
	$(".row-2").click(function(){
		arr=this.id.split("_");
		showHide("tr_"+arr[1],"img_"+arr[1]);
	});
});
function showHide(elem_id,img_id){
	if(document.getElementById(elem_id).style.display==""){
		document.getElementById(elem_id).style.display="none";
		document.getElementById(img_id).src="images/plus.png";
	}else{
		document.getElementById(elem_id).style.display="";
		document.getElementById(img_id).src="images/minus.png";
	}
}
function CancelBubbleEve(evt){
	evt = (evt) ? evt : event;
    evt.cancelBubble = true;
    return false;

}
</script>

<?php html_page_bottom1( __FILE__ ) ?>
