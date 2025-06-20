<?php
include '../../../database/db.php';

// Handle form submission (add/update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $created_by = $_POST['created_by'];
    $created_at = $_POST['created_at'];

    if (!empty($id)) {
        $stmt = $conn->prepare("UPDATE announcements SET title = ?, content = ?, created_by = ?, created_at = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $content, $created_by, $created_at, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO announcements (title, content, created_by, created_at) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $content, $created_by, $created_at);
    }

    $stmt->execute();
    header("Location: index.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Toko</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<div class="container mx-auto px-4 py-8">
    <header class="mb-10 text-center">
        <h1 class="text-4xl font-bold text-indigo-700 mb-2">Pengumuman Toko</h1>
        <p class="text-gray-600">Informasi terbaru dan pengumuman penting untuk pelanggan</p>
    </header>

    <div id="announcementList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        $result = $conn->query("SELECT a.*, u.username FROM announcements a JOIN users u ON a.created_by = u.id");
        if ($result->num_rows > 0):
            while ($announcement = $result->fetch_assoc()):
        ?>
        <div class="announcement-card bg-white rounded-lg p-6 relative" data-id="<?= $announcement['id'] ?>" data-createdby="<?= htmlspecialchars($announcement['username']) ?>" data-createdat="<?= $announcement['created_at'] ?>">
            <div class="aspect-[20/1] bg-gray-200 overflow-hidden mb-4 relative">
                <img src="https://placehold.co/1000x50/f3f4f6/9ca3af?text=<?= urlencode($announcement['title']) ?>" alt="Gambar" class="w-full h-full object-cover">
                <button class="absolute top-2 right-2 bg-black bg-opacity-50 text-white p-1 rounded-full">
                    <i class="fas fa-camera"></i>
                </button>
            </div>
            <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($announcement['title']) ?></h3>
            <p class="text-gray-600 mb-3"><?= htmlspecialchars($announcement['content']) ?></p>
            <p class="text-sm text-gray-500">
                Dibuat oleh: <?= htmlspecialchars($announcement['username']) ?>
                pada <?= date('d M Y H:i', strtotime($announcement['created_at'])) ?>
            </p>
            <div class="flex gap-2 mt-4">
                <button class="edit-btn px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                    <i class="fas fa-edit mr-1"></i> Edit
                </button>
                <a href="index.php?delete=<?= $announcement['id'] ?>" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600" onclick="return confirm('Yakin ingin menghapus?')">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </a>
            </div>
        </div>
        <?php endwhile; else: ?>
        <div class="text-center py-20 col-span-full">
            <i class="fas fa-bullhorn text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-500">Belum ada pengumuman</h3>
            <p class="text-gray-400">Tambahkan pengumuman pertama Anda</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Tombol tambah -->
<button id="addBtn" class="fixed bottom-8 right-8 w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700">
    <i class="fas fa-plus text-2xl"></i>
</button>

<!-- Modal tambah/edit -->
<div id="announcementModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Form Pengumuman</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="announcementForm" method="POST" action="index.php">
            <input type="hidden" id="editId" name="id">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 mb-2">Judul</label>
                <input type="text" id="title" name="title" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700 mb-2">Isi</label>
                <textarea id="content" name="content" rows="4" class="w-full px-4 py-2 border rounded-lg" required></textarea>
            </div>
            <div class="mb-4">
                <label for="createdby" class="block text-gray-700 mb-2">Dibuat Oleh</label>
                <select id="createdby" name="created_by" class="w-full px-4 py-2 border rounded-lg" required>
                    <option value="">-- Pilih User --</option>
                    <?php
                    $result_users = $conn->query("SELECT id, username FROM users");
                    while ($user = $result_users->fetch_assoc()) {
                        echo '<option value="' . $user['id'] . '">' . htmlspecialchars($user['username']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="created_at" class="block text-gray-700 mb-2">Tanggal Dibuat</label>
                <input type="datetime-local" id="created_at" name="created_at" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelBtn" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('announcementModal');
    const addBtn = document.getElementById('addBtn');
    const closeModal = document.getElementById('closeModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('announcementForm');

    addBtn.addEventListener('click', () => {
        form.reset();
        document.getElementById('editId').value = '';
        modal.classList.remove('hidden');
    });

    closeModal.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.announcement-card');
            const id = card.dataset.id;
            const title = card.querySelector('h3').textContent;
            const content = card.querySelector('p.text-gray-600').textContent;
            const createdBy = card.dataset.createdby;
            const createdAt = new Date(card.dataset.createdat).toISOString().slice(0, 16);

            document.getElementById('editId').value = id;
            document.getElementById('title').value = title;
            document.getElementById('content').value = content;
            document.getElementById('created_at').value = createdAt;

            const select = document.getElementById('createdby');
            for (let i = 0; i < select.options.length; i++) {
                if (select.options[i].text === createdBy) {
                    select.selectedIndex = i;
                    break;
                }
            }

            modal.classList.remove('hidden');
        });
    });
});
</script>
</body>
</html>
