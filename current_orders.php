<?php
include_once('../main.php');
include_once('../model/menu_model.php'); // 確保包含模型文件
$title = "當前訂單";
$active = "current_orders";
include_once('../header.php');

// 查詢條件
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';
$order_date = isset($_GET['order_date']) ? $_GET['order_date'] : '';
$order_time = isset($_GET['order_time']) ? $_GET['order_time'] : '';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'order_date';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// 取得訂單
$data = array(
    'user_id' => $user_id,
    'order_date' => $order_date,
    'order_time' => $order_time,
    'status' => 'current',
    'sort_by' => $sort_by,
    'sort_order' => $sort_order,
    'limit' => $items_per_page,
    'offset' => $offset
);
$orders = get_orders_by_conditions($data);

// 取得總訂單數量
$total_orders = get_orders_count($data);
$total_pages = ceil($total_orders / $items_per_page);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">當前訂單</h4>

    <div class="card">
        <h5 class="card-header">搜尋條件</h5>
        <div class="card-body">
            <form method="get" id="searchForm">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="user_id" placeholder="用戶ID" value="<?php echo $user_id; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="order_date" value="<?php echo $order_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="time" class="form-control" name="order_time" value="<?php echo $order_time; ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">查詢</button>
                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">取消</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <select class="form-control" name="sort_by">
                            <option value="order_date" <?php echo $sort_by == 'order_date' ? 'selected' : ''; ?>>訂單日期</option>
                            <option value="total_price" <?php echo $sort_by == 'total_price' ? 'selected' : ''; ?>>總金額</option>
                            <option value="order_time" <?php echo $sort_by == 'order_time' ? 'selected' : ''; ?>>訂單時間</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="sort_order">
                            <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>升序</option>
                            <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>降序</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($orders)): ?>
        <div class="accordion" id="ordersAccordion">
            <?php foreach ($orders as $order): ?>
                <div class="card">
                    <div class="card-header" id="heading<?php echo $order['id']; ?>">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?php echo $order['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $order['id']; ?>">
                                訂單號: <?php echo $order['order_number']; ?> - 日期: <?php echo $order['order_date'] . ' ' . $order['order_time']; ?>
                            </button>
                        </h5>
                    </div>
                    <div id="collapse<?php echo $order['id']; ?>" class="collapse" aria-labelledby="heading<?php echo $order['id']; ?>" data-parent="#ordersAccordion">
                        <div class="card-body">
                            <p class="card-text">總金額: <?php echo $order['total_price']; ?></p>
                            <p class="card-text">狀態: <?php echo $order['status'] == 'current' ? '待處理' : '完成'; ?></p>
                            <h6 class="card-subtitle mb-2 text-muted">訂單項目:</h6>
                            <ul>
                                <?php
                                $order_items = get_order_items($order['id']);
                                foreach ($order_items as $item):
                                ?>
                                    <li><?php echo $item['name'] . ' - ' . $item['quantity'] . ' x ' . $item['price']; ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <form method="post" action="complete_order.php">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <button type="submit" class="btn btn-success">完成</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&user_id=<?php echo $user_id; ?>&order_date=<?php echo $order_date; ?>&order_time=<?php echo $order_time; ?>&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>
    <?php else: ?>
        <p>沒有當前訂單。</p>
    <?php endif; ?>
</div>

<?php include_once('../footer.php'); ?>
<script>
function resetFilters() {
    location.href = '<?php echo $base_url; ?>current_orders.php';
}

$(document).ready(function() {
    $('.collapse').collapse();
});
</script>
