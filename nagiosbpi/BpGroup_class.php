<?php //main group class Nagios Business Process Intelligence addon 

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


class BpGroup
{
	//global $objects;  //call global objects array 
	var $name; //index key for global objects array 
	var $service_children = array(); //use method to add processed service arrays to this array 
	var $group_children = array();    //use method to add processed group arrays to this array
	var $memberlist = array();      //main member array, displaying both services and groups 
	var $child_states = array(); 
	var $parents = array();      //use to build heirarchy	
	var $has_group_children; //boolean 
	var $state; //current state of group 
	var $title; //title used for display output 
	//var $id; //may or may not use 
	var $desc; //description
	var $primary; //boolean, is the group a primary group?
	var $info;
	var $members; //text string representation of members 
	var $problems =0;
	var $childcount; //count of children 
	var $statecount; //count of child states calculated 
	var $options = array(); //assoc array of any '&' or '|' options from config file
	var $warning_threshold; //integer from cfg file, sets warning threshold 
	var $critical_threshold;
	var $priority;
	
	//////////////////////////////////////////////////////////
	//
	function __construct($array, $key)
	{
		global $errors;
		//print "<p><strong>New Group $key Constructed!</strong></p>";		
		$this->name = $key;
		if(isset($array['title'], $array['desc'], 
					$array['primary'], $array['info'], 
					$array['members'], $array['warning_threshold'], 
					$array['critical_threshold']))
		{
			$this->title=trim($array['title']);
			//$this->id = trim($array['id']);
			$this->desc = trim($array['desc']);
			$this->priority = trim($array['priority']);
			$this->primary = trim($array['primary']);
			$this->info = trim($array['info']);
			$this->members=trim($array['members']); //process this array
			$this->warning_threshold=trim($array['warning_threshold']);//setting to send a warning based on number of group problems
			$this->critical_threshold=trim($array['critical_threshold']);//setting to send a warning based on number of group problems
		}
		else //error catch 
		{
			$err = "Error: Group $key is missing arguments in configuration file";
			print "<p class='error'>$err</p>";
			$errors.=$err.'<br />';
		}
	}
	//get, set, and append methods 
	function get_title()
	{
		return $this->title;
	}
	function get_memberlist()
	{
		return $this->memberlist;
	}
	function get_primary()
	{
		return $this->primary;
	}
	function get_info_html()
	{
		//print "<p>".$this->info."</p>";
		$s = $this->info;
		if($s!='')
		{
			//print "not empty<br />";
			$info = "<th><a href='$s' target='_blank'>URL</a></th>";
			return $info; 
		}
		else 
		{ 
			return ''; 
		}
	}
	
	function return_state_details()
	{
		$output = array();
		$output['msg'] = "Group state is: ".return_state($this->state).";  ".$this->problems." Child Problems\n";
		$output['code'] = $this->state;		
		return $output;	
	}
	function get_state()
	{
		return $this->$state;
	}
	function set_state($state)
	{
		$this->state = $state;
	}
	function get_group_children()
	{
		return $this->group_children;
	}
	function append_service_children($data)
	{
		//add array to service_children
		$this->service_children[] = $data; 	
	}
	
	function append_group_children($data)
	{
		$this->group_children[] = $data;
	} 
	
	function append_memberlist($data)
	{
		$this->memberlist[] = $data; //add groupname or service to array 
	}
	function append_parent($name)
	{
		$this->parents[] = $name;
	}
	
