Name:		nagiosbpi
Version:	1.3.1
Release:	4%{?dist}
Summary:	Nagios Business Process Intelligence
Group:          Applications/System
BuildArch:      noarch
Requires:       nagios
License:	GPL
URL:		http://exchange.nagios.org/directory/Addons/Components/Nagios-Business-Process-Intelligence-(BPI)/details
Source:         %{name}-%{version}.tar.gz
Patch1:         bpi-redhat.patch
BuildRoot:	%(mktemp -ud %{_tmppath}/%{name}-%{version}-%{release}-XXXXXX)


%description
Nagios Business Process Intelligence is an advanced grouping tool that allows you to set more complex dependencies to determine groups states. Nagios BPI provides an interface to effectively view the 'real' state of the network. Rules for group states can be determined by the user, and parent-child relationships are easily identified when you need to 'drill down' on a problem.

%prep
%setup -q
%patch1

%build


%install
rm -rf $RPM_BUILD_ROOT

install -D -m 755 nagiosbpi/BpGroup_class.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/BpGroup_class.php
install -D -m 644 nagiosbpi/CHANGELOG.txt $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/CHANGELOG.txt
install -D -m 644 nagiosbpi/INSTALL $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/INSTALL
install -D -m 644 nagiosbpi/TODO $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/TODO
install -D -m 755 nagiosbpi/api_tool.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/api_tool.php
install -D -m 666 nagiosbpi/bpi.conf $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/bpi.conf
install -D -m 755 nagiosbpi/bpi.js $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/bpi.js
install -D -m 644 nagiosbpi/bpi_style.css $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/bpi_style.css
install -D -m 755 nagiosbpi/check_bpi.php $RPM_BUILD_ROOT%{_libdir}/nagios/plugins/check_bpi.php
install -D -m 755 nagiosbpi/config_functions/add_group.inc.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/config_functions/add_group.inc.php
install -D -m 755 nagiosbpi/config_functions/config_forms.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/config_functions/config_forms.php
install -D -m 755 nagiosbpi/config_functions/delete_group.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/config_functions/delete_group.php
install -D -m 755 nagiosbpi/config_functions/edit_group.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/config_functions/edit_group.php
install -D -m 755 nagiosbpi/config_functions/fix_config.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/config_functions/fix_config.php
install -D -m 644 nagiosbpi/constants.conf $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/constants.conf
install -D -m 755 nagiosbpi/constants.inc.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/constants.inc.php
install -D -m 755 nagiosbpi/footer.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/footer.php
install -D -m 755 nagiosbpi/functions/bpi2xml.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/functions/bpi2xml.php
install -D -m 755 nagiosbpi/functions/bpi_functions.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/functions/bpi_functions.php
install -D -m 755 nagiosbpi/functions/bpi_route_command.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/functions/bpi_route_command.php
install -D -m 755 nagiosbpi/functions/process_post.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/functions/process_post.php
install -D -m 755 nagiosbpi/functions/read_conf.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/functions/read_conf.php
install -D -m 755 nagiosbpi/functions/read_service_status.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/functions/read_service_status.php
install -D -m 755 nagiosbpi/header.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/header.php
install -D -m 644 nagiosbpi/images/BPI-logo.png $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/BPI-logo.png
install -D -m 644 nagiosbpi/images/children.png $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/children.png
install -D -m 644 nagiosbpi/images/collapse.gif $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/collapse.gif
install -D -m 644 nagiosbpi/images/collapse.png $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/collapse.png
install -D -m 644 nagiosbpi/images/expand.gif $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/expand.gif
install -D -m 644 nagiosbpi/images/expand.png $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/expand.png
install -D -m 644 nagiosbpi/images/expand1.png $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/expand1.png
install -D -m 644 nagiosbpi/images/tip.gif $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/images/tip.gif
install -D -m 755 nagiosbpi/inc.inc.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/inc.inc.php
install -D -m 755 nagiosbpi/index.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/index.php
install -D -m 755 nagiosbpi/jquery-1.4.4.min.js $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/jquery-1.4.4.min.js
install -D -m 755 nagiosbpi/nagiosbpi.inc.php $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/nagiosbpi.inc.php
install -D -m 755 nagiosbpi/set_bpi_perms.sh $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/set_bpi_perms.sh

