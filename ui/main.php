<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Light Siam U - Executive Portal</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Prompt:wght@300;400;500&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* --- Reset & Base Styles --- */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Prompt', sans-serif;
            /* พื้นหลังไล่สี Dark Midnight Blue */
            background: linear-gradient(135deg, #02111b 0%, #0c263a 50%, #1c3a52 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #e6e6e6;
            overflow-x: hidden; /* ป้องกัน scroll แนวนอน */
            position: relative; /* สำหรับหิมะ */
        }

        /* --- Main Container (Glassmorphism Effect) --- */
        .luxury-dashboard {
            /* พื้นหลังกระจกฝ้า */
            background: rgba(12, 38, 58, 0.65);
            backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(212, 175, 55, 0.25); /* ขอบสีทองจางๆ */
            border-radius: 30px;
            padding: 50px 40px;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5); /* เงาลึก */
            text-align: center;
            z-index: 10; /* ให้กล่องอยู่เหนือหิมะ */
            position: relative;
        }

        /* --- Header --- */
        .header-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            /* ไล่สีตัวอักษรทอง */
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        .header-subtitle {
            color: #aab8c2;
            font-size: 1.1rem;
            margin-bottom: 50px;
            font-weight: 300;
        }
        .header-subtitle::after {
            content: ""; display: block; width: 60px; height: 3px;
            background: #d4af37; margin: 20px auto 0; border-radius: 2px;
        }

        /* --- Menu Grid System --- */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        /* --- Menu Cards --- */
        .menu-card {
            text-decoration: none;
            background: linear-gradient(145deg, rgba(255,255,255,0.05), rgba(255,255,255,0.01));
            border: 1px solid rgba(212, 175, 55, 0.1);
            padding: 30px 20px;
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            z-index: 1; 
        }
        
        /* แสงสะท้อนสีทองเมื่อ Hover */
        .menu-card::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.2), transparent);
            transition: 0.6s;
            z-index: -1;
        }
        .menu-card:hover::before { left: 100%; }

        .menu-card:hover {
            transform: translateY(-10px);
            border-color: rgba(212, 175, 55, 0.6);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3), inset 0 0 15px rgba(212, 175, 55, 0.1);
        }

        .menu-card i {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #d4af37; /* ไอคอนสีทอง */
            transition: 0.4s;
        }
        .menu-card:hover i { transform: scale(1.1); color: #fcf6ba; }

        .menu-card span {
            font-family: 'Prompt', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            color: #e0e0e0;
            letter-spacing: 0.5px;
        }

        /* --- Logout Button (แยกออกมาให้เด่น) --- */
        .logout-container { margin-top: 30px; }
        .btn-logout {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 12px 35px;
            text-decoration: none;
            color: #ff6b6b; /* สีแดงอมส้ม */
            border: 2px solid rgba(255, 107, 107, 0.3);
            border-radius: 50px;
            font-weight: 600;
            transition: 0.3s;
            background: rgba(255, 107, 107, 0.05);
        }
        .btn-logout:hover {
            background: rgba(255, 107, 107, 0.15);
            border-color: #ff6b6b;
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.2);
        }

        /* --- Christmas Snow Effect CSS --- */
        .snowflake {
            position: fixed;
            top: -50px;
            /* color: #fff;  <- ลบบรรทัดนี้ออก เพราะเราจะกำหนดสีใน JS แทน */
            opacity: 0.9; /* เพิ่มความชัดขึ้นเล็กน้อยสำหรับไอคอนสี */
            z-index: 1; /* อยู่หลัง Dashboard */
            pointer-events: none; /* เพื่อให้คลิกทะลุได้ */
            animation: fall linear forwards;
            /* text-shadow: 0 0 5px rgba(255,255,255,0.4); <- ลด shadow ลงหน่อยสำหรับไอคอนสี */
        }

        @keyframes fall {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            100% {
                transform: translateY(110vh) translateX(20px) rotate(360deg);
                opacity: 0.7;
            }
        }

        /* --- Responsive adjustments --- */
        @media (max-width: 768px) {
            .header-title { font-size: 2.2rem; }
            .luxury-dashboard { padding: 30px 20px; }
            .menu-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
            .menu-card { padding: 20px 15px; }
            .menu-card i { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <main class="luxury-dashboard">
        <header>
            <h1 class="header-title">Light Siam U</h1>
            <p class="header-subtitle">Premium Management Portal</p>
        </header>

        <nav class="menu-grid">
            <a href="../Data/dashboard.php" class="menu-card">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a href="../Data/iot_control.php" class="menu-card">
                <i class="fa-solid fa-wifi"></i>
                <span>Smart IOT Control</span>
            </a>

            <a href="../Data/reportlist.php" class="menu-card">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>ดูการแจ้งซ่อม</span>
            </a>
            
            <a href="../Data/lightlist.php" class="menu-card">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>คลังหลอดไฟ</span>
            </a>

            <a href="../Data/lightbulbs.php" class="menu-card">
                 <i class="fa-solid fa-plus-circle"></i>
                <span>เพิ่มหลอดไฟใหม่</span>
            </a>

            <a href="../Data/edit_profile.php" class="menu-card">
                <i class="fa-solid fa-user-gear"></i>
                <span>แก้ไขข้อมูลส่วนตัว</span>
            </a>
        </nav>

        <div class="logout-container">
            <a href="../Data/logout.php" class="btn-logout">
                <i class="fa-solid fa-power-off"></i> ออกจากระบบ
            </a>
        </div>
    </main>

</body>
</html>