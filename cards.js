var cards = {
    URL : 'http://localhost/code/temp/cards/',

    addValue : function(){
        var gameid = $('#gameid').attr('game');  
        $.post('addValue.php', {'gameid' : gameid}, function(data){
            var mydata = JSON.parse(data);
            var total = mydata.total;

            var score_board = $('#total');
            score_board.empty();
            score_board.html(total);
        });
    },

    getCards : function(){
        $('#getCards').attr('disabled', 'true');
        $('#cashOut').attr('disabled', '');
        //grey out button
        //make thinking icon in table div
        $.post('getcards.php', function(data){
            //hide thinking
            //need to parse json here first
            var mydata = JSON.parse(data);
            var total = mydata.total;

            var score_board = $('#total');
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

        });
    },

    markCard : function(){
        //grey out card
        //thinking graphic
        //rotate card x 90deg.

        var gameid = $('#gameid').attr('game');  
        var positionx = $(this).attr('positionx');
        var positiony = $(this).attr('positiony');
        $.post('markcard.php', {'positionx' : positionx, 'positiony' : positiony, 'gameid' : gameid},
            function(data){
                var mydata = JSON.parse(data);

                var td = $('td[positionx='+positionx+'][positiony='+positiony+']');
                var img = $('<img>').attr('src', cards.URL+'img/'+mydata.card_value+'.png');

                $(td).empty();
                $(td).html(img);
                
                //rotate card x 90deg.
                //dissapear thinking graphic

                //do graphics stuff here
                if(mydata.card_value == 99){
                    $('#tablediv').empty();
                    $('#getCards').attr('disabled', '');
                    $('#cashOut').attr('disabled', 'true');
                }

            }
        );

    },

    cashOut : function(){
        var gameid = $('#gameid').attr('game');  
        $.post('cashout.php', {'gameid' : gameid},
            function(data){
                var mydata = JSON.parse(data);
                var score_board = $('#total');
                score_board.empty();
                score_board.html(mydata.total);
                $('#tablediv').empty();
                $('#getCards').attr('disabled', '');
                $('#cashOut').attr('disabled', 'true');
            }
        );

    }
}
