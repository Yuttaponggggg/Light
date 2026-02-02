<?php
// ไฟล์ job_done.php
session_start();
include '../Data/config.php'; 

$con = mysqli_connect(DB_Server, DB_User, DB_Password, DB_DatabaseName) 
    or die("Connection failed");

// รับค่า ID ที่ส่งมาจากปุ่ม
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // อัปเดตสถานะเป็น 'Completed' (เสร็จสิ้น)
    // *** สำคัญ: ต้องเปลี่ยนชื่อ id ให้ตรงกับ Primary Key ในตาราง report ของคุณ (เช่น id หรือ ReportID) ***
    $sql = "UPDATE report SET Status = 'Completed' WHERE ReportID = '$id'"; 
    
    $result = mysqli_query($con, $sql);

    if($result){
        echo "<script>
            alert('บันทึกงานเสร็จสิ้นเรียบร้อย');
            window.location.href='reportlist.php';
        </script>";
    } else {
        echo "<script>
            alert('เกิดข้อผิดพลาด');
            window.history.back();
        </script>";
    }
}
?>