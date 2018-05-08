$(function(){

	/* RunSite ScrollTop. Jaroslaw Goszowski */

	var docObject = 'body';
	if($.browser.mozilla || $.browser.msie) docObject = 'html, body';

	$(window).scroll(function(){
        if($(window).scrollTop() > 300)
        {
            $('.scroll-top-container').addClass('active');
        }
        
        else
        {
            $('.scroll-top-container').removeClass('active');
        }
    });
    
    $('.scroll-top').on('click', function(){
        $(docObject).scrollTop(50);
        $(docObject).animate({scrollTop: 0}, 200);
    });

});