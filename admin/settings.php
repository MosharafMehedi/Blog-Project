<?php
require_once 'auth.php';
require_once '../src/LH/Database.php';
require_once '../src/LH/Blog.php';

requireAuth();

$blog = new \LH\Blog();
$currentPostsPerPage = $blog->getPostsPerPage();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postsPerPage = (int)($_POST['posts_per_page'] ?? 5);
    $postsPerPage = max(1, min($postsPerPage, 20)); // Limit between 1-20
    
    $blog->setPostsPerPage($postsPerPage);
    $currentPostsPerPage = $postsPerPage;
    $success = 'Settings updated successfully!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Admin</title>
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
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Settings</h1>
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="posts_per_page" class="block text-gray-700 mb-2">Posts Per Page</label>
                    <select id="posts_per_page" name="posts_per_page" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach ([5, 10, 15, 20] as $count): ?>
                            <option value="<?= $count ?>" <?= $currentPostsPerPage == $count ? 'selected' : '' ?>>
                                <?= $count ?> posts
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>