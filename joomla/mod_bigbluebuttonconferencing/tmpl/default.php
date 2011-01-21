<?php // no direct access
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

defined( '_JEXEC' ) or die( 'Restricted access' ); 

if(!$msg['message'] || $msg['message'] != "redirect"){

if($msg['message']){
	echo $msg['message'];
}

	if(!$listOfMeetings){
		echo "No meeting rooms are currently available to join.";
	}else {
		?>
		<form name="form1" method="post" action="">
			<table>
				<tr>
					<td>Meeting</td>
					<td>
						<select name="meetingName">
							<?php
							foreach ($listOfMeetings as $meeting) {
								echo "<option>".$meeting->meetingName."</option>";
							}
							?>
						</select>
				</tr>
				<tr>
					<td>Name</td>
					<td><INPUT type="text" id="name" name="display_name" size="10"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><INPUT type="password" name="pwd" size="10"></td>
				</tr>
			</table>
			<INPUT type="submit" name="Submit" value="Join">
		</form>		
		<br \>
		<?php
	}

} else {

?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo './modules/mod_bigbluebuttonconferencing/js/heartbeat.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo './modules/mod_bigbluebuttonconferencing/js/md5.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo './modules/mod_bigbluebuttonconferencing/js/jquery.xml2json.js'; ?>"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$.jheartbeat.set({
				url: './modules/mod_bigbluebuttonconferencing/php/check.php?meetingID=<?php echo urlencode($msg["meetingID"]); ?>',
				delay: 5000
			}, function () {
			mycallback();
			});
		});


		function mycallback() {
			// Not elegant, but works around a bug in IE8
			var isMeetingRunning = ($("#HeartBeatDIV").text().search("true") > 0 );

			if (isMeetingRunning) {
				window.location = "<?php echo $msg['bbb_joinURL']; ?>";
			}
		}
	</script>

	<table>
		<tbody>
			<tr>
				<td>
					<p>Hi <?php echo $msg['name']; ?>,</p>
					<br />
					<p>Now waiting for the moderator to start the meeting.</p>
					<br />
					<center><img src="<?php echo './modules/mod_bigbluebuttonconferencing/images/polling.gif'; ?>" /></center>
					<br />
					<p>(Your browser will automatically refresh and join the meeting when it starts.)</p>
				</td>
			</tr>
		</tbody>
	</table>
<?php
}