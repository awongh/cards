<?php
define('SITEURL', 'http://localhost/code/temp/cards/');
define('REDISPATH', '../../libs/lib/');

require(REDISPATH.'Predis.php');

$redis = new Predis\Client();

function isLoggedIn() {
    global $User, $_COOKIE;
    $redis = new Predis\Client();

    if (isset($User)) return true;

    if (isset($_COOKIE['auth'])) {
        $authcookie = $_COOKIE['auth'];
        if ($userid = $redis->get("auth:$authcookie")) {
            if ($redis->get("uid:$userid:auth") != $authcookie) return false;
            loadUserInfo($userid);
            return true;
        }
    }
    return false;
}
function g($param) {
    global $_GET, $_POST, $_COOKIE;

    if (isset($_COOKIE[$param])) return $_COOKIE[$param];
    if (isset($_POST[$param])) return $_POST[$param];
    if (isset($_GET[$param])) return $_GET[$param];
    return false;
}
function gt($param) {
    $val = g($param);
    if ($val === false) return false;
    return trim($val);
}
function goback($msg) {
    $content = '<div id ="error">'.utf8entities($msg).'<br>';
    $content .= '<a href="javascript:history.back()">Please return back and try again</a></div>';
    include('static.php');
    exit;
}
function utf8entities($s) {
    return htmlentities($s,ENT_COMPAT,'UTF-8');
}
function getrand() {
    $fd = fopen("/dev/urandom","r");
    $data = fread($fd,16);
    fclose($fd);
    return md5($data);
}
function loadUserInfo($userid) {
    global $User;
    $redis = new Predis\Client();

    $User['id'] = $userid;
    $User['username'] = $redis->get("uid:$userid:username");
    return true;
}
function setGameId($uid){
    $redis = new Predis\Client();

    $game_id = getrand();
    $redis->set("uid:$uid:gameid", $game_id);
    
    //set this game as the latest
    $redis->set("uid:$uid:latest", $game_id);
    return $game_id;
}

function isValidGameId($uid, $gameid){
    $redis = new Predis\Client();
    //is a valid game
    $redis_id = $redis->get("uid:$uid:gameid");

    //exists as a game
    if($gameid == $redis_id){
        if($redis->get("uid:$uid:latest") == $redis_id){
            //is the latest
            return 1;
        }else{
            //isn't the latest game
            return 0;
        }
    }

    //was never a game
    return 0;
}
function getCurrentValue($id){
    $redis = new Predis\Client();

    return $redis->get("uid:$id:value");
}

function addCurrentValue($id, $value){
    $redis = new Predis\Client();

    $current = getCurrentValue($id);

    $temp = $current + $value;    

    $redis->set("uid:$id:value", $temp);

    return $temp;
}

function subCurrentValue($id, $value){
    $redis = new Predis\Client();

    $current = getCurrentValue($id);

    $current -= $value;

    $redis->set("uid:$id:value", $current);

    return $current;
}
