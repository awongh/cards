<?
    require('redis.php');

    if(!isLoggedIn()) header('Location: '.SITEURL.'login-register.php');

    loadUserInfo($User['id']);

    $total = getCurrentValue($User['id']);

    include('header.php');
    include('game.php');
    include('footer.php');
?>
