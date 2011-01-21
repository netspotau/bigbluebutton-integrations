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
		
		<?php
		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row =& $this->items[$i]; 
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="100" align="right" class="key">
					<label>
						<?php echo JText::_($row->opening); ?>
					</label>
				</td>
				<td>
					<input class="text_area" type="text" name="<?php echo $row->name;?>" id="<?php echo $row->name;?>" size="50" maxlength="250" value="<?php echo $row->varValue;?>" />
				</td>
				<td>
					<?php echo JText::_($row->closing); ?>
				</td>
				<input type="hidden" name="<?php echo $row->name.'_id'; ?>" value="<?php echo $row->id; ?>" />
			</tr>
			
			<?php
			$k = 1 - $k;
		}
		?>
    </table>
    </fieldset>
</div>
 
<div class="clr"></div>
 
<input type="hidden" name="option" value="com_bigbluebuttonconferencing" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="settings" />
</form>
