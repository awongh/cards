var cards = {
    URL : 'http://localhost/code/temp/cards/',

    /**
     * setup
     * called on document ready.
     * makes sure the correct buttons 
     * are enabled/diabled given the 
     * current score
    **/
    setup :function(){
        var score_board = $('#total');
        var total = score_board.html();
        if(total<50) $('#getCards').attr('disabled', 'disabled');
        $('#tablediv').hide();
        
    },

    /**
     * addValue
     * adds value to the user's
     * current running total
    **/
    addValue : function(){
        var gameid = $('#gameid').attr('game');  
        $.post('ajax.php', {'function' : 'addValue', 'gameid' : gameid}, function(data){
            var mydata = JSON.parse(data);
            var tablediv = $('#tablediv');
            if(!mydata.error){
                var score_board = $('#total');
                var current_score = score_board.html();
                var total = mydata.total;
                if(total >= 50) $('#getCards').removeAttr('disabled');
                score_board.empty();
                score_board.html(total);
            }else{
                alert(mydata.error);
                $(tablediv).slideUp('slow');
            }

            
        });
    },

    /**
     * getCards
     * gets a new game
    **/
    getCards : function(){

        //get the table div where we'll put the cards
        var tablediv = $('#tablediv');

        //loading image
        var load = $('<img>').attr('src', cards.URL+'img/ajax-loader-deck.gif'); 
        load.attr('id', 'deck-loading');
        $(tablediv).show().append(load);
        $('#getCards').attr('disabled', 'disabled');
        $('#cashOut').removeAttr('disabled');
        var score_board = $('#total');
        var current_score = (+score_board.html());

        //make sure user has enough credits
        if(current_score >= 50){
            $.post('ajax.php', {'function' : 'getCards'}, function(data){
                //deal with response
                var mydata = JSON.parse(data);
                if(!mydata.error){
                    var total = mydata.total;

                    //set new total minus 50 credits
                    score_board.empty();
                    score_board.html(total);
                    var gameid = mydata.gameid;
                    $('#gameid').attr('game', gameid);  

                    var table = $('<table>');
                    
                    //make board
                    for(var i=0; i<=5; i++){
                        var tablerow = $('<tr>');
                        
                        for(var e=0; e<=8; e++){
                            var img = $('<img>').attr('src', cards.URL+'img/front.png');
                            var td = $('<td>').html(img);
        
                            //assign each card an x and y attribute
                            td.attr("positionx", e);
                            td.attr("positiony", i);
                            $(td).click(cards.markCard);
                            $(tablerow).append(td);// append a record

                        }
                        $(table).append(tablerow);//start of new row, 9 rows

                    }
                    //show the cards
                    $(tablediv).empty();
                    $(tablediv).hide();
                    $(tablediv).prepend(table);
                    $('#tablediv').slideDown('slow');
                }else{
                    alert(mydata.error);
                }
            });
        }else{
            alert('sorry. out of credit.');
        }
    },

    /**
     * markCard
     * turns a card over.
     * gets the card value in response
    **/
    markCard : function(){
        var tablediv = $('#tablediv');
        //loading image
        var load = $('<img>').attr('src', cards.URL+'img/ajax-loader.gif'); 
        load.attr('id', 'card-loading');
        $(this).empty();
        $(this).html(load);
        var gameid = $('#gameid').attr('game');  
        var positionx = $(this).attr('positionx');
        var positiony = $(this).attr('positiony');

        //scope this
        var that = this;
        $.post('ajax.php', {'function' : 'markCard', 'positionx' : positionx, 'positiony' : positiony, 'gameid' : gameid},
            function(data){
                var mydata = JSON.parse(data);
                if(!mydata.error){
                
                    //get the correct card image
                    var img = $('<img>').attr('src', cards.URL+'img/'+mydata.card_value+'.png');

                    $(that).empty();
                    $(that).html(img);
                    
                    //is card a joker? 
                    if(mydata.card_value == 99){

                        //end game here
                        alert('joker! game over.');
                        var game_score = $('#game_total');
                        game_score.empty();
                        game_score.html('0');

                        $(tablediv).slideUp('slow');
                        $(tablediv).empty();
        
                        //make sure to disable correct buttons
                        var score_board = $('#total');
                        var score = score_board.html();
                        if((+score) >= 50) $('#getCards').removeAttr('disabled');
                        $('#cashOut').attr('disabled', 'true');
                    }else{

                        //card isn't a joker
                        //tally the game's score and display it
                        var game_score = $('#game_total');
                        var values = mydata.card_value.split('_');
                        var score = (+game_score.html()) + (+values[1]);
                        game_score.empty();
                        game_score.html(score);
                    }
                }else{
                    alert(mydata.error);
                    $(tablediv).slideUp('slow');
                }
            }
        );

    },

    /**
     * cashOut
     * ends a game.
     * gets the new running overall 
     * total in response
    **/
    cashOut : function(){
        var gameid = $('#gameid').attr('game');  
        $.post('ajax.php', {'function' : 'cashOut', 'gameid' : gameid},
            function(data){
                $('#tablediv').slideUp('slow');
                var mydata = JSON.parse(data);
                if(!mydata.error){

                    //zero out the game score
                    var game_score = $('#game_total');
                    game_score.empty();
                    game_score.html('0');
                
                    //update the overall score
                    var score_board = $('#total');
                    score_board.empty();
                    score_board.html(mydata.total);
                    $('#tablediv').empty();

                    //make sure the right buttons are disabled/enabled
                    //if(mydata.total >= 50) $('#getCards').attr('disabled', '');
                    if(mydata.total >= 50) $('#getCards').removeAttr('disabled');
                    $('#cashOut').attr('disabled', 'true');

                }else{
                    alert(mydata.error);
                    $(tablediv).slideUp('slow');
                }
            }
        );

    }
}
