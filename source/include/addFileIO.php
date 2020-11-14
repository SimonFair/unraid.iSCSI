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
<link type="text/css" rel="stylesheet" href="<?autov("/webGui/styles/jquery.filetree.css")?>">
<style>
span.key{width:104px;display:inline-block;font-weight:bold}
span.key.link{text-decoration:underline;cursor:pointer}
div.box{margin-top:8px;line-height:30px;margin-left:40px}
div.closed{display:none}
</style>
<script src="<?autov('/webGui/javascript/translate.'.($locale?:'en_US').'.js')?>"></script>
<script src="<?autov('/webGui/javascript/dynamix.js')?>"></script>
<script src="<?autov("/webGui/javascript/jquery.filetree.js")?>"></script>



<? $i=0 ; $strLabel = ($i > 0) ? appendOrdinalSuffix($i + 1) : ''; ?>

		<table class="domain_os other" data-category="Share" data-multiple="true" data-minimum="1" data-index="<?=$i?>" data-prefix="<?=$strLabel?>">
			<tr class="advanced">
				<td>_(Unraid Share)_:</td>
				<td>
					<input type="text" data-pickfolders="true" data-pickfilter="NO_FILES_FILTER" data-pickroot="/mnt/" value="<?=htmlspecialchars($arrShare['source'])?>" name="shares[<?=$i?>][source]" placeholder="_(e.g.)_ /mnt/user/..." title="_(path of Unraid share)_" />
				</td>
			</tr>

			<tr class="advanced">
				<td>_(Unraid Mount tag)_:</td>
				<td>
					<input type="text" value="<?=htmlspecialchars($arrShare['target'])?>" name="shares[<?=$i?>][target]" placeholder="_(e.g.)_ _(shares)_ (_(name of mount tag inside vm)_)" title="_(mount tag inside vm)_" />
				</td>
			</tr>
		</table>




	<script type="text/html" id="tmplShare">
		<table class="domain_os other">
			<tr class="advanced">
				<td>_(Unraid Share)_:</td>
				<td>
					<input type="text" data-pickfolders="true" data-pickfilter="NO_FILES_FILTER" data-pickroot="/mnt/" value="" name="shares[{{INDEX}}][source]" placeholder="_(e.g.)_ /mnt/user/..." title="_(path of Unraid share)_" />
				</td>
			</tr>

			<tr class="advanced">
				<td>_(Unraid Mount tag)_:</td>
				<td>
					<input type="text" value="" name="shares[{{INDEX}}][target]" placeholder="_(e.g.)_ _(shares)_ (_(name of mount tag inside vm)_)" title="_(mount tag inside vm)_" />
				</td>
			</tr>
		</table>
	</script>

  <table>



  <script>
$(function(){
  $("#destinationShare").fileTreeAttach();
});
</script>

<script>
function path_selected() {
  var share = $("#destinationShare").val();
  document.getElementById("pathfromjs").value = share;
}
</script>

<h1 style="text-align: center;"><span style="color: red;">WARNING!</span></a></h1>
<p style="text-align: center;"><b>Please be carefull which disk you choose and double check it again since it can happen that a wrong disk is displayed in the dropdown.<br>Disks that are mounted/assigned through the Unassigned-Devices-Plguin are included in the dropdown list! Please double check and be carefull, if you choose a wrong disk this could lead to data loss!</b></p>

<table>
<div id="title">
        <span class="left"></span>
    </div>
<td><b><font size="+1">Create FileIO Volume:</font></b></td>
<tr>
    <td>Path:</td>
    <td>Name:</td>
    <td>Size:</td>
    <td>Write Back:</td>
</tr>
<form id="s" method="post" autocomplete="off">
<tr style="height:20px;">
    <td>
        <input id="pathfromjs" name="pathfromjs" type="hidden"></input>
        <input type='text' size="15" class='setting' id="destinationShare" placeholder="/mnt/user/Unraid-Kernel-Helper/" name="shareFolder" data-pickroot="/mnt" data-pickfilter="HIDE_FILES_FILTER" data-pickfolders="true" onchange="path_selected()" required>
    </td>
    <td><input type = "text" style="width: 100px;" name = "name" placeholder="fileIO" required> .img</td>
    <td> <input type = "text" name = "size" placeholder="20G" required></td>
    <td>
        <select name="write_back">
            <option value="false">false</option>
            <option value="true">true</option>
        </select>
    </td>
</tr>
</table>
<option id="s" method ="post">
    <input type="submit" name="createFileIO" value="Create FileIO">
    </option>
</form>




</body>
</html>




<script>
$(function() {

		
});
</script>
