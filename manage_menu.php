<?php
include_once('../main.php');
$title = "管理菜單";
$active = "manage_menu";
include_once('../header.php');

// 查詢條件
$name = isset($_GET['name']) ? $_GET['name'] : '';
$description = isset($_GET['description']) ? $_GET['description'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// 取得菜單項目
$conn = conn();
$sql = "SELECT * FROM menu_items WHERE name LIKE ? AND description LIKE ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$search_name = '%' . $name . '%';
$search_description = '%' . $description . '%';
$stmt->bind_param('ssii', $search_name, $search_description, $offset, $items_per_page);
$stmt->execute();
$r = $stmt->get_result();

$menu_items = [];
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $menu_items[] = $row;
    }
}

// 取得總項目數量
$sql_total = "SELECT COUNT(*) as total FROM menu_items WHERE name LIKE ? AND description LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param('ss', $search_name, $search_description);
$stmt_total->execute();
$r_total = $stmt_total->get_result();
$total_items = $r_total->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">管理菜單</h4>

    <div class="card">
        <h5 class="card-header">搜尋條件</h5>
        <div class="card-body">
            <form method="get">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="name" placeholder="名稱" value="<?php echo $name; ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="description" placeholder="描述" value="<?php echo $description; ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">查詢</button>
                        <button type="reset" class="btn btn-secondary" onclick="location.href='<?php echo $base_url; ?>manage_menu.php'">取消</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">菜單項目</h5>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>名稱</th>
                        <th>描述</th>
                        <th>價格</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($menu_items)): ?>
                        <?php foreach ($menu_items as $item): ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['description']; ?></td>
                                <td><?php echo $item['price']; ?></td>
                                <td>
                                    <a href="edit_menu_item.php?id=<?php echo $item['id']; ?>" class="btn btn-warning">修改</a>
                                    <a href="delete_menu_item.php?id=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('確定要刪除此菜單項目嗎？');">刪除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">沒有找到相關資料</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-between">
                <button class="btn btn-primary" onclick="prevPage()">上一頁</button>
                <span id="page-info">第 <?php echo $page; ?> 頁</span>
                <button class="btn btn-primary" onclick="nextPage()">下一頁</button>
            </div>
        </div>
    </div>

    <a href="add_menu_item.php" class="btn btn-success mt-4">新增菜單項目</a>
</div>

<?php include_once('../footer.php'); ?>

<script>
function prevPage() {
    var currentPage = <?php echo $page; ?>;
    if (currentPage > 1) {
        location.href = '<?php echo $base_url; ?>manage_menu.php?page=' + (currentPage - 1) + '&name=<?php echo $name; ?>&description=<?php echo $description; ?>';
    }
}

function nextPage() {
    var currentPage = <?php echo $page; ?>;
    if (currentPage < <?php echo $total_pages; ?>) {
        location.href = '<?php echo $base_url; ?>manage_menu.php?page=' + (currentPage + 1) + '&name=<?php echo $name; ?>&description=<?php echo $description; ?>';
    }
}
</script>
