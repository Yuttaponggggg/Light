<?php
// 1. ต้องเริ่ม Session เสมอ (แม้กระทั่งตอนจะทำลาย)
// เพื่อให้ PHP รู้ว่าเราจะจัดการ Session ไหน
session_start();

// 2. ลบตัวแปรทั้งหมดใน Session
// (ลบข้อมูล EmployeeID ที่เราเก็บไว้)
session_unset();

// 3. ทำลาย Session (ฉีกตั๋วทิ้ง)
session_destroy();

// 4. ส่งผู้ใช้กลับไปหน้า login.php
// (ถ้าหน้าล็อกอินของคุณคือ login.html ก็แก้ตรงนี้ครับ)
header("Location:../ui/login.html");
exit; // จบการทำงานทันที ป้องกันโค้ดอื่นทำงานต่อ

?>