<?php
// templates/header.php
require __DIR__ . '/../config.php';

$user_profile_picture = 'default.png'; // Gambar default jika tidak login
// Jika pengguna sudah login, ambil foto profilnya dari database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $pic_query = "SELECT profile_picture FROM users WHERE id = $user_id";
    $pic_result = mysqli_query($conn, $pic_query);
    if ($pic_row = mysqli_fetch_assoc($pic_result)) {
        $user_profile_picture = $pic_row['profile_picture'];
    }
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
</head>
<body>

    <header class="app-header">
        <div class="header-container">
            <div class="logotype">
                <a href="dashboard.php">DoTask.</a>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="profile-menu">
                    <button class="profile-toggle" id="profile-toggle-btn">
                        <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <img src="uploads/<?php echo htmlspecialchars($user_profile_picture); ?>" alt="Profil" class="profile-avatar">
                    </button>
                    <div class="profile-dropdown" id="profile-dropdown-menu">
                        <a href="dashboard.php">Dashboard</a>
                        <a href="edit_profile.php">Pengaturan Profil</a>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin_dashboard.php">Dashboard Admin</a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="logout-link">Logout</a>
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

    <div class="main-content">
<?php
// templates/footer.php
?>
    </div> <footer class="app-footer">
        <p>&copy; <?php echo date('Y'); ?> DoTask. Dibuat dengan Filosofi GeminaWeb.</p>
    </footer>

    <script>
        // Hanya jalankan JS ini jika tombol profil ada di halaman
        const profileToggleBtn = document.getElementById('profile-toggle-btn');
        if (profileToggleBtn) {
            const profileDropdownMenu = document.getElementById('profile-dropdown-menu');
            
            profileToggleBtn.addEventListener('click', function(event) {
                // Mencegah event 'click' menyebar ke window
                event.stopPropagation();
                profileDropdownMenu.classList.toggle('show');
            });

            // Menutup dropdown jika klik di luar area menu
            window.addEventListener('click', function(event) {
                if (!profileToggleBtn.contains(event.target)) {
                    profileDropdownMenu.classList.remove('show');
                }
            });
        }
    </script>

    <script src="assets/js/main.js"></script>

</body>
</html>