<?php
include('redis.php');

if(!isLoggedIn()) return 'error';

loadUserInfo($User['id']);

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
