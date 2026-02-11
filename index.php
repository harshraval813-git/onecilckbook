<?php
// DB Connection
$conn = new mysqli("localhost", "root", "", "oneclickbook");

// Booking Logic
if(isset($_POST['book_now'])) {
    $v_id = $_POST['vehicle_id'];
    $name = $_POST['customer_name'];
    $days = $_POST['days'];
    $total = $days * $_POST['price'];

    $conn->query("INSERT INTO bookings (vehicle_id, customer_name, days, total_bill) VALUES ('$v_id', '$name', '$days', '$total')");
    $conn->query("UPDATE vehicles SET status = 'Booked' WHERE id = '$v_id'");
    
    echo "<script>alert('✅ Booking Successful! Bill: ₹$total'); window.location.href='index.php';</script>";
}

// Reset Logic
if(isset($_GET['reset'])) {
    $conn->query("UPDATE vehicles SET status = 'Available'");
    $conn->query("TRUNCATE TABLE bookings");
    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>1ClickBook | Final Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: #007bff; box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3); }
        .brand { color: white; font-weight: 900; font-size: 24px; letter-spacing: 1px; }
        
        .vehicle-card { background: white; border-radius: 15px; overflow: hidden; transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.1); border: none; }
        .vehicle-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.2); }
        
        /* Image Styling */
        .img-container { height: 220px; overflow: hidden; position: relative; }
        .vehicle-img { width: 100%; height: 100%; object-fit: cover; }
        
        /* Badges */
        .status-badge { position: absolute; top: 15px; right: 15px; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 12px; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.3); }
        .bg-available { background: #28a745; }
        .bg-booked { background: #dc3545; }
        
        .price-tag { color: #007bff; font-size: 1.5rem; font-weight: bold; }
        .btn-book { background: #007bff; color: white; font-weight: bold; border: none; }
        .btn-book:hover { background: #0056b3; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg p-3">
    <div class="container">
        <span class="brand">⚡ 1ClickBook</span>
        <a href="index.php?reset=1" class="btn btn-light btn-sm fw-bold text-primary">🔄 Reset System</a>
    </div>
</nav>

<div class="container mt-5 mb-5">
    <h2 class="text-center fw-bold mb-5">Choose Your Ride 🚀</h2>
    
    <div class="row g-4">
        <?php
        $result = $conn->query("SELECT * FROM vehicles");
        while($row = $result->fetch_assoc()) {
            $is_booked = ($row['status'] == 'Booked');
        ?>
        
        <div class="col-md-4">
            <div class="vehicle-card h-100 position-relative">
                
                <div class="img-container">
                    <span class="status-badge <?php echo $is_booked ? 'bg-booked' : 'bg-available'; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                    <img src="<?php echo $row['image_url']; ?>" class="vehicle-img">
                </div>
                
                <div class="p-4">
                    <h5 class="fw-bold"><?php echo $row['name']; ?></h5>
                    <p class="text-muted small"><?php echo $row['type']; ?></p>
                    <div class="price-tag mb-3">₹<?php echo $row['price_per_day']; ?><span class="text-muted fs-6 fw-normal">/day</span></div>

                    <?php if(!$is_booked) { ?>
                        <form method="POST">
                            <input type="hidden" name="vehicle_id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="price" value="<?php echo $row['price_per_day']; ?>">
                            
                            <div class="row g-2">
                                <div class="col-7">
                                    <input type="text" name="customer_name" class="form-control bg-light" placeholder="Name" required>
                                </div>
                                <div class="col-5">
                                    <input type="number" name="days" class="form-control bg-light" placeholder="Days" min="1" required>
                                </div>
                            </div>
                            
                            <button type="submit" name="book_now" class="btn btn-book w-100 mt-3">Book Now</button>
                        </form>
                    <?php } else { ?>
                        <button class="btn btn-secondary w-100 mt-3" disabled>⛔ Sold Out</button>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php } ?>
    </div>
</div>

</body>
</html>