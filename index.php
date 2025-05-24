<?php
require_once 'src/LH/Database.php';
require_once 'src/LH/Blog.php';

$blog = new \LH\Blog();
$postsPerPage = $blog->getPostsPerPage();
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($currentPage - 1) * $postsPerPage;
$totalPosts = $blog->getTotalPosts();
$totalPages = ceil($totalPosts / $postsPerPage);

$posts = $blog->getAllPosts($postsPerPage, $offset);
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
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
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Latest Posts</h1>
        
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($posts as $post): ?>
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <?php if ($post['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full h-48 object-cover">
                    <?php endif; ?>
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($post['title']) ?></h2>
                        <p class="text-gray-600 mb-4">
                            <?= substr(htmlspecialchars($post['description']), 0, 150) ?><?= strlen($post['description']) > 150 ? '...' : '' ?>
                        </p>
                        <a href="blog.php?id=<?= $post['id'] ?>" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                            Read More
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?>" class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="px-3 py-1 rounded <?= $i == $currentPage ? 'bg-blue-600 text-white' : 'border border-gray-300 hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?>" class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-100">Next</a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-white border-t mt-8 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-600">
            <p>© <?= date('Y') ?> My Blog. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>