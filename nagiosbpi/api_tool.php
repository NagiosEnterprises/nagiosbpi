<?php  //api_tool.php  function written to access BPI info from a nagios check.  Use with check_bpi.php 

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



if(isset($argv[1]))
{
	$input = trim($argv[1]);
}
else 
{
	echo "Error: Missing group ID as argument.\n";
	exit(3);
}

//initialize variables 
$info;
$service_details;
$host_details;
$objects = array();
$obj_count = 0;
$statecount = 0;
$unique = 0;
$config = true;



//run the api tool 
@do_stuff();

function do_stuff()
{
	global $input;
	global $info;
	global $service_details;
	global $host_details;
	global $objects;
	global $obj_count;
	global $statecount;
	global $unique;
	global $config;
	//$input = (isset($argv[1]) ? $argv[1] : die("Error: Missing group ID as an argument\n"));
	
	include(dirname(__FILE__).'/functions/read_conf.php');	
	include(dirname(__FILE__).'/constants.inc.php');	
	include(dirname(__FILE__).'/functions/read_service_status.php');		
	include(dirname(__FILE__).'/functions/bpi_functions.php');	
	include(dirname(__FILE__).'/BpGroup_class.php');	
	include(dirname(__FILE__).'/functions/bpi2xml.php');
		
	
	$info = get_info();
	list($host_details,$service_details) = grab_details(); //global array of service status 
	
	//initialize all BpGroup instances and determine properties of all groups
	
	bpi_init();  
	
	if(isset($objects[$input]))
	{
		$obj = $objects[$input];
		$output = $obj->return_state_details();
		//echo $output['msg'];
		// Exit correctly
		$num = $output['code'];
		fwrite(STDOUT, trim($output['msg'])."\n");
		//echo $output['code']."\n";
		exit($num);
	}
	else
	{
		echo "Unknown BPI Group Index\n";
		exit(3);
	}
	
	

}
?>