<?php
include 'config.php';

#IP過濾使用者
$successIP = ['211.75.42.64','36.233.153.107'];
foreach ($successIP as $key => $value) {
    if ($_SERVER['REMOTE_ADDR'] != $value) {
     //die('403');
    }
}

#GET 帶有id&type 的話就修改資料
if (!empty($_GET['id']) && !empty($_GET['type'])) {

    $sql = "UPDATE statistics SET type = " . $_GET['type'] . " WHERE id = " . $_GET['id'];
    $stmt = $conn->query($sql);

    $target_url = "https://capybaby.cc/mabinogi/statistics/admin.php";
    header("Location: $target_url");
}

#撈待審資料
$sql = "SELECT * FROM `statistics` WHERE type = 0;";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>驗證</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 mt-2">
                <?php
                foreach ($data as $key => $value) {

                    echo '<div class="card mt-3">';
                    echo '<img src="https://capybaby.cc/mabinogi/statistics/img/' . $value['image'] . '" class="card-img-top">';
                    echo '<div class="card-body">';
                    echo '<h3 class="card-title mt-2">' . $value['name'] . '</h3>';
                    echo '<br><table class="table table-hover"><tbody><tr><td>場次:</td>';
                    echo '<td>' . $value['count'] . '</td>';
                    echo '</tr><tr><td>衰變數量:</td>';
                    echo '<td>' . $value['decay'] . '</td>';
                    echo '</tr></tbody></table><br>';
                    echo '<a href="https://capybaby.cc/mabinogi/statistics/img/' . $value['image'] . '" class="btn btn-primary">詳細圖!</a>　';
                    echo '<a href="https://capybaby.cc/mabinogi/statistics/admin.php?type=1&id=' . $value['id'] . '" class="btn btn-primary">通過</a>　';
                    echo '<a href="https://capybaby.cc/mabinogi/statistics/admin.php?type=2&id=' . $value['id'] . '" class="btn btn-danger">不通過</a>';
                    echo '</div></div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
