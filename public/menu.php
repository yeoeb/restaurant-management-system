<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/models/menu_model.php'; // 確保包含菜單模型文件
$title = "菜單";
$active = "menu";
require_once dirname(__DIR__) . '/includes/header.php';

function fetch_menu_items($limit, $offset) {
    return get_menu_item_counter($limit, $offset); // 使用 get_menu_item_counter 函數
}

function get_total_items() {
    return get_menu_items_count();
}

if (isset($_GET['ajax'])) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 9;
    $offset = ($page - 1) * $limit;
    $menu_items = fetch_menu_items($limit, $offset);
    
    // 返回部分更新的HTML
    foreach ($menu_items as $item) {
        echo '
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">'.$item['name'].'</h5>
                        <p class="card-text">'.$item['description'].'</p>
                        <p class="card-text">'.$item['price'].'</p>
                        <button class="btn btn-primary" onclick="addToCart('.$item['id'].')">加入購物車</button>
                    </div>
                </div>
              </div>';
    }
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;
$menu_items = fetch_menu_items($limit, $offset);
$total_items = get_total_items();
$total_pages = ceil($total_items / $limit);
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">菜單</h4>

    <div id="menu-items" class="row">
        <?php foreach ($menu_items as $item): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $item['name']; ?></h5>
                        <p class="card-text"><?php echo $item['description']; ?></p>
                        <p class="card-text"><?php echo $item['price']; ?></p>
                        <button class="btn btn-primary" onclick="addToCart(<?php echo $item['id']; ?>)">加入購物車</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- 分頁導航 -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="#" onclick="fetchPage(<?php echo $page - 1; ?>)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="#" onclick="fetchPage(<?php echo $i; ?>)"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="#" onclick="fetchPage(<?php echo $page + 1; ?>)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>

    <h4 class="fw-bold py-3 mb-4">購物車</h4>
    <form id="orderForm" method="post" action="submit_order.php" onsubmit="return validateOrderForm()">
        <table class="table">
            <thead>
                <tr>
                    <th>名稱</th>
                    <th>價格</th>
                    <th>數量</th>
                    <th>總價</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <!-- 購物車項目會被動態添加到這裡 -->
            </tbody>
        </table>
        <div class="text-right">
            <h5>總金額: <span id="total-price">0</span></h5>
        </div>
        <input type="hidden" id="total_price" name="total_price" value="0">
        <input type="hidden" id="items" name="items" value='[]'>
        <button type="submit" class="btn btn-success">提交訂單</button>
    </form>
</div>

<script>
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function addToCart(itemId) {
    const items = <?php echo json_encode($menu_items); ?>;
    const item = items.find(i => i.id == itemId);
    item.price = parseFloat(item.price);

    const existingItem = cart.find(i => i.id == itemId);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({...item, quantity: 1});
    }
    localStorage.setItem('cart', JSON.stringify(cart)); // 保存到localStorage
    renderCart();
}

function removeFromCart(itemId) {
    cart = cart.filter(i => i.id != itemId);
    localStorage.setItem('cart', JSON.stringify(cart)); // 保存到localStorage
    renderCart();
}

function renderCart() {
    const cartItemsContainer = document.getElementById('cart-items');
    cartItemsContainer.innerHTML = '';
    let totalPrice = 0;
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        totalPrice += itemTotal;
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.price.toFixed(2)}</td>
            <td><input type="number" min="1" value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)"></td>
            <td>${itemTotal.toFixed(2)}</td>
            <td>
                <button class="btn btn-danger" onclick="removeFromCart(${item.id})">移除</button>
            </td>
        `;
        cartItemsContainer.appendChild(row);
    });
    document.getElementById('total-price').innerText = totalPrice.toFixed(2);
    document.getElementById('total_price').value = totalPrice.toFixed(2); // 確保正確設置總金額
    document.getElementById('items').value = JSON.stringify(cart); // 確保正確設置訂單項目
}

function updateQuantity(itemId, quantity) {
    const item = cart.find(i => i.id == itemId);
    if (item) {
        item.quantity = parseInt(quantity);
        localStorage.setItem('cart', JSON.stringify(cart)); // 保存到localStorage
        renderCart();
    }
}

function validateOrderForm() {
    const totalPrice = parseFloat(document.getElementById('total-price').innerText);
    if (totalPrice <= 0) {
        alert('總金額不能為0');
        return false;
    }
    return true;
}

window.onload = function() {
    renderCart(); // 頁面加載時顯示購物車內容
}

function fetchPage(page) {
    $.ajax({
        url: 'menu.php',
        type: 'GET',
        data: { ajax: 1, page: page },
        success: function(response) {
            $('#menu-items').html(response);
            history.pushState(null, '', 'menu.php?page=' + page); // 更新URL不重載頁面
            
            // 重新初始化頁面
            reinitializePage();
        },
        error: function(xhr) {
            console.error('AJAX request failed: ' + xhr.statusText);
        }
    });
}


function reinitializePage() {
    location.reload();
}


function cancel() {
    location.href='<?php echo $base_url; ?>menu.php';
}

</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
