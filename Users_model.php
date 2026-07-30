<?php

// 取得 AJAX 請求中的函數名稱
if (isset($_POST['functionName'])) {
    // 根據函數名稱調用相應的 PHP 函數
    if ($_POST['functionName'] == 'delete_users') {
        $data['id'] = $_POST['id'];
        delete_users($data); // 調用您寫的 PHP 函數
    }
}

function get_users_list($data) {
    $conn = conn(); // main.php
    if ($conn) {
        // 準備和綁定
        $sql = "SELECT * FROM users";

        if ($data['name'] || $data['account']) {
            // 假如有搜尋值
            $sql = $sql . " WHERE ";
        }

        if ($data['name']) {
            $sql = $sql . " name LIKE '%" . $data['name'] . "%'";
        }

        if ($data['name'] && $data['account']) {
            // 搜尋條件有兩個
            $sql = $sql . " AND ";
        }

        if ($data['account']) {
            $sql = $sql . " account LIKE '%" . $data['account'] . "%'";
        }

        $search_data = array();

        $r = mysqli_query($conn, $sql);

        // 獲得資料計數數數
        $result['num_rows'] = mysqli_num_rows($r);

        $page_start = ($data['page'] - 1) * 10;
        // 新增頁數限制條件
        $sql = $sql . " LIMIT " . $page_start . ", 10";
        $r = mysqli_query($conn, $sql);

        if ($r) {
            if (mysqli_num_rows($r) > 0) {
                while ($row = mysqli_fetch_assoc($r)) {
                    $search_data[] = $row;
                }
                $result['status'] = true;
                $result['data'] = $search_data;
            } else {
                $result['status'] = false;
            }
        } else {
            $result['status'] = false;
        }
    } else {
        $result['status'] = false;
    }
    return $result;
}
// 判斷帳號是否存在
function check_account_add($data)
{
    $conn = conn(); // main.php
    if ($conn) {
        $sql = "SELECT * FROM users WHERE account = '" . $data['account'] . "';";
        $r = mysqli_query($conn, $sql);
        if ($r) {
            if (mysqli_num_rows($r) > 0) {
                while ($row = mysqli_fetch_assoc($r)) {
                    $search_data[] = $row;
                }
                $result['status'] = true;
                $result['data'] = $search_data;
            } else {
                $result['status'] = false;
            }
        } else {
            $result['status'] = false;
        }
    } else {
        $result['status'] = false;
    }
    return $result;
}

// 新增帳號
function add_users($data) {
    $conn = conn(); // main.php 中的 conn 函數
    if ($conn) {
        $sql = "INSERT INTO users (name, account, password, type) VALUES ('" . $data['name'] . "','" . $data['account'] . "','" . $data['password'] . "','" . $data['type'] . "')";
        $result = mysqli_query($conn, $sql);
        return $result;
    }
    return false;
}
// 取得資料 by id
function get_users_by_id($data)
{
    $conn = conn(); // main.php
    if ($conn) {
        $sql = "SELECT * FROM users WHERE id = " . $data['id'];
        $r = mysqli_query($conn, $sql);
        if ($r) {
            if (mysqli_num_rows($r) > 0) {
                while ($row = mysqli_fetch_assoc($r)) {
                    $search_data[] = $row;
                }
                $result['status'] = true;
                $result['data'] = $search_data;
            } else {
                $result['status'] = false;
            }
        } else {
            $result['status'] = false;
        }
    } else {
        $result['status'] = false;
    }
    return $result;
}
// 判斷帳號是否存在 edit
function check_account_edit($data)
{
    $conn = conn(); // main.php
    if ($conn) {
        $sql = "SELECT * FROM users WHERE account = '" . $data['account'] . "' AND id != '" . $data['id'] . "';";
        $r = mysqli_query($conn, $sql);
        if ($r) {
            if (mysqli_num_rows($r) > 0) {
                while ($row = mysqli_fetch_assoc($r)) {
                    $search_data[] = $row;
                }
                $result['status'] = true;
                $result['data'] = $search_data;
            } else {
                $result['status'] = false;
            }
        } else {
            $result['status'] = false;
        }
    } else {
        $result['status'] = false;
    }
    return $result;
}
//login
function get_user_by_account($account) {
    $conn = conn(); // main.php 中的 conn 函数
    if ($conn) {
        $sql = "SELECT * FROM users WHERE account = '$account'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    return false;
}



// 更新用戶資料
function update_users($data)
{
    $conn = conn(); // main.php
    if ($conn) {
        $sql = "UPDATE users SET name='" . $data['name'] . "', account='" . $data['account'] . "', type='" . $data['type'] . "' WHERE id=" . $data['id'];
        $r = mysqli_query($conn, $sql);
        if ($r) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

// 修改帳號
function edit_users($data)
{
    $conn = conn(); // main.php
    if ($conn) {
        $sql = "UPDATE users SET name='" . $data['name'] . "', account='" . $data['account'] . "', type='" . $data['type'] . "' WHERE id='" . $data['id'] . "';";
        $r = mysqli_query($conn, $sql);
        if ($r) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
// 刪除帳號 (AJAX)
function delete_users($data)
{
    $conn = conn();
    if ($conn) {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $data['id']);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    } else {
        return false;
    }
}


?>