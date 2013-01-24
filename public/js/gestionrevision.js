$(document).ready(function() {
    $("#sel_tech").change(function() {
        //$("#load-label").html('<img src="/img/loading_squares.gif" alt="Loading" />');        
        id = $("#sel_tech option:selected").val();
        $.get('/maintenance/getintertech', {tech:id}, function(data)
        {
            //$("#load-label").html('');
            //$("#lst_vol").html(data);
        });
    });
});