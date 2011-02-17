<?php  //conf_builder.php  master control file for configuration front-end 

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



function bpi_route_command($cmd)
{
	print "<p class='gohome'><a href='index.php'>BPI Home</a></p>";
	print "<div class='container'>";
	
	switch($cmd)
	{
		///////////////////////////ADD GROUP////////////////////////////
		case 'add': //and new group form 
			//do stuff
			
			if(isset($_POST['addSubmitted']))
			{	
				
				$config=process_post($_POST);
				if(isset($config))
				{
					append_file($config);
				}	
				print "<p><a href='index.php?cmd=add'>Add More Groups</a></p>";
				
			}
			 //display empty form if not $_POST is set
			else  empty_form();

		break;
		
		///////////////////////////DELETE GROUP////////////////////////////
		case 'delete':
			//delete stuff
			if(isset($_GET['arg']))
			{
				//add javascript confirmation of group deletion 
				$arg = htmlentities(trim($_GET['arg']));
				delete_group($arg);
			}
			else  print "<p class='error'>Error: No BPI Group specifies to delete.</p>";
 
		break;
		
		///////////////////////////EDIT GROUP////////////////////////////
		case 'edit':
			//edit existing groups 
			if(isset($_GET['arg']))
			{
				//add javascript confirmation of group deletion 
				$arg = htmlentities(trim($_GET['arg']));
				$config = get_config_array($arg);
				
				if(isset($_POST['editSubmitted']))
				{
					$config=process_post($_POST);	//process the form data, make sure it comes back valid  
					if(isset($config))  edit_group($arg, $config);				
				}
				//if form hasn't been submitted, preload the form with config data 
				else  loaded_form($config);
			}
			//missing arguments in $_GET 
			else print "<p class='error'>Error: No BPI Group specifies to delete.</p>";

		break;
		
		case 'fixconfig':
			include('config_functions/fix_config.php'); 
		break; 
		
		default: //default to view page if value is bad 
		send_home();
		break;
	} //end SWITCH 
		
	print "</div>\n";


} //end bpi_route_command() 


?>

