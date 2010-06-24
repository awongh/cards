<?php
include('redis.php');

if(!isLoggedIn()) return 'notLoggedIn';

loadUserInfo($User['id']);

if (!gt('function') === false){
    $function = gt('function');

    if($function == 'getCards' || $function == 'cashOut' || $function == 'markCard' || $function == 'addValue'){
        $function($User, $redis);
        exit();
    }
}
echo "badParams";
exit();

function addValue($User, $redis){
    $value = addCurrentValue($User['id'], 50);
    $return = array('total'=>$value);
    echo json_encode($return);
}
function getCards($User, $redis){
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
            $deck[] = "$suit"."_"."$value";
        }
    }
    $deck[52] = "99";
    $deck[53] = "99";

    $x = range(0,8);
    $y = range(0,5);
    $board = array();
    foreach($x as $xpos){
        foreach($y as $ypos){
            $board[] = $xpos."_".$ypos; 
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
}

function markCard($User, $redis){
    if (gt('positionx') === false|| gt('positiony') === false|| !gt('gameid')){
        echo 'badParams';
        exit();
    }

    $positionx = gt('positionx');
    $positiony = gt('positiony');
    $gameid = gt('gameid');

    //is game id associated with user?
    if(!isValidGameId($User['id'], $gameid)) echo 'notAValisGameId';

    $card_value = $redis->get("card:$positionx"."_"."$positiony:gameid:$gameid");

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
}

function cashOut($User, $redis){
    if (!gt('gameid')){
        echo "badParams";
        exit();
    }

    $gameid = gt('gameid');

    //is game id associated with user?
    if(!isValidGameId($User['id'], $gameid)) echo 'notAValidGameId';

    //get from list
    // gameid:$gameid:cards $card 

    $value = 0;
    while($card = $redis->lpop("gameid:$gameid:cards")){
        $temp = explode(':', $card);
        $value += $temp[1];
        $values[] = $temp[1];
    }
    $total_value = addCurrentValue($User['id'], $value);

    $return = array( 'gameid'=>$gameid, 'total'=>$total_value);
    echo json_encode($return);
}
