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
?>
<?
$docroot = $docroot ?? $_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/emhttp';
// add translations
$_SERVER['REQUEST_URI'] = '';
require_once "plugins/unraid.iSCSI/include/lib.php";
require_once "$docroot/webGui/include/Translations.php";
require_once "$docroot/webGui/include/Helpers.php";
?>
<!DOCTYPE html>
<html <?=$display['rtl']?>lang="<?=strtok($locale,'_')?:'en'?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Security-Policy" content="block-all-mixed-content">
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=1600">
<meta name="robots" content="noindex, nofollow">
<meta name="referrer" content="same-origin">
<link type="text/css" rel="stylesheet" href="<?autov("/webGui/styles/default-fonts.css")?>">
<link type="text/css" rel="stylesheet" href="<?autov("/webGui/styles/default-popup.css")?>">
<style>
span.key{width:104px;display:inline-block;font-weight:bold}
span.key.link{text-decoration:underline;cursor:pointer}
div.box{margin-top:8px;line-height:30px;margin-left:40px}
div.closed{display:none}
</style>
<script src="<?autov('/webGui/javascript/translate.'.($locale?:'en_US').'.js')?>"></script>
</head>
<body>
<div class="box">
<div></div>
<div><span class="key"><?=_('SCSI Devices')?>:</span>
<?


    $new = $_GET["DEV"] ;
    $delete =$_GET["Remove"] ;
    $nicknames=explode(";", $_GET["nicknames"]) ;
    $nc=count($nicknames) -1 ;
    $ni=0 ;
    $blockdevs=array() ;
    do {
       
       $blockdevice[$nicknames[$ni+1]] = array(
            "by-id"=>$nicknames[$ni+2],  
            "nickname"=>$nicknames[$ni+3] ) ;
   

        $ni=$ni+4 ;
        } while ($ni<$nc) ;

    
    $newe=$x=explode(";", $new) ;
    $cmd="" ;
    $c = count($newe) -1 ;
    $i = $ii = 0 ;

    do {
       
    $devtype=$newe[$i+1] ;
    $devblock=$newe[($i+2)] ;
    $devbyid=$blockdevice[$devblock]["by-id"] ;
    $devexist=$newe[($i+3)] ;
    $devchange=$newe[($i+4)] ;
    $devro=$newe[($i+8)] ;
    $devrochange=$newe[($i+9)] ;
 
    if ($blockdevice[$devblock]["nickname"]=="") 
        if (!is_bool(strpos($devbyid,'/dev/zvol'))) {
            $devname= substr($devbyid, 10) ;
            $devname = str_replace('/',":",$devname) ;
        } else $devname=substr($devbyid, 16) ;
    else $devname=$blockdevice[$devblock]["nickname"] ;
       
    if ($ii) echo "<br><span class='key'></span>&nbsp;";
    if ($devexist=="true" && $devchange =="false")  { 
        print($devname)  ; 
        $ii++ ;
    
        if ($devtype=="disk")   $cmd=$cmd."/backstores/block/ delete ".$devname."\n" ;
        if ($devtype=="rom")   $cmd=$cmd."/backstores/pscsi/ delete ".$devname."\n" ;
         }
    
    
    if ($fioaction=="true")   $cmd=$cmd."/backstores/fileio/ delete ".$fioname."\n" ;
    if ($devexist=="false" && $devchange=="true" && $devtype=="disk")   $cmd=$cmd."/backstores/block/ create ".$devname." ".$devbyid." readonly=".$devrochange."\n" ;
    if ($devexist=="false" && $devchange=="true" && $devtype=="rom")   $cmd=$cmd."/backstores/pscsi/ create ".$devname." ".$devbyid."\n" ;

        
    $i=$i+10 ;
    } while ($i<$c) ;

    echo '<input type="hidden" id="cmds" name="commands" value="'.$cmd.'"' ;
    echo "<br><span class='key'></span>&nbsp;";
    if ($delete=="No") { 
        echo "Processing commands" ;
        processTargetcli($cmd) ;
        echo '<script type="text/javascript">',
     'parent.window.location.reload();',
     '</script>' 
     ;
     return ;
    }
    
?>
</div>
<div style="margin-top:24px;margin-bottom:12px"><span class="key"></span>

<input type="button" value="<?=_('Cancel')?>" onclick="top.Shadowbox.close()">
<input type="button" value="<?=_('Confirm')?>" onclick="updateDevs()">
</div></div>
<script type="text/javascript" src="<?autov('/webGui/javascript/dynamix.js')?>"></script>
<script>
function updateDevs(){
    var string = document.getElementById('cmds').value ;
    $.get( "/plugins/unraid.iSCSI/include/processCommands.php", { cmd: string } )
    .done(function(d) {
        parent.window.location.reload();
    }
  );
}
</script>
</body>
</html>
