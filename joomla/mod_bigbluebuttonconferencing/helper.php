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

require_once( dirname(__FILE__).DS.'php'.DS.'bbb_api.php' );

class modBigBlueButtonHelper{

    function getForm($params){
        //Makes sure there isn't duplicate meetings.
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__bbb";
		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
    }
	
    function join( $params ){
	
		$meetingName = JRequest::getVar( 'meetingName' );
		$name = JRequest::getVar( 'display_name' );
		$pwd = JRequest::getVar( 'pwd' );
		
		$query = "SELECT * FROM #__bbb WHERE meetingName ='".$meetingName."'";
		$db =& JFactory::getDBO();
		$db->setQuery( $query );
		$meeting = $db->loadObject();
			
		if(!$meeting || $meeting == null){
			$msg['message'] = "This meeting no longer exists.";
			return $msg;
		}
		
		if($meeting->moderatorPW != $pwd && $meeting->attendeePW != $pwd){
			$msg['message'] = "Incorrect Password.";
			return $msg;
		}
		
		
		// This is the security salt that must match the value set in the BigBlueButton server
		// This is the URL for the BigBlueButton server
		
		$query = "SELECT * FROM #__bbb_settings WHERE name = 'salt'";
		$db->setQuery( $query );
		$salt = $db->loadObject();
		
		$query = "SELECT * FROM #__bbb_settings WHERE name = 'url'";
		$db->setQuery( $query );
		$url = $db->loadObject();

		if(!$salt || $salt->varValue == '' || !$url || $url->varValue == ''){
			$msg['message'] = "You have to fill out the salt and url in the settings menu before you can join a meeting.";
			return $msg;
		}
		
		
		for(;;){

			$response = BigBlueButton::createMeetingArray($name, $meeting->meetingName."[".$meeting->meetingVersion."]", 'Welcome to '.$meeting->meetingName.'.', $meeting->moderatorPW, $meeting->attendeePW, $salt->varValue, $url->varValue, JURI::base() );		
			//Analyzes the bigbluebutton server's response
			if(!$response){//If the server is unreachable, then prompts the user of the necessary action
				$msg['message'] = "Unable to join the meeting. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running.";
				return $msg;
			}
			else if( $response['returncode'] == 'FAILED' && $response['messageKey'] != 'idNotUnique') { //The meeting was not created
				if($response['messageKey'] == 'checksumError'){
					$msg['message'] = "A checksum error occured. Make sure you entered the correct salt.";
				}
				else{
					$msg['message'] = $response['message'];
				}
				return $msg;
			}
			else if( $response['messageKey'] == 'idNotUnique' || $response['hasBeenForciblyEnded'] == 'true'){
				$meeting->meetingVersion = time();
				
				$db->updateObject('#__bbb',$meeting,'id',false);
				if ($db->getErrorNum()) {  
					$msg['message'] = $db->getErrorMsg();  
					return $msg;
				}			
			}
			else{ //The meeting was created, and the user will now be joined
				$bbb_joinURL = BigBlueButton::joinURL($meeting->meetingName."[".$meeting->meetingVersion."]", $name,$pwd, $salt->varValue, $url->varValue );
				
				
				if($meeting->waitForModerator == 'yes' && $meeting->moderatorPW != $pwd && !BigBlueButton::isMeetingRunning( $meeting->meetingName."[".$meeting->meetingVersion."]", $url->varValue, $salt->varValue )){
									
					$msg['message'] = "redirect";
					$msg['bbb_joinURL'] = $bbb_joinURL;
					$msg['name'] = $name;
					//$msg['meetingID'] = urlencode($meeting->meetingName."[".$meeting->meetingVersion."]");
					$msg['meetingID'] = BigBlueButton::isMeetingRunningURL( $meeting->meetingName."[".$meeting->meetingVersion."]",$url->varValue, $salt->varValue );
					
					return $msg;
				
				}
				
				?><script type="text/javascript"> window.location = "<?php echo $bbb_joinURL ?>";</script><?php
				return;
			}
		}
    }
}
?>
