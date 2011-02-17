<?php  //used only for XI Component installations 
// NAGIOS BPI COMPONENT XI-MOD 
//
// Copyright (c) 2010 Nagios Enterprises, LLC.  All rights reserved.
//  
// $Id: bpi2xi.inc.php 73 2010-07-14 20:44:23Z mguthrie $

require_once(dirname(__FILE__).'/../componenthelper.inc.php');

// respect the name
$bpi_component_name="nagiosbpi";

// run the initialization function
bpi_component_init();

////////////////////////////////////////////////////////////////////////
// COMPONENT INIT FUNCTIONS
////////////////////////////////////////////////////////////////////////

function bpi_component_init()
{
	global $bpi_component_name;
	
	$versionok=bpi_component_checkversion();
	
	$desc="IMPORTANT: Run the 'set_bpi_perms.sh' script after installation.";
	if(!$versionok)
		$desc="<b>Error: This component requires Nagios XI 2009R1.2B or later.</b>";
	
	$args=array(
		// need a name
		COMPONENT_NAME => $bpi_component_name,		
		// informative information
		COMPONENT_AUTHOR => "Nagios Enterprises, LLC",
		COMPONENT_DESCRIPTION => "Advanced grouping and dependency tool. Can be used for specialized checks. ".$desc,
		COMPONENT_TITLE => "Nagios BPI",
		// configuration function (optional)
		//COMPONENT_CONFIGFUNCTION => "bpi_component_config_func",
		);
		
	register_component($bpi_component_name,$args);
	
	// add a menu link
	if($versionok)
		register_callback(CALLBACK_MENUS_INITIALIZED,'bpi_component_addmenu');
	}
	



///////////////////////////////////////////////////////////////////////////////////////////
// MISC FUNCTIONS
///////////////////////////////////////////////////////////////////////////////////////////

function bpi_component_checkversion(){

	if(!function_exists('get_product_release'))
		return false;
	//requires greater than 2009R1.2
	if(get_product_release()<114)
		return false;

	return true;
	}
	
function bpi_component_addmenu($arg=null){
	global $bpi_component_name;
	
	$urlbase=get_component_url_base($bpi_component_name);
	
	
	$mi=find_menu_item(MENU_HOME,"menu-home-servicegroupgrid","id");
	if($mi==null)
		return;
		
	$order=grab_array_var($mi,"order","");
	if($order=="")
		return;
			
	$neworder=$order+0.1;	
	add_menu_item(MENU_HOME,array(
			"type" => "linkspacer",
			"title" => "",
			"id" => "menu-home-bpa_spacer",
			"order" => $neworder,
			"opts" => array()
			));
			
	$neworder=$neworder+0.1;
	add_menu_item(MENU_HOME,array(
		"type" => "link",
		"title" => "Nagios BPI",
		"id" => "menu-home-bpi",
		"order" => $neworder,
		"opts" => array(
			"href" => $urlbase."/index.php",
			)
		));

	}


?>