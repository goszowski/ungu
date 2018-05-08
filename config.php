<?php 





$developers_addr = array(

	'127.0.0.1'

);

define('_WEBSITE_CLOSE_', false);
define('_DEV_MODE_', true);
define('_DEBUG_ERRORS_', false);
define('_LOG_SCRIPT_TIME_', false);
define('_SMOOTHSCROLL_', false); // Плавне прокручування сторінки








// cache

define('_CAHCE_DATA_', false);

define('_CAHCE_DRIVER_', 'memcache'); // file or memcache;

if(_CAHCE_DATA_ === true and _CAHCE_DRIVER_ === 'memcache')
{
	$memcache_config['host'] = '127.0.0.1';
	$memcache_config['port'] = '11211';

	$memcache_obj = new Memcache;
	$memcache_obj->connect($memcache_config['host'], $memcache_config['port']) or die('Could not connect to Memcache');

}










define('_CACHE_CURRENT_NODE_', true);

define('_CAHCE_DATA_HOME_SLIDER_', 				 		true);
define('_CAHCE_DATA_ARTICLES_LIST_', 			 		true);
define('_CAHCE_DATA_NEWS_LIST_', 						true);
define('_CAHCE_DATA_OPINIONS_WIDGET_', 			 		true);
define('_CAHCE_DATA_NEWS_VIEW_', 				 		true);
define('_CAHCE_DATA_CAROUSEL_GALLERY_',			 		true);
define('_CAHCE_DATA_NEWS_VIEW_BROADCASTING_',	 		true);
define('_CAHCE_DATA_NEWS_VIEW_BROADCASTING_COUNT_', 	true);
define('_CAHCE_DATA_NEWS_VIEW_BROADCASTING_NEW_ITEMS_', true);
define('_CAHCE_DATA_SECTIONS_', 						true);
define('_CAHCE_DATA_BLOGS_SLIDER_', 					true);
define('_CAHCE_DATA_EXPLANATIONS_WG_', 					true);
define('_CAHCE_DATA_POPULARS_', 						true);
define('_CAHCE_DATA_PHRASE_WG_', 						true);
define('_CAHCE_DATA_NEWS_', 							true);
define('_CAHCE_DATA_TOP_NEWS_', 						true);
define('_CAHCE_DATA_ARTICLES_', 						true);
define('_CAHCE_DATA_POPULAR_IN_SOCIAL_', 				true);
define('_CAHCE_DATA_PHOTO_FULL_SCREEN_', 				true);
define('_CAHCE_DATA_MONTHS_', 							true);
define('_CAHCE_DATA_INTERVIEW_', 						true);
define('_CAHCE_DATA_BLOGS_', 							true);
define('_CAHCE_DATA_BLOG_VIEW_', 						true);
define('_CAHCE_DATA_ARTICLES_WIDGET_', 					true);
define('_CAHCE_DATA_AUTHORS_', 							true);
define('_CAHCE_DATA_BLOG_AUTHORS_', 					true);
define('_CAHCE_DATA_POLLS_', 							false);
define('_CAHCE_DATA_POLL_WG_', 							false);
define('_CAHCE_DATA_POLLS_WG_', 						false);
define('_CAHCE_DATA_OPINIONS_', 						true);
define('_CAHCE_DATA_OPINIONS_LINE_', 					true);
define('_CAHCE_DATA_QUESTIONS_', 						true);
define('_CAHCE_DATA_ANSWER_ITEM_', 						true);
define('_CAHCE_DATA_EXPERTS_', 							true);
define('_CAHCE_DATA_DIGEST_', 							true);
define('_CAHCE_DATA_VIDEOS_', 							true);
define('_CAHCE_DATA_VIDEO_ITEM_', 						true);
define('_CAHCE_DATA_ABOUT_', 							true);
define('_CAHCE_DATA_SECTIONS_', 						true);
define('_CAHCE_DATA_DIGEST_WG_', 						true);
define('_CAHCE_DATA_ARTICLE_PRESENTATION_', 			true);
define('_CAHCE_DATA_PHOTOS_', 							true);
define('_CAHCE_DATA_PHOTOALBUM_VIEW_DEFAULT_', 			true);
define('_CAHCE_DATA_INFOGRAPHICS_', 					true);
define('_CAHCE_DATA_INFOGRAPHIC_ITEM_', 				true);
define('_CAHCE_DATA_INFOGRAPHICS_WG_', 					true);
define('_CAHCE_DATA_VIDEOS_CTS_', 						true);
define('_CAHCE_DATA_TAGS_', 							true);
define('_CAHCE_DATA_TAGVIEW_', 							true);

