<?php 	ob_start();   print '<?xml version="1.0" encoding="UTF-8"?>';

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

include('header.php');  //html header stuff 
include('inc.inc.php');  //master include file for all functions and classes 

$info = get_info(); //read info from main configuration file (bpi.conf) 
$service_details = grab_details('service'); //global array of service status from status.dat file 
$objects = array(); //main array of bpi group objects 
$obj_count = 0;
$statecount = 0;
$unique = 0;
$config = true;
$errors = '';

//initialize all BpGroup instances and determine properties of all groups
//see bpi_functions.php for function details  
bpi_init();  

//handler for bad configurations 
if($config!=true)
{
	//print_r($errors);
	//allow for manual editing of the configuration file, and send error messages to that page 
	print "<p class='error'>Error in configuration.  Page cannot be displayed.</p>
			<form id='errorlog' method='post' action='fix_config.php'>
			 <input type='submit' value='Edit Configuration File' name='submit' />";
	//submit error messages as posts 		 
		print "<input type='hidden' name='errors' value=\"$errors\" />";

	print "</form>";
	die();
}

//handle any page requests and redirection
//see bpi_functions.php for function details 
bpi_page_router();
	   

//end php 
//close html page 
include(dirname(__FILE__).'/footer.php');
?>