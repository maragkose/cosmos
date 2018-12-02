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
	# $Id: list_public_page.php,v 1.2 2009/01/12 12:24:25 jigar Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'icon_api.php' );


	$f_sort	= gpc_get_string( 'sort', 'name' );
	$f_dir	= gpc_get_string( 'dir', 'ASC' );

	if ( 'ASC' == $f_dir ) {
		$t_direction = ASCENDING;
	} else {
		$t_direction = DESCENDING;
	}

	html_page_top1( lang_get( 'manage_projects_link' ) );
	
	html_page_top2();



# Project Menu Form BEGIN ?>
<br />
<script type="text/javascript" src="js/jquery/jquery.js"></script>
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="5">
		<?php echo lang_get( 'projects_title' ) ?>
	
	</td>
</tr>
<tr class="row-category">
	<td>&nbsp;</td>
	<td width="20%" style="text-align:left">
		<?php print_manage_project_sort_link( 'list_public_page.php', lang_get( 'name' ), 'name', $t_direction, $f_sort ) ?>
		<?php print_sort_icon( $t_direction, $f_sort, 'name' ) ?>
	</td>
	<!--<td width="10%">
		<?php//print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'status' ), 'status', $t_direction, $f_sort ) ?>
		<?php//print_sort_icon( $t_direction, $f_sort, 'status' ) ?>
	</td>-->
	<!--<td width="10%">
		<?php //print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'enabled' ), 'enabled', $t_direction, $f_sort ) ?>
		<?php //print_sort_icon( $t_direction, $f_sort, 'enabled' ) ?>
	</td>-->
	<!--<td width="10%">
		<?php //print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'view_status' ), 'view_state', $t_direction, $f_sort ) ?>
		<?php //print_sort_icon( $t_direction, $f_sort, 'view_state' ) ?>
	</td>-->
	<!--<td width="40%">
		<?php// print_manage_project_sort_link( 'manage_proj_page.php', lang_get( 'description' ), 'description', $t_direction, $f_sort ) ?>
		<?php// print_sort_icon( $t_direction, $f_sort, 'description' ) ?>
	</td>-->
</tr>
<?php


//*****************
	function t_project_hierarchy_cache( $p_show_disabled = false ) {
		global $g_cache_project_hierarchy;

		$t_project_table			= config_get( 'cosmos_project_table' );
		$t_project_hierarchy_table	= config_get( 'cosmos_project_hierarchy_table' );
		$t_enabled_clause = $p_show_disabled ? '1=1' : 'p.enabled = 1';

		$query = "SELECT DISTINCT p.id, ph.parent_id, p.name
				  FROM $t_project_table p
				  LEFT JOIN $t_project_hierarchy_table ph
				    ON ph.child_id = p.id
				  WHERE $t_enabled_clause and p.view_state<=10 and p.status<=10
				  ORDER BY p.name";

		$result = db_query( $query );
		$row_count = db_num_rows( $result );

		$g_cache_project_hierarchy = array();

		for ( $i=0 ; $i < $row_count ; $i++ ){
			$row = db_fetch_array( $result );

			if ( null === $row['parent_id'] ) {
				$row['parent_id'] = ALL_PROJECTS;
			}

			if ( isset( $g_cache_project_hierarchy[ $row['parent_id'] ] ) ) {
				$g_cache_project_hierarchy[ $row['parent_id'] ][] = $row['id'];
			} else {
				$g_cache_project_hierarchy[ $row['parent_id'] ] = array( $row['id'] );
			}
		}
	}
function t_project_hierarchy_get_subprojects( $p_project_id, $p_show_disabled = false ) {
		global $g_cache_project_hierarchy;

		if ( ( null === $g_cache_project_hierarchy ) || ( $p_show_disabled ) ) {
			t_project_hierarchy_cache( $p_show_disabled );
		}

		if ( isset( $g_cache_project_hierarchy[ $p_project_id ] ) ) {
			return $g_cache_project_hierarchy[ $p_project_id ];
		} else {
			return array();
		}
	}

	# retun an array of project IDs to which the user has access
	function t_user_get_accessible_projects( $p_show_disabled = false ) {
		global $g_user_accessible_projects_cache;
		
			$t_project_table			= config_get( 'cosmos_project_table' );
			$t_project_user_list_table	= config_get( 'cosmos_project_user_list_table' );
			$t_project_hierarchy_table	= config_get( 'cosmos_project_hierarchy_table' );

			$t_public	= VS_PUBLIC;
			$t_private	= VS_PRIVATE;
			$t_enabled_clause = $p_show_disabled ? '' : 'p.enabled = 1 AND';

			
			$query = "SELECT p.id, p.name, ph.parent_id,p.view_state
					  FROM $t_project_table p
					  LEFT JOIN $t_project_hierarchy_table ph
					    ON ph.child_id = p.id WHERE p.view_state<=10 and p.status<=10
					  ORDER BY p.name";
	
		
			$result = db_query( $query );
			$row_count = db_num_rows( $result );

			$t_projects = array();

			for ( $i=0 ; $i < $row_count ; $i++ ) {
				$row = db_fetch_array( $result );

				$t_projects[ $row['id'] ] = ( $row['parent_id'] === NULL ) ? 0 : $row['parent_id'];
			}

			# prune out children where the parents are already listed. Make the list
			#  first, then prune to avoid pruning a parent before the child is found.
			$t_prune = array();
			foreach ( $t_projects as $t_id => $t_parent ) {
				if ( ( $t_parent !== 0 ) && isset( $t_projects[$t_parent] ) ) {
					$t_prune[] = $t_id;
				}
			}
			foreach ( $t_prune as $t_id ) {
				unset( $t_projects[$t_id] );
			}
			
			$t_projects = array_keys( $t_projects );
		



		return $t_projects;
	}


