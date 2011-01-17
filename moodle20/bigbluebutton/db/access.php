<?php

/**
 * Capabilities for BigBlueButton
 *
 * Authors:
 *      Fred Dixon (ffdixon [at] blindsidenetworks [dt] org)
 *
 * @package   mod_bigbluebutton
 * @copyright 2010 Blindside Networks
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
	
	//
	// Ability to join a meeting
    'mod/bigbluebutton:join' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        )
    ),

	//
	// Ability to moderate a meeting
	'mod/bigbluebutton:moderate' => array(
	    'captype' => 'write',
	    'contextlevel' => CONTEXT_MODULE,
	    'legacy' => array(
	        'teacher' => CAP_ALLOW,
	        'editingteacher' => CAP_ALLOW,
	        'manager' => CAP_ALLOW
	    )
	)
);

?>
