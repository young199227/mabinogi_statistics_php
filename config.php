<?php
#
$servername = "localhost";
$username = "mabinogi";
$password = "Mm_122438929";
$dbname = "mabinogi";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("連接失敗：" . $conn->connect_error);
}

$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
} catch (Exception $e) {
    echo "Redis 連接失敗：" . $e->getMessage();
}

#ip封鎖
if ($_SERVER['REMOTE_ADDR'] === '35.201.243.26') {

    #紀錄
    $sql = "INSERT INTO test (ip,type) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "',1);";
    $result = $conn->query($sql);

    die('你使用方式怪怪的,解鎖請在巴哈聯絡我');
}


function redisGetIndexData(): array
{
    global $conn;
    global $redis;

    $indexData = [];

    #統計
    $sql = "SELECT count(id)上傳人數 , sum(count)場次, sum(decay)衰變數量 FROM `statistics` WHERE type = 1;";
    $result = $conn->query($sql);
    $indexData['countData'] = $result->fetch_assoc();

    #最高場次貢獻者
    $sql = "SELECT name, SUM(count) AS count FROM statistics WHERE type = 1 GROUP BY name ORDER BY count DESC LIMIT 1;";
    $result = $conn->query($sql);
    $indexData['maxUserData'] = $result->fetch_assoc();

    #衰變機率最高的人
    $sql = "SELECT name, (SUM(decay) / SUM(count)) * 100 AS probability FROM statistics WHERE type = 1 GROUP BY name ORDER BY probability DESC LIMIT 1;";
    $result = $conn->query($sql);
    $indexData['luckyUserData'] = $result->fetch_assoc();

    #通過審核的上傳角色
    $sql = "SELECT name ,SUM(count) AS count, SUM(decay) AS decay, (SUM(decay) / SUM(count)) * 100 AS probability FROM statistics WHERE type = 1 GROUP BY name ORDER BY count DESC;";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $userData[] = $row;
    }
    $indexData['userData'] = $userData;

    #存入redis快取
    $redis->set('indexData', json_encode($indexData));

    #拿出
    $data = $redis->get('indexData');

    #轉成php陣列後回傳
    $data = json_decode($data, true);
    return $data;
}
