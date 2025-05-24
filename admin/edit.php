<?php
require_once 'auth.php';
require_once '../src/LH/Database.php';
require_once '../src/LH/Blog.php';
require_once '../src/LH/RequestHandler.php';

use LH\RequestHandler;
use LH\Blog;

requireAuth();

// Check if post ID is provided
$postId = RequestHandler::get('id');
if (!$postId) {
    RequestHandler::redirect('dashboard.php');
}

$blog = new Blog();
$post = $blog->getPost($postId);

// If post doesn't exist, redirect to dashboard
if (!$post) {
    RequestHandler::redirect('dashboard.php');
}

$error = '';
$success = '';

// Handle form submission
if (RequestHandler::isPost()) {
    $title = RequestHandler::post('title');
    $description = RequestHandler::post('description');
    $image = RequestHandler::file('image');
    $removeImage = RequestHandler::post('remove_image') === '1';

    if (empty($title) || empty($description)) {
        $error = 'Title and description are required.';
    } else {
        $imagePath = $post['image'];

        // Handle image removal
        if ($removeImage && $imagePath) {
            $filePath = "../uploads/{$imagePath}";
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $imagePath = null;
        }

        // Handle new image upload
        if ($image && $image['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType = mime_content_type($image['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $error = 'Only JPG, JPEG, and PNG files are allowed.';
            } else {
                // Remove old image if exists
                if ($imagePath) {
                    $oldFilePath = "../uploads/{$imagePath}";
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

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
            $blog->updatePost($postId, $title, $description, $imagePath);
            $success = 'Post updated successfully!';
            // Refresh post data
            $post = $blog->getPost($postId);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function confirmImageRemoval() {
            return confirm('Are you sure you want to remove the featured image?');
        }
    </script>
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Edit Post</h1>
                <a href="dashboard.php" class="text-blue-600 hover:text-blue-800">← Back to Dashboard</a>
            </div>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="id" value="<?= htmlspecialchars($postId) ?>">
                
                <div>
                    <label for="title" class="block text-gray-700 mb-2">Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="description" class="block text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="6" required
                              class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($post['description']) ?></textarea>
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2">Current Featured Image</label>
                    <?php if ($post['image']): ?>
                        <div class="flex items-center space-x-4 mb-4">
                            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Current featured image" class="h-24 w-24 object-cover rounded">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="remove_image" value="1" class="rounded" onclick="return confirmImageRemoval()">
                                <span class="text-gray-700">Remove image</span>
                            </label>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 mb-4">No featured image currently set</p>
                    <?php endif; ?>
                    
                    <label for="image" class="block text-gray-700 mb-2">Upload New Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Only JPG, JPEG, and PNG files are allowed.</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="dashboard.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        Update Post
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>