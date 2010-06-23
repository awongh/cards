<?php

include("redis.php");

# Form sanity checks
if (!gt("username") || !gt("password"))
    goback("You need to enter both username and password to login.");

# The form is ok, check if the username is available
$username = gt("username");
$password = gt("password");
$userid = $redis->get("username:$username:id");
if (!$userid)
    goback("Wrong username or password");
$realpassword = $redis->get("uid:$userid:password");
if ($realpassword != $password)
    goback("Wrong useranme or password");

# Username / password OK, set the cookie and redirect to index.php
$authsecret = $redis->get("uid:$userid:auth");
setcookie("auth",$authsecret,time()+3600*24*365);
header("Location: ".SITEURL.'index.html');
