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
require_once("webGui/include/Helpers.php");

$unraid = parse_plugin_cfg("dynamix",true);
$display = $unraid["display"];

switch ($_POST['table']) {
// dt = Device Tab Tables  
// it = Initiator Tab Tables
// st = Status Tab Tables
// ft = Fileio Tab Tables
// lt = LUN Tab Tables 
// xt = Diag Tables
case 'st1':
    exec('targetcli sessions detail',$targetcli);
    foreach ($targetcli as $line) {
             echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
       }
  break;
case 'st2':
  $t=get_iscsi_json() ;
  $b=build_iscsi_devices($t) ; 
  if (!in_array("/dev/disk/by-id/usb-Sony_Hard_Drive_235853211C8A-0:1" ,$b , true)) echo "found drive" ;
  break;
case 'st3':
    exec('targetcli ls',$targetcli);
    foreach ($targetcli as $line) {
      echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
    }
    break;
case 'st4':
    exec('lsblk -S'  ,$lsscsi);
        foreach ($lsscsi as $line) {
      echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
     }   
    break;
case 'xt1':
      exec('cat /var/run/targetcli.last'  ,$TC);
          foreach ($TC as $line) {
        echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
       }   
      break;
case 'xt2':
        exec('cat /tmp/iscsicmd.run'  ,$TC);
            foreach ($TC as $line) {
          echo "<tr><td>".preg_replace('/\]  +/',']</td><td>',$line)."</td></tr>";
         }   
        break;
case 'dt1':
        $groups=get_unassigned_disks() ;

               
       echo "</tr><tr><td>Status      Readonly\n    Selection     User Defined Name </td><td>Block Name</td>" ;
        
         foreach ($groups[array_key_first($groups)] as $line2=>$d2) {
          if ($line2!="defined" && $line2!="partitions" && $line2!="unraid" && $line2!="nickname" && $line2!="by-id" && $line2!="bpartitions" && $line2!="readonly" && $line2!="name") {
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

          $iscsiset="iscsiset;".$device["type"].';'.$device["name"].';'.($defined ? "true" : "false") ;
          $iscsiro="iscsiro;".$device["type"].';'.$device["name"].';'.($readonly ? "true" : "false") ;
          $iscsinickname="iscsinickname".';'.$device["name"] .';'.$device["device"] .';' ;
          if ($device["type"]=="rom") $rodisabled=" disabled " ; else $rodisabled="" ;
            
          echo $unraid ? '   <input type="checkbox" value="" title="'._('In use by Unraid').'" disabled ' : '   <input type="checkbox" class="iscsi'.$dname.'" value="'.$iscsiset.'" '  ;
          echo ($defined && !$unraid) ? " checked>" : ">";
          echo $unraid ? '   <input type="checkbox" value="" title="'._('In use by Unraid').'" disabled ' : '   <input type="checkbox" class="iscsiro'.$dname.'" value="'.$iscsiro.'"'.$rodisabled  ;
          echo ($readonly && !$unraid) ? " checked>" : ">";
          echo '<input type="text" style="width: 100px;" name="'.$iscsinickname.'" placeholder="Use by-id" ' ;
          if ($device["name"] != "") echo 'value="'.$device["nickname"].'" ' ;
          echo $unraid ? ' hidden disabled '  : ' ' ;
          echo ($defined && !$unraid) ? " disabled >" : ">";
          echo "</td><td> ".$line."</td>";

            foreach ($device as $line2=>$d2) {
              if ($line2!="defined" && $line2!="partitions" && $line2!="unraid"  && $line2!="nicknamex" && $line2!="by-id" &&$line2!="bpartitions" && $line2!="readonly" && $line2!="namex"){
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
  var_dump($display) ;
  echo "</tr><tr>" ;
 foreach ($LIOdevices as $fileio) {
   if ($fileio["plugin"] == "fileio") {
   $iscsifio="iscsifio;".$fileio["name"].';'.$fileio["dev"] ;
   echo "<td>" ;
   echo '<input type="checkbox" class="'.$fileio["name"].'" value="'.$iscsifio.'" </input>     '  ;
   # echo "</td><td>" ; 
     echo $fileio["name"]."=>".str_pad($fileio["dev"],50)."</td><td>Write Back:".($fileio["write_back"] ? str_pad("true",10) : str_pad("false",10))."  Size:".my_scale($fileio["size"], $unit)." $unit"."</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>" ;
 }
 }
 echo '<tr><td><br>' ;
            echo '<input id="removeFileIO" disabled type="submit"  value="'._('Remove Fileio').'" onclick="removeFIO();" '.'>';
      #      echo "</td><td><br>" ; 
            echo '<input id="addFileio" type="submit" hidden value="'._('Add new FileIO').'" onclick="addFIO();" '.'>';
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

   $i=$j=$k=1 ;
   $sd = $tj["targets"] ;
   foreach($json["targets"] as $sd) {
     
   $tgt=$sd["tpgs"][0] ;
   $tluns=(isset($tgt["luns"]) ? $tgt["luns"] : []);
   $nodes=(isset($tgt["node_acls"]) ? $tgt["node_acls"] : []) ;
   $portals=$tgt["portals"] ;
   $parms=$tgt["parameters"] ;
   $enable=$tgt["enable"] ;
   $targetname=$sd["wwn"] ;
   #create LUN array based on index & name.  
   $tgtluns=build_lunindex($tluns) ;
   echo "<tr>" ;
   $iscsitgt="iscsitgt;".$targetname.';;' ;
   echo '<td><input type="checkbox" class="iscsitgt'.$i++.'"  value="'.$iscsitgt.'" </td>'  ;
     echo "  ".$targetname."</td>\n" ;
     echo "</tr><tr>" ; 

   
   
  foreach ($nodes as $init) {
    #if (array_search($d , array_column($LIOdevices, 'dev')) !==false || array_search($path , array_column($LIOdevices, 'dev')) !==false) $defined = true ; else $defined=false; 
    $iscsiiqn="iscsiiqn;".$init["node_wwn"].';'.$targetname.';' ;
    echo '<td><input type="checkbox" class="iscsiiqn'.$j++.'" value="'.$iscsiiqn.'" </td>'  ;
      echo "     ".$init["node_wwn"]." Mapped Luns:".count($init["mapped_luns"])." Attributes ".count($init["attributes"])."\n</tr>" ;
        foreach ($init["mapped_luns"] as $mapluns) 
          {
            
          $iscsimapl="iscsimap;".$mapluns["index"].';'.$init["node_wwn"] .";".$targetname ;
          echo "<td>" ; 
          echo '<input type="checkbox" class="iscsimapl'.$k++.$mapluns["tpg_lun"].'" value="'.$iscsimapl.'" '  ;
         $index=$mapluns["tpg_lun"];
            echo "</td>          Mapped Lun:".$mapluns["index"]." to Target lun:".$mapluns["tpg_lun"]." (".$tgtluns[$index]["storage_object"].")\n" ;
            echo "</td></tr>";
          }
        }
      }
             echo '<tr></td><td><br>';       
             echo '<input id="RmvInit" type="submit" disabled value="'._('Remove Selected Entries').'" onclick="removeInitMap();" '.'>';
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

  # Initiators ign and mapped LUNS
  # Tab LUNS
  case 'it2':
  $json=get_iscsi_json() ;
  $nodes=build_iscsi_initiators($json) ;
  echo "</tr><tr>" ;
  $j=1;
  foreach($json["targets"] as $sd) {
    
  $tgt=$sd["tpgs"][0] ;
  $luns=(isset($tgt["luns"]) ? $tgt["luns"] : []);
  $nodes=(isset($tgt["node_acls"]) ? $tgt["node_acls"] : []) ;
  $portals=$tgt["portals"] ;
  $parms=$tgt["parameters"] ;
  $enable=$tgt["enable"] ;
  $targetname=$sd["wwn"] ;
  $sluns=build_lunindex($luns) ;
  natsort($sluns) ;
  echo "</tr><tr>" ;
  $i=1;
  $iscsitgt="iscsiltgt;".$targetname.';;' ;
    echo '<td><input type="checkbox"  disabled class="iscsiltgt'.$j++.'" value="'.$iscsitgt.'">'  ;
    echo "    ".$targetname."</td>\n" ;
    echo "</tr><tr>" ; 
  
  

  echo "</tr><tr>" ;
  foreach ($sluns as $lun) {
    $iscsilun="iscslun;".$lun["index"].';'.$lun["storage_object"].";".$targetname ;
    echo "<td>" ; 
    echo '<input type="checkbox" class="iscsilun'.$lun["index"].$i++.'" value="'.$iscsilun.'"</input>'  ;
    
    echo "          Lun".$lun["index"]."->".str_pad($lun["storage_object"], 85)."alua ".$lun["alua_tg_pt_gp_name"]."\n" ;
    echo "</td></tr>";
  }
  }

 echo '</td><td><br>';
 echo '<input id="removelun" type="submit" disabled value="'._('Remove Selected LUN(s)').'" onclick="removeLUN();" '.'>';
    echo '<input id="createLUN" hidden type="submit" value="'._('Add new LUN').'" onclick="addLUN();" '.'  >';
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
