<?php
require_once("config.php");
require_once("functions.php");
require_once("check_login.php");

// check login
if($nologin!="1"){
  if($_SESSION[$config["session_prefix"]."login"]!="1"){
    unset($_SESSION[$config["session_prefix"]."login"]);
    exit(redirect("login.php"));
  ;};
;};

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE HTML>
<html lang='en'>
<head>
";


echo "
        <!-- iOS jako aplikace -->
        <meta name='apple-mobile-web-app-title' content='DB backup'>
        <link rel='apple-touch-icon' href='img/favicon.png'>
        <link rel='apple-touch-startup-image' href='img/favicon.png'>
        <meta name='apple-mobile-web-app-capable' content='yes'>
        <meta name='apple-mobile-web-app-status-bar-style' content='default'>
        <script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener('click',function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;'href'in d&&(d.href.indexOf('http')||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,'standalone')</script>
        <!-- iOS jako aplikace -->
";


echo "
<meta charset='UTF-8'>
<meta name='viewport' content='width=device-width, initial-scale=1.0' />
<link href='https://fonts.googleapis.com/css?family=Dosis:200,300,400,500,600,700,800&amp;subset=latin,latin-ext' rel='stylesheet' type='text/css' />
<link rel='stylesheet' href='style.css?v=".filemtime("style.css")."'>
<title>DB backup</title>
<meta name='author' content='OUBRECHT.com' >


    <meta name='robots' content='noindex, nofollow'>
    <meta name='googlebot' content='noindex' />
    <meta name='googlebot-news' content='nosnippet'>
    <meta name='AdsBot-Google' content='noindex' />


<meta name='format-detection' content='telephone=no'>
<link href='img/favicon.png' rel='shortcut icon' />
";

echo "</head>
<body>";
if($_SESSION[$config["session_prefix"]."state"]!=NULL){
  if($_SESSION[$config["session_prefix"]."state"][1]!=NULL){echo "<div id='state_div' class='state_green'>".$_SESSION[$config["session_prefix"]."state"][1]."</div>";}; // OK - zelený
  if($_SESSION[$config["session_prefix"]."state"][2]!=NULL){echo "<div id='state_div' class='state_red'>".$_SESSION[$config["session_prefix"]."state"][2]."</div>";}; // chyba - červený
  $_SESSION[$config["session_prefix"]."state"]=NULL;
  echo "
  <script>
  var hideState = function(){document.getElementById('state_div').style.display='none';};
  setTimeout(hideState, 2000);
  </script>
";};

echo "<div class='full_page'>

<div class='header'><a href='./'><h1>DB backup</h1></a>";
if($_SESSION[$config["session_prefix"]."login"]=="1"){
  echo "<a href='login.php?logout=1' title='Log out' onclick='return confirm(\"Really log out?\")'><img src='img/close2.png' alt='Log out' class='icon' style='float:right;' /></a>";
  echo "<p>Data is stored for ".$config["backup_max_days"]." days.</p>";
;};
echo "</div>

<div class='master_content'>
";

;?>