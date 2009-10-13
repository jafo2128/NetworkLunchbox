Network Lunchbox v0.1 by Jason Antman

# Network Lunchbox - http://lunchbox.jasonantman.com
# $LastChangedRevision$
# $HeadURL$

http://lunchbox.jasonantman.com

SETTING UP ON A NEW DEVICE:
-regenerate SSH host keys
-change root password (default 'changeme')
-change lunchbox user password (default 'lunchbox')
-change the hostname (/etc/hostname)
-run /lunchbox/setup.php
- You should also update a DNS pointer to the box.

HOW TO USE:
By default, the device will come up with eth0 configured via DHCP and eth1 and eth2 down. This is to make sure that routing and everything comes up.

Your main tool should be the "lunchbox" script, which more or less wraps everything important. It's just a shell script that gives you a menu-based interface to common commands.

