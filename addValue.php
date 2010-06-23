<?php
include('redis.php');

if(!isLoggedIn()) return 'notLoggedIn';

loadUserInfo($User['id']);

$value = addCurrentValue($User['id'], 50);
$return = array('total'=>$value);
echo json_encode($return);
