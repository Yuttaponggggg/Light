<?php
// เชื่อมต่อฐานข้อมูล
include '../Data/config.php';
$con = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName) 
    or die("Can not connect to database");

// เช็คว่ามีการกดปุ่มที่มี name="btrepot" หรือไม่
if (isset($_POST['btrepot'])) {
    
    // รับค่าจากฟอร์ม
    $studentID = $_POST['StudentID'];
    $roomNo = $_POST['Room_No'];
    $floor = $_POST['Floor'];
    $cause = $_POST['Cause'];

    // เขียนคำสั่ง SQL
    $sql = "INSERT INTO report SET 
            StudentID = '$studentID',
            Room_No = '$roomNo', 
            Floor = '$floor',
            Cause = '$cause'";

    // รันคำสั่ง SQL
    $result = mysqli_query($con, $sql) or die(mysqli_error($con) . " Data can not insert.");

    if ($result) {
        echo "<script>
                alert('รายงานสำเร็จ');
                window.location.href='../Data/report.php';
              </script>";
    } 
    
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อมไฟฟ้า | Light Siam U</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        /* --- Luxury Dark Blue & Gold Theme --- */
        
        body {
            font-family: 'Prompt', sans-serif;
            /* สีพื้นหลังน้ำเงินเข้มเกือบดำ */
            background-color: #051024; 
            /* ไล่เฉดสีพื้นหลังเล็กน้อยให้ดูมีมิติ */
            background-image: linear-gradient(to bottom, #051024, #020612);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #e0e0e0;
        }

        /* กล่องคอนเทนเนอร์ */
        .container {
            /* สีพื้นหลังกล่อง (น้ำเงินอมเทาเข้ม) */
            background-color: #0b1d36; 
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 12px;
            /* เงาฟุ้งและขอบบางๆ */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6); 
            border: 1px solid #1c3a63; 
            margin: 20px;
            position: relative;
        }

        /* หัวข้อ */
        h2 {
            text-align: center;
            /* สีทอง */
            color: #d4af37; 
            margin-bottom: 10px;
            font-family: 'Cinzel', serif; /* ฟอนต์หรู */
            font-weight: 700;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .subtitle {
            text-align: center;
            color: #8fa3bf;
            font-size: 14px;
            margin-bottom: 30px;
            font-weight: 300;
        }

        /* จัดการฟอร์ม */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            /* สีทองสำหรับ Label */
            color: #d4af37; 
            font-size: 14px;
            font-weight: 500;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            /* สีพื้นหลังช่องกรอก (น้ำเงินเข้มมาก) */
            background-color: #061121; 
            border: 1px solid #1f3655;
            border-radius: 6px;
            color: #ffffff;
            font-family: 'Prompt', sans-serif;
            font-size: 15px;
            box-sizing: border-box;
            transition: border 0.3s;
        }

        input[type="text"]::placeholder {
            color: #4a6fa5;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #d4af37; /* เปลี่ยนขอบเป็นสีทองเมื่อกด */
            box-shadow: 0 0 5px rgba(212, 175, 55, 0.3);
        }

        /* ปุ่มกด (Gradient Gold) */
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 25px; /* ปุ่มมนโค้งเหมือนในรูป */
            
            /* ไล่เฉดสีทอง */
            background: linear-gradient(180deg, #fcd34d 0%, #d99f0b 100%);
            
            /* สีตัวอักษรบนปุ่ม (สีเข้ม) */
            color: #3e2803; 
            font-size: 16px;
            font-weight: 600;
            font-family: 'Prompt', sans-serif;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(217, 159, 11, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(217, 159, 11, 0.4);
            background: linear-gradient(180deg, #ffe066 0%, #e6ac0c 100%);
        }

        button:active {
            transform: translateY(0);
        }

        /* ลิงก์ด้านล่าง */
        .footer-link {
            text-align: center;
            margin-top: 25px;
            font-size: 13px;
        }
        
        .footer-link a {
            color: #7da0ce;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color 0.2s;
        }

        .footer-link a:hover {
            color: #ffffff;
        }

        /* เส้นขีดตกแต่ง */
        hr {
            border: 0;
            height: 1px;
            background: #1c3a63;
            margin: 25px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Light Siam U</h2>
        <div class="subtitle">ระบบแจ้งซ่อมไฟฟ้า (Maintenance Request)</div>

        <form action="" method="POST">
            
            <div class="form-group">
                <label for="StudentID">รหัสนักศึกษา (Student ID)</label>
                <input type="text" id="StudentID" name="StudentID" placeholder="ระบุรหัสของคุณ" required>
            </div>

            <div class="form-group">
                <label for="Room_No">ห้องที่พบปัญหา (Room No.)</label>
                <input type="text" id="Room_No" name="Room_No" placeholder="เช่น 402, ห้องน้ำชาย" required>
            </div>

            <div class="form-group">
                <label for="Floor">ชั้น (Floor)</label>
                <input type="text" id="Floor" name="Floor" placeholder="ระบุชั้น" required>
            </div>

            <div class="form-group">
                <label for="Cause">สาเหตุ / อาการ (Issue)</label>
                <input type="text" id="Cause" name="Cause" placeholder="ระบุอาการเสีย" required 
                oninput="this.value = this.value.replace(/[^a-zA-Zก-๙]/g, '')"
                oninput="this.value = this.value.replace(/[^a-zA-Z]/g, '')">
            </div>

            <button type="submit" name="btrepot" id='btrepot'>บันทึกรายงาน</button>
        </form>

        <div class="footer-link">
             <a href="../ui/login.html">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                สำหรับเจ้าหน้าที่ (Staff Login)
             </a>
        </div>
    </div>

</body>
</html>