<?php
// templates/header.php (Dengan Notifikasi Terintegrasi)
require_once __DIR__ . '/../config.php';

// Inisialisasi variabel agar tidak error jika tidak login
$user_profile_picture = 'default.png';
$unread_notifications = 0;

// Jika pengguna sudah login, ambil semua data yang diperlukan untuk header
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // 1. Ambil foto profil
    $pic_query = "SELECT profile_picture FROM users WHERE id = $user_id";
    $pic_result = mysqli_query($conn, $pic_query);
    if ($pic_row = mysqli_fetch_assoc($pic_result)) {
        $user_profile_picture = $pic_row['profile_picture'];
    }

    // 2. Ambil jumlah notifikasi yang belum dibaca
    $notif_count_query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = $user_id AND is_read = 0";
    $notif_count_result = mysqli_query($conn, $notif_count_query);
    $notif_count_row = mysqli_fetch_assoc($notif_count_result);
    $unread_notifications = $notif_count_row['unread_count'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoTask - Manajemen Tugas Anda</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <header class="app-header">
        <div class="header-container">
            <div class="logotype">
                <a href="dashboard.php">DoTask.</a>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="header-right-actions">
                    
                    <a href="notifications.php" class="header-action-btn" title="Notifikasi">
                        <i data-feather="bell"></i>
                        <?php if ($unread_notifications > 0): ?>
                            <span class="notification-badge"><?php echo $unread_notifications; ?></span>
                        <?php endif; ?>
                    </a>

                    <div class="profile-menu">
                        <button class="profile-toggle" id="profile-toggle-btn">
                            <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <img src="uploads/<?php echo htmlspecialchars($user_profile_picture); ?>" alt="Profil" class="profile-avatar">
                        </button>
                        <div class="profile-dropdown" id="profile-dropdown-menu">
                            <a href="dashboard.php"><i data-feather="grid"></i> Dashboard</a>
                            <a href="edit_profile.php"><i data-feather="user"></i> Pengaturan Profil</a>
                             <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <a href="admin_dashboard.php"><i data-feather="sliders"></i> Dashboard Admin</a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a href="logout.php" class="logout-link"><i data-feather="log-out"></i> Logout</a>
                        </div>
                    </div>

                </div>
            <?php else: ?>
                <nav class="main-nav">
                    <a href="login.php">Login</a>
                    <a href="register.php" class="btn-register">Daftar</a>
                </nav>
            <?php endif; ?>
        </div>
    </header>

    <div class="main-container">