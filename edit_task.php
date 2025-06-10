<?php
// edit_task.php
require 'templates/header.php';

// Proteksi halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_GET['id']; // Ambil ID tugas dari URL

// Logika saat form disubmit (UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Query UPDATE
    $update_query = "UPDATE tasks SET title = '$title', description = '$description', status = '$status' WHERE id = $task_id AND creator_id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: dashboard.php?status=updated");
        exit();
    } else {
        $error = "Gagal memperbarui tugas.";
    }
}

// Ambil data tugas yang akan di-edit dari database
$query = "SELECT * FROM tasks WHERE id = $task_id AND creator_id = $user_id";
$result = mysqli_query($conn, $query);

// Cek apakah tugas itu ada dan milik pengguna yang sedang login
if (mysqli_num_rows($result) != 1) {
    echo "<p>Tugas tidak ditemukan atau Anda tidak memiliki izin untuk mengeditnya.</p>";
    require 'templates/footer.php';
    exit();
}

$task = mysqli_fetch_assoc($result);
?>

<h3>Edit Tugas</h3>
<?php if (isset($error)): ?>
    <p class="message-error"><?php echo $error; ?></p>
<?php endif; ?>

<form class="task-form" action="edit_task.php?id=<?php echo $task_id; ?>" method="POST">
    <div class="form-group">
        <label for="title">Judul Tugas</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Deskripsi (Opsional)</label>
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
    <button type="submit">Simpan Perubahan</button>
</form>

<?php
require 'templates/footer.php';
?>