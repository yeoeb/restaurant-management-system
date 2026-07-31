<?php

session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/user_model.php'; // 确保包含用户模型文件


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "登入";
$active = "login";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = $_POST['account'];
    $password = $_POST['password'];

    if ($account != "" && $password != "") {
        $user = get_user_by_account($account);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'user_type' => $user['type'] // 例如 'admin' 或 'customer'
            ];
            header("Location: index.php");
            exit();
        } else {
            $error_msg = "帳號或密碼錯誤";
        }
    } else {
        $error_msg = "請填寫帳號和密碼";
    }
}

require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Login -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="index.php" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <!-- Add your logo here -->
                            </span>
                            <span class="app-brand-text demo text-body fw-bolder">登入</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-2">歡迎回來！</h4>
                    <p class="mb-4">請輸入您的帳號和密碼</p>

                    <form id="formAuthentication" class="mb-3" action="" method="POST">
                        <div class="mb-3">
                            <label for="account" class="form-label">帳號</label>
                            <input type="text" class="form-control" id="account" name="account" placeholder="請輸入帳號" autofocus value="<?php echo isset($account) ? $account : ''; ?>" />
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">密碼</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">登入</button>
                        </div>
                        <div class="mb-3 text-center" id="error_msg" style="color:red;">
                            <?php if (isset($error_msg)) echo $error_msg; ?>
                        </div>
                    </form>
                    <div class="mb-3 text-center">
                        <p>沒有帳號？ <a href="register.php">註冊</a></p>
                    </div>
                </div>
            </div>
            <!-- /Login -->
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>