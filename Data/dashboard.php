<?php
session_start();
include '../Data/config.php';


if (!isset($_SESSION['EmployeeID'])) {
    header("Location: ../ui/login.html");
    exit;
}

// เชื่อมต่อฐานข้อมูล
$conn = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName) 
    or die("Connection failed");

// --- 1. ส่วนคำนวณตัวเลข (KPIs) ---

// 1.1 นับจำนวนการแจ้งซ่อมทั้งหมด
$sql_total = "SELECT COUNT(*) as count FROM report";
$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$count_repair = $row_total['count'];

// 1.2 นับงานที่ "เสร็จสิ้น" (ใช้คำว่า 'Completed' ตามไฟล์ job_done.php)
$sql_done = "SELECT COUNT(*) as count FROM report WHERE Status = 'Completed'";
$result_done = mysqli_query($conn, $sql_done);
$row_done = mysqli_fetch_assoc($result_done);
$count_done = $row_done['count'];

// 1.3 นับงานที่ "รอดำเนินการ" (คือสถานะไม่ใช่ Completed หรือเป็นค่าว่าง)
$sql_pending = "SELECT COUNT(*) as count FROM report WHERE Status != 'Completed' OR Status IS NULL";
$result_pending = mysqli_query($conn, $sql_pending);
$row_pending = mysqli_fetch_assoc($result_pending);
$count_pending = $row_pending['count'];

// 1.4 นับจำนวนหลอดไฟในสต็อก
// (ใช้ ` ` ครอบชื่อตารางเพื่อกัน error กรณีชื่อตารางเว้นวรรค)
$sql_stock = "SELECT COUNT(*) as count FROM `lightbulb`"; 
$result_stock = mysqli_query($conn, $sql_stock);
$count_stock = 0;
if ($result_stock) {
    $row_stock = mysqli_fetch_assoc($result_stock);
    $count_stock = $row_stock['count'];
}

// --- 2. ส่วนดึงข้อมูลสำหรับกราฟ (แยกตามชั้น) ---
$sql_chart = "SELECT Floor, COUNT(*) as count FROM report GROUP BY Floor ORDER BY Floor ASC";
$result_chart = mysqli_query($conn, $sql_chart);
$floors = [];
$counts = [];
while($row = mysqli_fetch_assoc($result_chart)){
    $floors[] = "ชั้น " . $row['Floor'];
    $counts[] = $row['count'];
}

