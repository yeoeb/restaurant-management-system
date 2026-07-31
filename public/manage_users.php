<?php

session_start();

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/user_model.php';
require_once dirname(__DIR__) . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['functionName']) && $_POST['functionName'] == 'delete_users') {
    $data['id'] = $_POST['id'];
    $result = delete_users($data);
    echo json_encode(array('success' => $result));
    exit();
}

$title = "帳號管理";
$active = "account";
require_once dirname(__DIR__) . '/includes/header.php';

// 取得用戶列表
$data = array(
    'name' => isset($_GET['name']) ? $_GET['name'] : '',
    'account' => isset($_GET['account']) ? $_GET['account'] : '',
    'page' => isset($_GET['page']) ? $_GET['page'] : 1
);
$users = get_users_list($data);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">帳號管理</h4>

    <div class="card">
        <h5 class="card-header">搜尋條件</h5>
        <div class="card-body">
            <form method="get">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="name" placeholder="姓名" value="<?php echo $data['name']; ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="account" placeholder="帳號" value="<?php echo $data['account']; ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">查詢</button>
                        <button type="reset" class="btn btn-secondary" onclick="location.href='<?php echo $base_url; ?>manage_users.php'">取消</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <h5 class="card-header">帳號列表</h5>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>姓名</th>
                        <th>帳號</th>
                        <th>身份</th>
                        <th>功能</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users['status']): ?>
                        <?php foreach ($users['data'] as $index => $user): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['account']; ?></td>
                                <td><?php echo $user['type']; ?></td> <!-- 新增這行顯示身份 -->
                                <td>
                                    <button class="btn btn-warning" onclick="location.href='<?php echo $base_url; ?>edit_user.php?id=<?php echo $user['id']; ?>'">修改</button>
                                    <button class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">刪除</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">沒有找到相關資料</td> <!-- 更新 colspan 為 5 -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-between">
                <button class="btn btn-primary" onclick="prevPage()">上一頁</button>
                <span id="page-info">第 <?php echo $data['page']; ?> 頁</span>
                <button class="btn btn-primary" onclick="nextPage()">下一頁</button>
            </div>
        </div>
    </div>

    <a href="<?php echo $base_url; ?>register.php" class="btn btn-success mt-4">新增帳號</a>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>

<script>
function deleteUser(userId) {
    if (confirm("確定要刪除此用戶嗎？")) {
        // 發送刪除用戶的AJAX請求
        $.ajax({
            url: '<?php echo $base_url; ?>manage_users.php',
            type: 'POST',
            data: {
                functionName: 'delete_users',
                id: userId
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    alert("用戶已刪除");
                    location.reload();
                } else {
                    alert("刪除失敗");
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr);
                alert("刪除失敗，請檢查控制台以獲取更多信息。");
            }
        });
    }
}


function prevPage() {
    var currentPage = <?php echo $data['page']; ?>;
    if (currentPage > 1) {
        location.href = '<?php echo $base_url; ?>manage_users.php?page=' + (currentPage - 1);
    }
}

function nextPage() {
    var currentPage = <?php echo $data['page']; ?>;
    location.href = '<?php echo $base_url; ?>manage_users.php?page=' + (currentPage + 1);
}
</script>
