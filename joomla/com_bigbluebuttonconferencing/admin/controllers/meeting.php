<?php
/*

   Copyright 2010 Blindside Networks

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Versions:
   0.1 --  Initial version written by Omar Shammas
                    (email : omar DOT shammas [a t ] g m ail DOT com)
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
require_once( JPATH_COMPONENT.DS.'includes'.DS.'bbb_api.php');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bigbluebuttonconferencing'.DS.'tables');

 
/**
 * BigBlueButton Component Controller
 */
class MeetingsControllerMeeting extends MeetingsController
{
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	 
		// Register Extra tasks
	}

    /**
	 * display the edit form
	 * @return void
	 */
	function add()
	{
		JRequest::setVar( 'view', 'meeting' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);
		
		parent::display();
	}
	
	/**
	 * save a record (and redirect to main page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('meeting');
	 
		if ($model->store($post)) {
			$msg = JText::_( 'Meeting Saved!' );
		} else {
			$msg = JText::_( 'Error Saving Meeting - ' );
			$msg .= $model->getError();
		}
	 
		$link = 'index.php?option=com_bigbluebuttonconferencing';
		$this->setRedirect($link, $msg);
	}
	
	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('meeting');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More Meetings Could not be Deleted' );
		} else {
			$msg = JText::_( 'Meeting(s) Deleted' );
		}
	 
		$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
	}
	
	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
	} 
	
	/**
	 * Joins a meeting
	 */
	function join()
	{	
		//get the meeting
		$id = JRequest::getVar('id');
		$query = ' SELECT * FROM #__bbb WHERE id ='.$id;
		$db =& JFactory::getDBO();
		$db->setQuery( $query );
		$meeting = $db->loadObject();
			
		if(!$meeting || $meeting == null){
			$msg = "This meeting no longer exists.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
		
		// This is the security salt that must match the value set in the BigBlueButton server
		// This is the URL for the BigBlueButton server
		$row =& JTable::getInstance('settings', 'Table');
		$row->load(1);
		$row->name == 'salt' ? $salt = $row->varValue : $url = $row->varValue;
		$row->load(2);
		$row->name == 'salt' ? $salt = $row->varValue : $url = $row->varValue;

		if(!$salt || $salt == '' || !$url || $url == ''){
			$msg = "You have to fill out the salt and url in the settings menu before you can join a meeting.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
			return;
		}
		
		$user =& JFactory::getUser();
		
		
		
		//Calls create meeting on the bigbluebutton server
		$response = BigBlueButton::createMeetingArray($user->name, $meeting->meetingName."[".$meeting->meetingVersion."]", 'Welcome to '.$meeting->meetingName.'.', $meeting->moderatorPW, $meeting->attendeePW, $salt, $url, JURI::base() );
		$createNew = false;
		//Analyzes the bigbluebutton server's response
		if(!$response){//If the server is unreachable, then prompts the user of the necessary action
			$msg = "Unable to join the meeting. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running.";
		}
		else if( $response['returncode'] == 'FAILED' ) { //The meeting was not created
			if($response['messageKey'] == 'idNotUnique'){
				$createNew = true;
			}
			else if($response['messageKey'] == 'checksumError'){
				$msg = "A checksum error occured. Make sure you entered the correct salt.";
			}
			else{
				$msg = $response['message'];
			}
		}
		else if($response['hasBeenForciblyEnded'] == 'true'){ //The meeting was created, and the user will now be joined
			$createNew = true;
		}
		else{
			$bbb_joinURL = BigBlueButton::joinURL($meeting->meetingName."[".$meeting->meetingVersion."]", $user->username, $meeting->moderatorPW, $salt, $url );
			?><script type="text/javascript"> window.location = "<?php echo $bbb_joinURL ?>";</script><?php
			return;
		}
		
		if($createNew){
		
			$meeting->meetingVersion = time();
			
			$db->updateObject('#__bbb',$meeting,'id',false);
			if ($db->getErrorNum()) {  
				$msg = $db->getErrorMsg();  
				$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
			}			
			
			//Calls create meeting on the bigbluebutton server
			$response = BigBlueButton::createMeetingArray($user->name, $meeting->meetingName."[".$meeting->meetingVersion."]", 'Welcome to '.$meeting->meetingName.'.', $meeting->moderatorPW, $meeting->attendeePW, $salt, $url, JURI::base() );
			
			if(!$response){//If the server is unreachable, then prompts the user of the necessary action
				$msg = "Unable to join the meeting. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running.";
			}
			else if( $response['returncode'] == 'FAILED' ) { //The meeting was not created
				if($response['messageKey'] == 'checksumError'){
					$msg = "A checksum error occured. Make sure you entered the correct salt.";
				}
				else{
					$msg = $response['message'];
				}
			}
			else{
				$bbb_joinURL = BigBlueButton::joinURL($meeting->meetingName."[".$meeting->meetingVersion."]", $user->username, $meeting->moderatorPW, $salt, $url );
				?><script type="text/javascript"> window.location = "<?php echo $bbb_joinURL ?>";</script><?php
				return;
			}
		}

		$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		return;			
	} 
	
	/**
	 * Deletes a meeting
	 */
	function delete()
	{	
		//get the meeting
		$id = JRequest::getVar('id');
		$query = ' SELECT * FROM #__bbb WHERE id ='.$id;
		$db =& JFactory::getDBO();
		$db->setQuery( $query );
		$meeting = $db->loadObject();
			
		if(!$meeting || $meeting == null){
			$msg = "This meeting no longer exists.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
		
		// This is the security salt that must match the value set in the BigBlueButton server
		// This is the URL for the BigBlueButton server
		$row =& JTable::getInstance('settings', 'Table');
		$row->load(1);
		$row->name == 'salt' ? $salt = $row->varValue : $url = $row->varValue;
		$row->load(2);
		$row->name == 'salt' ? $salt = $row->varValue : $url = $row->varValue;

		if(!$salt || $salt == '' || !$url || $url == ''){
			$msg = "You have to fill out the salt and url in the settings menu before you can delete a meeting.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
			return;
		}

		$response = BigBlueButton::endMeeting( $meeting->meetingName."[".$meeting->meetingVersion."]", $meeting->moderatorPW, $url, $salt);
		
		//Analyzes the bigbluebutton server's response
		if(!$response){//If the server is unreachable, then prompts the user of the necessary action
			$msg = "Unable to join the meeting. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
		else if( $response['returncode'] == 'FAILED' && $response['messageKey'] != 'notFound'  ) { //The meeting was not created
			if($response['messageKey'] == 'checksumError'){
				$msg = "A checksum error occured. Make sure you entered the correct salt.";
			}
			else{
				$msg = $response['message'];
			}
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
		else{ //The meeting was created, and the user will now be joined
			$query = "DELETE FROM #__bbb WHERE id=".$id;
			$db->setQuery( $query );
			$db->query();
			
			$msg = $meeting->meetingName." has been deleted.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
	}
	
	
	/**
	 * Ends a meeting
	 */
	function end()
	{	
		//get the meeting
		$id = JRequest::getVar('id');
		$query = ' SELECT * FROM #__bbb WHERE id ='.$id;
		$db =& JFactory::getDBO();
		$db->setQuery( $query );
		$meeting = $db->loadObject();
			
		if(!$meeting || $meeting == null){
			$msg = "This meeting no longer exists.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
		
		// This is the security salt that must match the value set in the BigBlueButton server
		// This is the URL for the BigBlueButton server
		$row =& JTable::getInstance('settings', 'Table');
		$row->load(1);
		$row->name == 'salt' ? $salt = $row->varValue : $url = $row->varValue;
		$row->load(2);
		$row->name == 'salt' ? $salt = $row->varValue : $url = $row->varValue;

		if(!$salt || $salt == '' || !$url || $url == ''){
			$msg = "You have to fill out the salt and url in the settings menu before you can delete a meeting.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
			return;
		}

		$response = BigBlueButton::endMeeting( $meeting->meetingName."[".$meeting->meetingVersion."]", $meeting->moderatorPW, $url, $salt);
		
		//Analyzes the bigbluebutton server's response
		if(!$response){//If the server is unreachable, then prompts the user of the necessary action
			$msg = "Unable to join the meeting. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
		else if( $response['returncode'] == 'FAILED' ) { //The meeting was not created
			if($response['messageKey'] == 'checksumError'){
				$msg = "A checksum error occured. Make sure you entered the correct salt.";
			}
			else{
				$msg = $response['message'];
			}
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
		else{ //The meeting was created, and the user will now be joined
			$msg = $meeting->meetingName." has been terminated.";
			$this->setRedirect( 'index.php?option=com_bigbluebuttonconferencing', $msg );
		}
	}
}