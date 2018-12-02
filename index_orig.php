<?php
# COSMOSBT - a php based candidatetracking system

# COSMOSBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# COSMOSBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with COSMOSBT.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package COSMOSBT
 * @copyright 
 * @copyright 
 * @link http://www.cosmosbt.org
 */
/**
 * COSMOSBT Core API's
 */
require_once( 'core.php' );

if ( auth_is_user_authenticated() ) {
	print_header_redirect( config_get( 'default_home_page' ) );
} else {
	print_header_redirect( 'login_page.php' );
}
