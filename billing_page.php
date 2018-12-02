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
	# $Id: billing_page.php,v 1.1.2.1 2007-10-13 22:32:30 giallu Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );
	
	$t_core_path = config_get( 'core_path' );
?>
<?php
/*
	compress_enable();
*/
?>
<?php html_page_top1( lang_get( 'time_tracking_billing_link' )  ) ?>
<?php html_page_top2() ?>

<br />

<?php
	$t_cosmos_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
?>
	<!-- Jump to Bugnote add form -->
<?php
	# Work break-down
	include( $t_cosmos_dir . 'billing_inc.php' );
	
	html_page_bottom1( __FILE__ );
?>
