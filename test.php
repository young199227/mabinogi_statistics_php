<?php

include 'config.php';

$sql = "INSERT INTO test (ip,type) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "',1);";
$result = $conn->query($sql);

// $redis->set('mykey', '');

// $data = $redis->get('mykey');

// echo $data;

// $redis->close();
