<?php

require 'vendor/autoload.php';
require_once 'config.php';
session_start();

use Abraham\TwitterOAuth\TwitterOAuth;

Flight::register('db', 'PDO', array('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD),
  function($db){
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
);

function main(){
    if(isset($_SESSION['access_token'])){
        Flight::view()->set('title', 'らぼったあ2');
        Flight::view()->set('button_text', 'らぼなう');
        Flight::view()->set('button_url', 'labo_now');
        Flight::render('main.php');
    }
    else{
        Flight::redirect('/login');
    }
}

function labo_now(){
    $current_date = date("Y/m/d H:i:s");
    $tweet_message = "らぼなう!" . $current_date;
    // Todo: labo statusをチェックする処理を入れる
    // Twitterに投稿する処理
    $code = post_tweet($tweet_message);
    // Todo: DBにInsertする処理を書く
    // jsonとして出力
    $array = [
        "code" => $code
    ];
    output_json($array);
}

function labo_in(){
    // Todo: DBにInsertする処理を書く
    // Todo: Twitterに投稿する処理を書く
}

function labo_rida(){
    // Todo: DBにInsertする処理を書く
    // Todo: Twitterに投稿する処理を書く
}

function post_tweet($tweet_message){
    $access_token = $_SESSION['access_token'];
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
    $connection->post('statuses/update', ['status' => $tweet_message]);
    if($connection->getLastHttpCode() == 200) {
        $code = 200;
    } else {
        $code = $connection->getLastHttpCode();
    }
    return $code;
}

function output_json($data){
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($data);
}

function twitter_oauth(){
    // Twitter連携のためログイン画面へ遷移する。
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
    
    $_SESSION['oauth_token'] = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
    
    $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    
    Flight::redirect($url);
}

function twitter_callback(){

    $request_token = [];
    $request_token['oauth_token'] = $_SESSION['oauth_token'];
    $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
    
    if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
        Flight::stop();
    }
    
    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
    
    $_SESSION['access_token'] = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
    
    session_regenerate_id();

    // oauth_token, oauth_token_secret, user_id, screen_nameが手に入る。
    // var_dump($_SESSION['access_token']);

    Flight::redirect('/');
}

// ルーティング
Flight::route('/', 'main');
Flight::route('/login', 'twitter_oauth');
Flight::route('POST /labo_now', 'labo_now');
Flight::route('GET /callback', 'twitter_callback');

Flight::start();