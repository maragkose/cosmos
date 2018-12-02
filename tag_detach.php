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
	# $Id: tag_detach.php,v 1.2.2.1 2007-10-13 22:34:45 giallu Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path . 'tag_api.php' );

	form_security_validate( 'tag_detach' );

	$f_tag_id = gpc_get_int( 'tag_id' );
	$f_candidate_id = gpc_get_int( 'candidate_id' );
	$t_user_id = auth_get_current_user_id();

	$t_tag_row = tag_get( $f_tag_id );
	$t_tag_candidate_row = tag_candidate_get_row( $f_tag_id, $f_candidate_id );

	if ( ! ( access_has_candidate_level( config_get( 'tag_detach_threshold' ), $f_candidate_id, $t_user_id ) 
		|| ( $t_user_id == $t_tag_candidate_row['user_id'] )
			&& access_has_candidate_level( config_get( 'tag_detach_own_threshold' ), $f_candidate_id, $t_user_id ) ) ) 
	{
		access_denied();
	}

	tag_candidate_detach( $f_tag_id, $f_candidate_id );

	form_security_purge( 'tag_detach' );
	
	print_successful_redirect_to_candidate( $f_candidate_id );
