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
<div><span> <?=_('** All lower entries will be removed for selected higher level. **')?></span>
</div>
<?

   # $json=get_iscsi_json() ;
   # $nodes=build_iscsi_initiators($json) ;
    $new = $_GET["INIT"] ;
    $newe=$x=explode(";", $new) ;
    $previqn=$prevtgt="" ;
    $cmd="" ;
    $c = count($newe) -1 ;
    $i = $ii = 0 ;
        do {
       $inittype=$newe[$i] ;
       $initname=$initmap=$newe[$i+1] ;
       $initaction=$newe[($i+4)] ;
       $iqntgtname=$initmapiqn=$newe[($i+2)] ;
       $maptgtname=$newe[($i+3)] ;
      

    if ($initaction=="true" && $inittype =="iscsiiqn" && $iqntgtname != $prevtgt  )  { 
       echo '<div><div><span class="key"></span>';
        print("iqn Name:".$initname)  ; 
        $ii++ ; 
        $cmd=$cmd."/iscsi/".$iqntgtname."/tpg1/acls/ delete ".$initname."\n" ;
        $previqn=$initname ;
      }
    if ($initaction=="true" && $inittype =="iscsimap" && $initmapiqn != $previqn && $maptgtname != $prevtgt)  { 
      echo '<div><div><span class="key"></span>';
      print("Map number:".$initmap." for iqn:".$initmapiqn) ; 
      $ii++ ;
      $cmd=$cmd."/iscsi/".$maptgtname."/tpg1/acls/".$initmapiqn."/ delete ".$initmap."\n" ;
     }
     if ($initaction=="true" && $inittype =="iscsitgt" )  { 
      echo '<div><div><span class="key"></span>';
      print("Target:".$initname) ; 
      $ii++ ;
      $cmd=$cmd."/iscsi/ delete ".$initname."\n" ;
      $prevtgt=$initname ;
     }
  
    
    $i=$i+5 ;
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
