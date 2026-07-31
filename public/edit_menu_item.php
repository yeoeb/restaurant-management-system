<?php
require_once dirname(__DIR__) . '/models/menu_model.php';
require_once('../model/menu_model.php'); // 确保包含菜单模型文件

$title = "修改菜單項目";
$active = "manage_menu";
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $data = array(
        'id' => $id,
        'name' => $name,
        'description' => $description,
        'price' => $price
    );

    $result = update_menu_item($data);
    if ($result) {
        echo '<script>alert("菜單項目更新成功"); window.location.href="manage_menu.php";</script>';
    } else {
        $error_msg = "更新菜單項目失敗";
    }
} else {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $menu_item = get_menu_item_by_id($id);
        if ($menu_item) {
            $name = $menu_item['name'];
            $description = $menu_item['description'];
            $price = $menu_item['price'];
        } else {
            echo '<script>alert("找不到指定的菜單項目"); window.location.href="manage_menu.php";</script>';
            exit;
        }
    } else {
        echo '<script>alert("未指定菜單項目 ID"); window.location.href="manage_menu.php";</script>';
        exit;
    }
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">修改菜單項目</h4>

    <?php if (isset($error_msg)): ?>
    <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
        <div class="mb-3">
            <label for="name" class="form-label">菜名:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">描述:</label>
            <input type="text" class="form-control" id="description" name="description" value="<?php echo isset($description) ? $description : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">價格:</label>
            <input type="number" class="form-control" id="price" name="price" value="<?php echo isset($price) ? $price : ''; ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">保存修改</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='manage_menu.php'">取消</button>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

