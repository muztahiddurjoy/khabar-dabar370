<?php
require_once('connect.php');

// Fetch food items
$sql = "SELECT * FROM food";
$result = mysqli_query($conn, $sql);

$foodItems = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $foodItems[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khabar Dabar - Cafeteria Management System</title>
    <!-- Bootstrap, FontAwesome, Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Hind Siliguri', sans-serif;
            background-color: #f8f9fa;
        }
        .navbar { background-color: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1);}
        .navbar-brand img { max-height: 45px; }
        .navbar-brand { display: flex; align-items: center; gap: 10px;}
        .search-container { position: relative; max-width: 500px; margin: 0 auto; }
        .search-input { border-radius: 30px; padding: 10px 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border: 1px solid #ddd; width: 100%;}
        .search-button { position: absolute; right: 5px; top: 5px; border-radius: 50%; width: 35px; height: 35px; background-color: #228B22; color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer;}
        .action-buttons .btn { border-radius: 30px; padding: 8px 20px; font-weight: 500; margin-left: 10px;}
        .btn-add-items, .btn-total-sales { background-color: #228B22; color: white; }
        .main-content { padding: 30px 0; }
        .card { border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.08); margin-bottom: 20px; transition: transform 0.3s; height: 100%; }
        .card:hover { transform: translateY(-5px); }
        .card-img-top { height: 150px; object-fit: cover; }
        .card-body { padding: 15px; display: flex; flex-direction: column; }
        .card-title { font-weight: 600; margin-bottom: 8px; font-size: 1rem; }
        .item-info { display: flex; justify-content: space-between; margin-top: 10px; align-items: center; }
        .quantity-badge { background-color: #f8f9fa; color: #333; padding: 3px 8px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .stars { color: rgb(9,116,23); font-size: 0.8rem; }
        .bangla-text { font-family: 'Hind Siliguri', sans-serif; }
        .footer { background-color: white; padding: 20px 0; text-align: center; margin-top: 50px; border-top: 1px solid #eee; }
        .red-text { color: red; }
        .green-text { color: green; }
        .price { color: rgb(9,116,23); font-weight: 600; }
        .category-badge { background-color: #228B22; color: white; padding: 3px 8px; border-radius: 20px; font-size: 0.7rem; font-weight: 500; margin-right: 5px; margin-bottom: 5px; display: inline-block; }
        .health-tag { background-color: #ff9800; color: white; padding: 3px 8px; border-radius: 20px; font-size: 0.7rem; font-weight: 500; margin-bottom: 5px; display: inline-block; }
        .category-container { margin-bottom: 8px; }
        .row { margin-left: -8px; margin-right: -8px;}
        .delete-btn { position: absolute; top: 5px; right: 5px; background-color: rgba(255, 0, 0, 0.7); color: white; border: none; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; z-index: 10; }
        .delete-btn:hover { background-color: red; }
        .card-container { position: relative; height: 100%; }
        .alert { border-radius: 10px; margin-bottom: 20px; }
        .food-item { display: flex; }
        .amount-control { display: flex; align-items: center; }
        @media (max-width:768px) { .action-buttons { margin-top:15px; } .action-buttons .btn { margin-left:0; margin-right:10px;} }
    </style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#"><img src="images.png" alt="Khabar Dabar Logo"><span class="green-text">খাবার <span class="red-text">দাবার</span></span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <div class="search-container mx-auto my-2 my-lg-0">
                <input type="text" class="search-input" placeholder="Search items..." id="searchInput">
                <button type="button" class="search-button"><i class="fas fa-search"></i></button>
            </div>
            <div class="action-buttons ms-auto">
                <a href="insert_food.php" class="btn btn-add-items"><i class="fas fa-plus-circle me-1"></i> Add Items</a>
                <a href="sales.php" class="btn btn-total-sales"><i class="fas fa-chart-line me-1"></i> Total Sales</a>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container main-content">
    <!-- Display messages if any -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <h2 class="mb-4">Available Food Items</h2>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3" id="itemsContainer">
        <?php if (empty($foodItems)): ?>
            <div class="col-12 text-center">
                <p>No food items available at the moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($foodItems as $item): ?>
                <div class="col food-item" data-categories="<?= htmlspecialchars(strtolower($item['category'] ?? '')); ?>" data-name="<?= htmlspecialchars(strtolower($item['name'] ?? '')); ?>">
                    <div class="card-container">
                        <a href="delete.php?id=<?= (int)($item['id'] ?? 0); ?>&name=<?= urlencode($item['name'] ?? ''); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this item?');">
                            <i class="fas fa-trash"></i>
                        </a>
                        <div class="card">
                            <img src="<?= !empty($item['image']) ? htmlspecialchars($item['image']) : 'placeholder.jpg'; ?>" class="card-img-top" alt="<?= htmlspecialchars($item['name'] ?? 'Food Item'); ?>">
                            <div class="card-body">
                                <h5 class="card-title bangla-text"><?= htmlspecialchars($item['name'] ?? ''); ?></h5>
                                <div class="stars">
                                    <?php
                                        $rating = isset($item['rating']) ? floatval($item['rating']) : 0;
                                        $fullStars = floor($rating);
                                        $halfStar = ($rating - $fullStars) >= 0.5;
                                        for ($i = 0; $i < $fullStars; $i++) echo '<i class="fas fa-star"></i>';
                                        if ($halfStar) echo '<i class="fas fa-star-half-alt"></i>';
                                        for ($i = 0; $i < 5 - ceil($rating); $i++) echo '<i class="far fa-star"></i>';
                                    ?>
                                </div>
                                <div class="category-container">
                                    <?php 
                                    if (!empty($item['category'])):
                                        foreach (explode(',', $item['category']) as $cat): 
                                            if (!empty(trim($cat))): 
                                    ?>
                                        <span class="category-badge"><?= htmlspecialchars(trim($cat)); ?></span>
                                    <?php 
                                            endif;
                                        endforeach; 
                                    endif;
                                    ?>
                                </div>
                                <?php if (!empty($item['healthtag'])): ?>
                                <div class="health-tag"><?= htmlspecialchars($item['healthtag']); ?></div>
                                <?php endif; ?>

                                <div class="item-info mt-auto">
                                    <div class="amount-control">
                                        <button type="button" class="btn btn-danger btn-sm me-1" onclick="updateAmount(<?= (int)($item['id'] ?? 0); ?>, '<?= htmlspecialchars($item['name'] ?? ''); ?>', 'decrease')">-</button>
                                        <span class="quantity-badge"><?= htmlspecialchars($item['amount'] ?? 0); ?></span>
                                        <button type="button" class="btn btn-success btn-sm ms-1" onclick="updateAmount(<?= (int)($item['id'] ?? 0); ?>, '<?= htmlspecialchars($item['name'] ?? ''); ?>', 'increase')">+</button>
                                    </div>
                                    <span class="price ms-2">Tk.<?= htmlspecialchars($item['price'] ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Hidden form for updating amount -->
<form id="updateForm" action="update_amount.php" method="POST" style="display: none;">
    <input type="hidden" id="updateId" name="id" value="">
    <input type="hidden" id="updateName" name="name" value="">
    <input type="hidden" id="updateAction" name="action" value="">
</form>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Khabar Dabar - Cafeteria Management System</p>
    </div>
</footer>

<!-- Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- Function to update amount -->
<script>
function updateAmount(id, name, action) {
    // Set values in the hidden form
    document.getElementById('updateId').value = id;
    document.getElementById('updateName').value = name;
    document.getElementById('updateAction').value = action;
    
    // Submit the form
    document.getElementById('updateForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    // Get all food items
    const foodItems = document.querySelectorAll('.food-item');
    
    // Add search functionality
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.querySelector('.search-button');

    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        // If search term is empty, show all items
        if (searchTerm === '') {
            foodItems.forEach(item => {
                item.style.display = 'block';
            });
            return;
        }
        
        // Search through food items
        foodItems.forEach(item => {
            const foodName = item.getAttribute('data-name') || '';
            const categories = item.getAttribute('data-categories') || '';
            
            // Check if search term is in food name or categories
            if (foodName.includes(searchTerm) || categories.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Add event listeners for search
    searchButton.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent form submission if inside a form
            performSearch();
        }
    });
    
    // Also trigger search when input changes (for better user experience)
    searchInput.addEventListener('input', function() {
        // Optional: Add debounce here if you want to limit how often this fires
        performSearch();
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 1000);
            });
        }, 5000);
    }
});
</script>

</body>
</html>