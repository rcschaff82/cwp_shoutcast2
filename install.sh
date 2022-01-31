#!/bin/bash
\cp -f shoutcast2.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
\cp -f update_class.php /usr/local/cwpsrv/htdocs/resources/admin/modules/
if ! id "shoutcast2" &>/dev/null; then
        useradd -m shoutcast2
fi
cd /home/shoutcast2
if ! [ -f "/home/shoutcast2/sc_serv" ];
then
wget http://download.nullsoft.com/shoutcast/tools/sc_serv2_linux_x64-latest.tar.gz
tar -xzf sc_serv2_linux_x64-latest.tar.gz
fi
if ! grep -q "\-- cwp_shoutcast2 --" /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
then
cat <<'EOF' >> /usr/local/cwpsrv/htdocs/resources/admin/include/3rdparty.php
<!-- cwp_shoutcast2 -->
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
                +' <a href="?module=shoutcast2" class=""><span aria-hidden="true" class="icon16 icomoon-icon-volume-high"></span>ShoutCast 2</a>'
                +'</li>';
                $("li#mn-3").before(newButtons);
        });
</script>
<!-- end cwp_shoutcast2 -->
EOF
fi
