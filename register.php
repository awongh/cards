<?
include("redis.php");

# Form sanity checks
if (!gt("username") || !gt("password") || !gt("password2"))
    goback("Every field of the registration form is needed!");
if (gt("password") != gt("password2"))
    goback("The two password fileds don't match!");

# The form is ok, check if the username is available
$username = gt("username");
$password = gt("password");
if ($redis->get("username:$username:id"))
    goback("Sorry the selected username is already in use.");

# Everything is ok, Register the user!
$userid = $redis->incr("global:nextUserId");
$redis->set("username:$username:id",$userid);
$redis->set("uid:$userid:username",$username);
$redis->set("uid:$userid:password",$password);

$authsecret = getrand();
$redis->set("uid:$userid:auth",$authsecret);
$redis->set("auth:$authsecret",$userid);

//set the users first value: 100
$redis->set("uid:$userid:value", 100);

# Manage a Set with all the users, may be userful in the future
//$redis->sadd("global:users",$userid);

# User registered! Login this guy
setcookie("auth",$authsecret,time()+3600*24*365);

//redirect here
header('Location: '.SITEURL.'index.php');
?>
