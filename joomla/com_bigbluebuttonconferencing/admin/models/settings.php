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
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_bigbluebuttonconferencing'.DS.'tables');


/**
 * Meetings Settings Model
 */
class MeetingsModelSettings extends JModel
{

	/**
     * @var array
     */
    var $_data;

	
    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */
    function _buildQuery()
    {
        $query = ' SELECT * '
            . ' FROM #__bbb_settings '
        ;
        return $query;
    }
 
    /**
     * Retrieves the settings data
     * @return array Array of objects containing the data from the database
     */
    function getData()
    {
        // Lets load the data if it doesn't already exist
        if (empty( $this->_data ))
        {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList( $query );
        }
 
        return $this->_data;
    }
		
	/**
	 * Method to store a record
	 */
	function store()
	{
		$row =& $this->getTable();
	 
		$data = JRequest::get('post');
		//Checks to see if either of them are blank
		if($data['salt'] == "" || $data['url'] == ""){
			$this->setError("All fields must be filled.");
			return false;
		}
		
		$salt = array('id' => $data['salt_id'], 'varValue' => $data['salt']);
		
		if(strripos($data['url'], "/bigbluebutton/") == false){
			if(substr($data['url'], -1) == "/"){
				$data['url'] .= "bigbluebutton/";
			}
			else{
				$data['url'] .= "/bigbluebutton/";
			}
		}
		
		$url = array('id' => $data['url_id'], 'varValue' => $data['url']);
		
		// Bind the form fields to the settings table
		if (!$row->bind($salt)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// Make sure the salt record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		
		// Bind the form fields to the settings table
		if (!$row->bind($url)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	 
		// Make sure the url record is valid
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
}