<div id="header">
    <div id="gameid" type="hidden"></div>
    <a class="button" id="logout" href="./logout.php">logout</a>
    <div id="nav">
        <button class="button" id="addValue" onclick="cards.addValue()">add value</button>
        <button class="button" id="getCards" onclick="cards.getCards()">get cards</button>
        <button class="button" id="cashOut" onclick="cards.cashOut()" disabled="true">end game</button>
    </div>
    <div id="scores">
        <div class="board">Your total is: <span id="total"><?php echo $total; ?></span></div> : 
        <div class="board">Your total for this game is: <span id="game_total">0</span></div>
    </div>
</div>
<div id="tablediv"><div/>