	/////////////////////////////////////////////////////////////////////////
	//processing the 'members' string from config file 
	function process_children()
	{
		global $errors;
		//print "<p>Function process_children()</p>";
		global $objects;
		global $statecount;
		global $config;
		$members = $this->members;
		//process members array and append to either group or service children array
		//append all members to $memberlist 
		$strings = explode(',', $members); //output look like: localhost;ping;&
		foreach($strings as $string)
		{	
			if(trim($string)== '')
			{
				continue;
			}
			//print "$string<br />";
			$this->membercount++;	
			$items = explode(';', $string); //explode member data.  ie localhost  ping   &   
			//print_r($items);
			array_walk($items, 'trim_value'); //trim whitespaces off ends
			
			//////////////////////////////////////GROUP//////////////////////////////////
			
			 
			if($items[0][0]=='$')
			{
				//print"<p>".$items[0]." This is a group</p>";
				$groupindex = substr($items[0], 1); //strip dollar sign 
				$this->has_group_children = true;
				//figure out how to establish parent variable 
				
				if(isset($objects[$groupindex]))
				{
					$group = $objects[$groupindex];
					//print "<p><strong>Adding Parent as ".$this->name."</strong></p>";
					$group->append_parent($this->name);
					
					//add childgroup name or ID to list of members
					//print trim($items[0]);
					//$this->append_memberlist(trim($items[0])); //should this be an array of just a keyword???? ARRAY 
					//add childgroup to childgroup array
					if(isset($items[1])) //if there is an option argument 
					{
						$opt = $items[1];
					}
					else
					{
						$opt = '';
						$config = false;
						$err= "Error: Missing '&' or '|' option for member:".$item[0].". Check config for: '".$this->name."'";
						print "<p class='error'>$err</p>";
						$errors.=$err.'<br />';
					}
					$array = array ('title' => $groupindex,
										 'id' => $items[0],
										 'option' => $opt,
										 'parent' => $this->name,
 										 'index' =>  $groupindex,
 										 'desc' => $this->desc,
										  );
					$this->append_group_children($array); //adds group name to list 					
				}

				else //Error catch, should not get here unless there is a config problem 
				{
					$config = false;
					$err = "Error: Processing $this->name Object with index $groupindex does not exist, check configuration";
					print "<p class='error'>$err</p>";
					$errors.=$err.'<br />';

				} 							
			}
			else //child is a service, process info  
			{
				//print "<p>".$items[0].$items[1]." This is a process</p>"; 
				//put this into assoc array, then spit into display function
				if(isset($items[2]))
				{ $opt = $items[2]; } //read for & or | option 
				else 
				{ 
					$opt = ''; 
					$config = false;
					$err = "Error: Missing '&' or '|' option for Group: ".$this->name." <br />Member:".$items[0].' '.$items[1];
					print "<p class='error'>$err</p>";
					$errors.=$err.'<br />';
				}
				
				$host = $items[0];
				$service = $items[1];				
				$status = $this->get_service_state($host, $service);	//get data array for service 	
				if($status > 0)
				{
					$this->problems++; //add to object problem counter 
				}
				//create return service state function 				
				$plugin_output = $status['plugin_output']; //return plugin_output function   
				$servicedata = array( 'host_name' => $host,
											 'service_description' => $service,
											 'membername' => $host.$service,
											 'option' => $opt,
											 'current_state' => $status['current_state'],
											 'plugin_output' => $plugin_output,
											  'parent' => $this->name,
											  'type' => 'service',
											 );
				
				//print "Service: $service <br/> Host: $host <br /> State: $state <br /> Output: $plugin_output <br />";
				$this->append_service_children($servicedata);
				$this->append_memberlist($servicedata);							  
				//create simple display function to seperate tags  
				//print "<li>".$items[0]." | ".$items[1]."</li>";
			}	//end main IF 
		}  //end FOREACH  		
	} //end process_children() method 
	
