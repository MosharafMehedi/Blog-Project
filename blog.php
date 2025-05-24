<?php
require_once 'src/LH/Database.php';
require_once 'src/LH/Blog.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$blog = new \LH\Blog();
$post = $blog->getPost($_GET['id']);

if (!$post) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> | My Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="index.php" class="text-xl font-bold text-gray-800">My Blog</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-800 hover:text-blue-600">Home</a>
                    <a href="admin/login.php" class="text-gray-800 hover:text-blue-600">Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <article class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if ($post['image']): ?>
                <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-64 md:h-96 object-cover">
            <?php endif; ?>
            
            <div class="p-6 md:p-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($post['title']) ?></h1>
                <div class="flex items-center text-gray-500 mb-6">
                    <span><?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                </div>
                <div class="prose max-w-none text-gray-700">
                    <?= nl2br(htmlspecialchars($post['description'])) ?>
                </div>
            </div>
        </article>

        <div class="mt-8">
            <a href="index.php" class="inline-block bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition-colors">
                ← Back to Blog
            </a>
        </div>
    </main>

    <footer class="bg-white border-t mt-8 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600">
            <p>© <?= date('Y') ?> My Blog. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>