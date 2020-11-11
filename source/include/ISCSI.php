<?PHP
/* Copyright 2020-2020, Simon Fairweather
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */
$plugin = "unraid.iSCSI";
$docroot = $docroot ?? $_SERVER['DOCUMENT_ROOT'] ?: '/usr/local/emhttp';
$translations = file_exists("$docroot/webGui/include/Translations.php");

if ($translations) {
	/* add translations */
	$_SERVER['REQUEST_URI'] = 'unraid.iSCSI';
	require_once "$docroot/webGui/include/Translations.php";
} else {
	/* legacy support (without javascript) */
	$noscript = true;
	require_once "$docroot/plugins/$plugin/include/Legacy.php";
}

require_once "plugins/unraid.iSCSI/include/lib.php";

switch ($_POST['table']) {
// dt = Device Tab Tables  
// it = Initiator Tab Tables
// st = Status Tab Tables
// ft = Fileio Tab Tables
// lt = LUN Tab Tables 
case 't1':
    exec('targetcli sessions detail',$targetcli);
    foreach ($targetcli as $line) {
             echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
       }
  break;
case 't2':
  $t=get_iscsi_json() ;
  $b=build_iscsi_devices($t) ; 
  if (!in_array("/dev/disk/by-id/usb-Sony_Hard_Drive_235853211C8A-0:1" ,$b , true)) echo "found drive" ;
  break;
case 't3':
    exec('targetcli ls',$targetcli);
    foreach ($targetcli as $line) {
      echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
    }
    break;
case 't4':
    exec('lsblk -S'  ,$lsscsi);
        foreach ($lsscsi as $line) {
      echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
     }   
    break;
case 'st2':
      exec('cat /var/run/targetcli.last'  ,$TC);
          foreach ($TC as $line) {
        echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
       }   
      break;

case 'dt1':
        $groups=get_unassigned_disks() ;
         
        echo "</tr><tr><td>Status      Readonly\n    Selection</td><td>Device</td>" ;
         foreach ($groups[array_key_first($groups)] as $line2=>$d2) {
          if ($line2!="defined" && $line2!="partitions" && $line2!="unraid" && $line2!="definedx" && $line2!="by-id" &&$line2!="bpartitions") {
            echo '<td>'.ucwords($line2)."</td>";
          }
        }
        echo "</tr><tr>" ;
        foreach ($groups as $line=>$d) {
          $device=$d ;
          $unraid=$device["unraid"] ;
          $defined=$device["defined"] ;
          $dname=$device["name"] ;
          $readonly = $device["readonly"];
     
          echo "</tr>" ;
          if (!$defined && !$unraid) { $colour="green" ;  $text="Device is available to be added." ;} else {$colour = "red"; $text="Already Defined." ;}
          if ($unraid) {$colour="grey" ; $text="In use by Unraid." ;}
            
            
          echo "<td> <i class=\"fa fa-circle orb ".$colour."-orb middle\" title=\""._($text)."\"></i>";

          $iscsiset="iscsiset;".$device["type"].';'.$device["device"].';'.($defined ? "true" : "false") ;
          $iscsiro="iscsiro;".$device["type"].';'.$device["device"].';'.($readonly ? "true" : "false") ;
            
          echo $unraid ? '   <input type="checkbox" value="" title="'._('In use by Unraid').'" disabled ' : '   <input type="checkbox" class="iscsi'.$dname.'" value="'.$iscsiset.'" '  ;
          echo ($defined && !$unraid) ? " checked>" : ">";
          echo $unraid ? '   <input type="checkbox" value="" title="'._('In use by Unraid').'" disabled ' : '   <input type="checkbox" class="iscsiro'.$dname.'" value="'.$iscsiro.'" disabled  '  ;
          echo ($readonly && !$unraid) ? " checked>" : ">";
          echo "</td><td> ".$line."</td>";

            foreach ($device as $line2=>$d2) {
              if ($line2!="defined" && $line2!="partitions" && $line2!="unraid"  && $line2!="definedx" && $line2!="by-id" &&$line2!="bpartitions"){
                 echo "<td>".$d2."</td>";
              }          
            }
           
           $part=$device["bpartitions"] ;
            if (!$unraid ){
              foreach($part as $part2) {
                
                $part2 = "Partion:".$part2["path"]." Label:".$part2["label"]." Type:".$part2["fstype"]." Vers:".$part2["fsver"] ;
                           echo "<tr><td></td><td><td style=\"padding-left: 50px;\":>".$part2."</td></tr>";
                        }

            if($defined ) {
                   $value= "ISCSI Dev:".substr($device["device"], 16) ;
              echo "<tr><td></td><td><td style=\"padding-left: 50px;\":>".$value."</td></tr>" ; 
            } 
          }
            echo "</td></tr>" ;
          }
    $noiommu=false ;
    echo '<tr><td><br>';
    echo '<input id="applycfg" type="submit" disabled value="'._('Add/Remove as Selected').'" onclick="applyCfgDev();" '.'>';
    echo '<span id="warningDev"></span>';
    echo '</td></tr>';
    echo <<<EOT
<script>
$("#dt1 input[type='checkbox']").change(function() {
  var matches = document.querySelectorAll("." + this.className);
  for (var i=0, len=matches.length|0; i<len; i=i+1|0) {
    matches[i].checked = this.checked ? true : false;
  }
  $("#applycfg").attr("disabled", false);
 });
</script>
EOT;
 break ;

 # Fileio
 case 'ft1':

   
  $json=get_iscsi_json() ;
  $LIOdevices=build_iscsi_devices($json) ;
  
  echo "</tr><tr>" ;
 foreach ($LIOdevices as $fileio) {
   if ($fileio["plugin"] == "fileio") {
   $iscsifio="iscsifio;".$fileio["name"].';'.$fileio["dev"] ;
   echo "<td>" ;
   echo '<input type="checkbox" class="'.$fileio["name"].'" value="'.$iscsifio.'" '  ;
   echo "</td><td>" ; 
     echo $fileio["name"]."=>".$fileio["dev"]."</td><td>Write Back:".($fileio["write_back"] ? "true" : "false")."</td><td>Size:".$fileio["size"]."</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>" ;
 }
 }
 echo '<tr><td><br>' ;
            echo '<input id="removeFileIO" disabled type="submit"  value="'._('Remove Fileio').'" onclick="removeFIO();" '.'>';
            echo "</td><td><br>" ; 
            echo '<input id="addFileio" type="submit"  disabled value="'._('Add new FileIO').'" onclick="addFIO();" '.'>';
            echo '<span id="warning"></span>';
            echo '</td><td>';
            echo '</td></tr>';
            echo <<<EOT
            <script>
            $("#ft1 input[type='checkbox']").change(function() {
              var matches = document.querySelectorAll("." + this.className);
              for (var i=0, len=matches.length|0; i<len; i=i+1|0) {
                matches[i].checked = this.checked ? true : false;
              }
              $("#removeFileIO").attr("disabled", false);
             });
            </script>
            EOT;
  break;  

# Initiators ign and mapped LUNS
case 'it1':

   
   $json=get_iscsi_json() ;
   $nodes=build_iscsi_initiators($json) ;
   $LIOdevices=build_iscsi_devices($json) ;
   
   echo '<script type="text/javascript"> document.getElementById("targetname").innerHTML =  "<b>'.$targetname.'</b>"</script>';
   echo "</tr><tr>" ;
  $i=1 ;
  foreach ($nodes as $init) {
    #if (array_search($d , array_column($LIOdevices, 'dev')) !==false || array_search($path , array_column($LIOdevices, 'dev')) !==false) $defined = true ; else $defined=false; 
    $iscsiiqn="iscsiiqn;".$init["node_wwn"].';' ;
    echo '<td><input type="checkbox" class="iscsiiqn'.$i++.'" value="'.$iscsiiqn.'" </td>'  ;
      echo $init["node_wwn"]." Mapped Luns:".count($init["mapped_luns"])." Attributes ".count($init["attributes"])."\n" ;
        foreach ($init["mapped_luns"] as $mapluns) 
          {
            
          $iscsimapl="iscsimap;".$mapluns["index"].';'.$init["node_wwn"] ;
          echo "<td>" ; 
          echo '<input type="checkbox" class="iscsimapl'.$mapluns["tpg_lun"].'" value="'.$iscsimapl.'" '  ;
                    
            echo "</td>      Mapped to lun:".$mapluns["tpg_lun"]." (".$luns[$mapluns["tpg_lun"]]["storage_object"].")\n" ;
            echo "</td></tr>";
          }
        }
          echo '<tr></td><td><br>';
          echo '<input id="RmvInit" type="submit" disabled value="'._('Remove Selected Initiator(s) or Mapping(s)').'" onclick="removeInitMap();" '.'>';
             echo '<input id="addInit" type="submit"  value="'._('Add new Initiator').'" onclick="addInit();" '.'disabled >';
             echo '<input id="addMap" type="submit"  value="'._('Add new mapping').'" onclick="addMap();" '.' disabled >';
             echo '<span id="warning"></span>';
             echo '</td></tr>';
             echo <<<EOT
<script>
$("#it1 input[type='checkbox']").change(function() {
  var matches = document.querySelectorAll("." + this.className);
  for (var i=0, len=matches.length|0; i<len; i=i+1|0) {
    matches[i].checked = this.checked ? true : false;
  }
  $("#RmvInit").attr("disabled", false);
 });
</script>
EOT;
  
  break;  
  # Initiator Tab LUNS
  case 'it2':
    $json=get_iscsi_json() ;
    $nodes=build_iscsi_initiators($json) ;
    sort($luns) ;
    echo "</tr><tr>" ;
    foreach ($luns as $lun) {
      $iscsilun="iscslun;".$lun["index"].';'.$lun["storage_object"] ;
      echo "<td>" ; 
      echo '<input type="checkbox" class="iscsilun'.$lun["index"].'" value="'.$iscsilun.'"'  ;
      echo "</td><td>" ; 
      echo "Lun".$lun["index"]."->".$lun["storage_object"]."</td><td>alua ".$lun["alua_tg_pt_gp_name"]."\n" ;
      echo "</td></tr>";
    }
  

   echo '</td><td><br>';
   echo '<input id="removelun" type="submit" disabled value="'._('Remove Selected LUN(s)').'" onclick="removeLUN();" '.'>';
   echo '</td><td><br>' ;
      echo '<input id="addLUN" type="submit" value="'._('Add new LUN').'" onclick="addLUN();" '.' disabled >';
      echo '<span id="warningLUN"></span>';
      echo '</td></tr>';
      echo <<<EOT
      <script>
      $("#it2 input[type='checkbox']").change(function() {
        var matches = document.querySelectorAll("." + this.className);
        for (var i=0, len=matches.length|0; i<len; i=i+1|0) {
          matches[i].checked = this.checked ? true : false;
        }
        $("#removelun").attr("disabled", false);
       });
      </script>
      EOT;
    break;
}
function make_mount_button($device) {
	global $paths, $Preclear;

	$button = "<span style='width:auto;text-align:right;'><button device='{$device['device']}' class='mount' context='Remove' role='%s' Mount><i class='Mount'></i>%s</button></span>";

	if (isset($device['partitions'])) {
		$mounted = isset($device['mounted']) ? $device['mounted'] : in_array(TRUE, array_map(function($ar){return $ar['mounted'];}, $device['partitions']));
		$disable = count(array_filter($device['partitions'], function($p){ if (! empty($p['fstype']) && $p['fstype'] != "precleared") return TRUE;})) ? "" : "disabled";
		$format	 = (isset($device['partitions']) && ! count($device['partitions'])) || $device['partitions'][0]['fstype'] == "precleared" ? true : false;
		$context = "disk";
	} else {
		$mounted =	$device['mounted'];
		$disable = (! empty($device['fstype']) && $device['fstype'] != "crypto_LUKS" && $device['fstype'] != "precleared") ? "" : "disabled";
		$format	 = ((isset($device['fstype']) && empty($device['fstype'])) || $device['fstype'] == "precleared") ? true : false;
		$context = "partition";
	}
	$is_mounting	= array_values(preg_grep("@/mounting_".basename($device['device'])."@i", listDir(dirname($paths['mounting']))))[0];
	$is_mounting	= (time() - filemtime($is_mounting) < 300) ? TRUE : FALSE;
	$is_unmounting	= array_values(preg_grep("@/unmounting_".basename($device['device'])."@i", listDir(dirname($paths['mounting']))))[0];
	$is_unmounting	= (time() - filemtime($is_unmounting) < 300) ? TRUE : FALSE;
	$is_formatting	= array_values(preg_grep("@/formatting_".basename($device['device'])."@i", listDir(dirname($paths['mounting']))))[0];
	$is_formatting	= (time() - filemtime($is_formatting) < 300) ? TRUE : FALSE;

	$dev			= basename($device['device']);
	$preclearing	= $Preclear ? $Preclear->isRunning(basename($device['device'])) : false;
	if ($device['size'] == 0) {
		$button = sprintf($button, $context, 'mount', 'disabled', 'fa fa-erase', _('Mount'));
	} elseif ($format) {
		$disable = (file_exists("/usr/sbin/parted") && get_config("Config", "destructive_mode") == "enabled") ? "" : "disabled";
		$disable = $preclearing ? "disabled" : $disable;
		$button = sprintf($button, $context, 'format', $disable, 'fa fa-erase', _('Format'));
	} elseif ($is_mounting) {
		$button = sprintf($button, $context, 'umount', 'disabled', 'fa fa-circle-o-notch fa-spin', ' '._('Mounting...'));
	} elseif ($is_unmounting) {
		$button = sprintf($button, $context, 'mount', 'disabled', 'fa fa-circle-o-notch fa-spin', ' '._('Unmounting...'));
	} elseif ($is_formatting) {
		$button = sprintf($button, $context, 'format', 'disabled', 'fa fa-circle-o-notch fa-spin', ' '._('Formatting...'));
	} elseif ($mounted) {
		$cmd = $device['command'];
		$script_running = is_script_running($cmd);
		if ($script_running) {
			$button = sprintf($button, $context, 'umount', 'disabled', 'fa fa-circle-o-notch fa-spin', ' '._('Running...'));
		} else {
			$disable = ! isset($device['partitions'][0]['mountpoint']) || is_mounted($device['partitions'][0]['mountpoint'], TRUE) ? $disable : "disabled";
			$disable = ! isset($device['mountpoint']) || is_mounted($device['mountpoint'], TRUE) ? $disable : "disabled";
			$button = sprintf($button, $context, 'umount', $disable, 'fa fa-export', _('Unmount'));
		}
	} else {
		$disable = ($device['partitions'][0]['pass_through'] || $preclearing ) ? "disabled" : $disable;
		$button = sprintf($button, $context, 'mount', $disable, 'fa fa-import', _('Mount'));
	}
	return $button;
}