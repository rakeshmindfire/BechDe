<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('libraries/TwitterAPIExchange.php');
require_once('libraries/twitter.php');
require_once 'libraries/db.php';


$settings = array(
    'oauth_access_token' => OAUTH_TWITTER_TOKEN,
    'oauth_access_token_secret' => OAUTH_TWITTER_TOKEN_SECRET,
    'consumer_key' => OAUTH_TWITTER_CONSUMER_KEY,
    'consumer_secret' => OAUTH_TWITTER_CONSUMER_SECRET
);

$twitter_err = 4;

function check_account_exists ($account) {
    $url = 'https://api.twitter.com/1.1/users/show.json';
    $getfield = '?screen_name=' . $account;
    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($GLOBALS['settings']);

    $result = $twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();
    
    return ! isset(json_decode($result)->errors);
}

function get_tweet_id () {
    $db = new dbOperation();
    $db->select('users', ['twitter_username'], ['id' => $_SESSION['id']]);
    $present = $db->fetch()['twitter_username']; 
    
    if( ! $present) {
        $GLOBALS['twitter_err'] = 1;
    } else {
    
        $valid = check_account_exists($present);

        if( ! $valid) {
            $GLOBALS['twitter_err'] = 2;
        }
    }
    return $present && $valid ? $present : FALSE ;

}

function post_tweet ($items_string) {
    $url = 'https://api.twitter.com/1.1/statuses/update.json';
    $requestMethod = 'POST';

    $postfields = array(
        'status' => '@' . get_tweet_id() .' Bought ' . $items_string . ' from QuickSeller'
        );

    $twitter = new TwitterAPIExchange($GLOBALS['settings']);
    $twitter->buildOauth($url, $requestMethod)
        ->setPostfields($postfields)
        ->performRequest();
}

function get_tweets () {
    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $getfield = '?screen_name='. get_tweet_id() .'&count=10';
    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($GLOBALS['settings']);

    return $twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();
}

function show_twitter_err() {
    $msg = '';
    
    switch ($GLOBALS['twitter_err']) {
        case 1 : 
            $msg = 'To see tweets provide Twitter username';
            break;
        case 2 : 
            $msg = 'Please provide a valid Twitter username';
            break;
        case 3 : 
            $msg = 'No recent tweets';
            break;
        default :
            $msg = 'Some Internal error';
            error_log_file('Invalid Twitter err code', FALSE);
            break;
    }
    
    return $msg;
}