<?php
require_once 'auth.php';
require_once '../src/LH/Database.php';
require_once '../src/LH/Blog.php';

requireAuth();

$blog = new \LH\Blog();
$posts = $blog->getAllPosts(10, 0); // Show 10 posts in dashboard
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Blog Posts</h1>
            <a href="create.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                + Create New Post
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($posts)): ?>
                <div class="p-6 text-center text-gray-600">
                    No blog posts found.
                </div>
            <?php else: ?>
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($posts as $post): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($post['image']): ?>
                                        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post image" class="h-10 w-10 rounded-full object-cover">
                                    <?php else: ?>
                                        <span class="text-gray-400">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($post['title']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500"><?= date('M j, Y', strtotime($post['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="edit.php?id=<?= $post['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <button onclick="confirmDelete(<?= $post['id'] ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <script>
        function confirmDelete(postId) {
            if (confirm('Are you sure you want to delete this post?')) {
                fetch('delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + postId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error deleting post');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting post');
                });
            }
        }
    </script>
</body>
</html>