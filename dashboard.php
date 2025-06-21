
<?php
require 'templates/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Tambah tugas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if (!empty($title)) {
        $query = "INSERT INTO tasks (title, description, status, creator_id) 
                  VALUES ('$title', '$description', '$status', $user_id)";
        mysqli_query($conn, $query);
        header("Location: dashboard.php");
        exit();
    }
}

// Hapus tugas
if (isset($_GET['delete_task']) && is_numeric($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];
    mysqli_query($conn, "DELETE FROM tasks WHERE id = $task_id AND creator_id = $user_id");
    header("Location: dashboard.php");
    exit();
}

// Filter status
$filter = $_GET['status'] ?? 'all';
$filter_condition = ($filter !== 'all') ? "AND status = '$filter'" : '';
?>

<div class="main-content">
    <!-- Header -->
    <div class="page-header">
        <br><h3>📋 Dashboard Tugas</h3>
        <p>Halo, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>. Kelola dan pantau semua tugas Anda di sini.</p>
    </div>

    <!-- Form Tambah -->
    <form class="task-form-inline" method="POST">
        <input type="text" name="title" placeholder="Judul tugas" required>
        <input type="text" name="description" placeholder="Deskripsi tugas" required>
        <select name="status">
            <option value="in_progress">Sedang Dikerjakan</option>
            <option value="pending">Ditunda</option>
            <option value="completed">Selesai</option>
        </select>
        <button type="submit" name="add_task">Tambah</button>
    </form>

    <!-- Filter Menu -->
    <div class="mb-4 mt-4" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="?status=all" class="nav-item <?php if ($filter == 'all') echo 'active'; ?>">Semua</a>
        <a href="?status=in_progress" class="nav-item <?php if ($filter == 'in_progress') echo 'active'; ?>">Sedang Dikerjakan</a>
        <a href="?status=pending" class="nav-item <?php if ($filter == 'pending') echo 'active'; ?>">Ditunda</a>
        <a href="?status=completed" class="nav-item <?php if ($filter == 'completed') echo 'active'; ?>">Selesai</a>
    </div>

    <!-- Daftar Tugas -->
    <div class="task-section">
        <h4>📝 Daftar Tugas Anda</h4>

        <?php
        $query = "SELECT * FROM tasks WHERE creator_id = $user_id $filter_condition ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0):
            while ($task = mysqli_fetch_assoc($result)):
                $status_badge = match($task['status']) {
                    'completed' => '<span class="status-badge status-completed">Selesai</span>',
                    'in_progress' => '<span class="status-badge status-in_progress">Sedang Dikerjakan</span>',
                    'pending' => '<span class="status-badge status-pending">Ditunda</span>',
                    default => '',
                };
        ?>
            <div class="task-item <?php echo $task['status'] === 'completed' ? 'task-completed' : ''; ?>">
                <div class="task-item-left">
                    <div class="task-checkbox" title="<?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>">
                        <?php if ($task['status'] === 'completed'): ?>
                            <span class="feather" data-feather="check"></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="task-item-title"><?php echo htmlspecialchars($task['title']); ?></div>
                        <div style="color: var(--light-text); font-size: 0.9em;">
                            <?php echo htmlspecialchars($task['description']); ?>
                        </div>
                        <div style="margin-top: 6px;"><?php echo $status_badge; ?></div>
                    </div>
                </div>
                <div class="task-item-right">
                    <div class="task-item-actions">
                        <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="action-btn" title="Edit">
                            <i class="feather" data-feather="edit-2"></i>
                        </a>
                        <a href="?delete_task=<?php echo $task['id']; ?>" class="action-btn" title="Hapus" onclick="return confirm('Yakin ingin menghapus tugas ini?');">
                            <i class="feather" data-feather="trash-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php
            endwhile;
        else:
            echo "<p>✅ Tidak ada tugas saat ini. Waktu untuk istirahat!</p>";
        endif;
        ?>
    </div>
</div>

<?php require 'templates/footer.php'; ?>

<!-- Feather Icons -->
<script src="https://unpkg.com/feather-icons"></script>
<script>feather.replace();</script>

