<?php
include('redis.php');

//check if user is logged in
if(!isLoggedIn()) return 'notLoggedIn';

loadUserInfo($User['id']);

//check for the function name, then call it
if (!gt('function') === false){
    $function = gt('function');

    if($function == 'getCards' || $function == 'cashOut' || $function == 'markCard' || $function == 'addValue'){
        $function($User, $redis);
        exit();
    }
}
$return = array('error'=>'bad params');
echo json_encode($return);
exit();

/**
 * addValue
 * adds 50 credits to the user's account
 * @param user array
 * @param redis object
 * @return echos json array with user's current total
**/
function addValue($User, $redis){
    $value = addCurrentValue($User['id'], 50);
    $return = array('total'=>$value);
    echo json_encode($return);
}

/**
 * getCards 
 * makes a new game and new shuffled deck
 * @param user array
 * @param redis object
 * @return echos json array with user's current total
 * and user's unique game id
**/
function getCards($User, $redis){
    $value = getCurrentValue($User['id']);

    if($value < 50){
        $return = array('error'=>'not enough credit');
        echo json_encode($return);
        exit();
    }

    //all possible cards
    $suits = array('c', 'd', 'h', 's');
    $values = array(2,3,4,5,6,7,8,9,10,11,12,13,20);
    $deck= array();
    foreach($suits as $suit){
        foreach($values as $value){
            $deck[] = "$suit"."_"."$value";
        }
    }
    //add jokers
    $deck[52] = "99";
    $deck[53] = "99";

    //make game board here: 9x6
    $x = range(0,8);
    $y = range(0,5);
    $board = array();
    foreach($x as $xpos){
        foreach($y as $ypos){
            $board[] = $xpos."_".$ypos; 
        }
    }

    //get game id here
    $gameid = setGameId($User['id']);

    
    //shuffle deck
    shuffle($deck);

    //deal cards: assign a card in the deck for each space on the board
    $allcards = array();
    foreach($deck as $position => $card){
        $pos = $board[$position];
        $redis->set("card:$pos:gameid:$gameid", $card);
        $allcards[$pos] = $card;
    }

    //take away 50 coins from user for this game
    $total = subCurrentValue($User['id'], 50);

    //return their current running total
    $return = array( 'gameid'=>$gameid, 'total'=>$total );
    echo json_encode($return);
}

/**
 * markCard 
 * gets the card from the data base
 * @param user array
 * @param redis object
 * @post params: x and y position of the requested card
 * @return echos json array with the card
 * and user's unique game id
**/
function markCard($User, $redis){
    if (gt('positionx') === false|| gt('positiony') === false|| !gt('gameid')){
        $return = array('error'=>'bad params');
        echo json_encode($return);
        exit();
    }

    //get position of requested card
    $positionx = gt('positionx');
    $positiony = gt('positiony');
    $gameid = gt('gameid');

    //is game id associated with user?
    if(!isValidGameId($User['id'], $gameid)){
        $return = array('error'=>'not a valid gameid');
        echo json_encode($return);
    }

    //get the card from the db
    $card_value = $redis->get("card:$positionx"."_"."$positiony:gameid:$gameid");

    //is it a joker?
    if($card_value == 99){

        //destroy the current tally of card values

        $length = $redis->llen("gameid:$gameid:cards");
        for($i=0; $i<$length; $i++){
            $redis->lpop("gameid:$gameid:cards");
        }
        $redis->set("uid:".$User['id'].":latest", '');
        $return = array( 'gameid'=>null, 'card_value'=>$card_value);
        echo json_encode($return);
        exit();
    }

    //add this card to the total for this game
    $redis->lpush("gameid:$gameid:cards", $card_value);

    //return the card value
    $return = array( 'gameid'=>$gameid, 'card_value'=>$card_value);
    echo json_encode($return);
}

/**
 * cashOut 
 * ends the user's game
 * @param user array
 * @param redis object
 * @return echos json array with the user's score for this game 
 * and user's unique game id
**/
function cashOut($User, $redis){
    if (!gt('gameid')){
        $return = array('error'=>'bad params');
        echo json_encode($return);
        exit();
    }
    $gameid = gt('gameid');

    //is game id associated with user?
    if(!isValidGameId($User['id'], $gameid)){
        $return = array('error'=>'not a valid gameid');
        echo json_encode($return);
    }

    //tally the total for this game
    $value = 0;

    //get number of cards (in list)
    $length = $redis->llen("gameid:$gameid:cards");
    for($i=0; $i<$length; $i++){
        //then add them together one at a time
        $card = $redis->lpop("gameid:$gameid:cards");
        $temp = explode("_", $card);
        $value += $temp[1];
    }

    //add this value to the user's overall total
    $total_value = addCurrentValue($User['id'], $value);

    //return their overall total
    $return = array( 'gameid'=>$gameid, 'total'=>$total_value);
    echo json_encode($return);
}
