#!/usr/bin/php
<?php
  // Network Lunchbox phone home script
require_once('/lunchbox/lunchbox-funcs.php');
require_once('/lunchbox/staticconfig.php');
require_once('/lunchbox/config.php');
require_once('/lunchbox/cryptastic.php');

$debug = true;

$cryptastic = new cryptastic;
doPhoneHome();

function doPhoneHome()
{
  global $phoneHome_url, $phoneHome_magicstring, $debug;
  fwrite(STDOUT, "Getting info and making array...\n"); // DEBUG
  $foo = makePhoneHomeArray(); // UNCOMMENT WHEN FINISHED WITH DEBUG
  if($debug){ echo var_dump($foo);}
  fwrite(STDOUT, "Serializing...\n"); // DEBUG
  $str = serialize($foo);
  fwrite(STDOUT, "Encrypting...\n"); // DEBUG
  $c = encrypt($str);
  fwrite(STDOUT, "Encrypting magic string...\n"); // DEBUG
  $s = encrypt($phoneHome_magicstring);
  fwrite(STDOUT, "Making POST variable string...\n"); // DEBUG
  $postData = "foo=".urlencode($c)."&bar=".urlencode($s);
  fwrite(STDOUT, "POSTing...\n"); // DEBUG
  $ch = curl_init($phoneHome_url);
  curl_setopt($ch, CURLOPT_POST      ,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS    ,$postData);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  $Rec_Data = curl_exec($ch);
  fwrite(STDOUT, "Done.\n"); // DEBUG
}

function makePhoneHomeArray()
{
  // gather the info for the phone home array
  $interfaces = getUpInterfaces();
  $cdp = getAllCDP();

  // cache CDP
  cacheCDP($cdp);

  $hostname = trim(shell_exec('hostname'));
  $uptime = getSysUptime();
  $arr = array("interfaces" => $interfaces, "hostname" => $hostname, "cdp" => $cdp, "uptime" => $uptime);
  return $arr;
}

?>