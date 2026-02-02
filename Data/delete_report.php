<?php
session_start();
include 'config.php'; 
$con = mysqli_connect(DB_Server,DB_User,DB_Password,DB_DatabaseName);

if (isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    
    // คำสั่งลบ
    $sql = "DELETE FROM report WHERE ReportID = '$delete_id'";

    if (mysqli_query($con, $sql)) {
        echo "<script>
                alert('ลบข้อมูลเรียบร้อยแล้ว');
                window.location.href='reportlist.php';
              </script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . mysqli_error($con);
    }
} else {
    header("location: reportlist.php");
}
?>