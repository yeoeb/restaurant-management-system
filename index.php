<?php
session_start();
include_once('../main.php');
$title = "首頁";
$active = "index";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include_once('../header.php');
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">歡迎光臨！</h4>
    <?php if (isset($_SESSION['welcome_msg'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['welcome_msg']; unset($_SESSION['welcome_msg']); ?>
        </div>
    <?php endif; ?>
    <p>請從左邊的菜單選擇操作項目。</p>
</div>

<?php include_once('../footer.php'); ?>
