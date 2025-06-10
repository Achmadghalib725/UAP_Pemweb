<?php
// dashboard.php
require 'templates/header.php';

// --- Proteksi Halaman ---
// Cek jika pengguna sudah login, jika tidak, arahkan ke halaman login.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID pengguna dari session untuk semua query di halaman ini.
$user_id = $_SESSION['user_id'];


// --- LOGIKA UNTUK MENAMBAHKAN TUGAS BARU ---
// Cek apakah form dengan tombol name="add_task" telah disubmit dengan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    
    // Ambil judul tugas dari form dan bersihkan dari karakter berbahaya.
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    // Pastikan judul tidak kosong.
    if (!empty($title)) {
        
        // Buat query SQL untuk memasukkan data.
        $query = "INSERT INTO tasks (title, creator_id) VALUES ('$title', $user_id)";
        
        // Jalankan query dan cek apakah berhasil
        if (mysqli_query($conn, $query)) {
            // Jika berhasil, refresh halaman untuk menampilkan tugas baru dan membersihkan form.
            header("Location: dashboard.php");
            exit();
        } else {
            // Jika gagal, tampilkan error database
            echo "Error: Gagal menambahkan tugas. " . mysqli_error($conn);
            exit(); 
        }
    }
}


// --- LOGIKA UNTUK MENGHAPUS TUGAS ---
// Cek apakah ada parameter 'delete_task' di URL.
if (isset($_GET['delete_task']) && is_numeric($_GET['delete_task'])) {
    $task_id_to_delete = $_GET['delete_task'];
    
    // Query hapus, pastikan hanya tugas milik pengguna yang login yang bisa dihapus.
    $query = "DELETE FROM tasks WHERE id = $task_id_to_delete AND creator_id = $user_id";
    mysqli_query($conn, $query);

    // Refresh halaman untuk menghilangkan tugas yang sudah dihapus.
    header("Location: dashboard.php");
    exit();
}
?>

<div class="page-header">
    <h3>Dashboard</h3>
</div>

<form class="task-form" action="dashboard.php" method="POST">
    <input type="text" name="title" placeholder="Apa yang akan Anda kerjakan hari ini?" required>
    <button type="submit" name="add_task">Tambah Tugas</button>
</form>

<div class="task-section">
    <h4>Daftar Tugas Anda</h4>
    <div class="task-list">
        <?php
        // Ambil semua tugas milik pengguna dari database, urutkan dari yang terbaru.
        $task_query = "SELECT * FROM tasks WHERE creator_id = $user_id ORDER BY created_at DESC";
        $task_result = mysqli_query($conn, $task_query);

        // Cek apakah ada tugas untuk ditampilkan.
        if (mysqli_num_rows($task_result) > 0):
            // Lakukan perulangan untuk setiap tugas dan tampilkan sebagai kartu.
            while($task = mysqli_fetch_assoc($task_result)):
                // Menambahkan kelas 'completed' jika status tugas sudah selesai untuk efek visual.
                $card_class = $task['status'] == 'completed' ? 'task-card completed' : 'task-card';
        ?>
            <div class="<?php echo $card_class; ?>">
                <span class="task-title"><?php echo htmlspecialchars($task['title']); ?></span>
                <div class="task-actions-new">
                    <a href="edit_task.php?id=<?php echo $task['id']; ?>" title="Edit">✏️</a>
                    <a href="dashboard.php?delete_task=<?php echo $task['id']; ?>" title="Hapus" onclick="return confirm('Anda yakin ingin menghapus tugas ini?');">🗑️</a>
                </div>
            </div>
        <?php
            endwhile;
        else:
            // Tampilkan pesan ini jika tidak ada tugas.
            echo "<p>Hebat! Tidak ada tugas yang perlu dikerjakan.</p>";
        endif;
        ?>
    </div>
</div>

<?php
// Memanggil footer untuk menutup halaman HTML.
require 'templates/footer.php';
?>