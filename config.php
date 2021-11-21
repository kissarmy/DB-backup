<?php
//Made by OUBRECHT.com
session_start();

//header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
//header("Cache-Control: post-check=0, pre-check=0", false);
//header("Pragma: no-cache");

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);



$config["session_prefix"]="db_backup";


//log in
$config["login_username"]="username";
$config["login_password"]="password";   //password in MD5

//reCaptcha v3 - https://www.google.com/recaptcha
$config["recaptcha_enable"]="0"; // 1= zapnuto, 0=vypnuto
$config["recaptcha_public_key"]="";
$config["recaptcha_private_key"]="";

//cron
$config["backup_zip"]="1";         //1 = zip files, 0 = sql plain files
$config["backup_max_days"]="10";   //X days for store backup per database
$config["cron_db_count"]="1";      //number (count) of DB backuped in one cycle, Min number is '1'!!!


;?>