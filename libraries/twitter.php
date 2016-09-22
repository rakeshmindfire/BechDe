<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('libraries/TwitterAPIExchange.php');
require_once('libraries/twitter.php');


$settings = array(
    'oauth_access_token' => OAUTH_TWITTER_TOKEN,
    'oauth_access_token_secret' => OAUTH_TWITTER_TOKEN_SECRET,
    'consumer_key' => OAUTH_TWITTER_CONSUMER_KEY,
    'consumer_secret' => OAUTH_TWITTER_CONSUMER_SECRET
);

function post_tweet ($items_string) {
    $url = 'https://api.twitter.com/1.1/statuses/update.json';
    $requestMethod = 'POST';

    $postfields = array(
        'status' => 'Bought ' . $items_string . ' from QuickSeller'
        );

    $twitter = new TwitterAPIExchange($GLOBALS['settings']);
    $twitter->buildOauth($url, $requestMethod)
        ->setPostfields($postfields)
        ->performRequest();
}

function get_tweets () {
    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $getfield = '?screen_name=naincy1804&count=10';
    $requestMethod = 'GET';

$twitter = new TwitterAPIExchange($GLOBALS['settings']);

    return $twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();
}