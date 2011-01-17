<?php

/**
 * Definition of log events
 *
 * Authors:
 *      Fred Dixon (ffdixon [at] blindsidenetworks [dt] org)
 *
 * @package   mod_bigbluebutton
 * @copyright 2010 Blindside Networks
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module'=>'bigbluebutton', 'action'=>'add', 'mtable'=>'bigbluebutton', 'field'=>'name'),
    array('module'=>'bigbluebutton', 'action'=>'update', 'mtable'=>'bigbluebutton', 'field'=>'name'),
    array('module'=>'bigbluebutton', 'action'=>'view', 'mtable'=>'bigbluebutton', 'field'=>'name'),
    array('module'=>'bigbluebutton', 'action'=>'view all', 'mtable'=>'bigbluebutton', 'field'=>'name')
);