# List projects that the current user has access to
	function t_print_project_option_list( $p_project_id = null, $p_include_all_projects = true, $p_filter_project_id = null, $p_trace = false ) {
		project_cache_all();
		$t_project_ids = t_user_get_accessible_projects();
		if ( $p_include_all_projects ) {
			PRINT '<option value="' . ALL_PROJECTS . '"';
			check_selected( $p_project_id, ALL_PROJECTS );
			PRINT '>' . lang_get( 'all_projects' ) . '</option>' . "\n";
		}

		$t_project_count = count( $t_project_ids );
		for ($i=0;$i<$t_project_count;$i++) {
			$t_id = $t_project_ids[$i];
			if ( $t_id != $p_filter_project_id ) {
				PRINT "<option value=\"$t_id\"";
				check_selected( $p_project_id, $t_id );
				PRINT '>' . string_display( project_get_field( $t_id, 'name' ) ) . '</option>' . "\n";
				t_print_subproject_option_list( $t_id, $p_project_id, $p_filter_project_id, $p_trace );
			}
		}
	}
	# --------------------
	# List projects that the current user has access to
	function t_print_subproject_option_list( $p_parent_id, $p_project_id = null, $p_filter_project_id = null, $p_trace = false, $p_parents = Array() ) {
		array_push( $p_parents, $p_parent_id );
		$t_project_ids = t_user_get_accessible_projects( $p_parent_id );
		$t_project_count = count( $t_project_ids );
		for ($i=0;$i<$t_project_count;$i++) {
			$t_full_id = $t_id = $t_project_ids[$i];
			if ( $t_id != $p_filter_project_id ) {
				PRINT "<option value=\"";
				if ( $p_trace ) {
				  $t_full_id = join( $p_parents, ";") . ';' . $t_id;
				}
				PRINT "$t_full_id\"";
				check_selected( $p_project_id, $t_full_id );
				PRINT '>' . str_repeat( '&nbsp;', count( $p_parents ) ) . str_repeat( '&raquo;', count( $p_parents ) ) . ' ' . string_display( project_get_field( $t_id, 'name' ) ) . '</option>' . "\n";
				print_subproject_option_list( $t_id, $p_project_id, $p_filter_project_id, $p_trace, $p_parents );
			}
		}
	}

//*************

	$t_manage_project_threshold = config_get( 'manage_project_threshold' );
	
	$t_projects = t_user_get_accessible_projects( true );
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
	

?>
<tr <?php echo helper_alternate_class() ?> id="alt_<?php echo $t_project['id'];?>">
	<td width="1%">
		<img src="images/plus.png" id="img_<?php echo $t_project['id'];?>" alt="" />
	</td>
	<td valign="top" width="100%">
		<a href="set_project.php?project_id=<?php echo $t_project['id'] ?>&ref=candidate_report_advanced_page.php"><?php echo str_repeat( "&raquo; ", $t_level ) . string_display( $t_project['name'] ) ?></a>
	</td>
	<!--<td>
		<?php //echo get_enum_element( 'project_status', $t_project['status'] ) ?>
	</td>
	<td>
		<?php //echo trans_bool( $t_project['enabled'] ) ?>
	</td>
	<td>
		<?php//echo get_enum_element( 'project_view_state', $t_project['view_state'] ) ?>
	</td>
	<td valign="top">
		<?php //echo $t_project['description'];?>
	</td>-->
</tr>
<tr id="tr_<?php echo $t_project['id'];?>" style="display:none">
	<td colspan="2"><span style="float:right"><a href="set_project.php?project_id=<?php echo $t_project['id'] ?>&ref=candidate_report_advanced_page.php">Apply Now</a></span><?php echo $t_project['description'];?><br /><span style="float:right"><a href="set_project.php?project_id=<?php echo $t_project['id'] ?>&ref=candidate_report_advanced_page.php">Apply Now</a></span></td>
</tr>
<?php
	
		$t_subprojects = t_project_hierarchy_get_subprojects( $t_project_id, true );

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
