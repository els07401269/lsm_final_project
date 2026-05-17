<?php
//session_start();

/*PROTECTION: redirect if not logged in */
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

/*GET LOGGED IN USER DATA */
$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }
        /*top nav bar*/
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2c3e50;
            padding: 10px 20px;
            color: white;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            
        }

        .logo {
            width: 80px;
            border-radius: 50%;
        }

        .logo-text {
            font-size: 18px;
            font-weight: bold;
        }

        /* SETTINGS BUTTON */
        .settings-btn {
            padding: 6px 12px;
            cursor: pointer;
            border: none;
            background-color: #34495e;
            color: white;
            border-radius: 5px;
        }

        .settings-btn:hover {
            background-color: #1abc9c;
        }

        .right {
            position: relative;
        }

        /* DROPDOWN MENU */
        .menu {
            display: none;
            position: absolute;
            right: 0;
            top: 35px;
            background: white;
            border-radius: 5px;
            width: 130px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
        }

        .menu a:hover {
            background-color: #ecf0f1;
        }

        .nav-bar {
            display: flex;
            background-color: #34495e;
        }

        .nav-bar a {
            padding: 14px 20px;
            text-decoration: none;
            color: white;
        }

        .nav-bar a:hover {
            background-color: #1abc9c;
        }

        .main {
            padding: 20px;
        }

        /* CARD DESIGN */
        .card {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        button {
            padding: 8px 15px;
            border: none;
            background-color: #1abc9c;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #16a085;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
        }

        td {
            padding: 10px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .welcome-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .welcome-text {
            font-size: 45px;
            font-weight: bold;
            color: #2c3e50;
            animation: fadeIn 1s ease-in-out;
        }

        .sub-text {
            font-size: 16px;
            color: #7f8c8d;
        }

        /* ANIMATION */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
</head>

<body>

<div class="top-bar">

    <div class="logo-container">
        <img src="../../assets/images/logo.png" class="logo">
        <span class="logo-text">JOSH JELO ELSA UNIVERSITY</span>
    </div>

    <div class="right">
        <button onclick="toggleMenu()" class="settings-btn">⚙ Settings</button>

        <div id="menu" class="menu">
            <a href="../logout.php">Logout</a>
        </div>
    </div>

</div>

<div class="nav-bar">
    <a href="#">Dashboard</a>
    <a href="#">Curriculum</a>
    <a href="#">Assignments</a>
    <a href="#">Forum</a>
    <a href="#">Statistics</a>
    <a href="#">Classes</a>
    <a href="#">Students</a>
</div>

<div class="main">

    <div class="welcome-box">
        <h1 class="welcome-text">
            Welcome, <?= htmlspecialchars($user['fname']) ?>!👋
        </h1>
        <p class="sub-text">JOSH JELO ELSA UNIVERSITY CLASSROOM</p>
    </div>

    <div class="card">
        <h2>Student Dashboard</h2>
        <p>Manage your school activities below.</p>
    </div>

    <div class="card">
        <h3>My Activities</h3>
        <p><b>No Activity Created</b></p>
        <button>Create Activity</button>
    </div>

    <div class="card">
        <h3>My Classes</h3>

        <table>
            <tr>
                <th>Class</th>
                <th>Students</th>
            </tr>
        </table>

    </div>

</div>

<script>
function toggleMenu() {
    var menu = document.getElementById("menu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}
</script>

</body>
</html>