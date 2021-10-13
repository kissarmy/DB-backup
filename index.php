<?php
require_once("header.php");


//backup now
if($_GET["backup_now"]!=NULL){
  $_GET = textFilterArray($_GET);
  $db = getDbDataById($_GET["backup_now"]);
  backupDbTables($db["server"], $db["username"], $db["password"], $db["db"], "*");
  $_SESSION[$config["session_prefix"]."state"][1]="Backuped";
  exit(redirect("./"));
;};

//delete file
if($_GET["delete_file"]!=NULL){
  $file = end(explode("/", $_GET["delete_file"]));
  $ext = end(explode(".",$file));
  if(file("backup/".$file) AND $ext=="sql" OR $ext=="zip"){
    unlink("backup/".$file);
    $_SESSION[$config["session_prefix"]."state"][1]="File was deleted";
  ;} else {$_SESSION[$config["session_prefix"]."state"][2]="Error: File not exist";};
  exit(redirect("./"));
;};

//backup DB
if($_GET["backup"]!=NULL){
  $_GET = textFilterArray($_GET);
  $db = getDbDataById($_GET["backup"]);
  $bakup_file = backupDbTables($db["server"], $db["username"], $db["password"], $db["db"], "*");
  if($bakup_file!=NULL){$_SESSION[$config["session_prefix"]."state"][1]="Backup is done. File: ".$bakup_file;} else {$_SESSION[$config["session_prefix"]."state"][2]="BACKUP ERROR!";};
  exit(redirect("./"));
}

//save new
if($_POST["form"]=="1" AND $_POST["id"]==NULL){
  $db = getDbArray();
  $db[] = array("server"=>$_POST["server"], "username"=>$_POST["username"], "password"=>$_POST["password"], "db"=>$_POST["db"], "note"=>$_POST["note"], "cron"=>$_POST["cron"]);
  saveDbArray($db);
  $_SESSION[$config["session_prefix"]."state"][1]="Saved";
  exit(redirect("./"));
;};


//save edit
if($_POST["form"]=="1" AND $_POST["id"]!=NULL){
  $_GET = textFilterArray($_GET);
  
  $db_array = getDbArray();
  $db_array_new=array();
  $i=1;
  foreach($db_array as $db){
    if($_POST["id"]==$i){
      $db_array_new[] = array("server"=>$_POST["server"], "username"=>$_POST["username"], "password"=>$_POST["password"], "db"=>$_POST["db"], "note"=>$_POST["note"], "cron"=>$_POST["cron"]);
    ;} else {
      $db_array_new[] = $db;
    ;};
    $i++;
  ;};
  saveDbArray($db_array_new);
  $_SESSION[$config["session_prefix"]."state"][1]="Saved";
  exit(redirect("./"));
;};


//cron enable/disable
if($_GET["cron"]!=NULL AND $_GET["id"]!=NULL){
  $_GET = textFilterArray($_GET);
  $db_array = getDbArray();
  $db_array_new=array();
  $i=1;
  foreach($db_array as $db){
    if($_GET["id"]==$i){
      $data = getDbDataById($i);
      $data["cron"]=$_GET["cron"];
      $db_array_new[] = array("server"=>$data["server"], "username"=>$data["username"], "password"=>$data["password"], "db"=>$data["db"], "note"=>$data["note"], "cron"=>$data["cron"]);
    ;} else {
      $db_array_new[] = $db;
    ;};
    $i++;
  ;};
  saveDbArray($db_array_new);
  $_SESSION[$config["session_prefix"]."state"][1]="Saved";
  exit(redirect("./"));
;};


