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
 
jimport( 'joomla.application.component.model' );
 
/**
 * BigBlueButton Model
 */
class BigBlueButtonModelMeeting extends JModel
{
    /**
    * Gets the greeting
    * @return string The greeting to be displayed to the user
    */
    function getMeeting()
    {
		db =& JFactory::getDBO();
 
		$query = 'SELECT meeting FROM #__bbb';
		$db->setQuery( $query );
		$meeting = $db->loadResult();

		return $meeting;

    }
}
