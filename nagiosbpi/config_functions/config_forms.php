<?php  //config_forms.php   form functions for configuration editor for Nagios BPI

// Nagios BPI (Business Process Intelligence) 
// Copyright (c) 2010 Nagios Enterprises, LLC.
// Written by Mike Guthrie <mguthrie@nagios.com>
//
// LICENSE:
//
// This work is made available to you under the terms of Version 2 of
// the GNU General Public License. A copy of that license should have
// been provided with this software, but in any event can be obtained
// from http://www.fsf.org.
// 
// This work is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
// General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
// 02110-1301 or visit their web page on the internet at
// http://www.fsf.org.
//
//
// CONTRIBUTION POLICY:
//
// (The following paragraph is not intended to limit the rights granted
// to you to modify and distribute this software under the terms of
// licenses that may apply to the software.)
//
// Contributions to this software are subject to your understanding and acceptance of
// the terms and conditions of the Nagios Contributor Agreement, which can be found 
// online at:
//
// http://www.nagios.com/legal/contributoragreement/
//
//
// DISCLAIMER:
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
// INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
// PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
// HOLDERS BE LIABLE FOR ANY CLAIM FOR DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
// OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
// GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, STRICT LIABILITY, TORT (INCLUDING 
// NEGLIGENCE OR OTHERWISE) OR OTHER ACTION, ARISING FROM, OUT OF OR IN CONNECTION 
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.



function empty_form()
{
	global $objects;
	global $service_details;
		//begin heredoc string 
	$outputform=<<<OUTPUTFORM
	
	<form id='outputform' method='post' action='{$_SERVER['PHP_SELF']}?cmd=add'>
	  <div class='floatLeft'> 
		<label for='groupIdInput'>*Group ID (Alphanumeric, no spaces)</label>
								<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="The Group ID is a unique identifier used internally by Nagios BPI.  Only alpha-numeric characters are allowed.  Spaces are not allowed." /><br />
			
			<input id='groupIdInput' type='text' name='groupID' value='' /><br />
		<label for='groupTitleInput'>*Display Name</label><br />
			<input id='groupTitleInput' type='text' name='groupTitle' value='' /><br />
		<label for='groupDescInput'>Group Description</label><br />
			<input id='groupDescInput' type='text' name='groupDesc' value='' /><br />
			
		<label for='groupInfoUrl'>Info URL</label><br />
			<input id='groupInfoUrl' type='text' name='groupInfoUrl' /><br /><br />			
		
		<label for='groupPrimaryInput'>Primary Group</label>
				<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="Primary Groups are visible on the top level. Non-primary groups must be added as a child member to a visible group in order to be displayed in the tree" /> <br />
			
			<input id='groupPrimaryInput' type='checkbox' checked='checked' name='groupPrimary' value='true' /><br />			
		<!-- WARNING THRESHOLD -->	
		<label for='groupWarn'>Warning Threshold</label>
									<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="Warning Threshold is the count of problems that must be reached in a group before the state changes to 'Warning.' Leaving this at '0' will ignore this check." /><br />
			
			<select id='groupWarn' name='groupWarn'>
				<option id='wc0' value='0'>0</option>
			</select>
			<br />
		<!-- CRITICAL THRESHOLD -->	
		<label for='groupCrit'>Critical Threshold</label>
									<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="Critical Threshold is the count of problems that must be reached in a group before the state changes to 'Critical.' Leaving this at '0' will ignore this check." /><br />
			
			<select id='groupCrit' name='groupCrit'>
				<option id='cc0' value='0'>0</option>
			</select>
			<br />
			
		<label for='groupDisplayInput'>Display Priority</label><br />	
			<select id='groupDisplayInput' name='groupDisplay'>
				<option value='1'>High</option>
				<option value='2'>Medium</option>
				<option value='3'>Low</option>
			</select><br /><br />
					
OUTPUTFORM;
	//end heredoc string
	print $outputform;
	
	print "<label for='multiple'>Available Groups and Services</label><br />
				<select id='multiple' multiple='multiple' size='10'>";

	//add groups to select list as options 
	foreach($objects as $object)
	{
		print "<option value='$".$object->name."'>".$object->title." (Group) </option>\n";
	} 
	//add hostname;service to select list 
	foreach($service_details as $service)
	{
		//create the identifier for a service 
		$var = trim($service['host_name']).';'.trim($service['service_description']);
		print "<option value='$var'>$var</option>\n";
	}
		
	print "</select><br />";	
	
	print "<p><a href='javascript:void(0)' onclick='dostuff()'>Add Member(s) 
				<img width='13' height='8' alt='=>' title='Add Member(s)' src='images/children.png' /></a></p>";
	print "<p class='note'> * denotes required field</p>";
	print "</div>"; //end float left 
	
	//RIGHT FORM 	
	//begin heredoc string 
	$rightForm=<<<RIGHTFORM
	
		<div class="floatRight">
		<label for='selectoutput'>*Group Members:</label>
			<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
				title="Group Members can be services or other groups. 'Essential' members can decide the entire group's state. If an essential member's state is 'Critical' or 'Unknown', the parent group is listed as 'Critical.' Non-essential members are clustered together and the parent group's state is determined by the threshold settings." />
		
		<a id="clearMembersLink" onclick="clearMembers()" href="javascript:void(0)">Clear All</a><br />		
		<table id='selectoutput'>
		<tr><th>Member Name</th><th>Essential Member</th><th>Remove</th></tr> 	
				
			<!-- insert javascript content here -->  	
		</table>
		<p><a href='javascript:void(0)' onclick="submitForm()">Write Configuration</a></p>
		<input type='hidden' name='addSubmitted' value='true' />
		</div>
		</form>	

RIGHTFORM;
	//end heredoc 	
	print $rightForm;
} //end function empty_form() 

