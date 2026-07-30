<?php
session_start();
include_once('../main.php');
include_once('../model/menu_model.php');

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    die("用戶未登錄或未設置用戶 ID");
}

$user_id = $_SESSION['user']['id'];
$total_price = $_POST['total_price'];
$order_items = json_decode($_POST['items'], true);

$order_result = submit_order($user_id, $total_price, $order_items);

if ($order_result) {
    header("Location: order_success.php?order_number=" . $order_result['order_number'] . "&order_datetime=" . $order_result['order_datetime']);
    exit();
} else {
    echo "訂單提交失敗";
}
?>
