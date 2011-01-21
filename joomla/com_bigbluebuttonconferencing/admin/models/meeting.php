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

jimport('joomla.application.component.model');

/**
 * Meetings Meeting Model
 */
class MeetingsModelMeeting extends JModel
{
	/**
	 * Constructor that retrieves the id from the request

	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the  meeting id
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to get a meeting
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM #__bbb WHERE id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->meetingName = null;
		}
		return $this->_data;
	}
	
	/**
	 * Method to store a record
	 */
	function store()
	{
		$row =& $this->getTable();
		$data = JRequest::get( 'post' );
		
		//Checks to see if either of them are blank
		if($data['meetingName'] == "" || $data['moderatorPW'] == "" || $data['attendeePW'] == "" ){
			$this->setError("All fields must be filled.");
			return false;
		}
		
		//Makes sure there isn't duplicate meetings.
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__bbb";
		$db->setQuery($query);
		$list = $db->loadObjectList();

		foreach ($list as $item) {
			if($data['meetingName'] == $item->meetingName){
				$this->setError($item->meetingName." already exists. Please use a different name.");
				return false;
			}
		}
		
		if($data['waitForModerator'] != 'yes') $data['waitForModerator'] = 'no';
		
		// Bind the form fields to the meeting table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	 
		// Make sure the meeting record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	 
		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	 
		return true;
	}
	
	/**
	 * Method to delete record(s)
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$row =& $this->getTable();
	 
		foreach($cids as $cid) {
			if (!$row->delete( $cid )) {
				$this->setError( $row->getErrorMsg() );
				return false;
			}
		}
	 
		return true;
	}

}