//////////////////////////////////////////////////////////////////////////////
//expecting array from get_config_array() that has relevant details for the form   
//get_config_array() is located on 
function loaded_form($array)
{
	global $objects;
	global $service_details;
		//begin heredoc string 
	$primary = $array['primary'] == 1 ? " checked='checked' " : '' ;
	$priority = $array['priority'];
	$warn = $array['warning_threshold'];
	$crit = $array['critical_threshold'];
	//call object to get properties 
	$obj = $objects[$array['groupID']];
	
	//TODO
	//do switch for the Display Priority that is selected 
	//get a members count
	//add options to warning count for each member -> if WT is a match, added the 'selected' attribute 
	//foreach member add the table entry with symbol removed.  Use symbol to determine checkboxes 
		
		
	$outputform=<<<OUTPUTFORM
	
	<form id='outputform' method='post' action='{$_SERVER['PHP_SELF']}?cmd=edit&arg={$array['groupID']}'>
	  <div class='floatLeft'> 
		<label for='groupIdInput'>*Group ID</label>
		<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="The Group ID is a unique identifier used internally by Nagios BPI.  Only alpha-numeric characters are allowed.  Spaces are not allowed." />
		<br />			

			<input id='groupIdInput' type='text' disabled='disabled' name='groupIDdisabled' value="{$array['groupID']}" />
			<input type='hidden' name='groupID' value="{$array['groupID']}" />
			<br />			
		<label for='groupTitleInput'>*Display Name</label><br />
			<input id='groupTitleInput' type='text' name='groupTitle' value="{$array['title']}" /><br />
		<label for='groupDescInput'>Group Description</label><br />
			<input id='groupDescInput' type='text' name='groupDesc' value="{$array['desc']}" /><br />
			
		<label for='groupInfoUrl'>Info URL</label><br />
			<input id='groupInfoUrl' type='text' name='groupInfoUrl' value="{$array['info']}" /><br /><br />			
		
		<label for='groupPrimaryInput'>Primary Group</label>
		<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="Primary Groups are visible on the top level. Non-primary groups must be added as a child member to a visible group in order to be displayed in the tree" /> <br />

				
			<input id='groupPrimaryInput' type='checkbox' name='groupPrimary' value='true' {$primary} /><br />			
		<!-- WARNING THRESHOLD -->	
		<label for='groupWarn'>Warning Threshold</label>
			<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="Warning Threshold is the count of problems that must be reached in a group before the state changes to 'Warning.' Leaving this at '0' will ignore this check." />
		
		<br />
			<select id='groupWarn' name='groupWarn'>
				<option id='wc0' value='0'>0</option>
			</select>
			<br />
			
		<!-- CRITICAL THRESHOLD -->	
		<label for='groupCrit'>Critical Threshold</label>
									<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
						title="Critical Threshold is the count of problems that must be reached in a group before the state changes to 'Critical.' Leaving this at '0' will ignore this check." /><br />
			
			<select id='groupCrit' name='groupCrit'>
				<option id='cc0' value='0'>0</option>
			</select>
			<br />			
			
			
			
		<label for='groupDisplayInput'>Priority</label><br />
			<select id='groupDisplayInput' name='groupDisplay'>
			
OUTPUTFORM;
	//end heredoc string
	print $outputform;
	
	//switch for display priority 
	switch($priority)
	{
		case 2:
		print "<option value='1'>High</option>
				<option selected='selected' value='2'>Medium</option>
				<option value='3'>Low</option>";
		break;
		case 3:
		print "<option value='1'>High</option>
				<option value='2'>Medium</option>
				<option selected='selected' value='3'>Low</option>";
		break;
		default:
		print "<option selected='selected' value='1'>High</option>
				<option value='2'>Medium</option>
				<option value='3'>Low</option>";
		break;
		
	}

	//close priority select list 
	print "</select><br /><br />";
					

	
	//////////////////////////////////Select list for all groups and services ///////////////////
	print "<label for='multiple'>Available Groups and Services</label><br />
				<select id='multiple' multiple='multiple' size='10'>";

	//add groups to select list as options 
	foreach($objects as $object)
	{
		print "<option value='$".$object->name."'>".$object->title." (Group) </option>\n";
	} 
	//add hostname;service to select list 
	foreach($service_details as $service)
	{
		//create the identifier for a service 
		$var = trim($service['host_name']).';'.trim($service['service_description']);
		print "<option value='$var'>$var</option>\n";
	}		
	print "</select><br />";	
	///////////////////////////////////end select list ///////////////////////////
	print "<p><a href='javascript:void(0)' onclick='dostuff()'>Add Member(s) 
				<img width='13' height='8' alt='=>' title='Add Member(s)' src='images/children.png' /></a></p>";
	print "<p class='note'> * denotes required field</p>";
	print "</div>"; //end float left 
	
	//RIGHT FORM 
	//begin heredoc string 	
	$rightform=<<<RIGHTFORM
		
		<div class='floatRight'>
		<label for='selectoutput'>*Group Members:</label>
			<img class="tooltip" src='images/tip.gif' width="15" height="15" alt="Info"
				title="Group Members can be services or other groups. 'Essential' members can decide the entire group's state. If an essential member's state is 'Critical' or 'Unknown', the parent group is listed as 'Critical.' Non-essential members are clustered together and the parent group's state is determined by the threshold settings." />

			<a id="clearMembersLink" onclick="clearMembers()" href="javascript:void(0)">Clear All</a>
		<br />		
		<table id='selectoutput'>
		<tr><th>Member Name</th><th>Essential Member</th><th>Remove</th></tr> 					
			<!-- insert javascript content here -->	
		</table>
		<p><a href='javascript:void(0)' onclick='submitForm()'>Write Configuration</a></p>
		<input type='hidden' name='editSubmitted' value='true' />
		</div>
		</form>
RIGHTFORM;
	//end heredoc 
	print $rightform; 
	
	//add members upon page load through the javascript function 
	$members = $obj->get_memberlist(); //get group members array 
	//print_r($members);
	print "\n<script type='text/javascript'>\n";
	//loop through members and print javascript for group or service 
	foreach($members as $member)
	{
		if($member['type'] == 'group')
		{
			//do group stuff
			$title = $member['title'].' (Group)';
			$value = '$'.$member['index'];
			$opt = $member['option'];
			print "\npreload('$title','$value', '$opt');\n";
			
		}
		if($member['type'] == 'service')
		{
			//do service stuff
			$title = $member['host_name'].';'.$member['service_description'];
			$value = $member['host_name'].';'.$member['service_description'];
			$opt = $member['option'];
			print "\npreload('$title','$value','$opt');\n";
			
		}
	}
	//set the warning value via javascript 
	print "\nsetThresholds('$warn', '$crit')\n;";
	//close script 
	print "\n</script>\n";
} //end function loaded_form() 


?>