<?php
// Network Lunchbox - http://lunchbox.jasonantman.com
// $LastChangedRevision$
// $HeadURL$

require_once('lunchbox-funcs.php');
require_once('config.php');
require_once('serverconfig.php');
require_once('cryptastic.php');

$debug = false;

$cryptastic = new cryptastic;

if(! isset($_POST['bar']))
{
  die("<ul><li>You are the weakest link. <em>Goodbye.</em></li><li><em>You</em> are the weakest link. Goodbye.</li><li>You <em>are</em> the weakest link. Goodbye.</li><li>You are <em>the</em> weakest link. Goodbye.</li><li>You are the <em>weakest</em> link. Goodbye.</li><li>You are the weakest <em>link</em>. Goodbye. <strong>???</strong></li></ul>");
}

$bar = decrypt($_POST['bar']);
if($bar != $phoneHome_magicstring)
{
    die("MS error.");
}

$foo = decrypt($_POST['foo']);
$arr = unserialize($foo);

mysql_connect($dbHost, $dbUser, $dbPass) or die("Unable to connect to MySQL.");
mysql_select_db($dbName) or die("Unable to select database: $dbName");

$hostname = $arr['hostname'];

$query = "INSERT INTO ".$tblPfx."hosts SET hostname='".mysql_real_escape_string($arr['hostname'])."',uptime=".((int)$arr['uptime']).",updated_ts=".time()." ON DUPLICATE KEY UPDATE uptime=".((int)$arr['uptime']).",updated_ts=".time().";";
mysql_query($query) or die("Error in query.");

$query = "DELETE FROM ".$tblPfx."cdp WHERE hostname='".mysql_real_escape_string($arr['hostname'])."';";
mysql_query($query) or die("Error in query.");

foreach($arr['cdp'] as $ifname => $arr2)
{
    global $tblPfx, $hostname;
    $query = "INSERT INTO ".$tblPfx."cdp SET hostname='".mysql_real_escape_string($hostname)."',ifname='".mysql_real_escape_string($ifname)."',";
    $queryPart = "";
    if(isset($arr2['Device-ID'])){ $queryPart .= "DeviceID='".mysql_real_escape_string($arr2['Device-ID'])."',";}
    if(isset($arr2['Address'])){ $queryPart .= "Address='".mysql_real_escape_string($arr2['Address'])."',";}
    if(isset($arr2['Port-ID'])){ $queryPart .= "PortID='".mysql_real_escape_string($arr2['Port-ID'])."',";}
    if(isset($arr2['Platform'])){ $queryPart .= "Platform='".mysql_real_escape_string($arr2['Platform'])."',";}
    if(isset($arr2['VTP Management Domain'])){ $queryPart .= "VTPManagementDomain='".mysql_real_escape_string($arr2['VTP Management Domain'])."',";}
    if(isset($arr2['Native VLAN ID'])){ $queryPart .= "NativeVLANID='".mysql_real_escape_string($arr2['Native VLAN ID'])."',";}
    if(isset($arr2['System Name'])){ $queryPart .= "SystemName='".mysql_real_escape_string($arr2['System Name'])."',";}
    if(isset($arr2['Management Address'])){ $queryPart .= "ManagementAddress='".mysql_real_escape_string($arr2['Management Address'])."',";}
    if(isset($arr2['Physical Location'])){ $queryPart .= "PhysicalLocation='".mysql_real_escape_string($arr2['Physical Location'])."',";}

    $queryPart .= "updated_ts=".time()." ";
    $query .= $queryPart."ON DUPLICATE KEY UPDATE ".$queryPart.";";
    if($debug){ error_log("LUNCHBOX: ".$query);} // DEBUG
    mysql_query($query) or die("Error in query.");
}

if(isset($arr['interfaces']))
{
    $query = "DELETE FROM ".$tblPfx."interfaces WHERE hostname='".mysql_real_escape_string($arr['hostname'])."';";
    if($debug){ error_log("LUNCHBOX: ".$query);} // DEBUG
    mysql_query($query) or die("Error in query.");

    foreach($arr['interfaces'] as $ifname => $arr)
    {
	global $tblPfx, $hostname;
	$query = "INSERT INTO ".$tblPfx."interfaces SET hostname='".mysql_real_escape_string($hostname)."',ifname='".mysql_real_escape_string($ifname)."',addr='".mysql_real_escape_string($arr['addr'])."',mac='".mysql_real_escape_string($arr['mac'])."',bcast='".mysql_real_escape_string($arr['bcast'])."',netmask='".mysql_real_escape_string($arr['netmask'])."',updated_ts=".time()." ON DUPLICATE KEY UPDATE addr='".mysql_real_escape_string($arr['addr'])."',mac='".mysql_real_escape_string($arr['mac'])."',bcast='".mysql_real_escape_string($arr['bcast'])."',netmask='".mysql_real_escape_string($arr['netmask'])."',updated_ts=".time().";";
	if($debug){ error_log("LUNCHBOX: ".$query);} // DEBUG
	mysql_query($query) or die("Error in query.");
    }
}


?>