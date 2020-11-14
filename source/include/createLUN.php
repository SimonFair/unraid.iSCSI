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

<p style="text-align: center;"><b>Assign a FileIO Volume/Block Volume to an IQN.</b></p>

<div id="title">
        <span class="left"></span>
    </div>

<td><b><font size="+1">Create FileIO LUN:</font></b></td>

<form id="s" method="post" >
  <select name="availIQN" required>
    <option selected="selected" value="">Choose IQN</option>
      <?php
      $output = $_SESSION['availiqns'];
      $data = explode("\n", $output);
      var_dump($data);
      foreach($data as $item){

      echo "<option value=\"$item\"> $item</option>";
}
?>
</select>


<select name="fileioNAME" required>
    <option selected="selected" value="" >Choose FileIO Volume</option>
      <?php
      $output = $_SESSION['availfilios'];
      $data = explode("\n", $output);
      var_dump($data);
      foreach($data as $item){

      echo "<option value=\"$item\"> $item</option>";

}
?>
</select>
</option>
<input type="submit" name="LUNFileIO" value="Create FileIO LUN">
</form>


<?php
if(isset($_POST['LUNFileIO'])) {
$availIQN = $_POST["availIQN"];
$fileioNAME = $_POST["fileioNAME"];

echo '<script>parent.window.location.reload();</script>';
}
?>





<div id="title">
        <span class="left"></span>
    </div>

<td><b><font size="+1">Create Block LUN:</font></b></td>

<form id="s" method="post" >
  <select name="availIQN" required>
    <option selected="selected" value="">Choose IQN</option>
      <?php
      $output = $_SESSION['availiqns'];
      $data = explode("\n", $output);
      var_dump($data);
      foreach($data as $item){

      echo "<option value=\"$item\"> $item</option>";
}
?>
</select>


<select name="blockNAME" required>
    <option selected="selected" value="" >Choose Block Volume</option>
      <?php
      $output = $_SESSION['availblockvols'];
      $data = explode("\n", $output);
      var_dump($data);
      foreach($data as $item){

      echo "<option value=\"$item\"> $item</option>";

}
?>
</select>
</option>
<input type="submit" name="LUNblock" value="Create FileIO LUN">
</form>



</script>
</body>
</html>
