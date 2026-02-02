<?php 
session_start();
include '../Data/config.php'; 


if (!isset($_SESSION['EmployeeID'])) {
    header("Location: ../ui/login.html");
    exit;
}

// เชื่อมต่อฐานข้อมูล
$con = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName)
    or die("Can not connect to database");

// ดึงข้อมูลทั้งหมด (เรียงจากใหม่ไปเก่า)
$sql = "SELECT * FROM report ORDER BY ReportID DESC"; 
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการแจ้งซ่อม (Report List)</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(12, 38, 58, 0.65);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        h2 { 
            font-family: 'Playfair Display', serif;
            text-align: center; 
            margin-bottom: 30px;
            font-size: 2.5rem;
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }

        /* --- Table --- */
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
            color: #d4af37;
            text-align: left; 
            font-weight: 600;
            border-bottom: 2px solid rgba(212, 175, 55, 0.3);
            white-space: nowrap;
        }
        
        .styled-table td { 
            padding: 15px; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e0e0e0;
            vertical-align: middle;
        }
        
        .styled-table tbody tr { transition: 0.3s; }
        .styled-table tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.05);
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

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }

        .btn-complete {
            background: rgba(46, 204, 113, 0.15);
            border: 1px solid rgba(46, 204, 113, 0.5);
            color: #2ecc71;
        }
        .btn-complete:hover {
            background: #27ae60;
            color: white;
            box-shadow: 0 0 10px rgba(46, 204, 113, 0.4);
            transform: translateY(-2px);
        }

        .btn-delete {
            background: rgba(255, 71, 87, 0.1); 
            border: 1px solid rgba(255, 71, 87, 0.4);
            color: #ff6b81;
        }
        .btn-delete:hover {
            background: #ff4757;
            color: white;
            box-shadow: 0 0 10px rgba(255, 71, 87, 0.4);
            transform: translateY(-2px);
        }
        
        /* สไตล์สำหรับข้อความเมื่อเสร็จแล้ว */
        .status-done {
            color: #2ecc71;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(46, 204, 113, 0.1);
            padding: 5px 10px;
            border-radius: 20px;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

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
        
        <h2><i class="fas fa-clipboard-list"></i> รายการแจ้งซ่อม (Report List)</h2>

        <div class="table-wrapper">
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>รหัสนักศึกษา</th>
                        <th>ห้องที่แจ้ง</th>
                        <th>ชั้น</th>
                        <th>สาเหตุ / อาการ</th>
                        <th style="text-align: center;">จัดการ</th> 
                    </tr>
                </thead>
                <tbody>
                    
                    <?php
                    // เริ่ม Loop วนข้อมูล
                    if (mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td style="font-weight: bold; color: #fff;"><?php echo $row['StudentID']; ?></td> 
                            <td><?php echo $row['Room_No']; ?></td>
                            <td><?php echo $row['Floor']; ?></td>
                            <td style="color: #ffd700;"><?php echo $row['Cause']; ?></td>
                            
                            <td style="text-align: center;">
                                <?php 
                                // เช็คสถานะ: ถ้า "ไม่ใช่" Completed ให้โชว์ปุ่ม
                                if ($row['Status'] != 'Completed') { 
                                ?>
                                    <div class="action-buttons">
                                        <a href="job_done.php?id=<?php echo $row['ReportID']; ?>" 
                                           class="btn-action btn-complete"
                                           onclick="return confirm('ยืนยันว่าการซ่อมห้อง <?php echo $row['Room_No']; ?> เสร็จสิ้นแล้ว?');">
                                           <i class="fas fa-check-circle"></i> เสร็จสิ้น
                                        </a>

                                        <a href="delete_report.php?id=<?php echo $row['ReportID']; ?>" 
                                           class="btn-action btn-delete"
                                           onclick="return confirm('⚠️ ยืนยันการลบข้อมูล?');">
                                           <i class="fas fa-trash-alt"></i> ลบ
                                        </a>
                                    </div>
                                <?php 
                                } else { 
                                    // ถ้าสถานะเป็น Completed แล้ว ให้โชว์ข้อความนี้
                                ?>
                                    <span class="status-done">
                                        <i class="fas fa-check-double"></i> ดำเนินการแล้ว
                                    </span>
                                <?php 
                                } 
                                ?>
                            </td>
                        </tr>
                    <?php 
                        } // จบ while loop
                    } else {
                        echo "<tr><td colspan='5' class='empty-state'>ไม่มีรายการแจ้งซ่อมในขณะนี้</td></tr>";
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>

</body>
</html>