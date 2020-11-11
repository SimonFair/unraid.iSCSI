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
<div><span> <?=_('** Luns will be removed for each File IO removed. **')?></span>
</div>
<div></div>
<div><span class="key"><?=_('File IO')?>:</span>
<?

   # $json=get_iscsi_json() ;
   # $nodes=build_iscsi_initiators($json) ;
    $new = $_GET["FIO"] ;
    $newe=$x=explode(";", $new) ;
   
    $cmd="" ;
    $c = count($newe) -1 ;
    $i = $ii = 0 ;
    do {
       $fioname=$newe[$i+1] ;
       $fioaction=$newe[($i+3)] ;
       $fiopath=$newe[($i+2)] ;
      
       
    if ($ii && $fioaction=="true") echo "<br><span class='key'></span>&nbsp;";
    if ($fioaction=="true")  { print("Name:".$fioname."=>".$fiopath)  ; $ii++ ; }
    
    
    if ($fioaction=="true")   $cmd=$cmd."/backstores/fileio/ delete ".$fioname."\n" ;

    $i=$i+4 ;
    } while ($i<$c) ;

    echo '<input type="hidden" id="cmds" name="commands" value="'.$cmd.'"' ;
    
?>
</div>
<div style="margin-top:24px;margin-bottom:12px"><span class="key"></span>
<input type="button" value="<?=_('Cancel')?>" onclick="top.Shadowbox.close()">
<input type="button" value="<?=_('Confirm')?>" onclick="removeFIO()">


</div></div>

<script type="text/javascript" src="<?autov('/webGui/javascript/dynamix.js')?>"></script>
<script>
function removeFIO(){
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
