<?php
if ( !isset( $include_path ) )
{
    echo "invalid access";
    exit( );
}


class shoutcast2 {
        private $dir = "/home/shoutcast2";
	private $natip;
        private $status_s2 = "";
        private $version_s2 = "";
        private $github_branches= "https://api.github.com/repos/rcschaff82/cwp_2fa/branches";
        private $github_url = "https://api.github.com/repos/rcschaff82/cwp_2fa/commits?per_page=1&sha=";
        private $github_branches2 = "https://api.github.com/repos/phalcon/cphalcon/branches";
        private $github_url2 = "https://api.github.com/repos/phalcon/cphalcon/commits?per_page=1&sha=";
        public function __construct()
        {
		$this->natip = trim(shell_exec("dig +short myip.opendns.com @resolver1.opendns.com"));
                echo '<center><b>Check if ShoutCast2 installed</b></center><br>';
        }
        public function initalize()
        {
                ///  This is the main function
                $this->check_is_s2_loaded();
                //$this->date_last_commit();
        }
        public function get_s2_version()
        {
                $s2_version = shell_exec("sudo -u shoutcast2 /home/shoutcast2/sc_serv -v");
                $this->version_s2 = $s2_version;
        }
        public function check_is_s2_loaded()
        {
                $php = shell_exec("sudo -u shoutcast2 command -v /home/shoutcast2/sc_serv");
        if ($php) {
                $this->get_s2_version();
                $this->alert = "alert-success";
                $this->message = "<strong>Success!</strong><br>$this->version_s2";
                $this->toHtml();
                $this->add_server();
                $this->list_server();
        }
        else {
        $this->alert = "alert-info";
            $this->message = "<strong>Info!</strong> Shoutcast2 Is not installed.<br>
            To install the ShoutCast follow the guidelines below, or use the auto install script.";

            //Show Help Installation
            $this->toHtml();
            $this->message_install();
        }
        }
        private function list_server() {
switch(@$_POST['action']) {
case "srv_start":
$filer =  @$_POST['srv_id'];
$cmd = "/home/shoutcast2/$filer.sh";
exec($cmd);
echo "Starting";
sleep(4);
break;
case "srv_stop":
$filer =  @$_POST['srv_id'];
echo "Stoppping<br>";
$ini_array = parse_ini_file("/home/shoutcast2/$filer");
 $port = $ini_array['portbase'];
$cmd = "kill `cat /home/shoutcast2/sc_serv_$port.pid`";
shell_exec($cmd);
sleep(4);
break;
case "srv_delete":
$filer =  @$_POST['srv_id'];
echo "Deleting";
$ini_array = parse_ini_file("/home/shoutcast2/$filer");
 $port = $ini_array['portbase'];
$port2 = intval($port) + 1;
$cmd = "kill `cat /home/shoutcast2/sc_serv_$port.pid`";
shell_exec($cmd);
sleep(4);
unlink("/home/shoutcast2/$filer");
unlink("/home/shoutcast2/$filer.sh");
copy("/etc/csf/csf.conf","/etc/csf/csf.conf.bu");
shell_exec("sed -i 's/,".$port.",".$port2."//g' /etc/csf/csf.conf");
break;
}
                echo <<<EOS
                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: 100%; height: auto;"><div class="table-responsive" style="overflow: hidden; width: 100%; height: auto;"><table class="table table-bordered">
<thead><tr>
<th>Server Status</th>
<th>Edit Server</th>
<th>Start Server</th>
<th>Stop Server</th>
<th>Admin Panel</th>
<th>Delete Server</th>
</tr></thead>

<tbody>
EOS;
foreach(glob($this->dir.'/*.conf') as $file) {
 $full = $file;
 $base = basename($file);
 $ini_array = parse_ini_file($file);
 $port = $ini_array['portbase'];
 $ip = (array_key_exists("srcip",$ini_array))?$ini_array['srcip']:$this->natip;
 $online = (file_exists($this->dir.'/sc_serv_'.$port.'.pid'))? "checkmark":"close";
echo <<<EOS
                                <tr><td><span title="Status" class="icon12 minia-icon-$online"></span></td><td><a href="index.php?module=file_editor&amp;file=$full">$base</a></td>
                                <td><form action="" method="post" onsubmit="return confirm('Are you sure you want to start server: $base ?');"><input type="hidden" name="srv_id" value="$base" size="0"><input type="hidden" name="action" size="0" value="srv_start"><div class="form-group"><button type="submit" class="btn btn-success btn-xs">Start</button></div></form></td>
                                <td><form action="" method="post" onsubmit="return confirm('Are you sure you want to stop server: $base ?');"><input type="hidden" name="srv_id" value="$base" size="0"><input type="hidden" name="action" size="0" value="srv_stop"><div class="form-group"><button type="submit" class="btn btn-warning btn-xs">Stop</button></div></form></td>
                                <td><div class="form-group"><button type="submit" onclick="location.href='//{$ip}:$port'" class="btn btn-default btn-xs">Admin Panel</button></div></td>
                                <td><form action="" method="post" onsubmit="return confirm('Are you sure you want to delete server: $base ?');"><input type="hidden" name="srv_id" value="$base" size="0"><input type="hidden" name="action" size="0" value="srv_delete"><div class="form-group"><button type="submit" class="btn btn-danger btn-xs">Delete</button></div></form></td></tr>
EOS;
}
echo <<<EOS
                        </tbody></table></div><div class="slimScrollBar ui-draggable" style="background: rgb(243, 243, 243); height: 5px; position: absolute; bottom: 3px; opacity: 0.4; display: none; border-radius: 5px; z-index: 99; width: 1117px;"></div><div class="slimScrollRail" style="width: 100%; height: 5px; position: absolute; bottom: 3px; display: none; border-radius: 5px; background: rgb(51, 51, 51); opacity: 0.3; z-index: 90;"></div></div>


EOS;


        }
        ///  Should be done ///   ip -4 addr | grep -oP '(?<=inet\s)\d+(\.\d+){3}'
        private function add_server() {
		$ips = networking_inet_ips();
		global $mysql_conn;
	//	$resp=mysqli_query($mysql_conn,"Select `username` FROM `user`");
	//	while($row=mysqli_fetch_assoc($resp)) {
		//var_dump($row);
	//	}
		$hostname = get_hostname();
		$selectip = "<label>Src/DstIP</label><select name='ip'><option value='ALL'>ALL</option>";
		foreach($ips as $ip) {
			if (ipv4_manage_is_ip_private( trim($ip))) $nat="*";
			$selectip .= "<option value='$ip'>$ip$nat</option>";
		}
		$selectip .="</select><br>";
echo <<<EOT
<div>* Requires a NAT connection.  Detected external ip of $this->natip</div>
<form method="post">
<input type="hidden" name="doadd" value="start">
<label>Port</label><input type="input" name="port" value="8000"><br>
<label>DJ Password</label><input type="input" name="pass" value=""><br>
<label>Admin Pass</label><input type="input" name="admin" value=""><br>
{$selectip}
<button type="submit" name="submit" value="submit">Add a Server</button>

</form>

EOT;

                 if(@$_POST['doadd'] == 'start') {
                        $port = intval($_POST['port']);
                        $port2 = $port + 1;
			$pass = $_POST['pass'];
                        $admin = $_POST['admin'];
			$ip = $_POST['ip'];
    			if (($se = shell_exec("netstat -paln | grep LISTEN | grep -oP :$port")) != "") {
                                $this->alert = "alert-error";
				$this->message = "<strong>Error!</strong><br>$port already in use by another application!";
				$this->toHtml();
				return false;
                        }
                        if (strlen($admin) < 6 || strlen($pass) < 6) {
                                $this->alert = "alert-error";
                                $this->message = "<strong>Error!</strong><br>Passwords must be at least 6 characters!";
                                $this->toHtml();
                                return false;
                        }
                        if ($admin == $pass) {
                                $this->alert = "alert-error";
                                $this->message = "<strong>Error!</strong><br>You cannot use the same password for both DJ and Admin";
                                $this->toHtml();
                                return false;
                        }

                        if ($port % 2 != 0) {
                                $this->alert = "alert-error";
                                $this->message = "<strong>Error!</strong><br>$port should only be an even number!";
                                $this->toHtml();
                                return false;

                        }
                        if (shell_exec("grep $port /home/shoutcast2/*.conf")) {
                                $this->alert = "alert-error";
                                $this->message = "<strong>Error!</strong><br>$port is already in use!";
                                $this->toHtml();
                                return false;
                        }
                        $input = <<<EOF
logfile=logs/sc_{$port}.log
w3clog=logs/sc_w3c_{$port}.log
banfile=control/sc_serv{$port}.ban
ripfile=control/sc_serv{$port}.rip
portbase={$port}
password={$pass}
adminpassword={$admin}
publicserver=always

EOF;
if ($ip != "ALL") {
   $input .= "srcip=$ip\ndstip=$ip";
}
$input2 = <<<EOF
#!/bin/bash
sudo -u shoutcast2 /home/shoutcast2/sc_serv daemon /home/shoutcast2/$port.conf > /dev/null 2>/dev/null &
EOF;
                file_put_contents("/home/shoutcast2/$port.conf",$input);
                file_put_contents("/home/shoutcast2/$port.conf.sh",$input2);
                chown("/home/shoutcast2/$port.conf","shoutcast2");
                chown("/home/shoutcast2/$port.conf.sh","shoutcast2");
                chgrp("/home/shoutcast2/$port.conf","shoutcast2");
                chgrp("/home/shoutcast2/$port.conf.sh","shoutcast2");
                chmod("/home/shoutcast2/$port.conf.sh",0755);
                copy("/etc/csf/csf.conf","/etc/csf/csf.conf.bu");
		shell_exec('sed -i -re "s@TCP_IN(.*)(\")@TCP_IN\1,'.$port.','.$port2.'\2@" /etc/csf/csf.conf');
		shell_exec('sed -i -re "s@TCP_OUT(.*)(\")@TCP_OUT\1,'.$port.','.$port2.'\2@" /etc/csf/csf.conf');
		shell_exec('sed -i -re "s@TCP6_IN(.*)(\")@TCP6_IN\1,'.$port.','.$port2.'\2@" /etc/csf/csf.conf');
		shell_exec('sed -i -re "s@TCP6_OUT(.*)(\")@TCP6_OUT\1,'.$port.','.$port2.'\2@" /etc/csf/csf.conf');
		
		}
        }
        private function message_install()
        {
        echo <<<EOD
        <form method="post">
        <input type="hidden" name="install" value="start">
        <button type="submit" name="submit" value="submit">Install Shoutcast2</button>
        </form>

EOD;
         if(@$_POST['install'] == 'start') {
                 shell_exec("useradd -m shoutcast2");
                shell_exec("cd /home/shoutcast2 && wget http://download.nullsoft.com/shoutcast/tools/sc_serv2_linux_x64-latest.tar.gz && tar -xzf sc_serv2_linux_x64-latest.tar.gz && chown -R shoutcast2:shoutcast2 *");
                echo "Please refresh the module";
        }

        }

