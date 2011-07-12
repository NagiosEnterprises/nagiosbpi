<?php //read_service_status.php 

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

define('STATUSFILE','/usr/local/nagios/var/status.dat'); 
ini_set('display_errors','on'); 

//expects 'host' or 'service' or 'program' as an argument 
function grab_details()
{

	$f = fopen(STATUSFILE, "r") or exit("Unable to open status.dat file!"); 
	$hostdetails = array(); 
	$servicedetails = array(); 
	
	//counters for iteration through file 
	$hostcounter = 0;
	$servicecounter = 0;		
	$case = 0;
	$service_id=0;
	$host_id=0; 

	$hoststring = 'hoststatus {';
	$servicestring = 'servicestatus {'; 	
		//echo "key is $keystring";
	$matches = array('host_name','service_description','current_state',
							'last_check','has_been_checked','plugin_output',
							'problem_has_been_acknowledged',	'scheduled_downtime_depth' ); 

		
	while(!feof($f)) //read through file and assign host and service status into separate arrays 
	{
	
		//var_dump($line)."<br />";
		$line = fgets($f); //Gets a line from file pointer.
//		print $line."<br />"; 
		
		if(strpos($line,$hoststring)!==false)
		{
//			echo "<h3>Found Host</h3>";
			$case = 1; //enable grabbing of host variables
			$hostcounter++;			
			$hostdetails[$hostcounter] = array(); //starts a new service array
			$host_id++; 		 				
		}	
		
		if(strpos($line,$servicestring)!==false)
		{
//			echo "<h3>Found Service</h3>";
			$case = 2; //enable grabbing of service variables
			$servicecounter++;			
			$servicedetails[$servicecounter] = array(); //starts a new service array
			$service_id++; 		 				
		}
		
		if(strpos($line, '}') !==false)
		{	 
			$case = 0; //turn off switches once a definition ends 		
		}
		
		//grab variables according to the enabled boolean switch
	
		switch($case) 
		{
			case 0:
			//switches are off, do nothing 
			break;
					
			case 1: //service definition
			//do something
			if(strpos($line,$hoststring)!==0 ) //eliminate definition line 
			{
				
				//echo "should be grabbing a line<br />";
				$strings = explode('=', $line);			
				$key = trim($strings[0]);
				if(!in_array($key,$matches)) break; 
//				echo $line."<br />"; 
				$value = trim($strings[1]);
				//added conditional to count for '=' signs in the performance data 
				if(isset($strings[2]))
				{
					$i=2;
					while(isset($strings[$i]))
					{
						$value.='='.$strings[$i]; //used for performance data 
						$i++;
					}
				}
				$hostdetails[$hostcounter][$key]= $value;
				$hostdetails[$hostcounter]['host_id']= $host_id;
				
			}
			break;
			
			case 2: //service status  
			if(strpos($line,$servicestring)!==0) //eliminate definition line 
			{
				//echo "should be grabbing a line<br />";
				
				$strings = explode('=',$line);			
				$key = trim($strings[0]);
				if(!in_array($key,$matches)) break; 
				$value = trim($strings[1]);
//				echo $line."<br />"; 
				//added conditional to count for '=' signs in the performance data 
				if(isset($strings[2]))
				{
					$i=2;
					while(isset($strings[$i]))
					{
						$value.='='.$strings[$i]; //used for performance data 
						$i++;
					}
				}
				$servicedetails[$servicecounter][$key]= $value;
				$servicedetails[$servicecounter]['service_id']= $service_id;
				
			}
			break;
			
		}	//end of switch 				
	} //end of while	
	
	fclose($f);	
	return array('host' => $hostdetails, 'service' => $servicedetails);

}//end of grab_host_details function 



//status detail arrays for application use 
//USAGE:   
$details = grab_details();
//$details = grab_details('service');
print_r($details['host']); 





?>