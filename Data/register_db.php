<?php
include "../Data/config.php"; //เอาไฟล์ configเข้ามา


$con = mysqli_connect(DB_Server,DB_User,DB_Password,DB_DatabaseName)
    or die("Can not connect to database, please check the parameter");
if(isset($_REQUEST['btsave'])){
if($con){
// ... (ส่วนรับค่าจากฟอร์ม) ...
$EmployeeID = $_POST['EmployeeID'];
$Password = $_POST['Password'];
$Name = $_POST['Name'];
$PhoneNo = $_POST['PhoneNo'];
$WorkHours = $_POST['WorkHours'];

// --- แก้ไขตรงนี้ ---
// เพิ่ม Role = 'admin' เข้าไปในคำสั่ง INSERT เลย
$sql = "INSERT INTO electrician SET 
        EmployeeID = '$EmployeeID',
        Password = '$Password',
        Name = '$Name',
        PhoneNo = '$PhoneNo',
        WorkHours = '$WorkHours',
        Role = 'admin'";

$result = mysqli_query($con,$sql) or die(mysqli_connect_errno()."Data can not insert.");
  if ($result) {
        echo "<script>
                alert('ลงทะเบียนสำเร็จ');
                window.location.href='../ui/login.html';
              </script>";
    }
}
}
?>