<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $is_type = true;

    $postName = trim($_POST["name"]);
    $postName = preg_replace('/\s(?=)/', '', $postName);

    if (empty($postName)) {
        $errName = '姓名異常或空值!';
        $is_type = false;
    }

    if (!is_numeric($_POST["count"])) {
        $errCount = '場次異常或空值!!';
        $is_type = false;
    }

    if (!is_numeric($_POST["decay"])) {
        $errDecay = '衰變數量異常或空值!';
        $is_type = false;
    }

    if (empty($_FILES["image"]) || !in_array($_FILES["image"]["type"], ['image/jpeg', 'image/png'])) {
        $errImage = '圖片異常或無圖!';
        $is_type = false;
    }

    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        $errImage = '圖片大小不能超過 5 M';
        $is_type = false;
    }

    #驗證無誤後準備上傳資料
    if ($is_type) {

        #先查詢ip今日上傳幾次
        $sql = "SELECT COUNT(ip) AS count_ip
                FROM `statistics`
                WHERE ip = '" . $_SERVER['REMOTE_ADDR'] . "'
                AND DATE(create_time) = '" . date("Y-m-d", time()) . "';";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        #如果小於2次才可上傳
        if ($row["count_ip"] < 2) {

            $create_time = time();

            $name = mysqli_real_escape_string($conn, $postName);
            $count = intval($_POST["count"]);
            $decay = intval($_POST["decay"]);
            $image = mysqli_real_escape_string($conn, $name . '_' . $create_time . '.jpg');

            $sql = "INSERT INTO statistics (name, count, decay ,image ,ip) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            // 檢查是否準備查詢成功
            if ($stmt) {

                $stmt->bind_param("siiss", $name, $count, $decay, $image, $_SERVER['REMOTE_ADDR']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {

                    copy($_FILES["image"]["tmp_name"], 'img/' . $image);

                    $htmlType = "新增成功,請等待人工審核 勿重複上傳";
                    echo '<script>';
                    echo 'alert("' . $htmlType . '");';
                    echo 'window.location.href = "https://capybaby.cc/mabinogi/statistics/";';
                    echo '</script>';
                } else {
                    $htmlType = "數據新增失敗";
                }
            } else {
                $htmlType = "準備失敗";
            }

        } else {
            $htmlType = '休息!今日已上傳2次';
            $is_type = false;
        }

        // 關閉連接
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>上傳驗證</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 mt-5">
                <form action="create.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="formFile" class="form-label">角色名稱</label>
                        <input class="form-control" type="text" name="name" id="name">
                        <span style="color: red;"><?php echo $errName; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">場次</label>
                        <input class="form-control" type="number" name="count" id="count">
                        <span style="color: red;"><?php echo $errCount; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">衰變數量</label>
                        <input class="form-control" type="number" name="decay" id="decay">
                        <span style="color: red;"><?php echo $errDecay; ?></span>
                    </div>
                    <div class="mb-3">
                        <label for="formFile" class="form-label">圖片：角色照+現在地下城次數(活動面板) 限.jpg.png 5M大小
                            <a href="https://capybaby.cc/mabinogi/statistics/img/水豚仔萌萌.jpg" class="btn btn-primary" target="_blank">範例圖</a>
                        </label>
                        <input class="form-control" type="file" name="image" id="image" accept=".jpg, .png">
                        <span style="color: red;"><?php echo $errImage; ?></span>
                    </div>
                    <input class="btn btn-primary mt-3" type="submit" value="上傳資料!!!">

                    <span style="color: red;"><?php echo $htmlType; ?></span>
                </form>
                <br>
                <a href="https://capybaby.cc/mabinogi/statistics/" class="btn btn-danger mt-5">點我返回</a>
            </div>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</html>