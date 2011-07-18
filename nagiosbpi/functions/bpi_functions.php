<?php //bpi_functions.php  main functions for Nagios BPI 

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


//function to trim spaces from array using array_walk()
function trim_value(&$value) 
{ 
    $value = trim($value); 
}


function set_perms()
{
	exec('set_bpi_perms.sh');
}


function bpi_init()
{ 
	global $objects;
	global $obj_count;
	global $info;
	global $statecount;
	global $config;
	//initialize all BpGroup instances 
	foreach($info as $key => $value)
	{			
		$Grp = new BpGroup($value, $key);
		$objects[$key] = $Grp;	
		$obj_count++;
	}
	
	//only display objects with display>0 for initial loop, then unpack groups after that
	//print "<h3>Processing Children...</h3>"; 

	{
		foreach($objects as $object)
		{
			$object->process_children();
		}
	}
	//$statecount = 0;
	//print "<h3>Getting object states...</h3>";
	if($config==true)
	{
		$loopstopper = 0; //safety variable to prevent a bad config from endlessly looping and hanging the browser 
		while($statecount != $obj_count && $loopstopper < ($obj_count+10))
		{
			$loopstopper++; 
			//continue looping through objects until status is determined for all groups 
			foreach($objects as $object)
			{
				$object->drill_down();	
				//$statecount increases each time an object's state is determine  
			}
			//print "<h4>STATECOUNT: $statecount</h4>";
		}	
		//error handling 
		if($loopstopper==($obj_count+10))
		{
			print "<p class='error'>Error determining status for all groups, too many iterations.  Check configuration file.</p>";
		}
	}
	else echo "CONFIG IS FALSE"; 
	
	//optionally output xml data 
	if(defined('XMLOUTPUT'))
	{
		@write_bpi_xml();
	}
	
}


//main display function for all bpi group trees 
function bpi_view($arg)
{
	global $objects;
	global $unique;
	foreach($objects as $object)
	{
		$prime = $object->get_primary();
		$priority = $object->priority;
		//echo "<p>$disp</p>";
		if($prime > 0 && $priority==$arg)
		{
			$title = $object->get_title();
			$state = return_state($object->state);
			if($object->has_group_children==true)
			{ $gpc_icon = "<th><img src='images/children.png' title='Contains Child Groups' height='8' width='13' alt='C' /></th>"; }
			else 
			{ $gpc_icon = ''; }
			$id = $object->name;
			$desc=$object->desc;
			$td_id = 'td'.$unique;
			$info_th=$object->get_info_html();
			$problems = $object->problems;	
			//display for only primary groups.  See the $object->display_tree() for subgroup displays 		
			//begin heredoc string 			
			$table=<<<TABLE
				 <table class='primary'>
						<tr>
							<th class='{$state}'>{$state}</th>
							<th class='group' >
							<a id='{$td_id}' href='javascript:void(0)' title="Group ID: {$id}" onclick='showHide("$id","$td_id")' class='grouphide'>{$title}</a></th>
							{$gpc_icon}
							{$info_th}
							<th>{$problems} problem(s)</th>							
							<th>{$desc}</th>
							<td><a href='index.php?cmd=edit&arg={$id}'>Edit</a></td>
							<td><a href="javascript:deleteGroup('index.php?cmd=delete&arg={$id}')">Delete</a></td>
						</tr>
					</table>
					
TABLE;
			print $table;
			//end heredoc string 
			//recursively display groups 
			print "<div class='hidden' id='$id'>";		
			$object->display_tree();
			print "</div>\n";		
			$unique++;	
		}
	}
}

/////////////////////////////////////////////////////////////////////////////
//expecting int 0-3
//returns state code: OK, WARNING, CRITICAL, UNKNOWN 
function return_state($arg)
{
	switch($arg)
	{
		case 0:
		$state = "Ok";
		break;
		
		case 1:
		$state = "Warning";
		break;
		
		case 2:
		$state = "Critical";
		break;
		
		case 3:
		$state = "Unknown";
		break;
		
		default:
		$state = "Unknown";
		break;
		
	}//end switch 
	return $state;
}//end method return_service_state() 



