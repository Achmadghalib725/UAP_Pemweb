<?php
// admin_dashboard.php
require 'templates/header.php';

// --- Proteksi Halaman ---
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// --- Logika Hapus Pengguna ---
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    $user_id_to_delete = $_GET['delete_user'];
    if ($user_id_to_delete != $_SESSION['user_id']) {
        $delete_query = "DELETE FROM users WHERE id = $user_id_to_delete";
        mysqli_query($conn, $delete_query);
        header("Location: admin_dashboard.php?status=user_deleted");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=cannot_delete_self");
        exit();
    }
}

// --- Logika untuk Mengambil Semua Pengguna ---
$users_query = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
$users_result = mysqli_query($conn, $users_query);


// =================================================================
// BAGIAN BARU: LOGIKA UNTUK MENGAMBIL SEMUA TUGAS DENGAN INFO PENGGUNA
// =================================================================
$tasks_query = "SELECT 
                    tasks.id, 
                    tasks.title, 
                    tasks.status, 
                    users.name AS creator_name 
                FROM tasks 
                JOIN users ON tasks.creator_id = users.id 
                ORDER BY tasks.created_at DESC";
$tasks_result = mysqli_query($conn, $tasks_query);


?>

<div class="page-header">
    <h3>Dashboard Admin</h3>
</div>
<p>Selamat datang di pusat kendali, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>

<div class="admin-section">
    <h4>Manajemen Pengguna</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Peran (Role)</th>
                <th>Bergabung Sejak</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo date('d M Y, H:i', strtotime($user['created_at'])); ?></td>
                <td class="actions">
                    <a href="admin_edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit">Edit</a>
                    <a href="admin_dashboard.php?delete_user=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('PERINGATAN: Menghapus pengguna juga akan menghapus semua tugas mereka. Anda yakin?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


<div class="admin-section">
    <h4>Manajemen Tugas Global</h4>
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
            <?php
            // Lakukan perulangan untuk setiap baris data tugas
            while ($task = mysqli_fetch_assoc($tasks_result)):
            ?>
            <tr>
                <td><?php echo $task['id']; ?></td>
                <td><?php echo htmlspecialchars($task['title']); ?></td>
                <td><?php echo htmlspecialchars($task['status']); ?></td>
                <td><?php echo htmlspecialchars($task['creator_name']); ?></td>
                <td class="actions">
                    <a href="admin_edit_task.php?id=<?php echo $task['id']; ?>" class="btn-edit">Edit</a>
                    <a href="admin_dashboard.php?delete_task=<?php echo $task['id']; ?>" class="btn-delete" onclick="return confirm('Anda yakin ingin menghapus tugas ini secara permanen?');">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


<?php
require 'templates/footer.php';
?>