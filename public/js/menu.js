

var obj = null;
var last_obj=2;
var flag=0;
function checkHover() {
    if(flag==0){
        if (obj) {
            obj.find('ul').fadeOut('fast');	
            last_obj=obj;
        } 
    }
}
$(document).ready(function() {
    $(".niveau1").unbind();
    $('.niveau1').hover(function() {

        flag=1;
        if($(this).find('ul').css('display')!='block' )
        {
            if (obj) 
            {            
                obj.find('ul').slideUp('fast');
                obj = null;
            }
            $(this).find('ul').slideDown('fast');
            obj=$(this);
        }
},function() {
    flag=0;
    if($(this).find('ul').css('display')=='block')
    {
        obj=$(this);
        setTimeout("checkHover()",750); 
    }
});


});