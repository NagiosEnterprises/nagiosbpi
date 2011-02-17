<?php  //nagiosbpi read_conf.php

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


//grabs data from bpi.conf file and turns into an array 
function get_info()
{

	$f = fopen(CONFIGFILE, "r") or exit("Unable to open file!");
	$case = 0;
	$keystring = 'define';
	$vars = array();
	//echo "key is $keystring";
		
	while(!feof($f)) //read through file and assign host and service status into separate arrays 
	{	
		//var_dump($line)."<br />";
		$line = fgets($f); //Gets a line from file pointer.
		if(preg_match('/#/', $line) || trim($line)=='' ) //line is a comment, do nothing 
		{
			continue;	 				
		}	
		
		if(preg_match('/'.$keystring.'/', $line))
		{
			//echo "starting grab?<br /><br />";
			$case = 1; //enable grabbing of variables
			$title= substr($line, (strpos($line,$keystring)+strlen($keystring)), (strlen($line) ) ); //creating index for array 
			$title = trim(preg_replace('/{/', '', $title));
			//echo "<p>$title</p>";		
			$vars[$title] = array(); //starts a new array 		 				
		}	
		
		if(preg_match('/}/', $line) )
		{	 
			$case = 0; //turn off switches once a definition ends 		
		}
		
		//grab variables according to the enabled boolean switch
	
		switch($case) 
		{
			case 0:
			//switches are off, do nothing 
			break;
					
			case 1: // definition
			//do something
			if(!preg_match('/'.$keystring.'/', $line) && preg_match('/=/', $line)) //eliminate definition line, and only grabs lines with data 
			{
				//echo "should be grabbing a line<br />";
				$strings = explode('=', trim($line));			
				$key = trim($strings[0]);
				$value = trim($strings[1]);
				$vars[$title][$key]= $value;
			}
			break;
			
		}	//end of switch 				
	} //end of while	
	return $vars;
	fclose($f);	

}//end of function 
//////////////////////////////////////////////////////////////////


//reads globals.conf file and returns an array for directory and URL locations 
function fetch_globals()
{

	$f = fopen(dirname(__FILE__).'/../constants.conf', "r") or exit("Unable to open 'constants.conf' file!");
	$vars = array();		
	while(!feof($f)) //read through file and assign host and service status into separate arrays 
	{	
		//var_dump($line)."<br />";
		$line = fgets($f); //Gets a line from file pointer.
		
		if(preg_match('/#/', $line) || trim($line)=='' ) //line is a comment, do nothing 
		{
			continue;	 				
		}	
		
		//echo "should be grabbing a line with data: $line<br />";
		$strings = explode('=', trim($line));			
		$key = trim($strings[0]);
		$value = trim($strings[1]);
		$vars[$key]= $value;
							
	} //end of while	
	fclose($f);	
	return $vars;
}	


//print_r($vars);





?>
