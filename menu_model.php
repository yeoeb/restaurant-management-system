<?php

function get_menu_items() {
    $conn = conn();
    $sql = "SELECT * FROM menu_items";
    $result = mysqli_query($conn, $sql);
    $menu_items = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $menu_items[] = $row;
        }
    }
    return $menu_items;
}

function add_menu_item($data) {
    $conn = conn();
    $sql = "INSERT INTO menu_items (name, description, price) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssd", $data['name'], $data['description'], $data['price']);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}

function submit_order($user_id, $total_price, $order_items) {
    $conn = conn();
    date_default_timezone_set('Asia/Taipei'); // 設置時區，確保時間正確
    $order_date = date("Y-m-d");
    $order_time = date("H:i:s");

    // 計算當天的訂單編號
    $sql = "SELECT COUNT(*) AS order_count FROM orders WHERE order_date = CURDATE()";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }
    $row = mysqli_fetch_assoc($result);
    $order_number = $row['order_count'] + 1;

    $sql = "INSERT INTO orders (user_id, total_price, order_date, order_time, order_number, status) VALUES (?, ?, ?, ?, ?, 'current')";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Error preparing statement for orders: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "idssi", $user_id, $total_price, $order_date, $order_time, $order_number);

    if (mysqli_stmt_execute($stmt)) {
        $order_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);

        foreach ($order_items as $item) {
            $menu_item_id = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                die("Error preparing statement for order items: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "iiid", $order_id, $menu_item_id, $quantity, $price);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);

        // 設置 session 變量
        $_SESSION['order_info'] = array('order_number' => $order_number, 'order_date' => $order_date, 'order_time' => $order_time);
        $_SESSION['total_price'] = $total_price;

        return array('order_number' => $order_number, 'order_date' => $order_date, 'order_time' => $order_time);
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return false;
    }
}








// menu_model.php

function get_menu_item() {
    $conn = conn();
    $sql = "SELECT * FROM menu_items";
    $result = mysqli_query($conn, $sql);
    $menu_items = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // 确保 price 是数字类型
            $row['price'] = floatval($row['price']);
            $menu_items[] = $row;
        }
    }
    mysqli_close($conn);
    return $menu_items;
}
function update_menu_item($data) {
    $conn = conn();
    $sql = "UPDATE menu_items SET name = ?, description = ?, price = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdi", $data['name'], $data['description'], $data['price'], $data['id']);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}
function delete_menu_item($id) {
    $conn = conn();
    $sql = "DELETE FROM menu_items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $result;
}




function get_order_by_id($order_id) {
    $conn = conn();
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $order;
}
// menu_model.php

// 獲取特定狀態的訂單
function get_orders_by_status($status) {
    $conn = conn();
    $sql = "SELECT * FROM orders WHERE status = ? ORDER BY order_date DESC, order_time DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $status);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $orders;
}

// 獲取用戶的歷史訂單
function get_user_orders($user_id) {
    $conn = conn();
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC, order_time DESC";
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        die("Failed to prepare statement: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die("Failed to execute statement: " . mysqli_error($conn));
    }
    
    $orders = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $orders;
}




// 獲取訂單的具體項目
function get_order_items($order_id) {
    $conn = conn();
    $sql = "SELECT oi.*, mi.name, mi.description 
            FROM order_items oi 
            JOIN menu_items mi ON oi.menu_item_id = mi.id 
            WHERE oi.order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $items = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $items;
}
function get_user_info($user_id) {
    $conn = conn();
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_info = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    // 添加调试信息
    if (!$user_info) {
        error_log("Failed to fetch user info for user_id: $user_id");
    }
    
    return $user_info;
}

function get_menu_item_by_id($id) {
    $conn = conn();
    $sql = "SELECT * FROM menu_items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $menu_item = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $menu_item;
}
function complete_order($order_id) {
    $conn = conn();
    $sql = "UPDATE orders SET status = 'completed' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $result;
}

// 在 menu_model.php 文件中

function get_menu_item_counter($limit = 9, $offset = 0) {
    $conn = conn(); // 使用 conn 函數獲取數據庫連接
    $query = "SELECT * FROM menu_items LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $menu_items = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $menu_items[] = $row;
        }
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $menu_items;
}

// 獲取菜單項目總數
function get_menu_items_count() {
    $conn = conn(); // 使用 conn 函數獲取數據庫連接
    $query = "SELECT COUNT(*) as count FROM menu_items";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = 0;
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $count;
}




