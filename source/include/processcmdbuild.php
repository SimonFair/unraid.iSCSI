<?PHP
/* Copyright 2020,Simon Fairweather

 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
function processTargetcli($cmdstr) {
	# Write command string a process
	# targetctl  /tmp/string > /var/run/targetcli.last
	#exec($cmdstr  ,$tj) ;
    echo "Command Processing......" ;
    $cmd=$cmdstr."\nexit\n"  ;
    exec("echo \"$cmd\" >/tmp/iscsicmd.run", $output, $myreturn );
	$cmd="targetcli </tmp/iscsicmd.run >/var/run/targetcli.last";
    exec("echo \"$cmd\" >/tmp/cmd.last", $output, $myreturn );
    #exec($cmd, $output, $return) ;

  
}
$cmd = $_GET["cmd"] ;
processTargetcli($cmd) ;
return(0) ;
?>