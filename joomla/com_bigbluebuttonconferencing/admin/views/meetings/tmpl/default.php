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

defined('_JEXEC') or die('Restricted access'); 

require_once( JPATH_COMPONENT.DS.'includes'.DS.'bbb_api.php');
$msg = null;
if(!$this->salt || $this->salt == '' || !$this->url || $this->url == '' ){
	$msg = "You have to fill out the salt and url in the settings menu before you can begin.";
}
else if( count($this->items) > 0){
	$printed = false;
    $k = 0;
	
    for ($i=0, $n=count( $this->items ); $i < $n; $i++)
    {
        $row =& $this->items[$i];
        $checked    = JHTML::_( 'grid.id', $i, $row->id );
		$joinLink = JRoute::_( 'index.php?option=com_bigbluebuttonconferencing&controller=meeting&task=join&id='. $row->id );
		$endLink = JRoute::_( 'index.php?option=com_bigbluebuttonconferencing&controller=meeting&task=end&id='. $row->id );
		$deleteLink = JRoute::_( 'index.php?option=com_bigbluebuttonconferencing&controller=meeting&task=delete&id='. $row->id );

		$response = BigBlueButton::getMeetingInfoArray( $row->meetingName.'['.$row->meetingVersion.']', $row->moderatorPW, $this->url, $this->salt );
		
		if(!$response){//If the server is unreachable, then prompts the user of the necessary action
			$msg = "Unable to display the meetings. Please check the url of the bigbluebutton server AND check to see if the bigbluebutton server is running.";
			break;
		}
		else if( isset($response['returncode']) && $response['returncode'] == 'FAILED' && $response['messageKey'] != 'notFound' &&  $response['messageKey'] != 'invalidPassword') { //The meeting was not created
			if($response['messageKey'] == 'checksumError'){
				$msg = "A checksum error occured. Make sure you entered the correct salt.";
			}
			else{
				$msg = $response['message'];
			}
			break;
		}
		else {
			if( ( isset($response['returncode']) && $response['returncode'] == 'FAILED' && $response['messageKey'] == 'notFound') 
				|| (isset($response['returncode']) && $response['returncode'] == 'FAILED' && $response['messageKey'] == 'invalidPassword')
				|| ($response['running'] == 'false') ) $running = false;
			else $running = true;
			
		if(!$printed){
			$printed = true;
			?>
			<form action="index.php" method="post" name="adminForm">
			<div id="editcell">
				<table class="adminlist">
				<thead>
					<tr>
						<th width="20">
						  <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
						</th>
						<th>
							<?php echo JText::_( 'Meeting Room Name' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'Wait For Moderator' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'Moderator Password' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'Attendee Password' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'Running?' ); ?>
						</th>
						<th>
							<?php echo JText::_( 'Actions' ); ?>
						</th>
					</tr>            
				</thead>
			<?php
		}
		
        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td align="center" >
              <?php echo $checked; ?>
            </td>
            <td align="center">
                <?php echo $row->meetingName; ?>
            </td>
			<td align="center">
                <?php 
				if($row->waitForModerator == 'yes') echo "Yes";
				else echo "No"; 
				?>
            </td>
			<td align="center">
                <?php echo $row->moderatorPW; ?>
            </td>
			<td align="center">
                <?php echo $row->attendeePW; ?>
            </td>
			<td align="center">
                <?php 
				if($running) echo "Yes"; 
				else echo "No"; 
				?>
            </td>
			<td align="center">
                <a href="<?php echo $joinLink; ?>"><?php echo JText::_( 'Join' ); ?></a>
				<?php if($running){ ?> <a href="<?php echo $endLink; ?>"><?php echo JText::_( 'End' ); ?></a><?php } ?>
				<a href="<?php echo $deleteLink; ?>"><?php echo JText::_( 'Delete' ); ?></a>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
		}
    }
	
	if(!$msg){
		?>
			</table>
		</div>
		<?php
	}
}
else{
	$msg = "There are no meeting rooms.";
}

if($msg){
?> 
<form action="index.php" method="post" name="adminForm">
	<dl id="system-message">
		<dt class="message">Message</dt>
		<dd class="message message fade">
			<ul>
				<li><?php echo $msg; ?></li>
			</ul>

		</dd>
	</dl>
<?php
}
?>
	<input type="hidden" name="option" value="com_bigbluebuttonconferencing" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="meeting" />
</form>