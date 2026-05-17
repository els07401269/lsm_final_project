<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

// 🔥 SAFETY CHECK (PREVENT STUDENT ACCESS)
if ($user['role'] !== 'professor') {
    header("Location: ../student/dashboard.php");
    exit;
}

$profile = $user['profile_pic'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
<title>Professor - Change Profile</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #f4f6f9;
}

/* CENTER CARD */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* CARD */
.card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    width: 400px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* AVATAR */
.avatar {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #2c3e50;
    margin-bottom: 15px;
}

input[type="file"] {
    margin-top: 15px;
}

button {
    margin-top: 15px;
    padding: 10px 20px;
    background: #2c3e50;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #34495e;
}

/* BACK LINK */
a {
    display: block;
    margin-top: 15px;
    color: #2c3e50;
    text-decoration: none;
    font-weight: bold;
}

/* NOTIF */
.success {
    background: #2ecc71;
    color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}

.error {
    background: #e74c3c;
    color: white;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}
</style>

</head>

<body>

<div class="container">

    <div class="card">

        <h2>Change Profile (Professor)</h2>

        <!-- pag nag success -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success">
                <?= $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- kapag nag error-->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- PROFILE IMAGE -->
        <?php if (!empty($profile) && file_exists("../../uploads/" . $profile)): ?>
            <img src="../../uploads/<?= $profile ?>" class="avatar">
        <?php else: ?>
            <img src="../../assets/images/default.png" class="avatar">
        <?php endif; ?>

        <!-- FORM -->
        <form action="../../controllers/upload_profile.php" method="POST" enctype="multipart/form-data">

            <input type="file" name="profile" accept="image/*" required>

            <br>

            <button type="submit">Upload New Profile</button>

        </form>

        <!--FIXED BACK BUTTON -->
        <a href="../professor/dashboard.php">← Back to Dashboard</a>

    </div>

</div>

</body>
</html>