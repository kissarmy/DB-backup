<?php
//Made by OUBRECHT.com

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

session_start();

$config["session_prefix"]="db_backup_";

//log in
$config["login_username"]="username";
$config["login_password"]="password";

//reCaptcha v3 - https://www.google.com/recaptcha
$config["recaptcha_enable"]="0"; // 1= zapnuto, 0=vypnuto
$config["recaptcha_public_key"]="";
$config["recaptcha_private_key"]="";

//cron
$config["backup_zip"]="1";         //1 = zip files, 0 = sql plain files
$config["backup_max_days"]="10";   //X days for store backup per database
$config["cron_db_count"]="1";      //number (count) of DB backuped in one cycle, Min number is '1'!!!


;?>