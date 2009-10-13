<?php

$PATH_ifconfig = "/sbin/ifconfig";
$PATH_ethtool = "/sbin/ethtool";
$PATH_tcpdump = "/usr/sbin/tcpdump";
$PATH_dhclient = "/sbin/dhclient";
$TCPDUMP_cdp = " -nn -v -i %%ifname%% -s 1500 -c 1 'ether[20:2] == 0x2000'";


?>
