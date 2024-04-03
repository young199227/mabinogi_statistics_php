<?php
include 'config.php';

#紀錄觀看IP
$sql = "INSERT INTO test (ip) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "');";
$result = $conn->query($sql);

#統計
$sql = "SELECT count(id)上傳人數 , sum(count)場次, sum(decay)衰變數量 FROM `statistics` WHERE type = 1;";
$result = $conn->query($sql);
$countData = $result->fetch_assoc();

#通過審核的上傳角色
$sql = "SELECT name ,SUM(count) AS count, SUM(decay) AS decay, (SUM(decay) / SUM(count)) * 100 AS probability FROM statistics WHERE type = 1 GROUP BY name ORDER BY count DESC;";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $userData[] = $row;
}

#最高場次貢獻者
$sql = "SELECT name, SUM(count) AS count FROM statistics WHERE type = 1 GROUP BY name ORDER BY count DESC LIMIT 1;";
$result = $conn->query($sql);
$countMaxData = $result->fetch_assoc();

#衰變機率最高的人
$sql = "SELECT name, (SUM(decay) / SUM(count)) * 100 AS probability FROM statistics WHERE type = 1 GROUP BY name ORDER BY probability DESC LIMIT 1;";
$result = $conn->query($sql);
$countLuckyData = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>衰變統計</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 mt-3">

                <div class="card d-flex align-items-center justify-content-center">
                    <div class="card-body">
                        <h5 class="card-title mt-2" style="text-align: center;">衰變統計</h5>
                        <table class="table table-hover" style="text-align: center;">
                            <tbody>
                                <tr>
                                    <td>上傳次數:</td>
                                    <td><?php echo $countData["上傳人數"]; ?></td>
                                </tr>
                                <tr>
                                    <td>場次:</td>
                                    <td><?php echo $countData["場次"]; ?></td>
                                </tr>
                                <tr>
                                    <td>衰變:</td>
                                    <td><?php echo $countData["衰變數量"]; ?></td>
                                </tr>
                                <tr>
                                    <td>機率:</td>
                                    <td><?php echo round((($countData["衰變數量"] / $countData["場次"]) * 100), 4) . '%'; ?></td>
                                </tr>
                                <tr>
                                    <td>　</td>
                                    <td>　</td>
                                </tr>
                                <tr>
                                    <td>最多場次:</td>
                                    <td><?php echo $countMaxData["name"] . ' ' . $countMaxData["count"] . '場'; ?></td>
                                </tr>
                                <tr>

                                </tr>
                            </tbody>
                        </table>
                        <div class="text-center">
                            <a href="https://capybaby.cc/mabinogi/statistics/create.php" class="btn btn-primary">點我去上傳資料!</a>
                        </div>
                    </div>
                </div>
                <br>

                <table class="table table-hover" style="text-align: center;">
                    <tbody>
                        <tr>
                            <td>施工日</td>
                            <td>施工項目</td>
                        </tr>
                        <tr>
                            <td>12/26</td>
                            <td>初版測試</td>
                        </tr>
                        <tr>
                            <td>12/27</td>
                            <td>一日限制上傳2次,觀看過審角色</td>
                        </tr>
                        <tr>
                            <td>1/9</td>
                            <td>歐洲人,最多場次,單人衰變機率</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-hover table-striped" style="text-align: center;">
                    <thead>
                        <tr>
                            <th>角色名稱</th>
                            <th>場次</th>
                            <th>衰變數量</th>
                            <th>機率</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($userData as $key => $value) {
                            echo '<tr>';
                            echo '<td>' . $value['name'] . '</td>';
                            echo '<td>' . $value['count'] . '</td>';
                            echo '<td>' . $value['decay'] . '</td>';
                            echo '<td>' . round($value["probability"], 4) . '%' . '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</html>
