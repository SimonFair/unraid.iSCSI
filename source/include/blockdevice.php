<?PHP
/* Copyright 2020, Simon Fairweather
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
#$old = (is_file("/boot/config/liochg.cfg")) ? rtrim(file_get_contents("/boot/config/liochg.cfg")) : '';

$new = $_GET["cfg"];

$newe=$x=explode(";", $new) ;
$newe= var_export($newe, true);
#if ($old !== $new) {
#  exec("cp -f /boot/config/vfio-pci.cfg /boot/config/liochg.cfg.bak");
  exec("echo \"$new\" >/tmp/liochg.cfg", $output, $myreturn );
  if ($myreturn !== "0") {
    echo "0";
  }
  exec("echo \"$newe\" >/tmp/liochg2.cfg", $output, $myreturn );
  if ($myreturn !== "0") {
    echo "0";
  }


  $bsblock="/backstore/block " ;
  $bspscsi="/backstore/pscsi "    ;
  $bsfileio="/backstores/fileio " ;
 
  /*

  0 => 'iscsiset',
  1 => 'disk',
  2 => '/dev/disk/by-id/scsi-35000cca027baa9a8',
  3 => 'false',
  4 => 'true',
  5 => 'iscsiro',
  6 => 'disk',
  7 => '/dev/disk/by-id/scsi-35000cca027baa9a8',
  8 => 'false',
  9 => 'true',

  */
  #build commands array
  # /backstores/block create usb-Sony_Hard_Drive_235853211C8A-0:0 /dev/disk/by-id/usb-Sony_Hard_Drive_235853211C8A-0:0 readonly=true 
  
 $index=0 ;
  $type = $x[$index+1] ;
  $name = substr($x[$index+2] ,16) ; 
  $devexist = $x[$index+3];
  $devchange =$x[$index+4];
  $readonly = $x[$index+8] ;
  $rochange = $x[$index+1] ;
  $key=$x[($index+2)] ;

 # if type=rom then
 #  $cmdstr=$bspscsi
 #  else $cmdstr=$bpblock    
 #  )

 # if remove
 # then add remove else add create_function
 # 
 # if exists and change of readonly
 #   if exists need to delete and then add as ro
if ($type="rom")  $cmd=$bspscsi ;
if ($type="disk") $cmd=$bsblck ;

if ($devexist && !$devchange) $cmd = $cmd."delete ".$name ;

$cmd=$type.$name." ".$devexist." ".$devchange ;

exec("echo \"$cmd\" >/tmp/liochg3.cfg", $output, $myreturn );
if ($myreturn !== "0") {
  echo "0";
}
?>