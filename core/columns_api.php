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
	# $Id: columns_api.php,v 1.21.2.1 2007-10-13 22:35:18 giallu Exp $
	# --------------------------------------------------------

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_selection( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td> &nbsp; </td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_edit( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td> &nbsp; </td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'id' ), 'id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'id' );
			echo '</td>';
		} else {
			echo lang_get( 'id' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_project_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'email_project' ), 'project_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'project_id' );
			echo '</td>';
		} else {
			echo lang_get( 'email_project' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_duplicate_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'duplicate_id' ), 'duplicate_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'duplicate_id' );
			echo '</td>';
		} else {
			echo lang_get( 'duplicate_id' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_reporter_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'reporter' ), 'reporter_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'reporter_id' );
			echo '</td>';
		} else {
			echo lang_get( 'reporter' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_handler_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'assigned_to' ), 'handler_id', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'handler_id' );
			echo '</td>';
		} else {
			echo lang_get( 'assigned_to' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_priority( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'priority_abbreviation' ), 'priority', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'priority' );
			echo '</td>';
		} else {
			echo lang_get( 'priority' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_reproducibility( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'reproducibility' ), 'reproducibility', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'reproducibility' );
			echo '</td>';
		} else {
			echo lang_get( 'reproducibility' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_projection( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'projection' ), 'projection', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'projection' );
			echo '</td>';
		} else {
			echo lang_get( 'projection' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_eta( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'eta' ), 'eta', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'eta' );
			echo '</td>';
		} else {
			echo lang_get( 'eta' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_resolution( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'resolution' ), 'resolution', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'resolution' );
			echo '</td>';
		} else {
			echo lang_get( 'resolution' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_fixed_in_version( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'fixed_in_version' ), 'fixed_in_version', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'fixed_in_version' );
			echo '</td>';
		} else {
			echo lang_get( 'fixed_in_version' );
		}
	}
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_fixed_in_version( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE  ) {
			echo '<td>';
			echo prepare_user_name( $p_row['fixed_in_version'] );
			echo '</td>';
	}
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_target_version( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE  ) {
			echo '<td>';
			echo prepare_user_name( $p_row['target_version'] );
			echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_target_version( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'target_version' ), 'target_version', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'target_version' );
			echo '</td>';
		} else {
			echo lang_get( 'target_version' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_view_state( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'view_status' ), 'view_state', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'view_state' );
			echo '</td>';
		} else {
			echo lang_get( 'view_status' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_os( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'os' ), 'os', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'os' );
			echo '</td>';
		} else {
			echo lang_get( 'os' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_os_build( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			#print_view_candidate_sort_link( lang_get( 'os_version' ), 'os_build', $p_sort, $p_dir, $p_columns_target );
		#	print_sort_icon( $p_dir, $p_sort, 'os_build' );
			echo '</td>';
		} else {
			echo lang_get( 'os_version' );
		}
	}
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_os_build( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_icon_path;
		#echo "#"; echo $p_row['build'];
		echo "\t<td>";
		$number=$p_row['os_build'];
		echo "<a href=\"mailto:$number\">";
		echo '<img width="28" height="28" src="' . $t_icon_path . 'mail.png' . '" alt="Mail Candidate" />';
		echo "</a>";
		echo "</td>\n";
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_platform( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'platform' ), 'platform', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'platform' );
			echo '</td>';
		} else {
			echo lang_get( 'platform' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_version( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'product_version' ), 'version', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'version' );
			echo '</td>';
		} else {
			echo lang_get( 'product_version' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_date_submitted( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'date_submitted' ), 'date_submitted', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'date_submitted' );
			echo '</td>';
		} else {
			echo lang_get( 'date_submitted' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_attachment( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			global $t_icon_path;

			$t_show_attachments = config_get( 'show_attachment_indicator' );

			if ( ON == $t_show_attachments ) {
				echo "\t<td>";
				echo '<img width="24" height="24" src="' . $t_icon_path . 'attachment.png' . '" alt="attachments" />';
				echo "</td>\n";
			}
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_category( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'category' ), 'category', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'category' );
			echo '</td>';
		} else {
			echo lang_get( 'category' );
		}
	}
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_category_id( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'category' ), 'category', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'category' );
			echo '</td>';
		} else {
			echo lang_get( 'category' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_sponsorship_total( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		$t_enable_sponsorship = config_get( 'enable_sponsorship' );

		if ( ON == $t_enable_sponsorship ) {
			if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
				echo "\t<td>";
				print_view_candidate_sort_link( sponsorship_get_currency(), 'sponsorship_total', $p_sort, $p_dir, $p_columns_target );
				print_sort_icon( $p_dir, $p_sort, 'sponsorship_total' );
				echo "</td>\n";
			} else {
				echo sponsorship_get_currency();
			}
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_severity( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'severity' ), 'severity', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'severity' );
			echo '</td>';
		} else {
			echo lang_get( 'severity' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_status( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'status' ), 'status', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'status' );
			echo '</td>';
		} else {
			echo  lang_get( 'status' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_last_updated( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'updated' ), 'last_updated', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'last_updated' );
			echo '</td>';
		} else {
			echo lang_get( 'updated' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_summary( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			print_view_candidate_sort_link( lang_get( 'summary' ), 'summary', $p_sort, $p_dir, $p_columns_target );
			print_sort_icon( $p_dir, $p_sort, 'summary' );
			echo '</td>';
		} else {
			echo lang_get( 'summary' );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_candidatenotes_count( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td> <img class="icon_link" src="images/notes.png" alt="Candidate Notes" /></td>';
		} else {
			echo '#';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_selection( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			global $t_checkboxes_exist, $t_update_candidate_threshold;

			echo '<td>';
			if ( access_has_candidate_level( $t_update_candidate_threshold, $p_row['id'] ) ) {
				$t_checkboxes_exist = true;
				printf( "<input type=\"checkbox\" name=\"candidate_arr[]\" value=\"%d\" />" , $p_row['id'] );
			} else {
				echo "&nbsp;";
			}
			echo '</td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	#
	# UPDATE COLUMN
	function print_column_edit( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			global $t_icon_path, $t_update_candidate_threshold;

			echo '<td>';
			if ( !candidate_is_readonly( $p_row['id'] )
		  		&& access_has_candidate_level( $t_update_candidate_threshold, $p_row['id'] ) ) {
				echo '<a href="' . string_get_candidate_update_url( $p_row['id'] ) . '">';
				echo '<img border="0" width="24" height="24" src="' . $t_icon_path . 'update.png';
				echo '" alt="' . lang_get( 'update_candidate_button' ) . '"';
				echo ' title="' . lang_get( 'update_candidate_button' ) . '" /></a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	#
	#
	function print_column_priority( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td>';
			if ( ON == config_get( 'show_priority_text' ) ) {
				print_formatted_priority_string( $p_row['status'], $p_row['priority'] );
			} else {
				print_status_icon( $p_row['priority'] );
			}
			echo '</td>';
		} else {
			echo get_enum_element( 'priority', $p_row['priority'] );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	#
	# ID COLUMN
	function print_column_id( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td>';
		print_candidate_link( $p_row['id'], false );
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_sponsorship_total( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		$t_enable_sponsorship = config_get( 'enable_sponsorship' );

		if ( $t_enable_sponsorship == ON ) {
			echo "\t<td class=\"right\">";
			if ( $p_row['sponsorship_total'] > 0 ) {
				$t_sponsorship_amount = sponsorship_format_amount( $p_row['sponsorship_total'] );
				echo string_no_break( $t_sponsorship_amount );
			}
			echo "</td>\n";
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_candidatenotes_count( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_filter;

		# grab the candidatenote count
		$t_candidatenote_stats = candidate_get_candidatenote_stats( $p_row['id'] );
		if ( NULL !== $t_candidatenote_stats ) {
			$candidatenote_count = $t_candidatenote_stats['count'];
			$v_candidatenote_updated = $t_candidatenote_stats['last_modified'];
		} else {
			$candidatenote_count = 0;
		}

		echo '<td class="center">';
		if ( $candidatenote_count > 0 ) {
			$t_candidatenote_link = '<a href="' . string_get_candidate_view_url( $p_row['id'] )
				. '&amp;nbn=' . $candidatenote_count . '#candidatenotes">'
				. $candidatenote_count . '</a>';

			if ( $v_candidatenote_updated > strtotime( '-'.$t_filter['highlight_changed'].' hours' ) ) {
				printf( '<span class="bold">%s</span>', $t_candidatenote_link );
			} else {
				echo $t_candidatenote_link;
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_attachment( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_icon_path;

		$t_show_attachments = config_get( 'show_attachment_indicator' );

		# Check for attachments
		$t_attachment_count = 0;
		if ( ( ON == $t_show_attachments ) && ( file_can_view_candidate_attachments( $p_row['id'] ) ) ) {
			$t_attachment_count = file_candidate_attachment_count( $p_row['id'] );
			$t_attachment_rows = candidate_get_attachments( $p_row['id'] );

		}

		if ( ON == $t_show_attachments ) {
			echo "\t<td>";
			if ( 0 < $t_attachment_count ) {
				$row = $t_attachment_rows[0];
				extract( $row, EXTR_PREFIX_ALL, 'v' );
				$t_href_end	= '</a>';
				$t_direct_option = config_get('direct_attachment_download');
				if ($t_direct_option == ON) {
					$t_href_start	= "<a href=\"file_download.php?file_id=$v_id&amp;type=candidate\">";
				} else {
					echo '<a href="' . string_get_candidate_view_url( $p_row['id'] ) . '#attachments">';
				}
				echo $t_href_start;
				echo '<img border="0" width="24" height="24" src="' . $t_icon_path . 'attachment.png' . '"';
				echo ' alt="' . lang_get( 'attachment_alt' ) . '"';
				echo ' title="' . $t_attachment_count . ' ' . lang_get( 'attachments' ) . '"';
				echo ' />';
				echo $t_href_end;
			} else {
				echo ' &nbsp; ';
			}
			echo "</td>\n";
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_category( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_sort, $t_dir;

		# grab the project name
		$t_project_name = project_get_field( $p_row['project_id'], 'name' );

		echo '<td class="center">';

		# type project name if viewing 'all projects' or if issue is in a subproject
		if ( ON == config_get( 'show_candidate_project_links' )
		  && helper_get_current_project() != $p_row['project_id'] ) {
			echo '<small>[';
			print_view_candidate_sort_link( $t_project_name, 'project_id', $t_sort, $t_dir, $p_columns_target );
			echo ']</small><br />';
		}

		echo string_display( $p_row['category'] );
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_severity( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">';
		print_formatted_severity_string( $p_row['status'], $p_row['severity'] );
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_eta( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td class="center">', get_enum_element( 'eta', $p_row['eta'] ), '</td>';
		} else {
			echo get_enum_element( 'eta', $p_row['eta'] );
		}
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_resolution( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">', get_enum_element( 'resolution', $p_row['resolution'] ), '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_status( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">';
		printf( '<span class="issue-status" title="%s">%s</span>'
			, get_enum_element( 'resolution', $p_row['resolution'] )
			, get_enum_element( 'status', $p_row['status'] )
		);

		# print username instead of status
		if ( ( ON == config_get( 'show_assigned_names' ) )
		  && ( $p_row['handler_id'] > 0 ) 
		  && ( access_has_candidate_level( config_get( 'view_handler_threshold' ), $p_row['id'] ) ) ) {
			printf( ' (%s)', prepare_user_name( $p_row['handler_id'] ) );
		}
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_handler_id( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">';
		if ( ( $p_row['handler_id'] > 0 ) && ( access_has_candidate_level( config_get( 'view_handler_threshold' ), $p_row['id'] ) ) ) {
			echo prepare_user_name( $p_row['handler_id'] );
		}
		echo '</td>';
	}
	
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_reporter_id( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		echo '<td class="center">';
		echo prepare_user_name( $p_row['reporter_id'] );
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_last_updated( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_filter;

		$t_last_updated = date( config_get( 'short_date_format' ), $p_row['last_updated'] );

		echo '<td class="center">';
		if ( $p_row['last_updated'] > strtotime( '-'.$t_filter['highlight_changed'].' hours' ) ) {
			printf( '<span class="bold">%s</span>', $t_last_updated );
		} else {
			echo $t_last_updated;
		}
		echo '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_date_submitted( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		$t_date_submitted = date( config_get( 'short_date_format' ), $p_row['date_submitted'] );

		echo '<td class="center">', $t_date_submitted, '</td>';
	}

	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_summary( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_icon_path;

		if ( $p_columns_target == COLUMNS_TARGET_CSV_PAGE ) {
			$t_summary = string_attribute( $p_row['summary'] );
		} else {
			$t_summary = string_display_line_links( $p_row['summary'] );
		}

		echo '<td class="left">';
	       	print_candidate_link ($p_row['id'], $t_summary, false);
		if ( VS_PRIVATE == $p_row['view_state'] ) {
			printf( ' <img src="%s" alt="(%s)" title="%s" />'
				, $t_icon_path . 'protected.gif'
				, lang_get( 'private' )
				, lang_get( 'private' )
			);
		}
		echo '</td>';
	}
	
	# --------------------
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_build( $p_row, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		global $t_icon_path;
		if ( access_has_candidate_level( config_get( 'roadmap_view_threshold' ), $p_row['id'] ) ) {
			#echo "#"; echo $p_row['build'];
			echo "\t<td>";
			$number=$p_row['build'];
			echo "<a href=\"sip:$number\">";
			echo '<img width="28" height="28" src="' . $t_icon_path . 'call.png' . '" alt="Call Candidate" />';
			echo "</a>";
			echo "</td>\n";
		}
	}
	# $p_columns_target: see COLUMNS_TARGET_* in constant_inc.php
	function print_column_title_build( $p_sort, $p_dir, $p_columns_target = COLUMNS_TARGET_VIEW_PAGE ) {
		if ( $p_columns_target != COLUMNS_TARGET_CSV_PAGE ) {
			echo '<td> &nbsp; </td>';
		}
	}
/**
 * Gets the localized title for the specified column.  The column can be native or custom.
 * The custom fields must contain the 'custom_' prefix.
 * @param string $p_column - The column name.
 * @return string The column localized name.
 * @access public
 */
function column_get_title( $p_column ) {
	$t_custom_field = column_get_custom_field_name( $p_column );
	if( $t_custom_field !== null ) {
		$t_field_id = custom_field_get_id_from_name( $t_custom_field );

		if( $t_field_id === false ) {
			$t_custom_field = '@' . $t_custom_field . '@';
		} else {
			$t_def = custom_field_get_definition( $t_field_id );
			$t_custom_field = lang_get_defaulted( $t_def['name'] );
		}

		return $t_custom_field;
	}

	$t_plugin_columns = columns_get_plugin_columns();
	if ( isset( $t_plugin_columns[ $p_column ] ) ) {
		$t_column_object = $t_plugin_columns[ $p_column ];
		return $t_column_object->title;
	}

	switch( $p_column ) {
		case 'attachment_count':
			return lang_get( 'attachments' );
		case 'candidatenotes_count':
			return '#';
		case 'category_id':
			return lang_get( 'category' );
		case 'edit':
			return '';
		case 'handler_id':
			return lang_get( 'assigned_to' );
		case 'last_updated':
			return lang_get( 'updated' );
		case 'os_build':
			return lang_get( 'os_version' );
		case 'project_id':
			return lang_get( 'email_project' );
		case 'reporter_id':
			return lang_get( 'reporter' );
		case 'selection':
			return '';
		case 'sponsorship_total':
			return sponsorship_get_currency();
		case 'version':
			return lang_get( 'product_version' );
		case 'view_state':
			return lang_get( 'view_status' );
		default:
			return lang_get_defaulted( $p_column );
	}
}
/**
 * Given a column name from the array of columns to be included in a view, this method checks if
 * the column is a custom column and if so returns its name.  Note that for custom fields, then
 * provided names will have the "custom_" prefix, where the returned ones won't have the prefix.
 *
 * @param string $p_column Column name.
 * @return string The custom field column name or null if the specific column is not a custom field or invalid column.
 * @access public
 */
function column_get_custom_field_name( $p_column ) {
	if( strncmp( $p_column, 'custom_', 7 ) === 0 ) {
		return utf8_substr( $p_column, 7 );
	}

	return null;
}
/**
 * Allow plugins to define a set of class-based columns, and register/load
 * them here to be used by columns_api.
 * @return array Mapping of column name to column object
 */
function columns_get_plugin_columns() {
	static $s_column_array = null;

	if ( is_null( $s_column_array ) ) {
		$s_column_array = array();

		$t_all_plugin_columns = event_signal( 'EVENT_FILTER_COLUMNS' );
		foreach( $t_all_plugin_columns as $t_plugin => $t_plugin_columns ) {
			foreach( $t_plugin_columns as $t_callback => $t_plugin_column_array ) {
				if ( is_array( $t_plugin_column_array ) ) {
					foreach( $t_plugin_column_array as $t_column_class ) {
						if ( class_exists( $t_column_class ) && is_subclass_of( $t_column_class, 'COSMOSColumn' ) ) {
							$t_column_object = new $t_column_class();
							$t_column_name = utf8_strtolower( $t_plugin . '_' . $t_column_object->column );
							$s_column_array[ $t_column_name ] = $t_column_object;
						}
					}
				}
			}
		}
	}

	return $s_column_array;
}
function columns_plugin_cache_issue_data( $p_candidates ) {
	$t_columns = columns_get_plugin_columns();

	foreach( $t_columns as $t_column_object ) {
		$t_column_object->cache( $p_candidates );
	}
}
/**
 * Returns true if the specified $p_column is a plugin column.
 * @param string $p_column A column name.
 */
function column_is_plugin_column( $p_column ) {
	$t_plugin_columns = columns_get_plugin_columns();
	return isset( $t_plugin_columns[ $p_column ] );
}
/**
 * Validates an array of column names and removes ones that are not valid.  The validation
 * is not case sensitive.
 *
 * @param array $p_columns - The array of column names to be validated.
 * @param array $p_columns_all - The array of all valid column names.
 * @return array The array of valid column names found in $p_columns.
 * @access public
 */
function columns_remove_invalid( $p_columns, $p_columns_all ) {
	$t_columns_all_lower = array_values( array_map( 'utf8_strtolower', $p_columns_all ) );
	$t_columns = array();

	foreach( $p_columns as $t_column ) {
		if( in_array( utf8_strtolower( $t_column ), $t_columns_all_lower ) ) {
			$t_columns[] = $t_column;
		}
	}

	return $t_columns;
}
/**
 * Get all accessible columns for the current project / current user..
 * @param int $p_project_id project id
 * @return array array of columns
 * @access public
 */
function columns_get_all( $p_project_id = null ) {
	$t_columns = columns_get_standard();

	# add plugin columns
	$t_columns = array_merge( $t_columns, array_keys( columns_get_plugin_columns() ) );

	# Add project custom fields to the array.  Only add the ones for which the current user has at least read access.
	if( $p_project_id === null ) {
		$t_project_id = helper_get_current_project();
	} else {
		$t_project_id = $p_project_id;
	}

	$t_related_custom_field_ids = custom_field_get_linked_ids( $t_project_id );
	foreach( $t_related_custom_field_ids as $t_id ) {
		if( !custom_field_has_read_access_by_project_id( $t_id, $t_project_id ) ) {
			continue;
		}

		$t_def = custom_field_get_definition( $t_id );
		$t_columns[] = 'custom_' . $t_def['name'];
	}

	return $t_columns;
}
/**
 * Get a list of standard columns.
 * @param bool $p_enabled_columns_only default true, if false returns all columns regardless of config settings
 * @return array of column names
 */
function columns_get_standard( $p_enabled_columns_only = true ) {
	$t_reflection = new ReflectionClass('BugData');
	$t_columns = $t_reflection->getDefaultProperties();

	$t_columns['selection'] = null;
	$t_columns['edit'] = null;

	# Overdue icon column (icons appears if an issue is beyond due_date)
	$t_columns['overdue'] = null;

	if( $p_enabled_columns_only && OFF == config_get( 'enable_profiles' ) ) {
		unset( $t_columns['os'] );
		unset( $t_columns['os_build'] );
		unset( $t_columns['platform'] );
	}

	if( $p_enabled_columns_only && config_get( 'enable_eta' ) == OFF ) {
		unset( $t_columns['eta'] );
	}

	if( $p_enabled_columns_only && config_get( 'enable_projection' ) == OFF ) {
		unset( $t_columns['projection'] );
	}

	if( $p_enabled_columns_only && config_get( 'enable_product_build' ) == OFF ) {
		unset( $t_columns['build'] );
	}

	# The following fields are used internally and don't make sense as columns
	unset( $t_columns['_stats'] );
	unset( $t_columns['profile_id'] );
	unset( $t_columns['sticky'] );
	unset( $t_columns['loading'] );

	return array_keys( $t_columns );
}

?>