function bpi_page_router()
{
	global $config;
	//processes $_GET and $_POST data 
	if(isset($_GET['cmd']))
	{
		$cmd = htmlentities($_GET['cmd']); //clean data 
		bpi_route_command($cmd);
	}//end IF 
	elseif(isset($_GET['filter']))
	{
		$filter=htmlentities($_GET['filter']);
		
		page_filter($filter);		
	}
	
	/////////////////////////DEFAULT//////////////////////////////////	
	else //no get variables, page defaults to 'view' mode 
	{	
		//////Default Display/////
		print "<div id='addgrouplink'><a href='index.php?cmd=add'>Create A New Group</a><br />
		
				   <p class='note'>Nagios BPI v".VERSION." 
				     <br />written by Mike Guthrie
					 <br />Nagios Enterprises
					 <a href='http://support.nagios.com/forum/' title='Nagios Forums'>Support Forum</a></p>
			  </div>
				<div id='notes'><p class='note'>Essential group members are denoted with: **</p></div>
				<h4 class='header'><a href='index.php?filter=1'>High Priority</a></h4>\n";
		bpi_view('1'); //display all group trees 
		print "<h4 class='header'><a href='index.php?filter=2'>Medium Priority</a></h4>\n";
		bpi_view('2');
		print "<h4 class='header'><a href='index.php?filter=3'>Low Priority</a></h4>\n";
		bpi_view('3');
		/////End default display/////
	}//end main IF 
}//end bpi_page_router()  



function send_home() //redirects user to index page 
{
	header('Location: '.BASEURL);
}



function page_filter($filter)
{
	print "<div id='addgrouplink'><a href='index.php?cmd=add'>Create A New Group</a></div>";
	print "<div id='notes'><p class='note'>Essential group members are denoted with: **</p></div>";
	switch($filter)
	{
		case 1:
		print "<h4 class='header'><a href='index.php?filter=1'>High Priority</a></h4>\n";
		print "<h5 class='header'><a href='index.php'>Show All Groups</a></h5>\n";
		bpi_view('1'); //display all group trees 
			
		break;
		
		case 2:
		print "<h4 class='header'><a href='index.php?filter=2'>Medium Priority</a></h4>\n";
		print "<h5 class='header'><a href='index.php'>Show All Groups</a></h5>\n";
		bpi_view('2');
		break;
		
		case 3:
		print "<h4 class='header'><a href='index.php?filter=3'>Low Priority</a></h4>\n";
		print "<h5 class='header'><a href='index.php'>Show All Groups</a></h5>\n";
		bpi_view('3');
		break;
		
		default:
		send_home();
		break;
		
	}

}

function error_check()
{
	global $errors;
	global $config; 
	global $bpiroot; 
	if(!is_writable(CONFIGFILE)) 
	{
		print "<p class='error'>File bpi.conf is not writable!  Please execute the following commands as the root user to set correct permissions: </p>"; 
		print "<pre>
		cd $bpiroot 
		chmod +x set_bpi_perms.sh
		./set_bpi_perms.sh 
		</pre><br />"; 
	} 

	//handler for bad configurations 
	//make this form disappear once fixconfig is accessed 
	if($config!=true || $errors != '')
	{
		//print_r($errors);
		//allow for manual editing of the configuration file, and send error messages to that page 

        if(isset($_POST['errors']) || isset($_POST['configeditor']) || isset($_GET['cmd']) ) return;  //do nothing
        else
		{	//submit error messages as posts 
			print "<p class='error'>WARNING: Errors in configuration file.</p>
					<form id='errorlog' method='post' action='index.php?cmd=fixconfig'>
					   <input type='submit' value='Edit Configuration File' name='submit' />
					   <input type='hidden' name='errors' value=\"$errors\" />
					</form>"; 
		}
		
	}
}



?>