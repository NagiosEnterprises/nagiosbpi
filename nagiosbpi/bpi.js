/*this function toggles the grids and configuration tables */

/*
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
*/
function showHide(id, td_id)
{
	//alert(id);
	//change background color of 'this' td 
	var tdID = "#"+td_id;	
	$(tdID).toggleClass('groupexpand')
	var divID = "#"+id;
	$(divID).slideToggle("fast");
	   
}
/*this function hides all lists that can be toggled*/
function hide()
{
	//alert('this is a functional alert');
	$(".hidden").hide();
}

var warnCount = 0;
var critCount = 0;
var unique = 0;
function dostuff()
{
	//unique;
	var titles = []; //display titles for select options
	var values = []; //option values 
	$('#multiple :selected').each(function(i, selected){ 
	  titles[i] = $(selected).text(); 
	  values[i] = $(selected).val();
	});
	
	for(i=0;i < titles.length; i++)
	{
		//create data id that ties to option
		unique++; 
				
		//input string for group or service selections 
		var string = '<tr class="trOption" id="tr'+unique+'"><td>'+titles[i]+'</td>';
		string += "<input type='hidden' name='members[]' value='"+values[i]+"' />";
		string += '<td><input type="checkbox" name="critical[]" value="'+values[i]+'" /></td>';				
		string += '<td><a href="javascript:void(0)" onclick="remove(\'tr'+unique+'\')">X</a></td></tr>';
		//write output to new table/form	
		$('#selectoutput').append(string); 
		
		//count used for warning threshold input 
		warnCount++;
		critCount++; 
		//write output to new table/form
		$('#groupWarn').append('<option id="wc'+warnCount+'" value="'+warnCount+'">'+warnCount+'</option>');
		$('#groupCrit').append('<option id="cc'+critCount+'" value="'+critCount+'">'+critCount+'</option>'); 	
	}
	
}

function preload(title, value, opt)
{
			//create data id that ties to option
		unique++; 
				
		//input string for group or service selections 
		if(opt=='|')
		{ var checked = 'checked="checked"'; }
		else { checked = ''; }		
		
		var string = '<tr class="trOption" id="tr'+unique+'"><td>'+title+'</td>';
		string += "<input type='hidden' name='members[]' value='"+value+"' />";
		string += '<td><input type="checkbox" '+checked+' name="critical[]" value="'+value+'" /></td>';				
		string += '<td><a href="javascript:void(0)" onclick="remove(\'tr'+unique+'\')">X</a></td></tr>';
		//write output to new table/form	
		$('#selectoutput').append(string); 
		
		//count used for warning threshold input 
		warnCount++;
		critCount++; 
		//write output to new table/form
		$('#groupWarn').append('<option id="wc'+warnCount+'" value="'+warnCount+'">'+warnCount+'</option>');
		$('#groupCrit').append('<option id="cc'+critCount+'" value="'+critCount+'">'+critCount+'</option>');
}


function submitForm()
{
	//validate required fields
	var id = $('#groupIdInput').val();
	var title = $('#groupTitleInput').val();
	if( id != '' && title != '' && warnCount > 0)
	{	
		//required fields met
		document.getElementById('outputform').submit();
	}
	else { alert('please fill in all required fields'); }
}

//removes item from output table
function remove(id)
{
	var ID = '#'+id;
	$(ID).remove();
	var optID = '#wc'+warnCount;
	$(optID).remove();
	warnCount--;
	critCount--;
	
}

//expecting delete url as the argument 
function deleteGroup(arg)
{
	var conf = confirm('Are you sure you want to delete this group?\nThis will permanently delete this group and all associated memberships.\nThis action cannot be undone.');
	if(conf===true)
	{
		//forward to delete page
		location.href=arg;
	}
	

}

function setThresholds(arg1, arg2)
{
	var wID = 'wc'+arg1;
	var opt1 = document.getElementById(wID);
	opt1.selected=true;

	var cID = 'cc'+arg2;
	var opt2 = document.getElementById(cID);
	opt2.selected=true; 
}

function clearMembers()
{
	var table = document.getElementById('selectoutput');
	//alert(table);
	var members = table.childNodes;
	for(i=0; i<=unique; i++)
	{
		idMask = 'tr'+i;
		if(document.getElementById(idMask))
		{
			remove(idMask);
		}
	}
}



    



