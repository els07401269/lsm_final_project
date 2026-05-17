<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . "/../../controllers/EnrollmentController.php";

$user = $_SESSION['user'];

$controller = new EnrollmentController();

$myClasses = $controller->getMyClasses($user["id"]);
?>

<!DOCTYPE html>
<html>

<head>

<title>My Classes</title>

<style>

:root{
    --primary:#2c3e50;
    --secondary:#34495e;
    --accent:#16a085;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial;
    background:linear-gradient(135deg,#153c6b,#7d784a);
    min-height:100vh;
}

.topbar{
    background:var(--primary);
    color:white;
    padding:15px 25px;
    display:flex;
    align-items:center;
    gap:15px;
    box-shadow:0 4px 12px rgba(0,0,0,.2);
}

.logo{
    height:60px;
}

.topbar h2{
    font-size:28px;
    font-weight:900;
    letter-spacing:2px;
}

.nav{
    background:var(--secondary);
    padding:14px 25px;
    display:flex;
    gap:25px;
}

.nav a{
    color:white;
    text-decoration:none;
    font-weight:bold;
    position:relative;
}

.nav a::after{
    content:'';
    position:absolute;
    left:0;
    bottom:-5px;
    width:0%;
    height:2px;
    background:var(--accent);
    transition:.3s;
}

.nav a:hover::after{
    width:100%;
}

.container{
    padding:40px;
}

.container h2{
    color:white;
    margin-bottom:25px;
    font-size:32px;
}

.card-wrapper{
    display:flex;
    flex-wrap:wrap;
    gap:25px;
}

.class-card{
    width:340px;
    background:white;
    border-radius:18px;
    padding:20px;
    display:flex;
    justify-content:space-between;
    position:relative;
    box-shadow:0 8px 20px rgba(0,0,0,.15);
    transition:.3s;
    border-top:5px solid var(--accent);
}

.class-card:hover{
    transform:translateY(-8px);
}

.class-title{
    font-size:22px;
    font-weight:900;
    color:var(--primary);
    margin-bottom:10px;
}

.class-info{
    margin-top:8px;
    color:#555;
}

.prof-img{
    width:58px;
    height:58px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid var(--accent);
}

.view-btn{
    display:inline-block;
    margin-top:15px;
    padding:10px 15px;
    background:var(--accent);
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-weight:bold;
    transition:.3s;
}

.view-btn:hover{
    background:#138d75;
}

.menu-wrapper{
    position:absolute;
    top:10px;
    right:15px;
}

.menu-btn{
    cursor:pointer;
    font-size:22px;
}

.dropdown-menu{
    display:none;
    position:absolute;
    right:0;
    top:25px;
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 4px 10px rgba(0,0,0,.15);
}

.dropdown-menu.show{
    display:block;
}

.dropdown-menu button{
    width:100%;
    padding:12px;
    border:none;
    background:none;
    cursor:pointer;
}

.dropdown-menu button:hover{
    background:#f2f2f2;
}

</style>

</head>

<body>

<div class="topbar">
    <img src="../../assets/images/logo.png" class="logo">
    <h2>MY CLASSES</h2>
</div>

<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="join_class.php">Join Class</a>
    <a href="my_classes.php">My Classes</a>
</div>

<div class="container">

    <h2>My Classes</h2>

    <div class="card-wrapper">

        <?php if (!empty($myClasses)): ?>

            <?php foreach ($myClasses as $class): ?>

                <div class="class-card">

                    <div class="card-left">

                        <div class="menu-wrapper">

                            <div class="menu-btn"
                                 onclick="toggleMenu(<?= $class['id'] ?>)">
                                ⋮
                            </div>

                            <div class="dropdown-menu"
                                 id="menu-<?= $class['id'] ?>">

                                <form method="POST"
                                      action="leave_class.php"
                                      onsubmit="return confirm('Leave this class?');">

                                    <input type="hidden"
                                           name="class_id"
                                           value="<?= $class['id'] ?>">

                                    <button type="submit">Leave Class</button>

                                </form>

                            </div>

                        </div>

                        <div class="class-title">
                            <?= htmlspecialchars($class['class_name']) ?>
                        </div>

                        <!-- 🔥 FIXED BLOCK (SAFE + CONSISTENT) -->
                        <div class="class-info">
                            Block:
                            <strong>
                                <?= trim($class['block'] ?? '') !== ''
                                    ? htmlspecialchars($class['block'])
                                    : 'No Block' ?>
                            </strong>
                        </div>

                        <div class="class-info">
                            Program:
                            <?= htmlspecialchars($class['program']) ?>
                        </div>

                        <div class="class-info">
                            Professor:
                            <?= htmlspecialchars($class['prof_name'] ?? 'Unknown Professor') ?>
                        </div>

                        <a href="view_class.php?id=<?= $class['id'] ?>"
                           class="view-btn">
                            View Class
                        </a>

                    </div>

                    <div class="card-right">

                        <?php if (
                            !empty($class['profile_pic']) &&
                            file_exists("../../uploads/" . $class['profile_pic'])
                        ): ?>

                            <img
                                src="../../uploads/<?= $class['profile_pic'] ?>"
                                class="prof-img"
                            >

                        <?php else: ?>

                            <img
                                src="../../assets/images/default.png"
                                class="prof-img"
                            >

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>No classes joined yet.</p>

        <?php endif; ?>

    </div>

</div>

<script>

function toggleMenu(id)
{
    const menu = document.getElementById("menu-" + id);

    document.querySelectorAll(".dropdown-menu").forEach(m => {
        if (m !== menu) m.classList.remove("show");
    });

    menu.classList.toggle("show");
}

document.addEventListener("click", function(e){
    if (!e.target.closest(".menu-wrapper")) {
        document.querySelectorAll(".dropdown-menu").forEach(m => {
            m.classList.remove("show");
        });
    }
});

</script>

</body>
</html>