$(function(){
	var cokkies_informer = $.cookie('cookies_informer');

	if(cokkies_informer == undefined) $('.cookie-informer').show();

	$('.cookie-informer-button.accept').on('click', function(){
		$.cookie('cookies_informer', '1', { expires: 365, path: '/' });
		$('.cookie-informer').css('bottom', -$('.cookie-informer').height());
		setTimeout(function(){
			$('.cookie-informer').fadeOut(200);
		}, 500);

		return false;
	});

});