// dump detail

define('_DUMP_DETAIL_MODE_', false);



// search cache

define('_CACHE_SEARCH_', true);


// Ajax mode
define('_AJAX_MODE_', false);



define('_IMAGES_PATH_LG_', 'http://cfts.org.ua/imglib');
define('_IMAGES_PATH_SM_', 'http://cfts.org.ua/imglib_thumbnails');




// number
define('_NEWSLINE_COUNT_', 80);
define('_ARTICLES_COUNT_', 22);
define('_WG_BLOGS_LIMIT_', 5);





// social links
define('_SOCIAL_facebook', 'https://www.facebook.com/pages/%D0%A6%D0%B5%D0%BD%D1%82%D1%80-%D1%82%D1%80%D0%B0%D0%BD%D1%81%D0%BF%D0%BE%D1%80%D1%82%D0%BD%D1%8B%D1%85-%D1%81%D1%82%D1%80%D0%B0%D1%82%D0%B5%D0%B3%D0%B8%D0%B9/323610344331794');
define('_SOCIAL_twitter', 'https://twitter.com/#!/Centertransport');
define('_SOCIAL_livejournal', 'http://center-trans.livejournal.com/');
define('_SOCIAL_google_plus', 'https://plus.google.com/102375166899427961365');
define('_SOCIAL_vk', 'http://vk.com/cts_portal');
define('_SOCIAL_rss', 'http://www.cfts.org.ua/import/rss.php');






$routes = array(
	
);






// widgets position

$C_widgets['home'] = array(

		array('widget_name' => 'opinions', 			'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'sections', 			'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'carousel_blogs', 	'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'explanations', 		'parameters' => array('limit'=>2),  'parent_id' => false),
		array('widget_name' => 'populars', 			'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'phrase', 			'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'socials_wg', 		'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'poll_wg', 			'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'popular_in_social', 'parameters' => false, 				'parent_id' => false),

	);


$C_widgets['news_view'] = array(

		array('widget_name' => 'opinions', 			'parameters' => false, 				'parent_id' => '39301'),
		array('widget_name' => 'popular_in_social', 'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'opinions_line', 	'parameters' => false, 				'parent_id' => '39257'),
		array('widget_name' => 'newsline', 			'parameters' => array('limit'=>4), 	'parent_id' => '39257')

	);


$C_widgets['articles'] = array(

		array('widget_name' => 'carousel_blogs', 	'parameters' => false, 				'parent_id' => false),
		array('widget_name' => 'explanations', 		'parameters' => array('limit'=>2), 	'parent_id' => false)

	);


$C_widgets['blogs'] = array(

		array('widget_name' => 'articles_widget', 	'parameters' => false, 				'parent_id' => false)

	);


$C_widgets['blogs_view'] = array(

		array('widget_name' => 'articles_widget', 	'parameters' => false, 				'parent_id' => false)

	);


$C_widgets['poll_view'] = array(

		array('widget_name' => 'polls', 			'parameters' => false, 				'parent_id' => false)

	);


$C_widgets['polls'] = array(

		array('widget_name' => 'articles_widget', 	'parameters' => false, 				'parent_id' => false)

	);


$C_widgets['allexperts'] = array(

		array('widget_name' => 'articles_line', 	'parameters' => array('limit'=>54), 'parent_id' => false)

	);

$C_widgets['infographic_view'] = array(

		array('widget_name' => 'infographics_wg', 	'parameters' => false, 'parent_id' => false)

	);
?>