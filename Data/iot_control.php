<?php
include 'config.php';
$conn = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName);

// ดึงข้อมูลสถานะล่าสุดมาโชว์
$sql = "SELECT * FROM `iot` ORDER BY Room_No ASC"; // เช็คชื่อตารางด้วยนะครับว่า iot หรือ iot_devices
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart IoT Control</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #1e272e;
            color: white;
            padding: 30px;
            text-align: center;
        }
        h1 { color: #00d2d3; margin-bottom: 40px; }
        .iot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            justify-content: center;
            max-width: 1000px;
            margin: 0 auto;
        }
        .room-card {
            background: #2f3640;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            border: 1px solid #353b48;
            transition: 0.3s;
        }
        .room-title { font-size: 24px; font-weight: bold; margin-bottom: 15px; display: block; }
        .status-indicator {
            width: 80px; height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 30px;
            transition: 0.3s;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }
        .light-on { background-color: #f1c40f; color: #fff; box-shadow: 0 0 30px #f1c40f; }
        .light-off { background-color: #7f8c8d; color: #2c3e50; }
        .btn-group { display: flex; gap: 10px; justify-content: center; }
        .btn {
            padding: 10px 20px; border: none; border-radius: 10px;
            cursor: pointer; font-family: 'Prompt', sans-serif; font-weight: bold;
            text-decoration: none; color: white; transition: 0.2s;
        }
        .btn-on { background-color: #2ed573; }
        .btn-on:hover { background-color: #26af61; transform: scale(1.05); }
        .btn-off { background-color: #ff4757; }
        .btn-off:hover { background-color: #eb4d4b; transform: scale(1.05); }
        .sensor-text { font-size: 14px; color: #bdc3c7; margin-top: 15px; }
        .back-link { margin-top: 50px; display: inline-block; color: #aaa; text-decoration: none; }
    </style>
</head>
<body>

    <h1>📱 Smart IoT Controller (จำลอง)</h1>

    <div class="iot-grid">
        
        <?php while($row = mysqli_fetch_assoc($result)) { 
            // ----- จุดที่แก้ (1): เปลี่ยนจากเว้นวรรค เป็น _ -----
            // ถ้า Database ใช้ Status_Light ให้ใช้แบบนี้:
            $isOn = ($row['Status_Light'] == 1); 
            
            // แต่ถ้า Database ยังเป็น Status Light (มีวรรค) ให้แก้เป็น $row['Status Light']
        ?>
        <div class="room-card">
            <span class="room-title">ห้อง <?php echo $row['Room_No']; ?></span>

            <div class="status-indicator <?php echo $isOn ? 'light-on' : 'light-off'; ?>">
                <i class="fas fa-lightbulb"></i>
            </div>

            <div class="btn-group">
                <a href="iot_api.php?room=<?php echo $row['Room_No']; ?>&s_light=1&s_room=1" class="btn btn-on">เปิดไฟ</a>
                <a href="iot_api.php?room=<?php echo $row['Room_No']; ?>&s_light=0&s_room=0" class="btn btn-off">ปิดไฟ</a>
            </div>

            <p class="sensor-text">
                Sensor Value: <strong><?php echo $row['Sensor']; ?></strong> <br>
                
                Status Room: <strong><?php echo $row['Status_Room']; ?></strong>
            </p>
        </div>
        <?php } ?>

        <div class="room-card" style="border: 2px dashed #555;">
            <span class="room-title" style="color: #777;">+ เพิ่มห้องจำลอง</span>
            <div class="status-indicator light-off" style="opacity: 0.5;">
                <i class="fas fa-plus"></i>
            </div>
            <div class="btn-group">
                <a href="iot_api.php?room=101&s_light=0" class="btn" style="background:#0984e3;">Create 101</a>
                <a href="iot_api.php?room=102&s_light=0" class="btn" style="background:#0984e3;">Create 102</a>
            </div>
        </div>

    </div>

    <a href="../ui/main.php" class="back-link">⬅️ กลับหน้าหลัก Dashboard</a>

</body>
</html>