	/////////////////////////////////////////////////////////////////
	//	objects display method, contains all of the 'view' for html output 
	// NOTE: this method only displays child groups, for primary groups see bpi_functions.php 
	function display_tree()
	{
		
		global $objects; //global BpObjects array 
		global $unique;
		//display a list of children in html
		$children = $this->get_memberlist();

		
		//print_r($children);
		print "<ul>";
		$tr_count= 0;	
		foreach($children as $child)
		{
			$tr_count++;
			//two-colored tables 
			if($tr_count % 2 == 1)
			{	$class = 'even';   }
			else
			{  $class = 'odd';    }
			
			$state = return_state($child['current_state']);
			$optmarker = ($child['option']== '|' ? '**' : '');
			
			if($child['type'] == 'service') //add a type property for group or service 
			{
				if(NAGV=='XI')
				{
					$hostlink = HOSTDETAIL.$child['host_name']; //url for host 					
					$servicelink = SERVICEDETAIL.$child['host_name'].'&service='.$child['service_description'].'&dest=auto';
				}		
				else //nagios core 
				{
					$host = preg_replace('/ /', '+', trim($child['host_name']));
					$hostlink = HOSTDETAIL.$host;
					$service = preg_replace('/ /', '+', trim($child['service_description']));
					$servicelink = SERVICEDETAIL.$host.'&service='.$service;
					//http://localhost/nagios/cgi-bin/extinfo.cgi?type=2&host=XI+Demo&service=HTTP
				}
			
				//BEGIN hereroc string		
				$listitem=<<<LISTITEM
						 <li class='servicelisting'>
				         <table class='servicedata'>
				         	<tr class='{$class}'>
				         		<td class='{$state}'>{$state}{$optmarker}</td>
				         		<td><a href='{$hostlink}' target='_blank'>{$child['host_name']}</a></td>
				         		<td><a href='{$servicelink}' target='_blank'>{$child['service_description']}</a></td>
				         		<td>{$child['plugin_output']}</td>
				         	</tr>
				         </table>
				       </li>				       
LISTITEM;
				//print heredoc string 				
				print $listitem;

			}
			elseif($child['type'] == 'group')  //GROUP listings  
			{
				$unique++; //used for jquery ID 
				$id = $child['index'].$unique; //creates a unique id for the <ul>
				$td_id = 'td'.$unique; //creates unique td id for changing style 				
				$obj = $objects[$child['index']]; //call appropriate object 
				$info = $obj->get_info_html(); //returns either an info URL or empty string 
				$group_desc = ($child['desc'] != '' ? '<td>'.$child['desc'].'</td>' : '');  
				$optmarker = ($child['option']== '|' ? '**' : '');
				if($obj->has_group_children==true)
				{ $gpc_icon = "<td><img src='images/children.png' title='Contains Child Groups' height='8' width='13' alt='C' /></td>"; }
				else 
				{ $gpc_icon = ''; }
			
				$tableitem=<<<TABLEITEM
					 <li class='grouplisting'>
							<table class='groupdata'>
								<tr class='{$class}'>
									<td class='{$state}'>{$state}{$optmarker}</td>
									<td class='group'><strong>
										<a id='{$td_id}' class='grouphide' title="Group ID: {$child['index']}" href='javascript:void(0)' onclick='showHide("{$id}","{$td_id}")'>{$child['title']}</strong></a></td>
										
									{$gpc_icon}
									{$info} 
									<td>{$child['problems']} problem(s)</td>									  
									{$group_desc}
									<td><a href="index.php?cmd=edit&arg={$child['index']}">Edit</a></td>
									<td><a href="javascript:deleteGroup('index.php?cmd=delete&arg={$child['index']}')">Delete</a></td>
								</tr>
							</table>
							
TABLEITEM;
				//end heredoc string  				
				print $tableitem;
				
				//child group creates a nested list 
				
				print "<ul class='hidden' id='$id'>\n";
				$obj->display_tree(); //recursively call child object until all are displayed 
				print "</ul>\n";
				
				print "</li>\n";//close list 
			}
			else //this option should never happen, but may be needed for debugging 
			{
				print "<li>Missing index 'type' for Group: $child</li>";
			}
		}
		print "</ul>"; //close list 
		
	} //end display_tree() method 
	
	///////////////////////////////////////////////////////////////
	//	expecting a hostname and service description as argument
	// scans 'service status' information and returns an array of 'current_state' and 'plugin_output'
	function get_service_state($host, $service)
	{
		//print "<p>function get_service_state()</p>";
		global $errors;
		global $service_details;
		global $config;
		foreach($service_details as $sd)
		{
			//var_dump($service);
			//print "<br /><br />";
			
			if(trim($sd['host_name'])==trim($host) && trim($sd['service_description'])==trim($service))
			{
				//create array for desired service stats.  Add more items as needed.
				//$state = $this->return_service_state($sd['current_state']);   
				$status = array( 'current_state' => $sd['current_state'],
									  'plugin_output' => $sd['plugin_output']
									  ); 
				//return state and plugin output 					  
				return $status;
			}

		}//end FOREACH
		if(!isset($status)) //error catch for bad naming in config file 
		{
			$config = false;
			$err = "Error: Can't find a service with host:$host service:$service, check configuration for group: '".$this->name."'";
			print "<p class='error'>$err</p>";
			$errors.=$err.'<br />';
		} 
	}//end get_service_state();
	
