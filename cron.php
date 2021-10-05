<?php

require_once("config.php");
require_once("functions.php");


// delete state from previos day
if(file(("cron_state.txt"))){
    if(date ("Ymd", filemtime("cron_state.txt")) != date("Ymd", time())){unlink("cron_state.txt");};
;};

//load state
$cron_state = file_get_contents("cron_state.txt");
$cron_state = json_decode($cron_state,  JSON_OBJECT_AS_ARRAY);
if($cron_state == NULL){$cron_state = array();};

// make backup
$i=1;
$state = array();
$db_array = getDbArray();

foreach($db_array as $db){
    if($db["cron"]!="1"){continue;};    //skip if is cron disabled
    
    $hash = md5($db["server"].$db["db"].$db["username"].$db["password"]);
    if(in_array($hash, $cron_state)){echo $db["db"]." - skiped (is backuped)<BR>\n"; continue;};// check if backup was maked today

    //make backup
    if($i>$config["cron_db_count"]){echo $db["db"]." - skiped (will be make backeup in the next step)<BR>\n"; continue;};//skip nex backup
    $file = backupDbTables($db["server"], $db["username"], $db["password"], $db["db"], "*");
    if($file!=NULL){$state[]=$hash;};
    echo $db["db"]." - backuped<BR>\n";
    $i++;
;};



//write state
file_put_contents("cron_state.txt", json_encode(array_merge($state, $cron_state), JSON_INVALID_UTF8_IGNORE));




//delete old backups SQL
foreach (glob("backup/*.sql") as $file) {
    if(time() - filectime($file) > (86400*$config["backup_max_days"])){
        unlink($file);
    ;};
;};
//delete old backups ZIP
foreach (glob("backup/*.zip") as $file) {
    if(time() - filectime($file) > (86400*$config["backup_max_days"])){
        unlink($file);
    ;};
;};


;?>