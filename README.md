NagiosBPI
=========

## Nagios Business Process Intelligence

NagiosBPI is used to group important services together to form a cohesive unit. 
Say you have an apache cluster made up of four servers, and you want to monitor 
the health of the site they're hosting. If one of four servers goes down, 
is that really something you need to be warned about? Wouldn't you rather be warned 
when two go down?

That's what this plugin was made for!


Installation on Nagios XI
-------------------------
<b>BPI should be installed by default on Nagios XI</b>. If you're using a
legacy version of Nagios XI, follow these steps:

1. Download the zip file onto a machine that can access XI from the web interface.

2. **For upgrades**:
   
       cp /usr/local/nagiosxi/html/includes/components/nagiosbpi/bpi.conf /tmp/bpi.conf

3. Go to the Admin->Manage Components page.

4. Upload the zip file using the upload tool on the page.

5. Execute the following commands on the server's console:

       chmod +x /usr/local/nagiosxi/html/includes/components/nagiosbpi/set_bpi_perms.sh
       /usr/local/nagiosxi/html/includes/components/nagiosbpi/set_bpi_perms.sh

6. Nagios BPI should now be accessible from the main-left menu of Nagios XI.


Installation on Nagios Core
---------------------------

1. Download the source code as a zip file and unzip it 
   into your `/tmp` directory on your Nagios server.

2. Copy the `nagiosbpi` folder from your unzipped location to inside of 
   your Nagios directory or your preferred location that is accessible 
   by your webserver. This location could be dependent on your linux 
   distribution, but for the following example we'll use 
   `/usr/local/nagios/share`.

       cd /usr/local/nagios/share/nagiosbpi 

3. Execute the permissions script as the root user:

       chmod +x set_bpi_perms.sh
       ./set_bpi_perms.sh

4. Edit the contents of the `constants.conf` file to match your directory 
   locations. **Make sure to use absolute locations!**

5. Launch Nagios BPI from your web browser http://YOURSERVER/nagios/nagiosbpi.

6. Start creating new BPI groups using the built-in configuration tools.


Running a check for BPI Groups on Nagios XI
-------------------------------------------
Please refer to our documentation at 
https://assets.nagios.com/downloads/nagiosxi/docs/Using_Nagios_BPI_v2.pdf
for instructions on using BPI with Nagios XI.

If your XI installation is missing the BPI wizard, or if you have a legacy 
version of Nagios XI, you can download and install the BPI wizard from 
http://exchange.nagios.org/directory/Addons/Configuration/Configuration-Wizards/Nagios-Business-Process-Intelligence-%28BPI%29-Wizard/details


Running a check for BPI Groups on Nagios XI
-------------------------------------------

1. Download the [check_bpi.php](https://raw.githubusercontent.com/NagiosEnterprises/nagiosbpi/master/nagiosbpi/check_bpi.php) script
2. Edit the `check_bpi.php` file to point to the directory location of the 'api_tool.php' file.  
   
     Example:

         #!/usr/bin/php 
         <?php
         $file = '/var/www/http_public/nagiosbpi/api_tool.php'; 
         include($file);

3. Copy this file to the directory of your nagios check plugins. (Example: `/usr/local/nagios/libexec/`)
4. Make sure the file is executable.

        chmod +x /usr/local/nagios/libexec/check_bpi.php

5. In your `nagios/etc` directory, locate your `commands.cfg` and add the following definition:

        define command {
           command_name          check_bpi
           command_line          $USER1$/check_bpi.php $ARG1$
        }

6. Create a dummy host definition similar to the following:

        define host {
           host_name             bpigroups
           use                   generic_host
           display_name          BPI Groups
           check_command         check_dummy!0
           address               1.0.0.0
           register              1
        }

7. Create service definitions like the following, use the BPI Group ID as the argument.  
   This can by found my mousing over the group name in the web interface, or by finding the `define [groupID]` statement in your `bpi.conf` file.

        define service {
           host_name             bpigroups
           service_description   My BPI Group 
           use                   generic_service
           check_command         check_bpi!myBpiGroupID
           register              1
        }   

   A sample BPI group definitions:

     * This group contains services and another group:

            define MyStuff {
                title=My Stuff
                desc=This is a test group for Nagios BPI
                primary=1
                info=www.yourwebsite.com
                members=localhost;PING;&, 192.168.5.11;Ping;&, $YourStuff;&, 
                warning_threshold=2 
                priority=1 
                critical_threshold=3
            }

     * This is a group with only services:

            define YourStuff {
                title=Your Stuff
                desc=This is a group with services only 
                primary=1
                info=http://www.example.com
                members=localhost;HTTP;&, localhost;SSH;&, localhost;Swap Usage;&, 
                warning_threshold=0
                priority=1
                critical_threshold=3
            }

Authors
-------

Nagios BPI was originally written in 2010 by Mike Guthrie and is maintained by
Nagios, LLC.

Full list of authors, contributors, and maintainers:

Mike Guthrie (original author, Nagios)  
Bryan Heden (Nagios)  
Jonathan Gazeley  
Josh Soref  
Sebastian Wolf (Nagios)  
Tony Yarusso (Nagios)  

Changelog
---------

For a list of changes to the software, see CHANGELOG.txt in the nagiosbpi 
directory

Current Version
---------------

This is version 1.3.1, released in 2011. The current version of this software
can be found at:

https://github.com/NagiosEnterprises/nagiosbpi

Other free software maintained by Nagios Enterprises can be found at:

https://github.com/NagiosEnterprises

License Notice
--------------

For the full license, please see the LICENSE file.

Copyright (c) 2010-2017 Nagios Enterprises, LLC

This work is made available to you under the terms of Version 2 of
the GNU General Public License. A copy of that license should have
been provided with this software, but in any event can be obtained
from http://www.fsf.org.

This work is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
02110-1301 or visit their web page on the internet at
http://www.fsf.org.

Questions?
----------

If you have questions about this addon, or encounter problems getting things
working along the way, your best bet for an answer or quick resolution is to check the
[Nagios Support Forums](https://support.nagios.com/forum/viewforum.php?f=5).

