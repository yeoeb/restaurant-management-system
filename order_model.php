<?php
// order_model.php

function save_order($user_id, $items, $total_price) {
    global $db;

    $db->begin_transaction();

    try {
        $query = "INSERT INTO orders (user_id, total_price) VALUES (?, ?)";
        $stmt = $db->prepare($query);
        if (!$stmt) {
            throw new Exception('準備查詢失敗: ' . $db->error);
        }
        $stmt->bind_param('id', $user_id, $total_price);
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception('執行查詢失敗: ' . $stmt->error);
        }
        $order_id = $stmt->insert_id;

        foreach ($items as $item) {
            $query = "INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            if (!$stmt) {
                throw new Exception('準備查詢失敗: ' . $db->error);
            }
            $stmt->bind_param('iiid', $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception('執行查詢失敗: ' . $stmt->error);
            }
        }

        $db->commit();
        return $order_id;
    } catch (Exception $e) {
        $db->rollback();
        error_log('訂單保存失敗: ' . $e->getMessage());
        return false;
    }
}
?>
