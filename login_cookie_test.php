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
	# $Id: login_cookie_test.php,v 1.1.1.1 2009/01/08 07:30:19 chirag Exp $
	# --------------------------------------------------------
?>
<?php
	# Check to see if cookies are working
?>
<?php require_once( 'core.php' ) ?>
<?php
	$f_return = gpc_get_string( 'return', config_get( 'default_home_page' ) );	
	//updated by Chirag, if user is admin or manager, we redirect him to report page we have created
	#	$t_access_level = user_get_field(auth_get_current_user_id(), 'access_level' );
	#	if ( $t_access_level >= MANAGER ) {
	#	$f_return ='summary_ofc_page.php';
	#	} 
	$c_return = string_prepare_header( $f_return );

	if ( auth_is_user_authenticated() ) {
		$t_redirect_url = $c_return;
	} else {
		$t_redirect_url = 'login_page.php?cookie_error=1';
	}

	print_header_redirect( $t_redirect_url, true, true );
?>
