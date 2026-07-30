<?php
include_once('../main.php');
include_once('../model/menu_model.php'); // 確保包含模型文件

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $result = complete_order($order_id);

    if ($result) {
        echo '<script>alert("訂單已完成"); window.location.href="current_orders.php";</script>';
    } else {
        echo '<script>alert("完成訂單失敗"); window.location.href="current_orders.php";</script>';
    }
} else {
    echo '<script>alert("無效的請求"); window.location.href="current_orders.php";</script>';
}
?>
