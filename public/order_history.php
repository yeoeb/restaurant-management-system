<?php

session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/menu_model.php';

$title = '歷史訂單';
$active = 'order_history';

require_once dirname(__DIR__) . '/includes/header.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
$user_id = $_SESSION['user']['id'];
$order_date = isset($_GET['order_date']) ? $_GET['order_date'] : '';
$total_price = isset($_GET['total_price']) ? $_GET['total_price'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// 取得訂單數據
$orders = get_user_orders_paged($user_id, $order_date, $total_price, $offset, $items_per_page);

// 取得總訂單數量
$total_orders = get_user_orders_count($user_id, $order_date, $total_price);
$total_pages = ceil($total_orders / $items_per_page);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">歷史訂單</h4>

    <div class="card">
        <h5 class="card-header">搜尋條件</h5>
        <div class="card-body">
            <form method="get">
                <div class="row">
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="order_date" value="<?php echo $order_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" name="total_price" placeholder="總金額" value="<?php echo $total_price; ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">查詢</button>
                        <button type="reset" class="btn btn-secondary" onclick="resetFilters()">取消</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">訂單號: <?php echo $order['order_number']; ?> - 日期: <?php echo $order['order_date'] . ' ' . $order['order_time']; ?></h5>
                    <p class="card-text">總金額: <?php echo $order['total_price']; ?></p>
                    <p class="card-text">
                        狀態: 
                        <?php 
                        if ($order['status'] == 'current') {
                            echo '待處理';
                        } else {
                            echo '完成';
                        }
                        ?>
                    </p>
                    <h6 class="card-subtitle mb-2 text-muted">訂單項目:</h6>
                    <ul>
                        <?php
                        $order_items = get_order_items($order['id']);
                        foreach ($order_items as $item):
                        ?>
                            <li><?php echo $item['name'] . ' - ' . $item['quantity'] . ' x ' . $item['price']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-between">
            <a class="btn btn-primary" href="?page=<?php echo $page - 1; ?>&order_date=<?php echo $order_date; ?>&total_price=<?php echo $total_price; ?>" <?php if($page <= 1) echo 'style="visibility: hidden;"'; ?>>上一頁</a>
            <span>第 <?php echo $page; ?> 頁 / 共 <?php echo $total_pages; ?> 頁</span>
            <a class="btn btn-primary" href="?page=<?php echo $page + 1; ?>&order_date=<?php echo $order_date; ?>&total_price=<?php echo $total_price; ?>" <?php if($page >= $total_pages) echo 'style="visibility: hidden;"'; ?>>下一頁</a>
        </div>
    <?php else: ?>
        <p>沒有歷史訂單。</p>
    <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

<script>
function resetFilters() {
    location.href = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
}
</script>
