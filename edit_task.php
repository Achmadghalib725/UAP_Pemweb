<?php
// edit_task.php (Versi Final dengan Pemicu Notifikasi)
require 'config.php';

// Keamanan & Inisialisasi
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { header("Location: dashboard.php"); exit(); }

$user_id = $_SESSION['user_id'];
$task_id = $_GET['id'];
$error_message = '';
$message_success = '';

// --- Logika Pengambilan Data Awal (Dibutuhkan oleh form POST juga) ---
$query = "SELECT t.*, u.name as creator_name FROM tasks t JOIN users u ON t.creator_id = u.id WHERE t.id = $task_id";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) == 0) {
    require 'templates/header.php';
    echo "<div class='content-wrapper'><p>Tugas tidak ditemukan.</p></div>";
    require 'templates/footer.php';
    exit();
}
$task = mysqli_fetch_assoc($result);


// --- Logika Pemrosesan Form (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // A. Logika untuk UPDATE TUGAS
    if (isset($_POST['update_task'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);

        $update_query = "UPDATE tasks SET title = '$title', description = '$description', status = '$status' WHERE id = $task_id";
        if ($_SESSION['user_role'] !== 'admin') {
            $update_query .= " AND creator_id = $user_id";
        }
        
        if (mysqli_query($conn, $update_query)) {
            $message_success = "Tugas berhasil diperbarui.";
            // Refresh data tugas setelah update
            $result = mysqli_query($conn, $query);
            $task = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Gagal memperbarui tugas.";
        }
    }

    // B. Logika untuk UNDANG KOLABORATOR
    if (isset($_POST['invite_user'])) {
        $invite_email = mysqli_real_escape_string($conn, $_POST['invite_email']);

        $find_user_query = "SELECT id FROM users WHERE email = '$invite_email'";
        $find_user_result = mysqli_query($conn, $find_user_query);

        if (mysqli_num_rows($find_user_result) > 0) {
            $invited_user = mysqli_fetch_assoc($find_user_result);
            $invited_user_id = $invited_user['id'];

            if ($invited_user_id == $task['creator_id']) {
                 $error_message = "Pengguna ini adalah pemilik tugas.";
            } else {
                $check_collab_query = "SELECT * FROM task_collaborators WHERE task_id = $task_id AND user_id = $invited_user_id";
                $check_collab_result = mysqli_query($conn, $check_collab_query);

                if (mysqli_num_rows($check_collab_result) > 0) {
                    $error_message = "Pengguna ini sudah menjadi kolaborator.";
                } else {
                    $insert_collab_query = "INSERT INTO task_collaborators (task_id, user_id) VALUES ($task_id, $invited_user_id)";
                    if (mysqli_query($conn, $insert_collab_query)) {
                        $message_success = "Pengguna berhasil diundang sebagai kolaborator.";

                        // PEMICU NOTIFIKASI
                        $inviter_name = $_SESSION['user_name'];
                        $task_title = $task['title'];
                        $notification_message = mysqli_real_escape_string($conn, "$inviter_name mengundang Anda untuk berkolaborasi di tugas '$task_title'");
                        $notification_link = "edit_task.php?id=" . $task_id;
                        $notif_query = "INSERT INTO notifications (user_id, message, link) VALUES ($invited_user_id, '$notification_message', '$notification_link')";
                        mysqli_query($conn, $notif_query);
                        
                    } else {
                        $error_message = "Gagal mengundang pengguna.";
                    }
                }
            }
        } else {
            $error_message = "Pengguna dengan email tersebut tidak ditemukan.";
        }
    }
}


// --- Keamanan & Pengambilan Data Kolaborator (Untuk Tampilan) ---
$is_collaborator_query = "SELECT COUNT(*) AS count FROM task_collaborators WHERE task_id = $task_id AND user_id = $user_id";
$is_collaborator_result = mysqli_query($conn, $is_collaborator_query);
$is_collaborator_row = mysqli_fetch_assoc($is_collaborator_result);
$is_collaborator = $is_collaborator_row['count'] > 0;

if ($_SESSION['user_role'] !== 'admin' && $task['creator_id'] != $user_id && !$is_collaborator) {
    require 'templates/header.php';
    echo "<div class='content-wrapper'><p>Anda tidak memiliki izin untuk melihat atau mengedit tugas ini.</p></div>";
    require 'templates/footer.php';
    exit();
}

$collaborators_query = "SELECT u.id, u.name, u.profile_picture FROM users u JOIN task_collaborators tc ON u.id = tc.user_id WHERE tc.task_id = $task_id";
$collaborators_result = mysqli_query($conn, $collaborators_query);

$owner_query = "SELECT id, name, profile_picture FROM users WHERE id = " . $task['creator_id'];
$owner_result = mysqli_query($conn, $owner_query);
$owner = mysqli_fetch_assoc($owner_result);

require 'templates/header.php';
?>

<div class="content-wrapper">
    <header class="content-header-main">
        <h1>Edit Tugas</h1>
        <a href="dashboard.php" class="btn-secondary" style="background-color: var(--light-bg); color: var(--dark-text);"> &lsaquo; Kembali</a>
    </header>

    <?php if ($message_success): ?><div class="message-success"><?php echo $message_success; ?></div><?php endif; ?>
    <?php if ($error_message): ?><div class="message-error"><?php echo $error_message; ?></div><?php endif; ?>

    <div class="edit-task-layout">
        <div class="main-edit-form">
            <form action="edit_task.php?id=<?php echo $task_id; ?>" method="POST">
                <div class="form-group">
                    <label for="title">Judul Tugas</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="pending" <?php if($task['status'] == 'pending') echo 'selected'; ?>>Tertunda</option>
                        <option value="in_progress" <?php if($task['status'] == 'in_progress') echo 'selected'; ?>>Dikerjakan</option>
                        <option value="completed" <?php if($task['status'] == 'completed') echo 'selected'; ?>>Selesai</option>
                    </select>
                </div>
                <button type="submit" name="update_task" style="background-color:var(--primary-color); color:white;">Simpan Perubahan</button>
            </form>
        </div>

        <div class="collaborators-section">
            <h4>Dibagikan dengan</h4>
            <div class="collaborators-list">
                <div class="collaborator-item owner">
                    <img src="uploads/<?php echo htmlspecialchars($owner['profile_picture']); ?>" alt="Avatar">
                    <span><?php echo htmlspecialchars($owner['name']); ?> (Pemilik)</span>
                </div>
                <?php while ($collaborator = mysqli_fetch_assoc($collaborators_result)): ?>
                    <div class="collaborator-item">
                        <img src="uploads/<?php echo htmlspecialchars($collaborator['profile_picture']); ?>" alt="Avatar">
                        <span><?php echo htmlspecialchars($collaborator['name']); ?></span>
                    </div>
                <?php endwhile; ?>
            </div>
            <hr>
            <h5>Undang Pengguna Baru</h5>
            <form action="edit_task.php?id=<?php echo $task_id; ?>" method="POST" class="invite-form">
                <div class="form-group">
                    <label for="email">Email Pengguna</label>
                    <input type="email" name="invite_email" placeholder="contoh@email.com" required>
                </div>
                <button type="submit" name="invite_user" style="background-color:var(--primary-color); color:white;">Undang</button>
            </form>
        </div>
    </div>
</div>

<?php
require 'templates/footer.php';
?>