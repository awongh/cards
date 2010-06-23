<html>
<head>
    <meta content="text/html; charset=iso-8859-1" http-equiv="Content-Type">
    <script type='text/javascript' src='http://localhost/code/libs/jquery-1.4.2.js' ></script>
    <script type='text/javascript' src='http://localhost/code/libs/json2.js' ></script>
    <script type='text/javascript' src='<?php echo SITEURL; ?>cards.js'></script>   
    <script type='text/javascript' src='<?php echo SITEURL; ?>ards.js'></script>   
</head>
<body>
    <div id="gameid" type="hidden"></div>
    <div>Your total is: <span id="total"><?php echo $total; ?></span></div>
    <a href="./logout.php">logout</a>
    <button id="addValue" onclick="cards.addValue()">add value</button>
    <button id="getCards" onclick="cards.getCards()">get cards</button>
    <button id="cashOut" onclick="cards.cashOut()" disabled="true">end game</button>
    <div id="tablediv"><div/>
</body>
</html>
