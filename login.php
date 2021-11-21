<?php
$nologin="1";
require_once("header.php");




//logout
if(isset($_GET["logout"])){
    unset($_SESSION[$config["session_prefix"]."login"]);
    exit(redirect("login.php"));
;};

//check data and login
if(isset($_POST["username"]) AND isset($_POST["password"])){

    // reCaptcha
	if($_POST["g-recaptcha-response"] != NULL AND $config["recaptcha_enable"]=="1"){
		$response_captcha=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$config["recaptcha_private_key"]."&response=".$_POST["g-recaptcha-response"]."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
		if($response_captcha['success'] == false){$_SESSION[$config["session_prefix"]."state"][2]="ERROR: reCaptcha"; exit(redirect("login.php"));}; // recaptcha error
	;};

    if($_POST["username"]==$config["login_username"] AND md5($_POST["password"])==$config["login_password"]){
        $_SESSION[$config["session_prefix"]."login"]="1";
        exit(redirect("./"));
    ;} else {
        unset($_SESSION[$config["session_prefix"]."login"]);
        echo "Bad username or password.<BR>";

        //exit(redirect("login.php"));
    ;};
;};

//if log in ok
if($_SESSION[$config["session_prefix"]."login"]=="1"){
    $_SESSION[$config["session_prefix"]."state"][1]="You are logged in";
    exit(redirect("./"));
;};

//login form
if($config["recaptcha_enable"]=="1"){echo "<script src='https://www.google.com/recaptcha/api.js'></script>";};
echo "
<script>
       function onSubmit(token) {
         document.getElementById('login-form').submit();
       }
</script>

<form method='post' id='login-form'>
<input type='email' name='username' id='username' required='required' placeholder='Username' /><BR>
<input type='password' name='password' id='password' required='required' placeholder='Password' /><BR>

<button class='g-recaptcha' data-sitekey='$config[recaptcha_public_key]' data-callback='onSubmit' data-action='submit'>Log in</button>

</form>
";

      
require_once("footer.php");
;?>