<?php
// เชื่อมต่อฐานข้อมูล
include 'config.php';
$con = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName) 
    or die("Can not connect to database");


// เช็คว่ามีการกดปุ่มที่มี name="btrepot" หรือไม่
if (isset($_POST['btrepot'])) {
    
    // รับค่าจากฟอร์ม
    $ProductID = $_POST['ProductID'];
    $Date_exp = $_POST['Date_exp'];
    $Modle = $_POST['Modle'];
    $Type = $_POST['Type'];
    $Name = $_POST['Name'];
    $Price = $_POST["Price"];

    // เขียนคำสั่ง SQL (แก้ไข Syntax ให้ถูกต้องแล้ว)
    // ใส่ Backtick ` ` ครอบชื่อตารางและชื่อคอลัมน์ที่มีเว้นวรรค
    $sql = "INSERT INTO `lightbulb` SET 
            ProductID = '$ProductID',
            `Date_exp` = '$Date_exp',   
            Modle = '$Modle',           
            Type = '$Type',             
            Name = '$Name',             
            Price = '$Price'";

    // รันคำสั่ง SQL
    $result = mysqli_query($con, $sql) or die(mysqli_error($con) . " Data can not insert.");

    if ($result) {
        echo "<script>
                alert('เพิ่มข้อมูลหลอดไฟสำเร็จ!');
                window.location.href='../Data/lightlist.php'; // เด้งไปหน้าสต็อก
              </script>";
    } 
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มหลอดไฟใหม่ (Add Stock)</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- Base Luxury Styles --- */
        body { 
            font-family: 'Prompt', sans-serif; 
            /* พื้นหลัง Midnight Gold Gradient */
            background: linear-gradient(135deg, #02111b 0%, #0c263a 50%, #1c3a52 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #e0e0e0;
            margin: 0;
        }

        /* --- Glassmorphism Card --- */
        .container {
            background: rgba(12, 38, 58, 0.65); /* สีพื้นโปร่งแสง */
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(212, 175, 55, 0.25); /* ขอบทอง */
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        /* เส้นแสงทองพาดด้านบน */
        .container::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
        }

        /* --- Typography --- */
        h2 { 
            text-align: center; 
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 30px;
            /* ไล่สีตัวอักษรทอง */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }

        /* --- Form Elements --- */
        .form-group { margin-bottom: 20px; }
        
        label { 
            display: block; margin-bottom: 8px; 
            color: #d4af37; /* สีทอง */
            font-weight: 500; font-size: 0.95rem;
        }
        
        .input-wrapper { position: relative; }
        
        .input-wrapper i {
            position: absolute; left: 15px; top: 50%; transform: translateY(-50%);
            color: #6c7a89; transition: 0.3s;
        }

        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 12px 15px 12px 45px; /* เว้นที่ให้ไอคอน */
            background: rgba(0, 0, 0, 0.3); /* พื้นหลังเข้มโปร่งแสง */
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-sizing: border-box;
            font-family: 'Prompt', sans-serif;
            font-size: 1rem;
            color: #fff;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.15);
        }
        
        input:focus + i { color: #d4af37; } /* ไอคอนเปลี่ยนสีเมื่อพิมพ์ */

        ::placeholder { color: #555; font-weight: 300; }

        /* --- Buttons --- */
        button {
            width: 100%;
            padding: 14px;
            /* สีทองไล่เฉด */
            background: linear-gradient(45deg, #d4af37, #c5a028);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            font-family: 'Prompt', sans-serif;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }
        button:hover { 
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }

        .back-link { 
            text-align: center; margin-top: 25px; display: block; 
            color: #888; text-decoration: none; transition: 0.3s;
        }
        .back-link:hover { color: #d4af37; text-decoration: underline; }

    </style>
</head>
<body>

    <div class="container">
        <h2><i class="fas fa-plus-circle"></i> เพิ่มหลอดไฟใหม่</h2>
        
        <form action="" method="POST">

            <div class="form-group">
                <label>รหัสสินค้า (Product ID)</label>
                <div class="input-wrapper">
                    <input type="text" name="ProductID" required placeholder="เช่น L-001">
                    <i class="fas fa-barcode"></i>
                </div>
            </div>

            <div class="form-group">
                <label>ชื่อสินค้า (Name)</label>
                <div class="input-wrapper">
                    <input type="text" name="Name" required placeholder="เช่น หลอด LED 9W">
                    <i class="fas fa-lightbulb"></i>
                </div>
            </div>

            <div class="form-group">
                <label>รุ่น (Model)</label>
                <div class="input-wrapper">
                    <input type="text" name="Modle" required placeholder="เช่น Gen 2">
                    <i class="fas fa-tag"></i>
                </div>
            </div>

            <div class="form-group">
                <label>ประเภท (Type)</label>
                <div class="input-wrapper">
                    <input type="text" name="Type" required placeholder="เช่น Warm White">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>

            <div class="form-group">
                <label>วันหมดอายุ (Expiry Date)</label>
                <div class="input-wrapper">
                    <input type="date" name="Date_exp" required>
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>

            <div class="form-group">
                <label>ราคา (Price)</label>
                <div class="input-wrapper">
                    <input type="number" name="Price" required placeholder="ระบุราคา (บาท)">
                    <i class="fas fa-coins"></i>
                </div>
            </div>

            <button type="submit" name="btrepot">
                <i class="fas fa-save"></i> บันทึกข้อมูล
            </button>

        </form>
        
        <a href="../ui/main.php" class="back-link">
            <i class="fas fa-arrow-left"></i> กลับไปหน้าหลัก
        </a>
    </div>

</body>
</html>