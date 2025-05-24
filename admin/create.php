<?php
require_once 'auth.php';
require_once '../src/LH/Database.php';
require_once '../src/LH/Blog.php';

requireAuth();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image'] ?? null;

    if (empty($title) || empty($description)) {
        $error = 'Title and description are required.';
    } else {
        $blog = new \LH\Blog();
        $imagePath = null;

        // Handle image upload
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType = mime_content_type($image['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $error = 'Only JPG, JPEG, and PNG files are allowed.';
            } else {
                $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $destination = '../uploads/' . $filename;

                if (move_uploaded_file($image['tmp_name'], $destination)) {
                    $imagePath = $filename;
                } else {
                    $error = 'Failed to upload image.';
                }
            }
        }

        if (empty($error)) {
            $blog->createPost($title, $description, $imagePath);
            $success = 'Post created successfully!';
            // Clear form
            $title = $description = '';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Admin Navigation -->
    <nav class="bg-blue-600 text-white shadow-md">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">Admin Panel</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="hover:bg-blue-700 px-3 py-2 rounded">Dashboard</a>
                    <a href="create.php" class="hover:bg-blue-700 px-3 py-2 rounded">Create Post</a>
                    <a href="settings.php" class="hover:bg-blue-700 px-3 py-2 rounded">Settings</a>
                    <a href="logout.php" class="hover:bg-blue-700 px-3 py-2 rounded">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Post</h1>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="title" class="block text-gray-700 mb-2">Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="description" class="block text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="6" required
                              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>
                
                <div>
                    <label for="image" class="block text-gray-700 mb-2">Featured Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Only JPG, JPEG, and PNG files are allowed.</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="dashboard.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        Create Post
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>