	///////////////////////////////////////////////////////////////////
	//
	//this function is the driver for determining states and problems for all groups.  
	//It starts at the bottom of the tree and works up with each loop 
	//
	function drill_down()
	{
		global $objects;
		//Does this group have child groups   
		if($this->has_group_children == true)
		{
			//drill down and keep drilling until you find a group where that setting is false
			//print "<p><em>State has children, must drill</em></p>";
			if(!isset($this->state))
			{
				//state is not set, we need to drill down and see if the children states are set   
				//if child states are all set, determine state
				//else .....do nothing??? 				
				
				//print "<p>Finding childrens' state for: ".$this->name."</p>";
				$groupcount = count($this->group_children);
				$c = 0;
				foreach($this->group_children as $child) //$child is an array 
				{
					$childObject = $objects[$child['title']];
					if(isset($childObject->state))
					{
						//print "<h6>".$child['title']." state is: ".$childObject->state."</h6>";
						$c++;
					}
					else
					{
						//check to see if all children have states set and state can be determined... 
						if($childObject->statecount == $childObject->membercount)
						{
							//print "<p>Ready to determine child state!!!</p>";
							$childObject->determine_this_state();
						}
					}
				}
				if($c == $groupcount)
				{
					//all group status is decided, determine child status 
					//print "<h2>Finding state ".$this->name."</h2>";
					$this->determine_this_state();
				}
			}
		} //end IF 
		else //NO child groups, determine state!! 
		{
			//check to see if state is already set 						
			if(!isset($this->state))
			{ 
				//print "<h2>Finding state ".$this->name."</h2>";
				$this->determine_this_state();
			}//end IF 
		} //end IF/ELSE 
	}//end MAIN IF 
	
