$(function(){

    $('[data-toggle="tooltip"]').tooltip();
	$(window).scroll(function(){
        if($(window).scrollTop() > 50)
        {
            $('.header').addClass('mini');
        }
        
        else
        {
            $('.header').removeClass('mini');
        }
    });



});