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
    $twitter_flag;
    if(isset($_SESSION['twitter_flag']) &&  $_SESSION['twitter_flag'] == true){
        echo "Twitterにログインしています。";
        $twitter_flag = true;
    }
    else{
        echo "Twitterにログインしていません。";
        $twitter_flag = false;
    }
}

function show_database($id){
    // debug用のメソッド
    $sql = "SELECT * FROM user WHERE id = '{$id}'";
    $db = Flight::db();
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $a = $stmt->fetchAll(PDO::FETCH_ASSOC);
    var_dump($a);
}

// ルーティング
Flight::route('/', 'main');
Flight::route('/sql/@id', function($id){
    show_database($id);
});

Flight::start();