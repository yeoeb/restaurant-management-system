<?php
require_once dirname(__DIR__) . '/models/menu_model.php';
require_once('../model/menu_model.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user']['id'];
    $total_price = $_POST['total_price'];
    $order_items = json_decode($_POST['items'], true); // Assuming items is a JSON encoded array

    $result = submit_order($user_id, $total_price, $order_items);
    if ($result) {
        header("Location: order_success.php?order_number=" . $result['order_number'] . "&order_date=" . $result['order_date']);
        exit();
    } else {
        $error_msg = "新增訂單失敗";
    }
}
require_once dirname(__DIR__) . '/includes/header.php';
?>

<form id="orderForm" method="post">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5 class="card-header">新增訂單</h5>
        </div>
        <br>
        <div class="card">
            <div class="row p-2">
                <div class="col-lg-6">
                    <div>
                        <label for="total_price" class="form-label">總價格:</label>
                        <input type="text" class="form-control" id="total_price" name="total_price">
                    </div>
                </div>
                <!-- 添加一个隐藏字段来存储 JSON 编码的订单项目信息 -->
                <input type="hidden" id="items" name="items" value='[]'>
                <!-- 這裡可以新增更多的輸入項目來顯示菜單項目 -->
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12 text-center" id="error_msg" style="color:red;">
                <?php if (isset($error_msg)) echo $error_msg; ?>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">提交訂單</button>
                <button type="button" class="btn btn-danger" onclick="cancel()">取消</button>
            </div>
        </div>
    </div>
</form>

<script>
function cancel(){
    location.href='<?php echo $base_url; ?>menu.php';
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
