<?php
define('SITEURL', 'http://localhost/code/temp/cards/');

require('Predis.php');

$redis = new Predis\Client();

/**
 * isLoggedIn
 * checks to see if user is looged in
**/
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

/**
 * g 
 * gets a $REQUEST value for the named $param
 * returns false if not found
**/
function g($param) {
    global $_GET, $_POST, $_COOKIE;

    if (isset($_COOKIE[$param])) return $_COOKIE[$param];
    if (isset($_POST[$param])) return $_POST[$param];
    if (isset($_GET[$param])) return $_GET[$param];
    return false;
}

/**
 * gt
 * trims the param
 * returns false if false 
**/
function gt($param) {
    $val = g($param);
    if ($val === false) return false;
    return trim($val);
}

/**
 * goback
 * outputs javascritp back link 
 * used for errors
**/
function goback($msg) {
    $content = '<div id ="error">'.utf8entities($msg).'<br>';
    $content .= '<a href="javascript:history.back()">Please return back and try again</a></div>';
    include('static.php');
    exit;
}

/**
 * utf8entities
 * encodes and html chars in utf-8 format
**/
function utf8entities($s) {
    return htmlentities($s,ENT_COMPAT,'UTF-8');
}

/**
 * getrand
 * gets random string value 
 * used for auth secrets and gameids
**/
function getrand() {
    $fd = fopen("/dev/urandom","r");
    $data = fread($fd,16);
    fclose($fd);
    return md5($data);
}

/**
 * loadUserinfo
 * loads user info if they are logged in
**/
function loadUserInfo($userid) {
    global $User;
    $redis = new Predis\Client();

    $User['id'] = $userid;
    $User['username'] = $redis->get("uid:$userid:username");
    return true;
}

/**
 * setGameId
 * sets a new unique gameid
 * @param userid
 * @return gameid
**/
function setGameId($uid){
    $redis = new Predis\Client();

    $game_id = getrand();
    $redis->set("uid:$uid:gameid", $game_id);
    
    //set this game as the latest
    $redis->set("uid:$uid:latest", $game_id);
    return $game_id;
}

/**
 * isValidGameId
 * checks the database if this game is 
 * associated with the user and if it is
 * the current game
 * @param userid
 * @param gameid
 * @return bool
**/
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

/**
 * getCurrentValue
 * gets the current overall total credits for the user
 * @param userid
 * @return total value
**/
function getCurrentValue($uid){
    $redis = new Predis\Client();

    return $redis->get("uid:$uid:value");
}

/**
 * addCurrentValue
 * adds a given value to the users 
 * overall total credits
 * @param userid
 * @param the value to be added
 * @return the new total value
**/
function addCurrentValue($id, $value){
    $redis = new Predis\Client();

    $current = getCurrentValue($id);

    $temp = $current + $value;    

    $redis->set("uid:$id:value", $temp);

    return $temp;
}

/**
 * subCurrentValue
 * subtracts a given value from the users 
 * overall total credits
 * @param userid
 * @param the value to be subtracted 
 * @return the new total value
**/
function subCurrentValue($id, $value){
    $redis = new Predis\Client();

    $current = getCurrentValue($id);

    $current -= $value;

    $redis->set("uid:$id:value", $current);

    return $current;
}
