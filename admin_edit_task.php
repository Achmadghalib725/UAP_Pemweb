<?php
// admin_edit_task.php
require 'templates/header.php';

// --- Keamanan Berlapis ---
// 1. Pastikan yang mengakses adalah admin yang sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Pastikan ada ID tugas yang dikirim melalui URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$task_id_to_edit = $_GET['id'];

// --- Logika untuk Memproses Form Saat Disubmit (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Validasi sederhana untuk status
    $allowed_statuses = ['pending', 'in_progress', 'completed'];
    if (in_array($status, $allowed_statuses)) {
        // Query untuk update data tugas
        $update_query = "UPDATE tasks SET title = '$title', description = '$description', status = '$status' WHERE id = $task_id_to_edit";
        
        if (mysqli_query($conn, $update_query)) {
            // Redirect kembali ke dashboard admin dengan pesan sukses
            header("Location: admin_dashboard.php?status=task_updated");
            exit();
        } else {
            $error_message = "Gagal memperbarui data tugas.";
        }
    } else {
        $error_message = "Status yang dipilih tidak valid.";
    }
}


// --- Logika untuk Mengambil Data Awal Tugas (GET) ---
// Kita gunakan JOIN untuk mendapatkan nama pembuat tugas juga, untuk konteks
$query = "SELECT tasks.*, users.name AS creator_name 
          FROM tasks 
          JOIN users ON tasks.creator_id = users.id 
          WHERE tasks.id = $task_id_to_edit";
$result = mysqli_query($conn, $query);

// Jika tugas dengan ID tersebut tidak ditemukan
if (mysqli_num_rows($result) == 0) {
    header("Location: admin_dashboard.php");
    exit();
}

$task = mysqli_fetch_assoc($result);

?>

<div class="page-header">
    <h3>Edit Tugas (Admin)</h3>
    <div class="page-actions">
        <a href="admin_dashboard.php" class="btn-secondary">&lsaquo; Kembali ke Dashboard Admin</a>
    </div>
</div>

<p>Anda sedang mengedit tugas milik: <strong><?php echo htmlspecialchars($task['creator_name']); ?></strong></p>

<?php if (isset($error_message)): ?>
    <div class="message-error"><?php echo $error_message; ?></div>
<?php endif; ?>

<form class="form-container" action="admin_edit_task.php?id=<?php echo $task_id_to_edit; ?>" method="POST">
    <div class="form-group">
        <label for="title">Judul Tugas</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Deskripsi</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($task['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="pending" <?php if($task['status'] == 'pending') echo 'selected'; ?>>Tertunda</option>
            <option value="in_progress" <?php if($task['status'] == 'in_progress') echo 'selected'; ?>>Dikerjakan</option>
            <option value="completed" <?php if($task['status'] == 'completed') echo 'selected'; ?>>Selesai</option>
        </select>
    </div>
    <div class="form-actions">
        <button type="submit">Simpan Perubahan</button>
    </div>
</form>

<?php
require 'templates/footer.php';
?>