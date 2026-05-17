<?php
session_start();

require_once "../../config/Database.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

$conn = Database::getInstance()->getConnection();

/*
|--------------------------------------------------------------------------
| GET ALL CLASSES OF PROFESSOR
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT *
    FROM classes
    WHERE professor_id = ?
    ORDER BY id DESC
");

$stmt->execute([$user['id']]);

$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<title>My Classes</title>

<style>

/* =========================
   BASE DESIGN (UNCHANGED)
========================= */

body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #153c6b, #7d784a);
}

.topbar{
    background:#2c3e50;
    color:white;
    padding:15px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{ height:55px; }

.nav a{
    color:white;
    text-decoration:none;
    margin-right:15px;
    font-weight:bold;
}

.container{ padding:40px; }

.card{
    width:100%;
    max-width:1100px;
    margin:auto;
}

.classes-grid{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:20px;
}

.class-box{
    background:white;
    border:1px solid #ddd;
    border-radius:10px;
    padding:20px;
    position:relative;
    box-shadow:0 3px 8px rgba(0,0,0,0.08);
}

.class-title{
    font-size:18px;
    font-weight:bold;
    color:#2c3e50;
}

.class-info{
    margin-top:5px;
    font-size:14px;
    color:#555;
}

.class-code{
    margin-top:5px;
    font-size:13px;
    color:gray;
}

.view-btn{
    display:inline-block;
    margin-top:12px;
    padding:8px 14px;
    background:#1abc9c;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

/* MENU */
.menu-wrapper{
    position:absolute;
    top:10px;
    right:10px;
}

.menu-btn{
    font-size:22px;
    cursor:pointer;
}

.dropdown-menu{
    display:none;
    position:absolute;
    right:0;
    top:25px;
    background:white;
    border:1px solid #ddd;
    border-radius:6px;
    min-width:140px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    z-index:999;
}

.dropdown-menu a{
    display:block;
    padding:10px;
    text-decoration:none;
    color:#333;
}

.dropdown-menu a:hover{
    background:#f2f2f2;
}

/* RESPONSIVE */
@media (max-width:900px){
    .classes-grid{ grid-template-columns:repeat(2, 1fr); }
}

@media (max-width:600px){
    .classes-grid{ grid-template-columns:1fr; }
}

</style>

</head>

<body>

<div class="topbar">

    <div style="display:flex; align-items:center; gap:15px;">

        <img src="../../assets/images/logo.png" class="logo">

        <div class="nav">

            <a href="dashboard.php">Home</a>
            <a href="create_class.php">Create Class</a>
            <a href="my_classes.php">My Classes</a>

        </div>

    </div>

</div>

<div class="container">

    <div class="card">

        <h2>My Classes</h2>

        <?php if (!empty($classes)): ?>

            <div class="classes-grid">

                <?php foreach ($classes as $class): ?>

                    <div class="class-box">

                        <!-- MENU -->
                        <div class="menu-wrapper">

                            <div class="menu-btn"
                                 onclick="toggleMenu(<?= $class['id'] ?>)">
                                ⋮
                            </div>

                            <div class="dropdown-menu"
                                 id="menu-<?= $class['id'] ?>">

                                <a href="view_class.php?id=<?= $class['id'] ?>">
                                    View Class
                                </a>

                                <a href="delete_class.php?id=<?= $class['id'] ?>"
                                   onclick="return confirm('Delete this class?');">
                                    Delete Class
                                </a>

                            </div>

                        </div>

                        <!-- TITLE -->
                        <div class="class-title">
                            <?= htmlspecialchars($class['class_name']) ?>
                        </div>

                        <!-- BLOCK (🔥 FINAL FIXED VERSION) -->
                        <div class="class-info">
                            Block:
                            <strong>
                                <?= trim($class['block'] ?? '') !== ''
                                    ? htmlspecialchars($class['block'])
                                    : 'No Block' ?>
                            </strong>
                        </div>

                        <!-- PROGRAM -->
                        <div class="class-info">
                            Program:
                            <?= htmlspecialchars($class['program']) ?>
                        </div>

                        <!-- CLASS CODE -->
                        <div class="class-code">
                            Class Code:
                            <?= htmlspecialchars($class['class_code']) ?>
                        </div>

                        <a class="view-btn"
                           href="view_class.php?id=<?= $class['id'] ?>">
                            Open Class
                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <p>No classes created yet.</p>

        <?php endif; ?>

    </div>

</div>

<script>

function toggleMenu(id)
{
    const menu = document.getElementById("menu-" + id);

    document.querySelectorAll(".dropdown-menu").forEach(m => {
        if (m !== menu) m.style.display = "none";
    });

    menu.style.display =
        (menu.style.display === "block") ? "none" : "block";
}

document.addEventListener("click", function(e){
    if (!e.target.closest(".menu-wrapper")) {
        document.querySelectorAll(".dropdown-menu").forEach(m => {
            m.style.display = "none";
        });
    }
});

</script>

</body>
</html>