function get_user_orders_by_conditions($data) {
    $conn = conn();
    $sql = "SELECT * FROM orders WHERE user_id = ? AND status != 'current'";
    $params = array($data['user_id']);

    if (!empty($data['order_date'])) {
        $sql .= " AND order_date = ?";
        $params[] = $data['order_date'];
    }
    if (!empty($data['total_price'])) {
        $sql .= " AND total_price = ?";
        $params[] = $data['total_price'];
    }

    $sql .= " LIMIT ?, ?";
    $params[] = (int)$data['offset'];
    $params[] = (int)$data['limit'];

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('s', count($params) - 2) . 'ii', ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}



function get_menu_items_by_conditions($limit, $offset, $name, $price) {
    $conn = conn();
    $sql = "SELECT * FROM menu_items WHERE 1=1";
    $params = array();
    $types = "";

    if (!empty($name)) {
        $sql .= " AND name LIKE ?";
        $params[] = '%' . $name . '%';
        $types .= "s";
    }
    if (!empty($price)) {
        $sql .= " AND price <= ?";
        $params[] = $price;
        $types .= "d";
    }

    $sql .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $limit;
    $types .= "ii";

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $menu_items = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $menu_items[] = $row;
        }
    }
    return $menu_items;
}

function get_menu_items_count_by_conditions($name, $price) {
    $conn = conn();
    $sql = "SELECT COUNT(*) as total FROM menu_items WHERE 1=1";
    $params = array();
    $types = "";

    if (!empty($name)) {
        $sql .= " AND name LIKE ?";
        $params[] = '%' . $name . '%';
        $types .= "s";
    }
    if (!empty($price)) {
        $sql .= " AND price <= ?";
        $params[] = $price;
        $types .= "d";
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];

    return $total;
}

function get_orders_by_conditions($data) {
    $conn = conn();
    $sql = "SELECT * FROM orders WHERE status = ?";
    $params = array($data['status']);
    $types = "s";

    if (!empty($data['user_id'])) {
        $sql .= " AND user_id = ?";
        $params[] = $data['user_id'];
        $types .= "i";
    }
    if (!empty($data['order_date'])) {
        $sql .= " AND order_date = ?";
        $params[] = $data['order_date'];
        $types .= "s";
    }
    if (!empty($data['order_time'])) {
        $sql .= " AND order_time = ?";
        $params[] = $data['order_time'];
        $types .= "s";
    }

    $sql .= " ORDER BY " . $data['sort_by'] . " " . $data['sort_order'];
    $sql .= " LIMIT ?, ?";
    $params[] = (int)$data['offset'];
    $params[] = (int)$data['limit'];
    $types .= "ii";

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    return $orders;
}

function get_orders_count($data) {
    $conn = conn();
    $sql = "SELECT COUNT(*) as total FROM orders WHERE status = ?";
    $params = array($data['status']);
    $types = "s";

    if (!empty($data['user_id'])) {
        $sql .= " AND user_id = ?";
        $params[] = $data['user_id'];
        $types .= "i";
    }
    if (!empty($data['order_date'])) {
        $sql .= " AND order_date = ?";
        $params[] = $data['order_date'];
        $types .= "s";
    }
    if (!empty($data['order_time'])) {
        $sql .= " AND order_time = ?";
        $params[] = $data['order_time'];
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];

    return $total;
}

function get_user_orders_paged($user_id, $order_date, $total_price, $offset, $limit) {
    $conn = conn();
    $sql = "SELECT * FROM orders WHERE user_id = ?";
    if (!empty($order_date)) {
        $sql .= " AND order_date = ?";
    }
    if (!empty($total_price)) {
        $sql .= " AND total_price = ?";
    }
    $sql .= " LIMIT ?, ?";
    
    $stmt = $conn->prepare($sql);
    if (!empty($order_date) && !empty($total_price)) {
        $stmt->bind_param("isiii", $user_id, $order_date, $total_price, $offset, $limit);
    } elseif (!empty($order_date)) {
        $stmt->bind_param("isii", $user_id, $order_date, $offset, $limit);
    } elseif (!empty($total_price)) {
        $stmt->bind_param("iiii", $user_id, $total_price, $offset, $limit);
    } else {
        $stmt->bind_param("iii", $user_id, $offset, $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_user_orders_count($user_id, $order_date, $total_price) {
    $conn = conn();
    $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
    if (!empty($order_date)) {
        $sql .= " AND order_date = ?";
    }
    if (!empty($total_price)) {
        $sql .= " AND total_price = ?";
    }
    
    $stmt = $conn->prepare($sql);
    if (!empty($order_date) && !empty($total_price)) {
        $stmt->bind_param("isi", $user_id, $order_date, $total_price);
    } elseif (!empty($order_date)) {
        $stmt->bind_param("is", $user_id, $order_date);
    } elseif (!empty($total_price)) {
        $stmt->bind_param("ii", $user_id, $total_price);
    } else {
        $stmt->bind_param("i", $user_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

