<?php

function genStaticConfig()
{
  $fname = "/lunchbox/staticconfig.php";

  $out = "";
  
  /*
  $PATH_ifconfig = "/sbin/ifconfig";
  $PATH_ethtool = "/usr/sbin/ethtool";
  $PATH_tcpdump = "/usr/sbin/tcpdump";
  */

  $out .= "<"."?"."php"."\n\n";

  // ifconfig
  $foo = "$"."PATH_ifconfig = ".'"'.trim(shell_exec('which ifconfig')).'";'."\n";
  $out .= $foo;

  // ethtool
  $foo = "$"."PATH_ethtool = ".'"'.trim(shell_exec('which ethtool')).'";'."\n";
  $out .= $foo;

  // tcpdump
  $foo = "$"."PATH_tcpdump = ".'"'.trim(shell_exec('which tcpdump')).'";'."\n";
  $out .= $foo;

  // dhclient
  $foo = "$"."PATH_dhclient = ".'"'.trim(shell_exec('which dhclient')).'";'."\n";
  $out .= $foo;

  $out .= '$'.'TCPDUMP_cdp = '.'" -nn -v -i %%ifname%% -s 1500 -c 1 \'ether[20:2] == 0x2000\'";'."\n";

  $out .= "\n\n";
  $out .= '?'.">"."\n";
  $fh = fopen($fname, "w");
  fwrite($fh, $out);
  fclose($fh);
  return true;
}

/**
 * Return an array of UP eth interfaces, like (ifname => array(mac, addr, netmask, bcast))
 *
 * @return array
 */
function getUpInterfaces()
{
  global $PATH_ifconfig;
  $ret = array();
  $foo = trim(shell_exec($PATH_ifconfig." -a"));
  $foo = explode("\n\n", $foo);
  foreach($foo as $str)
    {
      $bar = explode("\n", $str);
      $baz = "";
      foreach($bar as $s)
	{
	  if(substr(trim($s), 0, 9) == "inet addr")
	    {
	      $baz = $s;
	    }
	}
      $ifname = substr($bar[0], 0, strpos($bar[0], " "));
      $bar[0] = trim($bar[0]);
      $temp = array();
      $temp['mac'] = substr($bar[0], strrpos($bar[0], " ")+1);

      if($baz != "")
	{
	  $temp2 = explode(" ", $baz);
	  foreach($temp2 as $part)
	    {
	      if(substr($part, 0, 5) == "addr:")
		{
		  $temp['addr'] = trim(substr($part, strpos($part, ":")+1));
		}
	      elseif(substr($part, 0, 6) == "Bcast:")
		{
		  $temp['bcast'] = trim(substr($part, strpos($part, ":")+1));
		}
	      elseif(substr($part, 0, 5) == "Mask:")
		{
		  $temp['netmask'] = trim(substr($part, strpos($part, ":")+1));
		}
	    }
	}

      if(file_exists("/var/lib/dhcp3/dhclient.".$ifname.".leases"))
	{
	  $temp['dhcp'] = true;
	}
      
      if(substr($ifname, 0, 3) == "eth")
	{
	  $ret[$ifname] = $temp;
	}
    }
  return $ret;
}

/**
 * Return an array of CDP information for all up interfaces
 * @return array
 */
function getAllCDP()
{
  $foo = getUpInterfaces();
  $ret = array();
  foreach($foo as $ifname => $arr)
    {
      if(isset($arr['addr']))
	{
	  $ret[$ifname] = getCDP($ifname);
	}
    }
  return $ret;
}

/**
 * Write CDP info to a cache file.
 * @param array $CDParray array returned from getAllCDP()
 */
function cacheCDP($CDParray)
{
  $fh = fopen("/tmp/lunchbox/cdpcache.ser", "w");
  $str = serialize($CDParray);
  fwrite($fh, $str);
  fclose($fh);
}

/**
 * Get CDP information for an interface
 * @param $ifname string interface name
 * @return array
 */
function getCDP($ifname)
{
  global $TCPDUMP_cdp, $PATH_tcpdump;
  $cmd = $PATH_tcpdump." ".str_replace("%%ifname%%", $ifname, $TCPDUMP_cdp);
  echo $cmd."\n";
  $foo = trim(shell_exec($cmd));
  $bar = explode("\n", $foo);
  $ret = array();

  foreach($bar as $line)
    {
      $line = trim($line);
      if(substr($line, 0, 9) == "Device-ID")
	{
	  $ret['Device-ID'] = substr($line, strpos($line, "'")+1, (strrpos($line, "'") - strpos($line, "'"))-1);
	}
      elseif(substr($line, 0, 7) == "Address")
	{
	  $ret['Address'] = substr($line, strrpos($line, " ")+1);
	}
      elseif(substr($line, 0, 7) == "Port-ID")
	{
	  $ret['Port-ID'] = substr($line, strpos($line, "'")+1, (strrpos($line, "'") - strpos($line, "'"))-1);
	}
      elseif(substr($line, 0, 8) == "Platform")
	{
	  $ret['Platform'] = substr($line, strpos($line, "'")+1, (strrpos($line, "'") - strpos($line, "'"))-1);
	}
      elseif(substr($line, 0, 21) == "VTP Management Domain")
	{
	  $ret['VTP Management Domain'] = substr($line, strpos($line, "'")+1, (strrpos($line, "'") - strpos($line, "'"))-1);
	}
      elseif(substr($line, 0, 14) == "Native VLAN ID")
	{
	  $ret['Native VLAN ID'] = substr($line, strrpos($line, " ")+1);
	}
      elseif(substr($line, 0, 11) == "System Name")
	{
	  $ret['System Name'] = substr($line, strpos($line, "'")+1, (strrpos($line, "'") - strpos($line, "'"))-1);
	}
      elseif(substr($line, 0, 18) == "Management Address")
	{
	  $ret['Management Address'] = substr($line, strrpos($line, " ")+1);
	}
      elseif(substr($line, 0, 17) == "Physical Location")
	{
	  $ret['Physical Location'] = substr($line, strrpos($line, " ")+1);
	  if(strstr($ret['Physical Location'], "/"))
	    {
	      $ret['Physical Location'] = substr($ret['Physical Location'], strpos($ret['Physical Location'], "/")+1);
	    }
	}
    }
  return $ret;
}

/**
 * Get how long the system has been up in seconds.
 * @return integer
 */
function getSysUptime()
{
  $cmd = 'cat /proc/uptime | awk \'BEGIN { FS = " " } ; { print $2 }\'';
  $foo = trim(shell_exec($cmd));
  $bar = (int)$foo;
  return $bar;
}

function encrypt($str)
{
  global $cryptastic, $phoneHome_key, $phoneHome_salt;
  $key = $cryptastic->pbkdf2($phoneHome_key, $phoneHome_salt, 1000, 32);
  if(! $key){ die("Failed to generate key!");}

  $foo = $cryptastic->encrypt($str, $key);
  if(! $foo){ die("Failed to complete encryption.");};
  return $foo;
}

function decrypt($ciphertext)
{
  global $cryptastic, $phoneHome_key, $phoneHome_salt;
  $key = $cryptastic->pbkdf2($phoneHome_key, $phoneHome_salt, 1000, 32);
  if(! $key){ die("Failed to generate key!");}

  $foo = $cryptastic->decrypt($ciphertext, $key);
  if(! $foo){ die("Failed to complete encryption.");};
  return $foo;
}


?>