//delete
if($_GET["delete"]!=NULL){
  $_GET = textFilterArray($_GET);

 //delete backup files
 $db = getDbDataById($_GET["delete"]);
 $db["server"] = str_replace(".", "-", str_replace(" ", "", $db["server"]));
 foreach (glob("backup/*_".$db["server"]."_".$db["db"]."_*.*") as $file) {
     unlink($file);
 ;};
 

  //delete DB config
  $db_array = getDbArray();
  $db_array_new = array();
  $i=1;
  foreach($db_array as $db){
    if($_GET["delete"]!=$i){$db_array_new[] = $db;};
    $i++;
  ;};
  saveDbArray($db_array_new);

  $_SESSION[$config["session_prefix"]."state"][1]="Deleted";
  exit(redirect("./"));
;};



//form
if($_GET["form"]=="new" OR $_GET["form"]=="edit"){
  $_GET = textFilterArray($_GET);

  //load data for edit
  if($_GET["form"]=="edit" AND $_GET["id"]!=NULL){
    $db=getDbDataById($_GET["id"]);
  ;};


  echo "
  <b>Add/Edit MySQL connection</b><BR><BR>
  <form method='post'>
    <label>MySQL server</label>
    <input type='text' name='server' id='server' required='required' value='$db[server]' placeholder='MySQL server' /><BR>

    <label>Username</label>
    <input type='text' name='username' id='username' required='required' value='$db[username]' placeholder='MySQL username' /><BR>

    <label>Password</label>
    <input type='text' name='password' id='password' required='required' value='$db[password]' placeholder='MySQL password' /><BR>

    <label>Database name</label>
    <input type='text' name='db' id='db' required='required' value='$db[db]' placeholder='MySQL database name' /><BR>

    <label>Note</label>
    <input type='text' name='note' id='note' value='$db[note]' placeholder='Note' /><BR>

    <label>Enable cron</label>
    <input type='checkbox' name='cron' id='cron' value='1' ".(($db["cron"]==1)?" checked ":"")." /><BR>
    <BR><BR>
    <input type='hidden' name='form' value='1'/>
    <input type='hidden' name='id' value='$_GET[id]'/>
    <input type='submit' value='Save' />
    <button onclick='document.location.href=\"./\"; return false;' class='close'>Close</button>
  </form>
  ";
;};



//list of databases
if($_GET["form"]==NULL){
  echo "<a style='float: right;' href='./'><img title='Refresh' class='icon' src='img/refresh.png'></a>";
  echo "<a href='?form=new'>Add new</a> | <a target='_blank' href='cron.php'>Cron</a>";
  echo "<table class='sortable'>
  <tr>
  <td class='tablehead'>Server</td>
  <td class='tablehead'>DB</td>
  <td class='tablehead'>Note</td>
  <td class='tablehead'>Backups</td>
  </tr>
  ";
  $i=1;
  $db_array = getDbArray();
  foreach($db_array as $db){
    if($db["cron"]==1){$cron_icon="<a href='?cron=0&id=$i'><img src='img/play.png' class='icon' title='Cron is enebled' onclick='return confirm(\"Disable cron for: $db[db]?\")' /></a>";} else {$cron_icon="<a href='?cron=1&id=$i'><img src='img/pause.png' class='icon' title='Cron is disabled' onclick='confirm(\"Enable cron for: $db[db]?\")' /></a>";};
    echo "<tr>";
    echo "<td>
    $db[server]<BR>
    $cron_icon
    <a href='?backup_now=$i' target='_blank' onclick='return confirm(\"Backup database: $db[db]?\")' title='Backup $db[db]'><img src='img/folder_empty.png' class='icon' /></a>
    <a href='?form=edit&id=$i' title='Edit $db[db]'><img src='img/edit.png' class='icon' /></a>
    <a href='?delete=$i' onclick='return confirm(\"Delete database: $db[db]?\")' title='Delete $db[db]'><img src='img/close2.png' class='icon' /></a>
    </td>";
    echo "<td>$db[db]</td>";
    echo "<td>$db[note]</td>";
    echo "<td>".listBackupsPerDb($db["server"], $db["db"])."</td>";
    echo "</tr>";

    $i++;
  ;};

;};
echo "</table>";
      
require_once("footer.php");
;?>