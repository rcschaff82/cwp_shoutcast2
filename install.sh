#!/bin/bash
useradd -m shoutcast2
cd /home/shoutcast2
wget http://download.nullsoft.com/shoutcast/tools/sc_serv2_linux_x64-latest.tar.gz
tar -xzf sc_serv2_linux_x64-latest.tar.gz
cp shoutcast2.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
cat <<'EOF' >> /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
<noscript>
</ul>
<li class="custom-menu"> <!-- this class "custom-menu" was added so you can remove the Developer Menu easily if you want -->
    <a href="?module=shoutcast2"><span class="icon16 icomoon-icon-volume-high"></span>ShoutCast 2</a>
</li>
<li style="display:none;"><ul>
</noscript>
<script type="text/javascript">
        $(document).ready(function() {
                var newButtons = ''
                +' <li>'
                +' <a href="?module=shoutcast2" class=""><span aria-hidden="true" class="icon16 icomoon-icon-volume-hight"></span>ShoutCast 2</a>'
                +'</li>';
                $("li#mn-3").before(newButtons);
        });
</script>
EOF
