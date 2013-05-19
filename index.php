<?php
/**
 * @package Twitter
 * @version 1
 */
/*
Plugin Name: Twitter
Plugin URI: http://www.re.vu/mvaqasuddin
Description: This is custom twitter wordpress plugin by our team.
Author: Vaqas Uddin
Version: 1
Author URI: http://www.re.vu/mvaqasuddin
*/
add_shortcode('twitter', function($atts,$content){
	$atts = shortcode_atts(array(
		'username' => 'vaqasuddin',
		'content' => !empty( $content ) ? $content : "Follow Me On Twitter",
		'show_tweets' => false,
		'num_tweets' => 5,
		'tweets_reset_time' => 30,
		'name' => 'vaqas',
		'helloworld' => 'helloworld'
	),$atts);
	
	extract( $atts );
	
	if ( $show_tweets )
	{
		$tweets = fetch_tweets( $num_tweets, $username, $tweets_reset_time);
	}
	
	return "$tweets <p><a href='http://www.twitter.com/$username'>$content</a></p>";
});

function fetch_tweets( $num_tweets, $username, $tweets_reset_time)
{
	$tweets = curl("https://api.twitter.com/1/statuses/user_timeline/$username.json");
	$data = array();
	foreach( $tweets as $tweet )
	{
		if($num_tweets-- === 0) break;
		$data[] = $tweet->text;
	}
	
	$recent_tweets = array( (int) date('i',time()) );
	$ulli = "</li><li>";
	$recent_tweets[] = '<ul class="twitter"><li>'.implode($ulli,$data).'</li></ul>';
	cache( $recent_tweets );
	return $recent_tweets[1];
}

function curl($url)
{
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($c, CURLOPT_TIMEOUT, 30);
		return json_decode(curl_exec($c) );
	
}

function cache($recent_tweets)
{
	global $id;
	add_post_meta( $id, 'Twitter',$recent_tweets );
}