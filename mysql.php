<?php

Flight::register('db', 'mysqli', array('localhost','my_user','my_pass','my_dbname'));

$db = Flight::db();

$x = $db->query("SELECT * FROM `test_table` LIMIT 1")->fetch_assoc();

print_r($x);