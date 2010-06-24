var cards = {
    URL : 'http://localhost/code/temp/cards/',

    setup :function(){
        var score_board = $('#total');
        var total = score_board.html();
        if(total<50) $('#getCards').attr('disabled', 'disabled');
        $('#tablediv').hide();
        
    },

    addValue : function(){
        var gameid = $('#gameid').attr('game');  
        $.post('addValue.php', {'gameid' : gameid}, function(data){
            var score_board = $('#total');
            var current_score = score_board.html();

            
            var mydata = JSON.parse(data);
            var total = mydata.total;

            if(total >= 50) $('#getCards').attr('disabled', '');
            score_board.empty();
            score_board.html(total);

            
        });
    },

    getCards : function(){
        $('#getCards').attr('disabled', 'disabled');
        $('#cashOut').attr('disabled', '');
        var score_board = $('#total');
        var current_score = (+score_board.html());

        //grey out button
        //make thinking icon in table div
        if(current_score >= 50){
            $.post('getcards.php', function(data){
                //hide thinking
                //need to parse json here first
                var mydata = JSON.parse(data);
                var total = mydata.total;

                score_board.empty();
                score_board.html(total);
                var gameid = mydata.gameid;
                $('#gameid').attr('game', gameid);  

                var table = $('<table>');
                
                for(var i=0; i<=5; i++){
                    var tablerow = $('<tr>');
                    
                    for(var e=0; e<=8; e++){
                        var img = $('<img>').attr('src', cards.URL+'img/front.png');
                        var td = $('<td>').html(img);
                        td.attr("positionx", e);
                        td.attr("positiony", i);
                        $(td).click(cards.markCard);
                        $(tablerow).append(td);// append a record

                    }
                    $(table).append(tablerow);//start of new row, 9 rows

                }
                var tablediv = $('#tablediv');
                $(tablediv).prepend(table);
                $('#tablediv').slideDown('slow');
            });
        }else{
            alert('sorry. out of credit.');
        }
    },

    markCard : function(){
        //grey out card
        //thinking graphic
        //rotate card x 90deg.

        var gameid = $('#gameid').attr('game');  
        var positionx = $(this).attr('positionx');
        var positiony = $(this).attr('positiony');
        var that = this;
        $.post('markcard.php', {'positionx' : positionx, 'positiony' : positiony, 'gameid' : gameid},
            function(data){
                var mydata = JSON.parse(data);
                var img = $('<img>').attr('src', cards.URL+'img/'+mydata.card_value+'.png');

                $(that).empty();
                $(that).html(img);
                
                //rotate card x 90deg.
                //dissapear thinking graphic

                //do graphics stuff here
                if(mydata.card_value == 99){
                    alert('joker! game over.');
                    var game_score = $('#game_total');
                    game_score.empty();
                    game_score.html('0');

                    $('#tablediv').slideUp('slow');
                    $('#tablediv').empty();

                    if(mydata.total >= 50) $('#getCards').attr('disabled', '');
                    $('#cashOut').attr('disabled', 'true');
                }else{

                    var game_score = $('#game_total');
                    var values = mydata.card_value.split(':');
                    var score = (+game_score.html()) + (+values[1]);
                    game_score.empty();
                    game_score.html(score);
                }


            }
        );

    },

    cashOut : function(){
        var gameid = $('#gameid').attr('game');  
        $.post('cashout.php', {'gameid' : gameid},
            function(data){
                $('#tablediv').slideUp('slow');
                var mydata = JSON.parse(data);

                var game_score = $('#game_total');
                game_score.empty();
                game_score.html('0');

                var score_board = $('#total');
                score_board.empty();
                score_board.html(mydata.total);
                $('#tablediv').empty();

                if(mydata.total >= 50) $('#getCards').attr('disabled', '');
                $('#cashOut').attr('disabled', 'true');
            }
        );

    }
}
