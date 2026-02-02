<?php 
session_start();
include 'config.php';

// เชื่อมต่อฐานข้อมูล
$con = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName)
    or die("Can not connect to database");

if (!isset($_SESSION['EmployeeID'])) {
    header("Location: ../ui/login.html");
    exit;
}

// แก้ไข SQL: ใส่ Backtick ครอบชื่อตารางที่มีเว้นวรรค
$sql = "SELECT * FROM `lightbulb` ORDER BY ProductID DESC";

$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คลังหลอดไฟ (Stock)</title>
    
    <!-- Fonts: Playfair Display + Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- Base Luxury Styles --- */
        body { 
            font-family: 'Prompt', sans-serif; 
            background: linear-gradient(135deg, #02111b 0%, #0c263a 50%, #1c3a52 100%);
            min-height: 100vh;
            padding: 40px 20px;
            color: #e6e6e6;
            margin: 0;
        }
        
        /* Glassmorphism Container */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: rgba(12, 38, 58, 0.65);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(212, 175, 55, 0.25); /* ขอบทองจางๆ */
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        /* Typography */
        h2 { 
            font-family: 'Playfair Display', serif;
            text-align: center; 
            margin-bottom: 30px;
            font-size: 2.5rem;
            /* ไล่สีตัวอักษรทอง */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }

        /* --- Luxury Table --- */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .styled-table {
            border-collapse: collapse; 
            width: 100%; 
            font-size: 0.95em; 
        }
        
        .styled-table thead tr { 
            background-color: rgba(0, 0, 0, 0.3);
        }
        
        .styled-table th { 
            padding: 20px 15px; 
            color: #d4af37; /* สีทอง */
            text-align: left; 
            font-weight: 600;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            white-space: nowrap;
        }
        
        .styled-table td { 
            padding: 15px; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e0e0e0;
        }
        
        /* Hover Effect */
        .styled-table tbody tr { transition: 0.3s; }
        .styled-table tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.05); /* ไฮไลท์สีทองจางๆ */
            transform: scale(1.005);
        }

        /* --- Buttons --- */
        .btn-back { 
            display: inline-flex; align-items: center; gap: 8px;
            margin-bottom: 25px;
            padding: 10px 25px; 
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.4);
            color: #d4af37; 
            text-decoration: none; 
            border-radius: 50px; 
            transition: 0.3s;
            font-size: 14px;
        }
        .btn-back:hover { 
            background: #d4af37; 
            color: #000; 
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
        }

        /* ปุ่มลบ (Luxury Red) */
        .btn-delete {
            background: rgba(255, 71, 87, 0.1); 
            border: 1px solid rgba(255, 71, 87, 0.4);
            color: #ff6b81;
            padding: 6px 15px; 
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
            display: inline-block;
        }
        .btn-delete:hover {
            background: #ff4757;
            color: white;
            box-shadow: 0 0 10px rgba(255, 71, 87, 0.4);
        }
        
        /* Empty State */
        .empty-state {
            padding: 40px;
            text-align: center;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="../ui/main.php" class="btn-back"><i class="fas fa-arrow-left"></i> กลับหน้าหลัก</a>
        
        <h2><i class="fas fa-boxes-stacked"></i> คลังหลอดไฟ (Inventory)</h2>

        <div class="table-wrapper">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>รหัสสินค้า</th>
                        <th>ชื่อสินค้า</th>
                        <th>รุ่น (Model)</th>
                        <th>ประเภท</th>
                        <th>ราคา</th>
                        <th>วันหมดอายุ</th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td style="font-weight: bold; color: #fff;"><?php echo $row['ProductID']; ?></td> 
                            <td><?php echo $row['Name']; ?></td>
                            <td><?php echo $row['Modle']; ?></td>
                            <td><span style="color: #aaa;"><?php echo $row['Type']; ?></span></td>
                            <td style="color: #ffd700;"><?php echo number_format($row['Price']); ?> ฿</td>
                            <td><?php echo $row['Date_exp']; ?></td>
                            
                        </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='7' class='empty-state'>ยังไม่มีข้อมูลหลอดไฟในสต็อก</td></tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>

</body>
</html>