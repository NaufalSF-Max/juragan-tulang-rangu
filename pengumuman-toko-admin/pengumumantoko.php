<?php
include '../database/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $created_by = $_POST['created_by'];
    $created_at = $_POST['created_at'];

    $stmt = $conn->prepare("INSERT INTO announcements (title, content, created_by, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $content, $created_by, $created_at);

    if ($stmt->execute()) {
        $message = "Pengumuman berhasil ditambahkan!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}

// Handle delete request
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
    <style>
        .announcement-card {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }
        .floating-btn:hover {
            transform: scale(1.1);
        }
        .image-container {
            aspect-ratio: 20/1;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 1rem;
            position: relative;
        }
        .image-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .edit-image-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-bold text-indigo-700 mb-2">Pengumuman Toko</h1>
            <p class="text-gray-600">Informasi terbaru dan pengumuman penting untuk pelanggan</p>
        </header>

        <!-- Announcement List -->
        <div id="announcementList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $result = $conn->query("SELECT a.*, u.username FROM announcements a JOIN users u ON a.created_by = u.id");
            if ($result->num_rows > 0):
                while ($announcement = $result->fetch_assoc()):
            ?>
            <div class="announcement-card bg-white rounded-lg p-6 relative" data-id="<?= $announcement['id'] ?>">
                <!-- Image Section with 20:1 Aspect Ratio -->
                <div class="image-container rounded-lg overflow-hidden">
                    <img src="https://placehold.co/1000x50/f3f4f6/9ca3af?text=<?= urlencode($announcement['title']) ?>" 
                         alt="Gambar pengumuman: <?= htmlspecialchars($announcement['title']) ?>" 
                         class="image-placeholder">
                    <button class="edit-image-btn" title="Edit Gambar">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                
                <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($announcement['title']) ?></h3>
                <p class="text-gray-600 mb-3"><?= htmlspecialchars($announcement['content']) ?></p>
                <p class="text-sm text-gray-500">
                    Dibuat oleh: <?= htmlspecialchars($announcement['username']) ?> 
                    pada <?= date('d M Y H:i', strtotime($announcement['created_at'])) ?>
                </p>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="edit-btn px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </button>
                    <a href="index.php?delete=<?= $announcement['id'] ?>" 
                       class="delete-btn px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-center"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </a>
                </div>
            </div>
            <?php
                endwhile;
            else:
            ?>
            <div id="emptyState" class="text-center py-20 col-span-full">
                <i class="fas fa-bullhorn text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-500">Belum ada pengumuman</h3>
                <p class="text-gray-400">Tambahkan pengumuman pertama Anda</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Floating Add Button -->
    <button id="addBtn" class="floating-btn bg-indigo-600 text-white flex items-center justify-center hover:bg-indigo-700">
        <i class="fas fa-plus text-2xl"></i>
    </button>

    <!-- Modal -->
    <div id="announcementModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Tambah Pengumuman Baru</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="announcementForm" method="POST" action="index.php">
                <input type="hidden" id="editId" name="id">

                <!-- Image Upload Section -->
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Gambar Pengumuman</label>
                    <div class="image-container rounded-lg overflow-hidden mb-2">
                        <img id="imagePreview" src="https://placehold.co/1000x50/f3f4f6/9ca3af?text=Pilih+Gambar" 
                             alt="Preview gambar pengumuman" 
                             class="image-placeholder">
                    </div>
                    <input type="file" id="imageUpload" accept="image/*" class="hidden">
                    <button type="button" id="uploadBtn" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-upload mr-2"></i> Unggah Gambar
                    </button>
                </div>

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 mb-2">Judul</label>
                    <input type="text" id="title" name="title" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-gray-700 mb-2">Isi</label>
                    <textarea id="content" name="content" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="createdby" class="block text-gray-700 mb-2">Dibuat Oleh</label>
                    <select id="createdby" name="created_by" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
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
                    <input type="datetime-local" id="created_at" name="created_at" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
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
        const addBtn = document.getElementById('addBtn');
        const modal = document.getElementById('announcementModal');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const form = document.getElementById('announcementForm');
        const uploadBtn = document.getElementById('uploadBtn');
        const imageUpload = document.getElementById('imageUpload');
        const imagePreview = document.getElementById('imagePreview');

        // Modal control
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

        // Image upload handling
        uploadBtn.addEventListener('click', () => {
            imageUpload.click();
        });

        imageUpload.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    imagePreview.src = event.target.result;
                };
                
                reader.readAsDataURL(file);
            }
        });

        // Edit button functionality
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const card = btn.closest('.announcement-card');
                const id = card.dataset.id;
                const title = card.querySelector('h3').textContent;
                const content = card.querySelector('p.text-gray-600').textContent;
                
                document.getElementById('editId').value = id;
                document.getElementById('title').value = title;
                document.getElementById('content').value = content;
                
                modal.classList.remove('hidden');
            });
        });

        // Edit image button
        document.querySelectorAll('.edit-image-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const card = this.closest('.announcement-card');
                const id = card.dataset.id;
                alert('Fitur edit gambar untuk pengumuman ID: ' + id);
            });
        });
    });
    </script>
</body>
</html>
