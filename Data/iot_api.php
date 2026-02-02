<?php
    include 'config.php'; 
    // เชื่อมต่อฐานข้อมูล
    $conn = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName);

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // รับค่า room จาก URL
    if (isset($_GET['room'])) {
        
        $room = $_GET['room'];
        
        // รับค่าสถานะ (ใช้ Ternary Operator: ถ้ามีค่าให้ใช้ค่านั้น ถ้าไม่มีให้เป็น 0 หรือ 2 ตาม Default)
        $status_room = isset($_GET['s_room']) ? $_GET['s_room'] : 0;
        $status_light = isset($_GET['s_light']) ? $_GET['s_light'] : 0;
        $sensor_val = isset($_GET['sensor']) ? $_GET['sensor'] : 2;

        // คำสั่ง SQL ที่ถูกต้อง (ตรงกับชื่อใน Database ของคุณ)
        $sql = "INSERT INTO `iot` (`Room_No`, `Status_Room`, `Status_Light`, `Sensor`) 
                VALUES ('$room', '$status_room', '$status_light', '$sensor_val')
                ON DUPLICATE KEY UPDATE 
                `Status_Room` = '$status_room',
                `Status_Light` = '$status_light',
                `Sensor` = '$sensor_val'";

        // รันคำสั่ง SQL
        if (mysqli_query($conn, $sql)) {
            // ถ้าสำเร็จ ให้กระโดดไปหน้า iot_control.php
            header("location: iot_control.php");
        } else {
            // ถ้ามี Error ให้แสดงออกมา
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Error: No room specified.";
    }
?>