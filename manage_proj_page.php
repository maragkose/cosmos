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
	# $Id: manage_proj_page.php,v 1.2 2009/01/12 12:40:30 jigar Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'icon_api.php' );

	auth_reauthenticate();

	$f_sort	= gpc_get_string( 'sort', 'name' );
	$f_dir	= gpc_get_string( 'dir', 'ASC' );

	if ( 'ASC' == $f_dir ) {
		$t_direction = ASCENDING;
	} else {
		$t_direction = DESCENDING;
	}

	html_page_top1( lang_get( 'manage_projects_link' ) );
	html_page_top2();

	print_manage_menu( 'manage_proj_page.php' );

# Project Menu Form BEGIN ?>
<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="5">
		<?php echo lang_get( 'projects_title' ) ?>
		<?php
		# Check the user's global access level before allowing project creation
		if ( access_has_global_level ( config_get( 'create_project_threshold' ) ) ) {
			print_button( 'manage_proj_create_page.php', lang_get( 'create_new_project_link' ) );
		}
		?>
	</td>
</tr>
<tr class="row-category">
	<td>&nbsp;</td>
	<td width="20%">
		<?php //print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'name' ), 'name', $t_direction, $f_sort ) ?>
		<?php //print_sort_icon( $t_direction, $f_sort, 'name' ) ?>
	</td>
	<!--<td width="10%">
		<?php //print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'status' ), 'status', $t_direction, $f_sort ) ?>
		<?php //print_sort_icon( $t_direction, $f_sort, 'status' ) ?>
	</td>
	<td width="10%">
		<?php //print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'enabled' ), 'enabled', $t_direction, $f_sort ) ?>
		<?php //print_sort_icon( $t_direction, $f_sort, 'enabled' ) ?>
	</td>
	<td width="10%">
		<?php //print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'view_status' ), 'view_state', $t_direction, $f_sort ) ?>
		<?php ///print_sort_icon( $t_direction, $f_sort, 'view_state' ) ?>
	</td>
	<td width="40%">
		<?php //print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'description' ), 'description', $t_direction, $f_sort ) ?>
		<?php //print_sort_icon( $t_direction, $f_sort, 'description' ) ?>
	</td>-->
</tr>
<?php
	$t_manage_project_threshold = config_get( 'manage_project_threshold' );
	$t_projects = user_get_accessible_projects( auth_get_current_user_id(), true );
	$t_full_projects = array();
	foreach ( $t_projects as $t_project_id ) {
		$t_full_projects[] = project_get_row( $t_project_id );
	}
	$t_projects = multi_sort( $t_full_projects, $f_sort, $t_direction );
	$t_stack 	= array( $t_projects );

	while ( 0 < count( $t_stack ) ) {
		$t_projects   = array_shift( $t_stack );

		if ( 0 == count( $t_projects ) ) {
			continue;
		}

		$t_project = array_shift( $t_projects );
		$t_project_id = $t_project['id'];
		$t_level      = count( $t_stack );

		# only print row if user has project management privileges
		if (access_has_project_level( $t_manage_project_threshold, $t_project_id, auth_get_current_user_id() ) ) {

?>
<tr <?php echo helper_alternate_class() ?> id="alt_<?php echo $t_project['id'];?>">
	<td width="1%">
		<img src="images/plus.png" id="img_<?php echo $t_project['id'];?>" alt="" />
	</td>
	<td valign="top">
		<a href="manage_proj_edit_page.php?project_id=<?php echo $t_project['id'] ?>"><?php echo str_repeat( "&raquo; ", $t_level ) . string_display( $t_project['name'] ) ?></a>
	</td>
	<!--<td>
		<?php //echo get_enum_element( 'project_status', $t_project['status'] ) ?>
	</td>
	<td>
		<?php //echo trans_bool( $t_project['enabled'] ) ?>
	</td>
	<td valign="top">
		<?php //echo get_enum_element( 'project_view_state', $t_project['view_state'] ) ?>
	</td>
	<td>
		<?php
		//Updated by Chirag A to display HTML
		 //echo $t_project['description']; ?>
	</td>-->
	<tr id="tr_<?php echo $t_project['id'];?>" style="display:none">
		<td colspan="2"><span style="float:right"><a href="set_project.php?project_id=<?php echo $t_project['id'] ?>&ref=candidate_report_advanced_page.php">Apply Now</a></span><?php echo $t_project['description'];?><br /><span style="float:right"><a href="set_project.php?project_id=<?php echo $t_project['id'] ?>&ref=candidate_report_advanced_page.php">Apply Now</a></span></td>
	</tr>
</tr>
<?php
		}
		$t_subprojects = project_hierarchy_get_subprojects( $t_project_id, true );

		if ( 0 < count( $t_projects ) || 0 < count( $t_subprojects ) ) {
			array_unshift( $t_stack, $t_projects );
		}

		if ( 0 < count( $t_subprojects ) ) {
            $t_full_projects = array();
		    foreach ( $t_subprojects as $t_project_id ) {
                $t_full_projects[] = project_get_row( $t_project_id );
            }
			$t_subprojects = multi_sort( $t_full_projects, $f_sort, $t_direction );
			array_unshift( $t_stack, $t_subprojects );
		}
	}
?>
</table>
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