// --- 3. ดึงรายการล่าสุด 5 รายการ (Recent Activity) ---
// หมายเหตุ: ตรวจสอบชื่อคอลัมน์ ID ในตาราง report ของคุณ (เช่น id หรือ ReportID)
// ในที่นี้ผมใช้ id ตาม code เก่า ถ้าใน DB คุณชื่อ ReportID ให้แก้ตรง ORDER BY ReportID
$sql_list = "SELECT * FROM report ORDER BY ReportID DESC LIMIT 5";
$result_list = mysqli_query($conn, $sql_list);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Dashboard</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* --- Luxury Theme Styles (Deep Blue & Gold) --- */
        body {
            font-family: 'Prompt', sans-serif;
            background: linear-gradient(135deg, #02111b 0%, #0c263a 50%, #1c3a52 100%);
            color: #e0e0e0;
            margin: 0; padding: 30px;
            min-height: 100vh;
        }

        .container { max-width: 1200px; margin: 0 auto; }

        /* Header */
        header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 40px; padding-bottom: 20px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.3);
        }
        h1 {
            font-family: 'Playfair Display', serif; 
            margin: 0; font-size: 2.5rem;
            /* Gradient Text Gold */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .btn-back {
            text-decoration: none; color: #d4af37; 
            background: rgba(255,255,255,0.05);
            padding: 10px 25px; border-radius: 30px; border: 1px solid #d4af37;
            transition: 0.3s; font-weight: 500;
        }
        .btn-back:hover { background: #d4af37; color: #000; box-shadow: 0 0 15px rgba(212, 175, 55, 0.4); }

        /* Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr; /* กราฟกว้าง 2 ส่วน, การ์ดกว้าง 1 ส่วน */
            gap: 25px;
            margin-bottom: 40px;
        }
        @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } }

        /* Stats Cards Area */
        .stats-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .stat-card {
            background: rgba(12, 38, 58, 0.65);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            padding: 20px; border-radius: 20px;
            display: flex; flex-direction: column; justify-content: space-between;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            transition: transform 0.3s;
            height: 120px;
        }
        .stat-card:hover { transform: translateY(-5px); border-color: #d4af37; }
        
        .stat-header { display: flex; justify-content: space-between; align-items: start; }
        .stat-icon { font-size: 24px; color: #d4af37; opacity: 0.8; }
        .stat-number { font-size: 36px; font-weight: 600; color: #fff; margin-top: 10px; }
        .stat-label { font-size: 14px; color: #8fa3bf; }

        /* Chart Section */
        .chart-container {
            background: rgba(12, 38, 58, 0.65);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        /* Table Section */
        .table-section h3 { color: #d4af37; margin-bottom: 20px; border-left: 4px solid #d4af37; padding-left: 10px; }
        
        .luxury-table { 
            width: 100%; border-collapse: collapse; 
            background: rgba(0,0,0,0.2); border-radius: 15px; overflow: hidden;
        }
        .luxury-table th {
            background: rgba(0, 0, 0, 0.4); color: #d4af37;
            padding: 15px; text-align: left; font-weight: 600;
        }
        .luxury-table td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #eee; }
        .luxury-table tr:hover { background: rgba(212, 175, 55, 0.05); }

        /* Badge Status */
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-done { background: rgba(46, 204, 113, 0.15); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.4); }
        .badge-pending { background: rgba(255, 71, 87, 0.15); color: #ff6b81; border: 1px solid rgba(255, 71, 87, 0.4); }

    </style>
</head>
<body>

<div class="container">
    <header>
        <div>
            <h1>Executive Dashboard</h1>
            <span style="color: #8fa3bf;">ภาพรวมสถานะระบบ (Real-time Data)</span>
        </div>
        <a href="../ui/main.php" class="btn-back"><i class="fas fa-arrow-left"></i> กลับหน้ารายการ</a>
    </header>

    <div class="dashboard-grid">
        
        <div class="chart-container">
            <h3 style="margin-top:0; color:#d4af37; font-size:16px;">สถิติการแจ้งซ่อมแยกตามชั้น</h3>
            <canvas id="repairChart" style="max-height: 250px;"></canvas>
        </div>

        <div class="stats-column">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">แจ้งซ่อมทั้งหมด</span>
                    <i class="fas fa-clipboard-list stat-icon"></i>
                </div>
                <div class="stat-number"><?php echo $count_repair; ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">รอดำเนินการ</span>
                    <i class="fas fa-clock stat-icon" style="color:#ff6b81;"></i>
                </div>
                <div class="stat-number" style="color:#ff6b81;"><?php echo $count_pending; ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">ซ่อมเสร็จสิ้น</span>
                    <i class="fas fa-check-circle stat-icon" style="color:#2ecc71;"></i>
                </div>
                <div class="stat-number" style="color:#2ecc71;"><?php echo $count_done; ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">สต็อกหลอดไฟ</span>
                    <i class="fas fa-lightbulb stat-icon" style="color:#70a1ff;"></i>
                </div>
                <div class="stat-number" style="color:#70a1ff;"><?php echo $count_stock; ?></div>
            </div>
        </div>

    </div>

    <div class="table-section">
        <h3><i class="fas fa-history"></i> รายการเคลื่อนไหวล่าสุด (5 รายการ)</h3>
        <table class="luxury-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ห้อง</th>
                    <th>อาการเสีย</th>
                    <th>ผู้แจ้ง</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (mysqli_num_rows($result_list) > 0) {
                    while($row = mysqli_fetch_assoc($result_list)) { 
                        
                        // เช็คสถานะ
                        $status_db = $row['Status'];
                        $status_show = "รอดำเนินการ";
                        $badge_class = "badge-pending";

                        if($status_db == 'Completed') {
                            $status_show = "เสร็จสิ้น";
                            $badge_class = "badge-done";
                        }
                ?>
                    <tr>
                        <td>#<?php echo isset($row['id']) ? $row['id'] : $row['ReportID']; ?></td>
                        <td style="font-weight: bold; color: #fff;"><?php echo $row['Room_No']; ?></td>
                        <td><?php echo $row['Cause']; ?></td>
                        <td><?php echo $row['StudentID']; ?></td>
                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status_show; ?></span></td>
                    </tr>
                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding:30px;'>ยังไม่มีข้อมูลการแจ้งซ่อม</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

<script>
    const ctx = document.getElementById('repairChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar', // กราฟแท่ง
        data: {
            labels: <?php echo json_encode($floors); ?>,
            datasets: [{
                label: 'จำนวนงานซ่อม',
                data: <?php echo json_encode($counts); ?>,
                backgroundColor: 'rgba(212, 175, 55, 0.5)',
                borderColor: '#d4af37',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#aaa' } },
                x: { grid: { display: false }, ticks: { color: '#aaa' } }
            },
            plugins: { legend: { labels: { color: '#e0e0e0' } } }
        }
    });
</script>

</body>
</html>