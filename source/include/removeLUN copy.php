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
extract(parse_plugin_cfg('dynamix',true));

$var = parse_ini_file('state/var.ini');

function dmidecode($key,$n,$all=true) {
  $entries = array_filter(explode($key,shell_exec("dmidecode -qt$n")));
  $properties = [];
  foreach ($entries as $entry) {
    $property = [];
    foreach (explode("\n",$entry) as $line) if (strpos($line,': ')!==false) {
      list($key,$value) = explode(': ',trim($line));
      $property[$key] = $value;
    }
    $properties[] = $property;
  }
  return $all ? $properties : $properties[0];
}
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
<script>
// server uptime & update period
var uptime = <?=strtok(exec("cat /proc/uptime"),' ')?>;

function add(value, label, last) {
  label += (parseInt(value)!=1?'s':'');
  return parseInt(value)+' '+_(label)+(!last?', ':'');
}
function two(value, last) {
  return (parseInt(value)>9?'':'0')+parseInt(value)+(!last?':':'');
}
function updateTime() {
  document.getElementById('uptime').innerHTML = add(uptime/86400,'day')+two(uptime/3600%24)+two(uptime/60%60)+two(uptime%60,true);
  uptime++;
  setTimeout(updateTime, 1000);
}
</script>
</head>
<body onLoad="updateTime()">
<div class="box">
<div><span class="key"><?=_('Model')?>:</span>
</div>
<div><span class="key link" onclick="document.getElementsByClassName('dimm_info')[0].classList.toggle('closed')"><?=_('Memory')?>:</span>
<?
/*
 Memory Device (16) will get us each ram chip. By matching on MB it'll filter out Flash/Bios chips
 Sum up all the Memory Devices to get the amount of system memory installed. Convert MB to GB
 Physical Memory Array (16) usually one of these for a desktop-class motherboard but higher-end xeon motherboards
 might have two or more of these.  The trick is to filter out any Flash/Bios types by matching on GB
 Sum up all the Physical Memory Arrays to get the motherboard's total memory capacity
 Extract error correction type, if none, do not include additional information in the output
 If maximum < installed then roundup maximum to the next power of 2 size of installed. E.g. 6 -> 8 or 12 -> 16
*/
$sizes = ['MB','GB','TB'];
$memory_type = $ecc = '';
$memory_installed = $memory_maximum = 0;
$memory_devices = dmidecode('Memory Device','17');
foreach ($memory_devices as $device) {
  if ($device['Type']=='Unknown') continue;
  list($size, $unit) = explode(' ',$device['Size']);
  $base = array_search($unit,$sizes);
  if ($base!==false) $memory_installed += $size*pow(1024,$base);
  if (!$memory_type) $memory_type = $device['Type'];
}
$memory_array = dmidecode('Physical Memory Array','16');
foreach ($memory_array as $device) {
  [$size, $unit] = explode(' ',$device['Maximum Capacity']);
  $base = array_search($unit,$sizes);
  if ($base>=1) $memory_maximum += $size*pow(1024,$base);
  if (!$ecc && $device['Error Correction Type']!='None') $ecc = "{$device['Error Correction Type']} ";
}
if ($memory_installed >= 1024) {
  $memory_installed = round($memory_installed/1024);
  $memory_maximum = round($memory_maximum/1024);
  $unit = 'GiB';
} else $unit = 'MiB';

// If maximum < installed then roundup maximum to the next power of 2 size of installed. E.g. 6 -> 8 or 12 -> 16
$low = $memory_maximum < $memory_installed;
if ($low) $memory_maximum = pow(2,ceil(log($memory_installed)/log(2)));
echo "$memory_installed $unit $memory_type $ecc("._('max. installable capacity')." $memory_maximum $unit".($low?'*':'').")";
?>
<div class="dimm_info closed">
<?
foreach ($memory_devices as $device) {
  if ($device['Type']=='Unknown') continue;
  $size = preg_replace('/( .)B$/','$1iB',$device['Size']);
  echo "<span class=\"key\"></span> {$device['Manufacturer']} {$device['Part Number']}, {$size} {$device['Type']} @ {$device['Configured Memory Speed']}";
}
?>
</div>
</div>
<div><span class="key"><?=_('LUNS')?>:</span></div><div>
<?
$json=get_iscsi_json() ;
$nodes=build_iscsi_initiators($json) ;
echo count($luns)."\n" ;
var_dump($luns) ;
?>
</div>
<div><span class="key"><?=_('Uptime')?>:</span> <span id="uptime"></span></div>
<div style="margin-top:24px;margin-bottom:12px"><span class="key"></span>
<input type="button" value="<?=_('Close')?>" onclick="top.Shadowbox.close()">
<?if ($_GET['more']):?>
<a href="<?=htmlspecialchars($_GET['more'])?>" class="button" style="display:inline-block;padding:1px" target="_parent"><?=_('More')?></a>
<?endif;?>
</div></div>
</body>
</html>
