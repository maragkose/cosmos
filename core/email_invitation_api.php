<?php
#==================================================================================	
class invitation {
#==================================================================================	

	var $i_date = "";      
	var $i_time  = "";    
	var $i_location  = "";
	var $i_start = "";     
	var $i_end = "";       
	var $i_subject = "";     
	var $i_body = "";     
	var $i_uid = "";     
	var $i_headers = "";     
	var $i_calevent = "";     
	var $i_attendee_string = 'ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE:MAILTO:';

	const first_date_str  = '1st Interview Date';
	const first_time_str  = '1st Interview Time';
	const location_str    = 'Interview Room';
	const second_date_str = '2nd Interview Date';
	const second_time_str = '2nd Interview Time';

	#==================================================================================	
	public function __construct() {
	#==================================================================================	
		$this->i_headers = "From: COSMOS@nsn.com\n"; 
		$this->i_headers .= "MIME-Version: 1.0\n"; 
		$this->i_headers .= "Content-Type: text/calendar; method=REQUEST;/n"; 
		$this->i_headers .= ' charset="UTF-8"'; 
		$this->i_headers .= "/n"; 
		$this->i_headers .= "Content-Transfer-Encoding: 8bit";
	}
	#==================================================================================	
	private function buildEvent($p_attendees){
	#==================================================================================
	
		#log_event(LOG_EMAIL, "Sending timestamp $this->i_start");

		$message = "
		BEGIN:VCALENDAR\n
		METHOD:REQUEST\n
		PRODID:-//Microsoft Corporation//Outlook 14.0 MIMEDIR//EN\n 
		VERSION:2.0\n
		BEGIN:VEVENT\n
		DTSTAMP:$this->i_start\n
		DTSTART:$this->i_start\n
		DTEND:$this->i_end\n
		SUMMARY:Interview Invitation\n
		UID:$this->i_uid\n
		$p_attendees
		ORGANIZER:MAILTO:tota.papatriantafillou@nsn.com\n
		LOCATION:$this->i_location\n
		DESCRIPTION:$this->i_body\n
		SEQUENCE:0\n
		PRIORITY:5\n
		CLASS:PUBLIC\n
		STATUS:CONFIRMED\n
		TRANSP:OPAQUE\n
		BEGIN:VALARM\n
		ACTION:DISPLAY\n
		DESCRIPTION:REMINDER\n
		TRIGGER;RELATED=START:-PT00H15M00S\n
		END:VALARM\n
		END:VEVENT\n
		END:VCALENDAR\n";

		return $message;
	}
	#==================================================================================	
	public function create($type, $name, $id,  $post_data=null, $from_form=false){
	#==================================================================================	
		
		$oInvitation = new invitation;

		$oInvitation->i_uid = $id;	
		$t_location = invitation::location_str;

		switch ($type) {
		
		case FIRST_INTERVIEW_INVITE:
			$t_date_str = invitation::first_date_str;
			$t_time_str = invitation::first_time_str;
			$t_duration = 3600;
			$oInvitation->i_subject  = lang_get('1st_subject') .  '[' .  $name . ']';
		break;

		case SECOND_INTERVIEW_INVITE:
			$t_date_str = invitation::second_date_str;
			$t_time_str = invitation::second_time_str;
			$t_duration = 1800;
			$oInvitation->i_subject  = lang_get('2nd_subject') .  '[' .  $name . ']';
		break;

		default:
			$t_date_str = invitation::second_date_str;
			$t_time_str = invitation::second_time_str;
		}

		$oInvitation->i_date      = custom_field_get_value_from($t_date_str, $id, $post_data, $from_form);
		$oInvitation->i_time      = custom_field_get_value_from($t_time_str, $id, $post_data, $from_form);
		$oInvitation->i_location  = custom_field_get_value_from($t_location, $id, $post_data, $from_form);
		$oInvitation->i_start     = format_start($oInvitation->i_date, $oInvitation->i_time);
		$oInvitation->i_end       = format_end($oInvitation->i_start, $t_duration);
		$oInvitation->i_body      = lang_get('body_invitation_1') . $name . '. '; 
		$oInvitation->i_body     .= lang_get('body_invitation_link') . get_candidate_hyperlink($id);
		log_event(LOG_EMAIL, "INVITATION $oInvitation->i_date");
		log_event(LOG_EMAIL, "INVITATION $oInvitation->i_time");
		return $oInvitation;
	}
	#==================================================================================	
	public function send($p_to, $p_subject = '') {
	#==================================================================================	

		$t_recipients = array();
		
		if ($p_to == '' || $this->i_time == 'Undefined') {
			return false;
		} else if ($p_to == 'MANAGERS') {
			$t_recipients = user_get_manager_emails();
		} else {
			array_push($t_recipients, $p_to);
		}
		if ($p_subject != '') {
			$this->i_subject = $p_subject;
		}
		
		$t_attendees = '';
		
		foreach ($t_recipients as $t_recipient => $t_mail){
			$t_attendees .= $this->i_attendee_string . $t_mail . "\n";
		}
		
		$t_message = $this->buildEvent($t_attendees);
		$t_debug_email = config_get( 'debug_email' );
		
		if ( OFF !== $t_debug_email ) {
			mail($t_debug_email, $this->i_subject, $t_message, $this->i_headers);
		} else {
			foreach ($t_recipients as $t_recipient => $t_mail){
				mail($t_mail, $this->i_subject, $t_message, $this->i_headers);
				log_event(LOG_EMAIL, "Sending to $t_recipients, $t_message");
			}
		}
		return true;
	}

}
?>
