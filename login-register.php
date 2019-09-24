<?php include('header.php'); ?>
<div id="header">
    <div id="gameid" type="hidden"></div>
    <p class="blurb">Welcome. Cards is an ajax web app built with redis. Enjoy.</p>
</div>
<div id="content">
    <h2>login</h2>
    <form action="./login.php" method="post">
        <label>username</label>
        <input type="text" name="username" />
        <label>password</label>
        <input type="text" name="password" />
        <input class="submit" type="submit" name="submit" value="login"/>
    </form>
    <h2>register</h2>
    <form action="./register.php" method="post">
        <label>username</label>
        <input type="text" name="username" />
        <label>password</label>
        <input type="text" name="password" />
        <label>password, again</label>
        <input type="text" name="password2" />
        <input class="submit" type="submit" name="submit" value="register"/>
    </form>
</div>
<?php include('footer.php'); ?>
