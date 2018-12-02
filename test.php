<?php
function s() {
require_once( 'core.php' );
$t_core_path = config_get( 'core_path' );
require_once( $t_core_path.'email_api.php' );
require_once( $t_core_path.'utility_api.php' );
$headers = "From: a@b.com\n"; 
$headers .= "MIME-Version: 1.0\n"; 
$headers .= "Content-Type: text/calendar; method=REQUEST;/n"; 
$headers .= ' charset="UTF-8"'; 
$headers .= "/n"; 
$headers .= "Content-Transfer-Encoding: 8bit";
gpc_isset('target_version');
echo format_end('20120303T190000Z', config_get('sec_interview_duration'));
die("3");
$message = "
BEGIN:VCALENDAR\n
METHOD:REQUEST\n
PRODID:-//Microsoft Corporation//Outlook 14.0 MIMEDIR//EN\n 
VERSION:2.0\n
BEGIN:VEVENT\n
DTSTAMP:20120818T050000Z\n
DTSTART:20120818T050000Z\n
DTEND:20120818T060000Z\n
SUMMARY:test request\n
UID:325\n
ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE:MAILTO:emmanouil.maragkos@nsn.com\n
ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE:MAILTO:spiros@chrisanthakis@nsn.com\n
ORGANIZER:MAILTO:d@e.com\n
LOCATION:your location\n
DESCRIPTION:come to join us\n
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

mail("emmanouil.maragkos@nsn.com", "Great Party", $message, $headers);

}
function sendIcalEvent($to_address, $startTime, $endTime, $subject, $description,  $location)
{
require_once( 'core.php' );
$t_core_path = config_get( 'core_path' );
require_once( $t_core_path.'email_api.php' );
$from_name = 'cosmos';
$from_address = 'cosmos@nsn.com';
$to_name = "Manos"; 
$domain = 'nsn.com';
//Create Email Headers
$mime_boundary = "----Meeting Booking----".MD5(TIME());
// $headers .= "Content-type: text/calendar; method=REQUEST; charset=UTF-8\r\n";
$headers = "From: From Name <From Mail>\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/calendar; method=REQUEST;\n";
$headers .= 'charset="UTF-8"';
$headers .= "\n";
$headers .= "Content-Transfer-Encoding: 7bit";
$headers .= "\n";
//Create Email Body (HTML)
#$message = "--$mime_boundary\r\n";
#$message .= "Content-Type: text/html; charset=UTF-8\n";
#$message .= "Content-Transfer-Encoding: 8bit\n\n";
#$message .= "<html>\n";
#$message .= "<body>\n";
#$message .= '<p>Dear '.$to_name.',</p>';
#$message .= '<p>'.$description.'</p>';
#$message .= "</body>\n";
#$message .= "</html>\n";
#$message .= "--$mime_boundary\r\n";

//status / url / recurid /
$ical = 'BEGIN:VCALENDAR' . "\r\n" .
'PRODID:-//Microsoft Corporation//Outlook 12.0 MIMEDIR//EN' . "\r\n" .
'VERSION:2.0' . "\r\n" .
'CALSCALE:GREGORIAN' . "\r\n" .
'METHOD:REQUEST' . "\r\n" .
'X-MS-OLK-FORCEINSPECTOROPEN:TRUE' . "\r\n" .
'BEGIN:VEVENT' . "\r\n" .
'CREATED:20131101T020000' . "\r\n" .
'DTSTAMP:20131101T020000' . "\r\n" .
'DTSTART:20131101T020000' . "\r\n" .
'DTEND:20131101T021000' . "\r\n" .
'LOCATION:' . $location . "\r\n" .
#'UID:'.date("Ymd\TGis", strtotime($startTime)).rand()."@".$domain."\r\n" .
'UID:325' . "\r\n" .
'SUMMARY;LANGUAGE=en-us:' . $subject . "\r\n" .
'X-ALT-DESC;FMTTYPE=text/html:hello' . "\r\n" .
'PRIORITY:3' . "\r\n" .
'SEQUENCE:0' . "\r\n" .
'STATUS:CONFIRMED' . "\r\n" .
'TRANSP:OPAQUE' . "\r\n" .
'CLASS:PUBLIC' . "\r\n" .
'ORGANIZER;CN=' . $to_name . ':MAILTO:' . $to_name . "\r\n" .
'ATTENDEE;CN="'.$to_name.'";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:'.$to_address. "\r\n" .
'BEGIN:VALARM' . "\r\n" .
'TRIGGER:-PT15M' . "\r\n" .
'ACTION:DISPLAY' . "\r\n" .
'DESCRIPTION:Reminder' . "\r\n" .
'END:VALARM' . "\r\n" .
'END:VEVENT'. "\r\n" .
'END:VCALENDAR'. "\r\n";
$message .= 'Content-Type: text/calendar;name="meeting.ics";method=REQUEST\n';
#$message .= 'Content-Disposition: attachment';
$message .= 'Content-Transfer-Encoding: base64';
$message .= $ical;

		if( !is_blank( $to_address ) ) {
			mail($to_address, $subject, $message,  $headers);
		}
}
?>
<?php
	require_once( 'core.php' );
	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'email_api.php' );
	require_once( $t_core_path.'user_api.php' );

	#html_page_top1();
	#html_meta_redirect('my_view_page.php');
	#html_page_top2();
	#email_send_all();
?>
<br />
<div align="center">
<?php
	#get_candidate_hyperlink("43");
	#email_send_invitations('MANAGERS','1/1/2012','13:00','0.43','hello','test', '4');
   	print_button_link("sip:123123", "clik");	
	echo lang_get( 'operation_successful' );
var_dump($_GET);
?>
</div>
