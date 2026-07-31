<?php
session_start();
session_unset();
session_destroy();
header("Location: login.php");
echo '<script>
    localStorage.removeItem("cart");
    window.location.href = "login.php"; // 重定向到登入頁面
</script>';
exit();
?>