touch $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/bpi.conf.backup
chmod 666 $RPM_BUILD_ROOT%{_datarootdir}/nagios/html/bpi/bpi.conf.backup

%post

%clean
rm -rf $RPM_BUILD_ROOT

%files
%defattr(-,root,root,-)
%dir %attr(755,root,nagios) %{_datarootdir}/nagios/html/bpi/
%{_datarootdir}/nagios/html/bpi/BpGroup_class.php
%{_datarootdir}/nagios/html/bpi/CHANGELOG.txt
%{_datarootdir}/nagios/html/bpi/INSTALL
%{_datarootdir}/nagios/html/bpi/TODO
%{_datarootdir}/nagios/html/bpi/api_tool.php
%config(noreplace) %{_datarootdir}/nagios/html/bpi/bpi.conf
%config(noreplace) %{_datarootdir}/nagios/html/bpi/bpi.conf.backup
%{_datarootdir}/nagios/html/bpi/bpi.js
%{_datarootdir}/nagios/html/bpi/bpi_style.css
%{_libdir}/nagios/plugins/check_bpi.php
%dir %attr(755,root,nagios) %{_datarootdir}/nagios/html/bpi/config_functions/
%{_datarootdir}/nagios/html/bpi/config_functions/add_group.inc.php
%{_datarootdir}/nagios/html/bpi/config_functions/config_forms.php
%{_datarootdir}/nagios/html/bpi/config_functions/delete_group.php
%{_datarootdir}/nagios/html/bpi/config_functions/edit_group.php
%{_datarootdir}/nagios/html/bpi/config_functions/fix_config.php
%config(noreplace) %{_datarootdir}/nagios/html/bpi/constants.conf
%{_datarootdir}/nagios/html/bpi/constants.inc.php
%{_datarootdir}/nagios/html/bpi/footer.php
%dir %attr(755,root,nagios) %{_datarootdir}/nagios/html/bpi/functions/
%{_datarootdir}/nagios/html/bpi/functions/bpi2xml.php
%{_datarootdir}/nagios/html/bpi/functions/bpi_functions.php
%{_datarootdir}/nagios/html/bpi/functions/bpi_route_command.php
%{_datarootdir}/nagios/html/bpi/functions/process_post.php
%{_datarootdir}/nagios/html/bpi/functions/read_conf.php
%{_datarootdir}/nagios/html/bpi/functions/read_service_status.php
%{_datarootdir}/nagios/html/bpi/header.php
%dir %attr(755,root,nagios) %{_datarootdir}/nagios/html/bpi/images/
%{_datarootdir}/nagios/html/bpi/images/BPI-logo.png
%{_datarootdir}/nagios/html/bpi/images/children.png
%{_datarootdir}/nagios/html/bpi/images/collapse.gif
%{_datarootdir}/nagios/html/bpi/images/collapse.png
%{_datarootdir}/nagios/html/bpi/images/expand.gif
%{_datarootdir}/nagios/html/bpi/images/expand.png
%{_datarootdir}/nagios/html/bpi/images/expand1.png
%{_datarootdir}/nagios/html/bpi/images/tip.gif
%{_datarootdir}/nagios/html/bpi/inc.inc.php
%{_datarootdir}/nagios/html/bpi/index.php
%{_datarootdir}/nagios/html/bpi/jquery-1.4.4.min.js
%{_datarootdir}/nagios/html/bpi/nagiosbpi.inc.php
%{_datarootdir}/nagios/html/bpi/set_bpi_perms.sh




%changelog
* Mon Nov 14 2016 Jonathan Gazeley <jonathan.gazeley@bristol.ac.uk> - 1.3.1-3%{?dist}
- Deploy check_bpi plugin to the right place
* Mon Nov 14 2016 Jonathan Gazeley <jonathan.gazeley@bristol.ac.uk> - 1.3.1-2%{?dist}
- Permissions fixes
* Mon Sep 10 2012 Jonathan Gazeley <jonathan.gazeley@bristol.ac.uk> - 1.3.1-1%{?dist}
- initial package
