<?php
include_once('../main.php');
include_once('../model/Users_model.php'); // 确保包含用户模型文件
$title = "註冊";
$active = "users";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 如果收到了 POST 請求
    $name = $_POST['name'];
    $account = $_POST['account'];
    $password = $_POST['password'];
    $password_2 = $_POST['password_2'];
    $type = 3; // 默認設置為顧客

    // 簡單驗證表單輸入
    if ($account != "" && $password != "" && $password == $password_2) {
        // 插入新用戶信息到數據庫
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $result = add_users(array(
            'name' => $name,
            'account' => $account,
            'password' => $hashed_password,
            'type' => $type
        ));

        if ($result) {
            header("Location: account.php"); // 新增成功後跳轉到帳號管理頁面
            exit();
        } else {
            $error_msg = "新增帳號失敗";
        }
    } else {
        $error_msg = "請確保所有必填字段已填寫且密碼匹配";
    }
} else {
    $name = "";
    $account = "";
    $password = "";
    $password_2 = "";
}

include_once('../header.php');
?>

<form method="post" onsubmit="return validateForm()">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5 class="card-header">註冊</h5>
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
                        <label for="password" class="form-label">密碼:</label>
                        <input type="password" class="form-control" id="password" name="password" value="<?php echo $password; ?>">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="password_2" class="form-label">確認密碼:</label>
                        <input type="password" class="form-control" id="password_2" name="password_2" value="<?php echo $password_2; ?>">
                    </div>
                </div>
                <input type="hidden" id="type" name="type" value="3">
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12 text-center" id="error_msg" style="color:red;">
                <?php if (isset($error_msg)) echo $error_msg; ?>
            </div>
            <div class="col-12 text-center">
                <button type="submit" name="submit" class="btn btn-primary">註冊</button>
                <button type="button" class="btn btn-danger" onclick="cancel()">取消</button>
            </div>
        </div>
    </div>
</form>
<?php include_once('../footer.php'); ?>

<script>
function validateForm() {
    document.getElementById("error_msg").innerHTML = "";
    var pass = 1;
    var error = "";

    // 獲取表單中的用戶名和電子郵件
    var name = document.getElementById("name").value;
    var account = document.getElementById("account").value;
    var password = document.getElementById("password").value;
    var password_2 = document.getElementById("password_2").value;

    // 驗證用戶名是否為空和長度
    if (name.trim() == "") {
        error += "<p>姓名為必填資料</p>";
        pass = 0;
    } else if (name.trim().length < 2) {
        error += "<p>姓名最少需要2個字符</p>";
        pass = 0;
    }

    // 驗證帳號是否為空
    if (account.trim() == "") {
        error += "<p>帳號為必填資料</p>";
        pass = 0;
    }

    // 驗證密碼是否為空
    if (password.trim() == "") {
        error += "<p>密碼為必填資料</p>";
        pass = 0;
    }

    // 驗證確認密碼是否為空和匹配
    if (password_2.trim() == "") {
        error += "<p>確認密碼為必填資料</p>";
        pass = 0;
    } else if (password.trim() !== password_2.trim()) {
        error += "<p>確認密碼必須與密碼一致</p>";
        pass = 0;
    }

    if (pass) {
        // 如果所有驗證都通過，允許表單提交
        return true;
    } else {
        // 驗證未通過，不允許，輸出錯誤訊息
        document.getElementById("error_msg").innerHTML = error;
        return false;
    }
}

function cancel(){
    location.href='<?php echo $base_url ; ?>account.php';
}
</script>
