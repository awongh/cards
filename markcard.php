<?php
include('redis.php');

//$User = array('id'=>1);
if(!isLoggedIn()) return 'notLoggedIn';

loadUserInfo($User['id']);

if (gt('positionx') === false|| gt('positiony') === false|| !gt('gameid')){
    echo 'badParams';
    exit();
}

$positionx = gt('positionx');
$positiony = gt('positiony');
$gameid = gt('gameid');

//is game id associated with user?
if(!isValidGameId($User['id'], $gameid)) echo 'notAValisGameId';

$card_value = $redis->get("card:$positionx:$positiony:gameid:$gameid");

if($card_value == 99){
    while($redis->lpop("gameid:$gameid:cards") ){
        //empty the queue, player has lost this game
    }
    $redis->set("uid:".$User['id'].":latest", '');
    $return = array( 'gameid'=>null, 'card_value'=>$card_value);
    echo json_encode($return);
    exit();
}

//set into list
//LPUSH gameid:$gameid:cards $card 
$redis->lpush("gameid:$gameid:cards", $card_value);

$return = array( 'gameid'=>$gameid, 'card_value'=>$card_value);
echo json_encode($return);
