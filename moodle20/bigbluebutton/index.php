<?php

/**
 * View all BigBlueButton instances in this course.
 *
 * Authors:
 * 	Fred Dixon (ffdixon [at] blindsidenetworks [dt] org)
 *
 * @package   mod_bigbluebutton
 * @copyright 2010 Blindside Networks 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

require_once('../../config.php');
require_once('lib.php');


$id = required_param('id', PARAM_INT);		// Course Module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // bigbluebutton instance ID

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);
require_course_login($course, true);

$coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
$moderator = has_capability('mod/bigbluebutton:moderate', $coursecontext);

add_to_log($course->id, 'bigbluebutton', 'view all', "index.php?id=$course->id", '');


/// Get all required stringsbigbluebutton

$strbigbluebuttons = get_string('modulenameplural', 'bigbluebutton');
$strbigbluebutton  = get_string('modulename', 'bigbluebutton');

$PAGE->set_pagelayout('incourse');

/// Print the header

$navlinks = array();
$navlinks[] = array('name' => $strbigbluebuttons, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);

$PAGE->set_url('/mod/bigbluebutton/index.php', array('id'=>$id));
$PAGE->navbar->add($strbigbluebuttons, "index.php?id=$course->id");
$PAGE->set_title($strbigbluebuttons);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

// print_header_simple($strbigbluebuttons, '', $navigation, '', '', true, '', navmenu($course));

/// Get all the appropriate data

if (! $bigbluebuttons = get_all_instances_in_course('bigbluebutton', $course)) {
    notice('There are no instances of bigbluebutton', "../../course/view.php?id=$course->id");
}

/// Print the list of instances (your module will probably extend this)

$timenow  = time();
$strweek  = get_string('week');
$strtopic = get_string('topic');
$heading_name  			= get_string('index_header_name', 'bigbluebutton' );
$heading_users			= get_string('index_heading_users', 'bigbluebutton');
$heading_viewer  		= get_string('index_heading_viewer', 'bigbluebutton');
$heading_moderator 		= get_string('index_heading_moderator', 'bigbluebutton' );
$heading_actions 		= get_string('index_heading_actions', 'bigbluebutton' );


$table = new html_table();

if ($course->format == 'weeks') {
    $table->head  = array ($strweek, $heading_name, $heading_users, $heading_viewer, $heading_moderator, $heading_actions);
    $table->align = array ('center', 'center', 'center', 'center', 'center',  'center' );
} else if ($course->format == 'topics') {
    $table->head  = array ($strtopic, $strname);
    $table->align = array ('center', 'left', 'left', 'left');
} else {
    $table->head  = array ($strname);
    $table->align = array ('left', 'left', 'left');
}


$salt = trim($CFG->BigBlueButtonSecuritySalt);
$url = trim(trim($CFG->BigBlueButtonServerURL),'/').'/';
$logoutURL = $CFG->wwwroot;

if( isset($_POST['submit']) && $_POST['submit'] == 'end' ) { 
	//
	// A request to end the meeting
	//
	if (! $bigbluebutton = $DB->get_record('bigbluebutton', array('id'=>$a))) {
        	print_error("BigBlueButton ID $a is incorrect");
	}
	echo get_string('index_ending', 'bigbluebutton');

	$meetingID = $bigbluebutton->meetingid;
	$modPW = $bigbluebutton->moderatorpass;

	$getArray = BigBlueButton::endMeeting( $meetingID, $modPW, $url, $salt );
	// print_object( $getArray );
	$bigbluebutton->meetingid = bigbluebutton_rand_string( 16 );
	if (! $DB->update_record('bigbluebutton', $bigbluebutton) ) {
		notice( "Unable to assign a new meetingid" );
	} else {
		redirect('index.php?id='.$id);
	}
}

// print_object( $bigbluebuttons );

foreach ($bigbluebuttons as $bigbluebutton) {
	$info = null;
	$joinURL = null;
	$user = null;
	$result = null;
	$users = "-";
	$running = "-";
	$actions = "-";
	$viewerList = "-";
	$moderatorList = "-";
		
	// print_object( $bigbluebutton );

    if (!$bigbluebutton->visible) {
    	// Nothing to do
    } else {
		$modPW = $bigbluebutton->moderatorpass;
		$attPW = $bigbluebutton->viewerpass;

		// print "## $modPW ##";
		
		$joinURL = '<a href="view.php?id='.$bigbluebutton->coursemodule.'">'.format_string($bigbluebutton->name).'</a>';
		// $status = $bigbluebutton->meetingid;

		//echo "XX";

		//
		// Output Users in the meeting
		//
		$getArray = BigBlueButton::getMeetingInfoArray( $bigbluebutton->meetingid, $modPW, $url, $salt );

		// print_object( $getArray );

		if (!$getArray) {
			//
			// The server was unreachable
			//
			print_error( get_string( 'index_unable_display', 'bigbluebutton' ));
			// print_error( 'Unable to display the meetings. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running.' );
			return;
		}

		if (isset($getArray['messageKey'])) {
			//
			// There was an error returned
			//
			if ($info['messageKey'] == "checksumError") {
				print_error( get_string( 'index_checksum_error', 'bigbluebutton' ));
				return;
			}

			if ($getArray['messageKey'] == "notFound" ) {
				//
				// The meeting does not exist yet on the BigBlueButton server.  This is OK.
				//
			} else {
				//
				// There was an error
				//
				$users = $getArray['messageKey'].": ".$info['message'];
			}
		} else {

			//
			// The meeting info was returned
			//
			if ($getArray['running'] == 'true') {
				// $status =  get_string('index_running', 'bigbluebutton' );
				
				if ( $moderator ) {
					$actions = '<form name="form1" method="post" action=""><INPUT type="hidden" name="id" value="'.$id.'"><INPUT type="hidden" name="a" value="'.$bigbluebutton->id.'"><INPUT type="submit" name="submit" value="end" onclick="return confirm(\''. get_string('index_confirm_end', 'bigbluebutton' ).'\')"></form>';
				}

				$xml = $getArray['attendees'];
				if (count( $xml ) && count( $xml->attendee ) ) {
					$users = count( $xml->attendee );
					$viewer_count = 0;
					$moderator_count = 0;
					foreach ( $xml->attendee as $attendee ) {
						if ($attendee->role == "MODERATOR" ) {
							if ( $viewer_count++ > 0 ) {
								$moderatorList .= ", ";
							} else {
								$moderatorList = "";
							}
							$moderatorList .= $attendee->fullName;
						} else {
							if ( $moderator_count++ > 0 ) {
								$viewerList .= ", ";
							} else {
								$viewerList = "";
							}
							$viewerList .= $attendee->fullName;
						}
					}
				}
			}
		}
	}

	if ($course->format == 'weeks' or $course->format == 'topics' ) {
		$table->data[] = array ($bigbluebutton->section, $joinURL, $users, $viewerList, $moderatorList, $actions );
	} else {
		$table->data[] = array ($bigbluebutton->section, $joinURL, $users, $viewerList, $moderatorList, $actions );
	}
}

echo $OUTPUT->heading(get_string('index_heading', 'bigbluebutton'));
echo html_writer::table($table);
echo $OUTPUT->footer();

?>
