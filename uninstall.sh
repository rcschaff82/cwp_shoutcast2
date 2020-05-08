#!/bin/bash
\rm -f /usr/local/cwpsrv/htdocs/resources/admin/modules/shoutcast2.php
userdel -f shoutcast2
\rm -rf /home/shoutcast2
# Remove From Menu
sd=$(grep -n "<\!-- cwp_shoutcast2 --" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php | cut -f1 -d:)
ed=$(grep -n "<\!-- end cwp_shoutcast2 --" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php | cut -f1 -d:)
cmd="$sd"",""$ed""d"
sed -i.bak -e "$cmd" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php

