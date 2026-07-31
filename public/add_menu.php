<?php
require_once dirname(__DIR__) . '/models/menu_model.php';
require_once('../model/menu_model.php');
$title = "新增菜單項目";
$active = "add_menu";
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $result = add_menu_item(array(
        'name' => $name,
        'description' => $description,
        'price' => $price
    ));

    if ($result) {
        header("Location: menu.php");
        exit();
    } else {
        $error_msg = "新增菜單項目失敗";
    }
}
?>

<form method="post">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <h5 class="card-header">新增菜單項目</h5>
        </div>
        <br>
        <div class="card">
            <div class="row p-2">
                <div class="col-lg-6">
                    <div>
                        <label for="name" class="form-label">菜名:</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="description" class="form-label">描述:</label>
                        <input type="text" class="form-control" id="description" name="description">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div>
                        <label for="price" class="form-label">價格:</label>
                        <input type="text" class="form-control" id="price" name="price">
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-12 text-center" id="error_msg" style="color:red;">
                <?php if (isset($error_msg)) echo $error_msg; ?>
            </div>
            <div class="col-12 text-center">
                <button type="submit" name="submit" class="btn btn-primary">新增</button>
                <button type="button" class="btn btn-danger" onclick="cancel()">取消</button>
            </div>
        </div>
    </div>
</form>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

<script>
function cancel(){
    location.href='<?php echo $base_url; ?>menu.php';
}
</script>
