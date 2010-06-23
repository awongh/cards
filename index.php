<?
    require('redis.php');

    if(!isLoggedIn()) header('Location: '.SITEURL.'login.html');

    loadUserInfo($User['id']);

    $total = getCurrentValue($User['id']);

    include('game.php');
?>
