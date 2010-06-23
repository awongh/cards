<?
include("redis.php");

if (!isLoggedIn()) {
    header("Location: ".SITEURL.'login.html');
    exit;
}

$newauthsecret = getrand();
$userid = $User['id'];
$oldauthsecret = $redis->get("uid:$userid:auth");

$redis->set("uid:$userid:auth",$newauthsecret);
$redis->set("auth:$newauthsecret",$userid);
$redis->delete("auth:$oldauthsecret");

header("Location: ".SITEURL.'login.html');
?>
