<?PHP
/* Copyright 2005-2020, Lime Technology
 * Copyright 2012-2020, Bergware International.
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

$newe=explode(";", $new) ;
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

  $bsblockcrt="/backstore/block create " ;
  $bsblockcrt="/backstore/block delete " ;
  $bspscsi="/backstore/pscsi create "    ;
  $bspscsi="/backstore/pscsi delete "    ;
  $bsfileio="/backstores/fileio create  " ;
  $bsfileio="/backstores/fileio delete  " ;
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
$index=0
do {

  $type = $x[$index+1] ;
  $name = $x[$index+2] ; need to remove /dev/disk/by-id 
  $name = strpos()
  $devexist = $x[$index+3];
  $devchange =$x[$index+4];
  $readonly = $x[$index+8] ;
  $rochange = $x[$index+1] ;
  $key=$x[($index+2)] ;

  if type=rom then
   $cmdstr=$bspscsi
   else $cmdstr=$bpblock    
   )

  if remove
  then add remove else add create_function
  
  if exists and change of readonly
    if exists need to delete and then add as ro
}

?>