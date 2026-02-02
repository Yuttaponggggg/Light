<?php
session_start();

include "config.php";
$conn = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName)
    or die("Can not connect to database");



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $employeeID = $_POST['EmployeeID'];
    $password = $_POST['Password']; // <-- รหัสธรรมดาที่ผู้ใช้กรอก

    // เราจะค้นหาทั้ง 2 อย่างใน SQL เลย
    // นี่คือวิธีที่ไม่ปลอดภัย (เสี่ยงต่อ SQL Injection ถ้าไม่ใช้ bind_param)
    
    // 1. เตรียมคำสั่ง (ค้นหาทั้ง ID และ Password ที่ตรงกัน)
    $stmt = $conn->prepare("SELECT * FROM electrician WHERE EmployeeID = ? AND Password = ? LIMIT 1");
    
    // 2. "ผูก" ค่า 2 ตัว (s = string, s = string)
    $stmt->bind_param("ss", $employeeID, $password);

    // 3. สั่งทำงาน
    $stmt->execute();

    // 4. ดึงผลลัพธ์
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); 

    // ถ้า $user มีค่า (แปลว่าหาเจอ) = ล็อกอินผ่าน
    if ($user){
    
        $_SESSION['EmployeeID'] = $user['EmployeeID'];
        $_SESSION['UserLevel'] = $user['Role'];
        header("location:../ui/main.php");
        exit;

    } else {
        echo "<script>
            alert('รหัสพนักงานหรือรหัสผ่านไม่ถูกต้อง');
            window.location.href='../ui/login.html';
        </script>";
    }

    $stmt->close();

} 
    exit;

$conn->close();
?>