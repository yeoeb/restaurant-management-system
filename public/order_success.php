<?php
session_start(); // 確保 session 已啟動
require_once dirname(__DIR__) . '/models/menu_model.php';
$title = "訂單提交成功";
$active = "";
require_once dirname(__DIR__) . '/includes/header.php';

// 獲取提交結果
$order_info = $_SESSION['order_info'];
$order_number = $order_info['order_number'];
$order_date = $order_info['order_date'];
$order_time = $order_info['order_time'];
$total_price = $_SESSION['total_price'];
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <h5 class="card-header">訂單提交成功</h5>
        <div class="card-body">
            <h5 class="card-title">感謝您的訂購！</h5>
            <p class="card-text">您的訂單號碼是：<?php echo $order_number; ?></p>
            <p class="card-text">訂單日期：<?php echo $order_date; ?></p>
            <p class="card-text">訂單時間：<?php echo $order_time; ?></p>
            <p class="card-text">總金額：<?php echo $total_price; ?></p>
            <a href="menu.php" class="btn btn-primary">返回菜單</a>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
