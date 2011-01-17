<?php

/**
 * Library calls for Moodle and BigBlueButton.
 *
 * Authors:
 *      Fred Dixon (ffdixon [at] blindsidenetworks [dt] org)
 *
 * @package   mod_bigbluebutton
 * @copyright 2010 Blindside Networks
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

include( 'bbb_api/bbb_api.php' );

defined('MOODLE_INTERNAL') || die;

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $bigbluebutton An object from the form in mod_form.php
 * @return int The id of the newly inserted bigbluebutton record
 */
function bigbluebutton_add_instance($bigbluebutton) {
	global $DB;

	$bigbluebutton->timecreated = time();

	if ($DB->record_exists( 'bigbluebutton', array('name'=>$bigbluebutton->name))) {
		print_error("A meeting with that name already exists.");
		return false;
	}

	$bigbluebutton->moderatorpass = bigbluebutton_rand_string( 16 );
	$bigbluebutton->viewerpass = bigbluebutton_rand_string( 16 );
	$bigbluebutton->meetingid = bigbluebutton_rand_string( 16 );

	return $DB->insert_record('bigbluebutton', $bigbluebutton);
}


/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $bigbluebutton An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function bigbluebutton_update_instance($bigbluebutton) {
	global $DB;

	$bigbluebutton->timemodified = time();
	$bigbluebutton->id = $bigbluebutton->instance;

	if (! isset($bigbluebutton->wait)) {
		$bigbluebutton->wait = 0;
	}


    # You may have to add extra stuff in here #

    return $DB->update_record('bigbluebutton', $bigbluebutton);
}


/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function bigbluebutton_delete_instance($id) {
    global $CFG, $DB;

    if (! $bigbluebutton = $DB->get_record('bigbluebutton', array('id' => $id))) {
        return false;
    }

    $result = true;

    //
	// End the session associated with this instance (if it's running)
	//
	$meetingID = $bigbluebutton->meetingid;
	$modPW = $bigbluebutton->moderatorpass;
	$url = trim(trim($CFG->BigBlueButtonServerURL),'/').'/';
	$salt = trim($CFG->BigBlueButtonSecuritySalt);

	$getArray = BigBlueButton::endMeeting( $meetingID, $modPW, $url, $salt );
	
	if (! $DB->delete_records('bigbluebutton', array('id' => $bigbluebutton->id))) {
		$result = false;
	}

	return $result;
}


/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 */
function bigbluebutton_user_outline($course, $user, $mod, $bigbluebutton) {
    return true;
}


/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 */
function bigbluebutton_user_complete($course, $user, $mod, $bigbluebutton) {
    return true;
}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in bigbluebutton activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function bigbluebutton_print_recent_activity($course, $isteacher, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 **/
function bigbluebutton_cron () {
    return true;
}


/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @global object
 * @param object $coursemodule
 * @return object|null
 */
function bigbluebutton_get_coursemodule_info($coursemodule) {
    global $DB;

    if ($bigbluebutton = $DB->get_record('bigbluebutton', array('id'=>$coursemodule->instance), 'id, name')) {
        $info = new stdClass();
        // no filtering hre because this info is cached and filtered later
        $info->extra = format_module_intro('bigbluebutton', $bigbluebutton, $coursemodule->id, false);
        $info->name  = $bigbluebutton->name;
        return $info;
    } else {
        return null;
    }
}


/**
 * @return array
 */
function bigbluebutton_get_view_actions() {
    return array();
}

/**
 * @return array
 */
function bigbluebutton_get_post_actions() {
    return array();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function bigbluebutton_reset_userdata($data) {
    return array();
}


/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of bigbluebutton. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $bigbluebuttonid ID of an instance of this module
 * @return mixed boolean/array of students
 */
function bigbluebutton_get_participants($bigbluebuttonid) {
    return false;
}


/**
 * This function returns if a scale is being used by one bigbluebutton
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $bigbluebuttonid ID of an instance of this module
 * @return mixed
 */
function bigbluebutton_scale_used($bigbluebuttonid, $scaleid) {
    $return = false;

    return $return;
}

/**
 * Checks if scale is being used by any instance of bigbluebutton.
 * This function was added in 1.9
 *
 * This is used to find out if scale used anywhere
 * @param $scaleid int
 * @return boolean True if the scale is used by any bigbluebutton
 */
function bigbluebutton_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('bigbluebutton', 'grade', -$scaleid)) {
        return true;
    } else {
        return false;
    }
}
/**
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 */

function bigbluebutton_supports($feature) {
    switch($feature) {
        case FEATURE_IDNUMBER:                return true;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        // case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_BACKUP_MOODLE2:          return true;

        default: return null;
    }
}




//////////////////////////////////////////////////////////////////////////////////////
/// Any other bigbluebutton functions go here.  Each of them must have a name that
/// starts with bigbluebutton_
/// Remember (see note in first lines) that, if this section grows, it's HIGHLY
/// recommended to move all funcions below to a new "localib.php" file.

# function taken from http://www.php.net/manual/en/function.mt-rand.php
# modified by Sebastian Schneider
# credits go to www.mrnaz.com
function bigbluebutton_rand_string($len, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
{
    $string = '';
    for ($i = 0; $i < $len; $i++)
    {
        $pos = rand(0, strlen($chars)-1);
        $string .= $chars{$pos};
    }
    return (sha1($string));
}

?>
