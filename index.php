<?php
	require 'config.php';
	require 'vendor/autoload.php';
	error_reporting(E_ALL);
	$app = new Slim\Slim(array(
			'templates.path' => 'templates'
		)
	);
	$twitter = new Twitter('rlwbf6BFcjLtJxYILNCKQ','Ly62HoW0hemGHsBO7ruxr4HZ6yDS9xtbUqeiBBqmd44', '246610792-kXAuQ3FzSLENnAxHD3w0vLU5BvWuMwIFK91Bahcy', 'pi7bKGHIauqeZxMz8YmUSDijCV9ThQYlXGOl5FPsdQfp8');

	$helper = new Helpers\ResolutionHelper();
	
	$app->get('/:twitterId', function($twitterId) use ($app, $helper){

			$app->render('tags.php',array('twitterId'=>$twitterId));
	});

	$app->get('/api/:twittername', function($twittername) use ($app, $twitter, $helper) {
		$tags = array();
		$name = '';
		$maxId = null;
		$maxDate = null;
		$i = 1;
		do {
			$statuses = $twitter->request('statuses/user_timeline.json', 'GET', 
				array(
					'screen_name'	=>$twittername, 
					'count'			=>200, 
					'include_rts'	=>1, 
					'max_id'		=> $maxId
				)
			);

			foreach($statuses as $status) {
				$maxId = ($maxId == null || $status->id < $maxId) ? $status->id : $maxId;
				$maxDate = $status->created_at;
				foreach($status->entities->hashtags as $hashtag) {
					$tags[] = "#" . "{$hashtag->text} ";
				}
				$name = $status->user->name;
			}
			$i++;
		} while (count($statuses) == 200 && $i < 20);

		//$helper->tweets($string);
		echo $helper->tweets($tags);
	});

	$app->run();
?>