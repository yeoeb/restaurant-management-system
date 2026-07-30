<?php
include_once('../main.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $conn = conn();
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($result) {
        echo json_encode(array("success" => true));
    } else {
        echo json_encode(array("success" => false));
    }
    exit();
}
?>
