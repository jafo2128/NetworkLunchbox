#!/usr/bin/php
<?php
// Network Lunchbox v0.1
// lunchbox.php - main wrapper script
require_once('/lunchbox/version.php');
require_once('/lunchbox/staticconfig.php');
require_once('/lunchbox/lunchbox-funcs.php');

lunchbox_header();
while(true)
  {
    lunchmenu();
  }

function lunchbox_header()
{
  global $vernum;
  echo "============================================================\n";
  echo "== ".str_pad("Network LunchBox ".$vernum, 54, " ", STR_PAD_BOTH)." ==\n";
  echo "== ".str_pad("http://lunchbox.jasonantman.com", 54, " ", STR_PAD_BOTH)." ==\n";
  echo "============================================================\n";
  echo "============================================================\n";
  echo "== ".str_pad("Hostname: ".trim(shell_exec("hostname")), 54)." ==\n";

  foreach(getUpInterfaces() as $ifname => $arr)
    {
      if(isset($arr['addr']))
	{
	  $foo = "\033[0;32m".$ifname." up\033[m\t";
	  $foo .= $arr['mac']."\t".$arr['addr'];
	  echo "== ".str_pad($foo, 53)." ==\n";
	}
      else
	{
	  $foo = "\033[0;31m".$ifname." down\033[m\t";
	  $foo .= $arr['mac']."\t";
	  echo "== ".str_pad($foo, 53)."   ==\n";
	}
    }

  echo "============================================================\n";
}

function lunchmenu()
{
  global $PATH_dhclient, $PATH_tcpdump;

  echo "\n\n";
  echo "============================================================\n";
  echo " 0/q\t\tquit\n";
  echo " 1/cdp\t\tShow cached CDP information\n";
  echo " 2/dhcpdump\tDump DHCP packets (tcpdump) on selected interface\n";
  echo " 3/dhcp\t\tGet DHCP (dhclient) on selected interface\n";
  echo " 4/ew\t\tEnable write on root partition\n";
  echo " 5/dw\t\tDisable write on root partition\n";
  echo "\n";
  fwrite(STDOUT, "Selection: ");
  $foo = trim(fgets(STDIN));

  echo "============================================================\n";
  echo "\n\n";

  if($foo == "0" || $foo == "q" || $foo == "quit")
    {
      die();
    }
  elseif($foo == "1" || $foo == "cdp")
    {
      showCDPcache();
    }
  elseif($foo == "2" || $foo == "dhcpdump")
    {
      $ifName = ifMenu();
      $cmd = $PATH_tcpdump." -i $ifName -s 1500 -vvv -n port 67 or port 68";
      echo $cmd."\n";
      passthru($cmd);
    }
  elseif($foo == "3" || $foo == "dhcp")
    {
      $ifName = ifMenu();
      $cmd = $PATH_dhclient." ".$ifName;
      passthru($cmd);
    }
  elseif($foo == "4" || $foo == "ew")
   {
      shell_exec("enablewrite");
   }
  elseif($foo == "5" || $foo == "dw")
   {
      shell_exec("disablewrite");
   }
}

function showCDPcache()
{
  if(! file_exists('/tmp/lunchbox/cdpcache.ser'))
    {
      echo "CDP CACHE FILE ('/tmp/lunchbox/cdpcache.ser') DOES NOT EXIST!\nPerhaps the phoneHome.php script has not yet run?\n";
      return false;
    }
  $foo = file_get_contents('/tmp/lunchbox/cdpcache.ser');
  $mtime = filemtime('/tmp/lunchbox/cdpcache.ser');
  $cdp = unserialize($foo);
  if(! $cdp){ return false;}
  echo "Cache Age: ".prettyTime(time() - $mtime)."\n";
  foreach($cdp as $ifname => $arr)
    {
      echo "=== $ifname\n";
      foreach($arr as $key => $val)
	{
	  echo str_pad("      ".$key, 30, ".").$val."\n";
	}
    }
}

function ifMenu()
{
  echo "SELECT INTERFACE: \n";
  $choices = array();
  $count = 0;
  foreach(getUpInterfaces() as $ifname => $arr)
    {
      $choices[$count] = $ifname;
      echo " ".$count.") ".$ifname." (";
      if(isset($arr['addr'])) { echo "\033[0;32mup\033[m";} else { echo "\033[0;31mdown\033[m\t";}
      echo ")\n";
      $count++;
    }
  $foo = trim(fgets(STDIN));
  if(array_key_exists(((int)$foo), $choices))
    {
      return $choices[((int)$foo)];
    }
  elseif(in_array($foo, $choices))
    {
      return array_search($foo, $choices);
    }
  else
    {
      return ifMenu();
    }
}

function prettyTime($i)
{
  $s = "";
  if($i > 86400)
    {
      $foo = (int)($i / 86400);
      $s = $foo."d";
      $i = $i % 86400;
    }
  if($i > 3600)
    {
      $foo = (int)($i / 3600);
      $s .= $foo."h";
      $i = $i % 3600;
    }
  if($i > 60)
    {
      $foo = (int)($i / 60);
      $s .= $foo."m";
      $i = $i % 60;
    }
  $s .= ((int)$i)."s";
  return $s;
}

?>