        public function toHtml()
        {
                        echo '<div class="alert '.$this->alert.'">
                                <a class="close" data-dismiss="alert">×</a>
                                '.$this->message.'
                               </div>';
        }

        public function date_last_commit()
        {
                $context = stream_context_create(array(
                  'http' => array(
                        'header'=> "User-Agent: http://mikeangstadt.name\r\n"
                  )
                ));

                $branches = @json_decode(file_get_contents($this->github_branches, false, $context));
                //var_dump($branches);
                $response = @file_get_contents($this->github_url.$branches[0]->commit->sha, false, $context);

                if ($response === false){
                  //throw new Exception("Error contacting github.");
                }

                //parse the JSON
                $json = json_decode($response);
                if ($json === null){
                  //throw new Exception("Error parsing JSON response from github.");
                }
                if (isset($json->error)){
                  throw new Exception($json->error);
                }

                $date = new DateTime($json[0]->commit->author->date);

                $this->date_last_commits = $date->format("Y-m-d H:i:s");
                $this->last_commits_message = $json[0]->commit->message;

                $this->update();
                //return $date->format("Y-m-d H:i:s");
        }

        public function update()
        {

                echo '<center><b>Github last commits date:</b><br>';
                echo $this->date_last_commits.'<br>';
                echo $this->last_commits_message.'<br><br>';

                if(@$_POST['update'] == 'start') {
                $this->message_install();
                } else {
                echo '<div class="btn-group">
                      <form action="index.php?module=shoutcast2" method="POST">
                          <input type="hidden" name="update" value="start">
                          <button class="btn btn-warning">Show Update Instruction</button>
                      </form>
                      </div></center><br>';
                }

        }


}
try {
        include_once('update_class.php');
        $update = new gitupdate('rcschaff82','cwp_shoutcast2','shoutcast2');
        $force = (isset($_GET['forceupdate']))?'Y':'N';
        $update->checkupdate($force);
} catch (exception $e) {
        $exception = $e->getMessage();
}

$shoutcast = new shoutcast2();
$shoutcast->initalize();

?>
