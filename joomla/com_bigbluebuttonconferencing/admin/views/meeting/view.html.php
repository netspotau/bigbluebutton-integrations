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

/**
 *Meetings View for BigBlueButton Component
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
 
jimport( 'joomla.application.component.view' );
 
/**
 * Meetings View
 */
class MeetingsViewMeeting extends JView
{
    /**
 * display method of meeting view
 * @return void
 **/
function display($tpl = null)
{
    //get the meeting
    $meeting        =& $this->get('Data');
  
    JToolBarHelper::title(   JText::_( 'BigBlueButton Conferencing' ).': <small><small>[ ' .JText::_( 'New' ).' ]</small></small>' );
    JToolBarHelper::save();
    JToolBarHelper::cancel();
 
	$this->assignRef('meeting', $meeting);
	
    parent::display($tpl);
}

}
