<?php  //bpi2xml.php creates xml output for the bpi

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



function create_bpi_xml()
{
	global $objects;
	$array = array();
	
	//begin xml opening tags 
	$xmldata =  '<?xml version="1.0" encoding="iso-8859-1"?>'."\n"; //doctype
	$xmldata .= "<bpigroups>\n"; //root tag
	
	foreach($objects as $obj)
	{
		//single variables 
		$id = $obj->name;
		$hasgroupchildren = $obj->has_group_children;
		$state = $obj->state;
		$title = $obj->title;
		$desc = $obj->desc;
		$primary = $obj->primary;
		$info = $obj->info;
		$problems = $obj->problems;
		$warn = $obj->warning_threshold;
		$crit = $obj->critical_threshold;
		$priority = $obj->priority;
		
		//arrays 
		$members = $obj->memberlist;
		$childstates = $obj->child_states;
		$parents = $obj->parents;
		
		//open child set of data 
		$xmldata .= 
		"\n<bpigroup>\n
		<id>$id</id>\n
		<title>$title</title>
		<hasgroupchildren>$hasgroupchildren</hasgroupchildren>\n
		<state>$state</state>\n
		<description>$desc</description>\n
		<primary>$primary</primary>\n
		<info>$info</info>\n
		<problems>$problems</problems>\n
		<warningthreshold>$warn</warningthreshold>\n
		<criticalthreshold>$crit</criticalthreshold>\n
		<priority>$priority</priority>\n";
							

		//loops for arrays 
		////////////////////////////		
		$xmldata .= "<members>\n";
		foreach($members as $member)
		{
			$xmldata .= "<member>\n";
			foreach($member as $key=>$value)
			{
				$key = htmlentities($key);
				$value=htmlentities($value);
				$xmldata .= "<$key>$value</$key>\n";			
			}
			$xmldata .= "</member>\n";
		}
		$xmldata .= "</members>\n";
		////////////////////////////////
		$xmldata .= "<childstates>\n";
		foreach($childstates as $cs)
		{
			$xmldata .= "<childstate>$cs</childstate>\n";
		}
		$xmldata .= "</childstates>\n";
		////////////////////////////////////
		$xmldata .= "<parents>\n";
		foreach($parents as $parent)
		{
			$xmldata .= "<parent>$parent</parent>\n";
		}
		$xmldata .= "</parents>\n";
		//close group 
		$xmldata .= "</bpigroup>\n\n";
		
	}//end main foreach loop 
	//close xml document 
	$xmldata .= "</bpigroups>\n"; //root tag
	
	return $xmldata; 
}



function write_bpi_xml()
{
	$xml = create_bpi_xml();  //change print to return data 

	if($f = @fopen(XMLOUTPUT, 'wb'))
	{
		@fwrite($f, $xml); //output data to an xml file 
		@fclose($f);
	}

}

?>