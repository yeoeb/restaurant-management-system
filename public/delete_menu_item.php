<?php
require_once dirname(__DIR__) . '/models/menu_model.php';
require_once('../model/menu_model.php'); // 确保包含菜单模型文件

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 调用删除菜单项函数
    $result = delete_menu_item($id);

    if ($result) {
        echo "<script>
                alert('刪除菜單項目成功');
                window.location.href = 'manage_menu.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('刪除菜單項目失敗');
                window.location.href = 'manage_menu.php';
              </script>";
        exit();
    }
} else {
    echo "<script>
            alert('未找到要刪除的菜單項目');
            window.location.href = 'manage_menu.php';
          </script>";
    exit();
}
?>
