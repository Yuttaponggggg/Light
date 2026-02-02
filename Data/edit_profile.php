<?php
session_start();
require_once 'login_connect.php'; // หรือไฟล์ config.php ของคุณ

// 1. ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['EmployeeID'])) {
    header("Location: ../ui/login.html");
    exit;
}

$employeeID = $_SESSION['EmployeeID'];
$message = ""; 
$msg_type = ""; // เพิ่มตัวแปรเช็คสีข้อความ (เขียว/แดง)

// เช็คตัวแปรเชื่อมต่อ (กันเหนียว)
// ถ้าในไฟล์ connect ตั้งชื่อว่า $con ให้แก้บรรทัดนี้เป็น $conn = $con;
if (!isset($conn) && isset($con)) { $conn = $con; }


// ---------------------------------------------------------
// 2.  ส่วนประมวลผล (UPDATE) 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // รับข้อมูลใหม่
    $newName = $_POST['Name'];
    $newPhone = $_POST['PhoneNo'];
    $newHours = $_POST['WorkHours'];

    // เตรียมคำสั่ง UPDATE (ใช้ ? แค่ 4 ตัวพอ: Name, Phone, Hours, ID)
    $sql = "UPDATE electrician SET Name = ?, PhoneNo = ?, WorkHours = ? WHERE EmployeeID = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // ssss = string 4 ตัว
        $stmt->bind_param("ssss", $newName, $newPhone, $newHours, $employeeID);

        if ($stmt->execute()) {
            $message = "บันทึกข้อมูลสำเร็จ!";
            $msg_type = "success";
        } else {
            $message = "เกิดข้อผิดพลาด: " . $stmt->error;
            $msg_type = "error";
        }
        $stmt->close();
    } else {
        $message = "Database Error: " . $conn->error;
    }
}

// ---------------------------------------------------------
// 3. ส่วนดึงข้อมูล (SELECT) เอามาโชว์ในฟอร์ม
$sql_get = "SELECT Name, PhoneNo, WorkHours, Role FROM electrician WHERE EmployeeID = ?";
if ($stmt = $conn->prepare($sql_get)) {
    $stmt->bind_param("s", $employeeID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

if (!$user) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลส่วนตัว</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            width: 100%;
            max-width: 450px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="text"] {
            width: 100%; padding: 10px;
            border: 1px solid #ddd; border-radius: 8px;
            box-sizing: border-box; font-family: 'Prompt', sans-serif;
        }
        input[type="text"]:read-only { background-color: #e9ecef; color: #666; } /* ช่องที่ห้ามแก้ */

        button {
            width: 100%; padding: 12px;
            background-color: #ffc107; color: #000; /* สีเหลือง */
            border: none; border-radius: 8px;
            font-size: 16px; font-family: 'Prompt', sans-serif;
            cursor: pointer; margin-top: 10px;
        }
        button:hover { background-color: #e0a800; }

        .alert { padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { text-decoration: none; color: #666; }
    </style>
</head>
<body>

    <div class="container">
        <h2>✏️ แก้ไขข้อมูลส่วนตัว</h2>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $msg_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            
            <div class="form-group">
                <label>รหัสพนักงาน:</label>
                <input type="text" value="<?php echo htmlspecialchars($employeeID); ?>" readonly>
            </div>

            <div class="form-group">
                <label>ตำแหน่ง:</label>
                <input type="text" value="<?php echo htmlspecialchars($user['Role']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="Name">ชื่อ-นามสกุล:</label>
                <input type="text" id="Name" name="Name" required
                       value="<?php echo htmlspecialchars($user['Name']); ?>">
            </div>

            <div class="form-group">
                <label for="PhoneNo">เบอร์โทรศัพท์:</label>
                <input type="text" id="PhoneNo" name="PhoneNo" required
                       value="<?php echo htmlspecialchars($user['PhoneNo']); ?>">
            </div>

            <div class="form-group">
                <label for="hours">เวลาทำงาน:</label>
                <input type="text" id="hours" name="WorkHours" 
                       value="<?php echo htmlspecialchars($user['WorkHours']); ?>">
            </div>

            <button type="submit">บันทึกการเปลี่ยนแปลง</button>
        </form>

        <div class="back-link">
            <a href="../ui/main.php">⬅️ กลับไปหน้าหลัก</a>
        </div>
    </div>

</body>
</html>