	///////////////////////////////////////////////////////////////////////////
	//expecting that object has a completed list of children with $state properties 
	//set for all members.  
	// requires that all members of the $memberlist array have an 'option' and 'current_state' value set 
	function determine_this_state()
	{
		global $objects; 
		global $statecount;
		$andMembers = array();
		$membersOfNewArray = array();		
		$members = $this->get_memberlist();
		$problemcount = 0;
		//////////////////////////////////
		//break array into two groups, & members and | members 
		foreach($members as $member)
		{
			//print "<p>".$member['membername']."</p>";
			//print "<br /><br />";
			//establish number of & members and put into a group
			if(trim($member['option']) == '&')
			{
				//print "<p>Adding & member: ".$member['membername']."</p>";
				$andMembers[] = trim($member['current_state']);
			}
			else
			{
				//print_r($member);
				//"<p>Adding member: ".$member['membername']." state to New Array: ".trim($member['current_state'])."</p>";
				$membersOfNewArray[] = trim($member['current_state']); //member does not have an '&' option 
			} //end IF 			
		}//end foreach
		
		//determine number of &group problems 
		$andProblems = 0;
		foreach($andMembers as $a)
		{
			//print "<p>a is: $a</p>";
			if($a > 0)
			{
				$andProblems++;
				$problemcount++;
			}
		}
		$andCount = count($andMembers);
		//print "<p>".$this->name.' : '.$andCount."</p>";
		// establish & group status 
		//if everyone in the &group is having problems, state is critical
		//print "<p><strong>AndProblems=$andProblems andCount=$andCount</strong></p>";
		
		//////DETERMINE '&' ClUSTER STATE ///////////////////////////////////////////		
		$critical = false;
		$warning = false;
		$andState = 0;
		//check for critical status 
		if($andProblems == $andCount && $andProblems != 0) //all members are having problems
		{
			//print "<p>All & children have problems</p>";
			$andState = 2; //state is CRITICAL
			$critical = true; 
		}
		if($andProblems > $this->critical_threshold && $this->critical_threshold !=0)
		{
			$andState = 2;
			$critical = true;
		}
		//check for warning threshold 
		if(!isset($critical))
		{		
			if($andProblems > $this->warning_threshold && $this->warning_threshold !=0)
			{
				$andState = 1; //state is WARNING 
				$warning = true;
			}			
			else //no problems detected so far 
			{
				$andState = 0;  //no problems in &group, state is OK 
			} 
		}
		//print "<p>andState is: $andState </p>";
		/////////////////////////END DETERMINE '&' CLUSTER STATE//////////////////////////////		

		$status = $andState; //assign a default variable to $status just in case 
		switch($status)
		{
			case 0:
				//if $andState came back OK, state is still OK 
				
				foreach($membersOfNewArray as $stat)
			   {
					//print "<p>$stat</p>";					
					//if there is a problem state, take action 
					if($stat > 0)
					{
						$problemcount++;
						//check against thresholds 
						if($problemcount > $this->critical_threshold && $this->critical_threshold !=0)
						{
							//threshold hit, group state is critical.  break loop 
							$status = 2;
							continue; 
						}
						elseif($problemcount > $this->warning_threshold && $this->warning_threshold !=0)
						{
							//warning threshold hit, break loop 
							$status = 1;
							continue;
						}
						
						//problem is detected, but thresholds are not hit
						//stat is a state of an essential member 
						switch($stat)
						{
							case 1:
								if($status == 1 || $status == 0 )
								{
									$status = 1; //status is at warning stage 							
								}
							break;
							
							case 2: //essential member with critical state, group is critical 
							$status = 2;
							break;
							
							case 3: //unknown state, group is critical 
							default: //any value greater than 3, and less than 0 
							$status = 2;  
							break;
							
						}	//end switch 
					} //end IF 	
				}//end foreach 				
			break;
			
			case 1: //$andState is at WARNING level, group is already at Warning state  
				//do stuff
				foreach($membersOfNewArray as $stat)
			   {
					if($stat > 0)
					{
						//add any additional group problems to the total count 
						$problemcount++;
					}
					//check against critical threshold 
					if($problemcount > $this->critical_threshold && $this->critical_threshold !=0)
					{
						//critical theshold hit, break loop and return state 
						$status = 2;
						continue;
					}
					//$stat is an essential member's state  
					switch($stat)
					{
						case 1:
						if($status == 1 || $status == 0 )
						{
							$status = 1; //state is at warning stage 
						}
						break;
						
						case 2: //state is already critical, do nothing 
						$status = 2;
						break;
						
						case 3:
						$status = 2;  //uknown service will create critical group status
						break;
						
						default: //do nothing for other states 
						break; 
					}	//end switch				
				}//end foreach 			
			break;
			
			case 2: //$status is already critical, group is already critical, just add problems to the count  
			default: //anything else known will be critical 
				//doesn't matter, group is already critical 
				foreach($membersOfNewArray as $stat)
			   {
					if($stat > 0)
					{
						//add any additional group problems to the total count 
						$problemcount++;
					}
				}
				$status=2;
			break;			
			
					
		}
	
		//extra check for thresholds
		if($problemcount >= $this->critical_threshold && $status < 2 && $this->critical_threshold !=0) 
		{ $status = 2; }
		if($problemcount >= $this->warning_threshold && $status == 0 && $this->warning_threshold !=0) 
		{ $status=1; } 

		$this->state = $status;
		$this->problems = $problemcount;
						
		//////////////////////////////////////////////////////////////////////////////////
		//PART II Send completed array to parent's $memberlist so status can be determined. 
		// 
		//if this group has a parent, append this group status to it's memberlist array
		
		if(count($this->parents > 0 ))
		{
			//print "<h5>Name : ".$this->name. " Parents:</h5>";
			//print_r($this->parents);
			foreach($this->parents as $parent)
			{
				if(isset($objects[$parent]) )
				{
					//call parent object 
					$obj = $objects[$parent];
										
					//find state option
					//get option value from parent for this child group 
					//get group children array 
					$gc = $obj->get_group_children();
					//print "<p><em>Fetching group children...</em></p>";
					//print_r($gc);
					$opt = '';
					//Find option argument for this config 
					foreach($gc as $g)
					{			
						if($g['title']==$this->name)					
						{
							//print "<p> ".$g['title']." : ".$g['option']."</p>";
							$opt = $g['option'];
						}					
					}
					//add member array to parent memberlist 
					$array = array(  'title' => $this->title,										
										 'type' => 'group',
										 'current_state' => $status,
										 'problems' => $problemcount,
										  'parent' => $parent,
										  'type' => 'group',
										  'option' => $opt,
										  'index' => $this->name,
										  'desc' => $this->desc,
										  'info' => $this->info,
														 );
					$obj->append_memberlist($array);
					//add to parent statecount 
					$obj->statecount++;
				}	
			}	
						
		}	//end IF object has parent(s) 	

		//global statecount, controls main loop 
		$statecount++;	
	}//end of determine_this_state() method 
	
		
} //end of BpGroup class 





?>