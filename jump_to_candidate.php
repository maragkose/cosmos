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
	# $Id: jump_to_candidate.php,v 1.22.2.1 2007-10-13 22:33:17 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	auth_ensure_user_authenticated();

	# Determine which view page to redirect back to.
	$f_candidate_id		= gpc_get_int( 'candidate_id' );

	print_header_redirect_view( $f_candidate_id );
?>
