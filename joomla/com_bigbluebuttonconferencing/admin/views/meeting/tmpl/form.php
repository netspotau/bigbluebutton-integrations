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

defined('_JEXEC') or die('Restricted access'); ?>
 
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Details' ); ?></legend>
        <table class="admintable">
        <tr>
            <td width="100" align="right" class="key">
                <label>
                    <?php echo JText::_( 'Meeting Name' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="meetingName" id="meetingName" size="32" maxlength="250" />
            </td>
        </tr>
		<tr>
            <td width="100" align="right" class="key">
                <label>
                    <?php echo JText::_( 'Moderator Password' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="moderatorPW" id="moderatorPW" size="32" maxlength="250" />
            </td>
        </tr>
		<tr>
            <td width="100" align="right" class="key">
                <label>
                    <?php echo JText::_( 'Attendee Password' ); ?>:
                </label>
            </td>
            <td>
                <input class="text_area" type="text" name="attendeePW" id="attendeePW" size="32" maxlength="250" />
            </td>
        </tr>
		<tr>
            <td width="100" align="right" class="key">
                <label>
                    <?php echo JText::_( 'Wait For Moderator to start meetings' ); ?>:
                </label>
            </td>
            <td>
				<input type="checkbox" name="waitForModerator" value='yes'/>
            </td>
        </tr>
    </table>
    </fieldset>
</div>
 
<div class="clr"></div>
 
<input type="hidden" name="option" value="com_bigbluebuttonconferencing" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="meeting" />
</form>
