<?php
include('redis.php');

if(!isLoggedIn()) return 'notLoggedIn';

loadUserInfo($User['id']);

$value = getCurrentValue($User['id']);

if($value < 50){
    echo "lessThanFifty";
    exit();
}

$suits = array('c', 'd', 'h', 's');
$values = array(2,3,4,5,6,7,8,9,10,11,12,13,20);
$deck= array();
foreach($suits as $suit){
    foreach($values as $value){
        $deck[] = "$suit:$value";
    }
}
$deck[52] = "99";
$deck[53] = "99";

$x = range(0,8);
$y = range(0,5);
$board = array();
foreach($x as $xpos){
    foreach($y as $ypos){
        $board[] = "$xpos:$ypos"; 
    }
}
$gameid = setGameId($User['id']);

shuffle($deck);

$allcards = array();
foreach($deck as $position => $card){
    $pos = $board[$position];
    $redis->set("card:$pos:gameid:$gameid", $card);
    $allcards[$pos] = $card;
}

$total = subCurrentValue($User['id'], 50);

$return = array( 'gameid'=>$gameid, 'total'=>$total );
echo json_encode($return);
