<?php //process_post.php    processes data from config editor 

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



function process_post($array)
{
	global $objects;
	//clean post data 
	//verify that all required elements are set
	
	//print_r($_POST);	
	
	 
	if(isset($array['groupID'], $array['groupDisplay'],$array['members'], $array['groupTitle']))
	{
		$groupID = htmlentities(trim($array['groupID']));
		$title = htmlentities(trim($array['groupTitle']));
		$display = htmlentities(trim($array['groupDisplay']));
		$members = $array['members'];
		//optional config parameters 
		$desc = (isset($array['groupDesc']) ? htmlentities(trim($array['groupDesc'])) : '');
		$primary = (isset($array['groupPrimary']) ? 1 : 0);
		$critical = (isset($array['critical']) ? $array['critical'] : false);
		$info = ( isset($array['groupInfoUrl']) ? htmlspecialchars(trim($array['groupInfoUrl'])) : '');
		$warning = (isset($array['groupWarn']) ? htmlentities(trim($array['groupWarn'])) : '0');
		$crit = (isset($array['groupCrit']) ? htmlentities(trim($array['groupCrit'])) : '0');
		//echo "<p>Printing members list:</p>";
		//print_r($members);
		
		$memberString = '';
		if($critical)
		{			
			//for($i=0; $i < count($members); $i++)
			foreach($members as $member)
			{
				//if member is in critical array add the | symbol
				if(in_array($member, $critical))
				{
					$memberString .= $member.';|, ';
				} 
				//else add the & symbol
				else
				{
					$memberString .= $member.';&, ';
				} 
			} //end foreach 

		} //end if 
		else //all members are & members, assign & values  
		{
			foreach($members as $member)
			{
				$memberString .= $member.';&, ';
			}
		}
		
		//create config output, using heredoc string syntax  
		$config=<<<TEST
				
##################################
define {$groupID} {
		title={$title}
		desc={$desc}
		primary={$primary}
		info={$info}
		members={$memberString}
		warning_threshold={$warning}
		critical_threshold={$crit} 
		priority={$display}
		
}
				
TEST;
//end heredoc 

		//return configuration definition string 
		//print "<pre>New Config String:\n$config</pre>";
		return $config;
	}	
	else
	{
		print '<p class="error">Missing data from required fields. Please go back and complete all fields.</p>';
	}
}



?>