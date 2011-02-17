<?php //delete_group.php 


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


//deletes a BPI group from the config file
//expecting a group ID as $arg 
function delete_group($arg)
{
	//grab entire config definition as a string 
	$config = get_config_string($arg);
	
	if($config) //only take action is the group is found 
	{
		//make a backup copy of file first 
		//$backup = BACKUPCONFIG;
		
		if(copy(CONFIGFILE, CONFIGBACKUP))
		{
			print "<p>Backup successfully created.</p>";
			$contents = file_get_contents(CONFIGFILE); 
			//replace old config string with new 
			//print "<pre>$config</pre>";
		
			$new  = str_replace($config, '', $contents, $count0);
			print "<p>Groups Deleted: $count0</p>";
			
			//print "<pre>$new</pre>";			
			if(isset($new) && $count0 > 0)
			{
				//delete all instances where group is a child/member of another group 
				//removes $group;|,
				$matchstring1 = '$'.$arg.';|,';  //used for str_replace 
				$pregString1 = '/\$'.$arg.';\|,/'; //used for preg_match 
				//removes $group;&,			
				$matchstring2 = '$'.$arg.';&,';  //used for str_replace 
				$pregString2 = '/\$'.$arg.';\&,/'; //used for preg_match 
				 
				$changecount = 0;
				if(preg_match($pregString1, $new) )
				{
					$new = str_replace($matchstring1, '', $new, $count1); //replace all matched instances of string 
					//print "<p>Matched |, should be replaced.</p>";
					if(isset($count1)) 
					{ $changecount += $count1;  }
				}
				if(preg_match($pregString2, $new))
				{
					$new = str_replace($matchstring2, '', $new, $count2);
					//print "<p>Matched & , should be replaced.</p>";
					if(isset($count2)) 
					{ $changecount += $count2;  }
				}
				
				
				//write to file
				file_put_contents(CONFIGFILE, $new);
				//check if write was successful 	

				if($changecount > 0)
				{
					print "<p>Group has been removed as a member from $changecount other groups.</p>";
				}
				print "<p>File successfully written!</p>";

			}
			else //bad groupID, not found in config file 
			{
				"<p class='error'>Unable to match string in config file.</p>";
			}
		}
		else
		{
			print "<p class='error'>Backup failed.  Aborting change.  
						Verify that backup directory is writeable.</p>";
		}

	}
	else
	{
		print "<p class='error'>Unable to find group in config file, no changes made.</p>";
	}

}//end of function 





?>