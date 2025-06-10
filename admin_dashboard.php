<?php
// admin_dashboard.php (Versi Final dengan Struktur yang Benar)
require 'config.php';

// ==========================================================
// SEMUA LOGIKA PEMROSESAN DITARUH DI SINI (SEBELUM HTML)
// ==========================================================

// 1. Proteksi Halaman
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// 2. Logika Hapus Pengguna
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    if ($_GET['delete_user'] != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = {$_GET['delete_user']}");
    }
    header("Location: admin_dashboard.php?status=user_deleted");
    exit();
}

// 3. Logika Hapus Tugas
if (isset($_GET['delete_task']) && is_numeric($_GET['delete_task'])) {
    mysqli_query($conn, "DELETE FROM tasks WHERE id = {$_GET['delete_task']}");
    header("Location: admin_dashboard.php?status=task_deleted");
    exit();
}


// --- Logika untuk Mengambil Data untuk Ditampilkan ---
$users_query = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);

$tasks_query = "SELECT tasks.id, tasks.title, tasks.status, users.name AS creator_name 
                FROM tasks JOIN users ON tasks.creator_id = users.id ORDER BY tasks.created_at DESC";
$tasks_result = mysqli_query($conn, $tasks_query);


// Panggil Header SETELAH semua logika di atas selesai
require 'templates/header.php';
?>

<div class="content-wrapper">
    <header class="content-header-main">
        <h1>Dashboard Admin</h1>
        <p>Selamat datang di pusat kendali, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>
    </header>

    <div class="admin-section">
        <h4>Manajemen Pengguna</h4>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>"><?php echo htmlspecialchars($user['role']); ?></span></td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        <td class="actions">
                            <a href="admin_edit_user.php?id=<?php echo $user['id']; ?>" class="action-btn-sm btn-edit">Edit</a>
                            <a href="admin_dashboard.php?delete_user=<?php echo $user['id']; ?>" class="action-btn-sm btn-delete" onclick="return confirm('PERINGATAN: Menghapus pengguna juga akan menghapus semua tugas mereka. Anda yakin?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-section">
        <h4>Manajemen Tugas Global</h4>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Tugas</th>
                        <th>Judul Tugas</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = mysqli_fetch_assoc($tasks_result)): ?>
                    <tr>
                        <td><?php echo $task['id']; ?></td>
                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                        <td><span class="status-badge status-<?php echo htmlspecialchars($task['status']); ?>"><?php echo htmlspecialchars($task['status']); ?></span></td>
                        <td><?php echo htmlspecialchars($task['creator_name']); ?></td>
                        <td class="actions">
                            <a href="admin_edit_task.php?id=<?php echo $task['id']; ?>" class="action-btn-sm btn-edit">Edit</a>
                            <a href="admin_dashboard.php?delete_task=<?php echo $task['id']; ?>" class="action-btn-sm btn-delete" onclick="return confirm('Anda yakin ingin menghapus tugas ini secara permanen?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div> <?php
require 'templates/footer.php';
?>