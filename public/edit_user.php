<?php

session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/user_model.php';
require_once dirname(__DIR__) . '/includes/header.php';

$title = "修改帳號";
$active = "users";
include('../header.php');

// 處理表單提交邏輯
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $account = $_POST['account'];
    $type = $_POST['type'];

    $search_data['id'] = $id;
    $search_data['name'] = $name;
    $search_data['account'] = $account;
    $search_data['type'] = $type;

    // 驗證結果是否重複
    $error_msg = "";
    // 檢查是否存在帳號
    $r = check_account_edit($search_data);
    if ($r['status']) {
        // 帳號已存在，顯示錯誤訊息
        $error_msg = "<p>此帳號已存在，無法修改</p>";
    } else if (empty($type)) {
        // 確認是否選擇身份
        $error_msg = "<p>請選擇身份</p>";
    } else {
        // 更新帳號資料
        $rr = update_users($search_data);
        if ($rr) {
            // 更新成功，重定向到 index 頁面
            echo "<script>window.location.href = 'manage_users.php';</script>";
        } else {
            // 更新失敗，顯示錯誤訊息
            $error_msg = "<p>更新失敗</p>";
        }
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // 處理 GET 請求
    if (isset($_GET['id'])) {
        // 獲得 ID
        $id = $_GET['id'];
        $search_data['id'] = $id;

        // 根據 ID 獲取資料
        $r = get_users_by_id($search_data);
        if ($r['status']) {
            $id = $r['data'][0]['id'];
            $name = $r['data'][0]['name'];
            $account = $r['data'][0]['account'];
            $type = $r['data'][0]['type'];
        } else {
            // 沒有找到 id，重定向到 index 頁面
            echo "<script>window.location.href = 'manage_users.php';</script>";
        }
    } else {
        // 沒有 id，重定向到 index 頁面
        echo "<script>window.location.href = 'manage_users.php';</script>";
    }
}

?>

<form method="post" onsubmit="return validateForm()">
    <input type="hidden" name="id" id="id" value="<?php if (isset($id)) echo $id; ?>">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5 class="card-header">修改帳號</h5>
        </div>
        <br>
        <div class="card">
            <div class="row p-2">
                <div class="col-lg-6">
                    <div>
                        <label for="name" class="form-label">姓名:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="account" class="form-label">帳號:</label>
                        <input type="text" class="form-control" id="account" name="account" value="<?php echo $account; ?>">
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div>
                        <label for="type" class="form-label">身份:</label>
                        <select id="type" name="type" class="form-select">
                            <option value="">請選擇身份</option>
                            <option value="admin" <?php if ($type == 'admin') echo 'selected'; ?>>管理員</option>
                            <option value="editor" <?php if ($type == 'editor') echo 'selected'; ?>>編輯</option>
                            <option value="customer" <?php if ($type == 'customer') echo 'selected'; ?>>顧客</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12 text-center" id="error_msg" style="color:red;">
                <?php if (isset($error_msg)) echo $error_msg; ?>
            </div>
            <div class="col-12 text-center">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <button type="submit" name="submit" class="btn btn-primary">修改</button>
                <button type="button" class="btn btn-danger" onclick="cancel()">取消</button>
            </div>
        </div>
    </div>
</form>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

<script>
function validateForm() {
    document.getElementById("error_msg").innerHTML = "";
    var pass = 1;
    var error = "";

    // 獲取表單中的用戶名和電子郵件
    var name = document.getElementById("name").value;
    var account = document.getElementById("account").value;
    var type = document.getElementById("type").value;

    // 驗證用戶名是否為空和長度
    if (name.trim() == "") {
        error += "<p>姓名為必填資料</p>";
        pass = 0;
    } else if (name.trim().length < 2) {
        error += "<p>姓名最少需要2個字符</p>";
        pass = 0;
    }

    // 驗證帳號是否為空和格式
    if (account.trim() == "") {
        error += "<p>帳號為必填資料</p>";
        pass = 0;
    }

    // 驗證身份是否選擇
    if (type == "") {
        error += "<p>身份為必填資料</p>";
        pass = 0;
    }

    if (!pass) {
        document.getElementById("error_msg").innerHTML = error;
    }
    
    return pass;
}

function cancel() {
    window.location.href = "manage_users.php";
}
</script>
