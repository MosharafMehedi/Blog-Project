<?php
require_once 'auth.php';
require_once '../src/LH/Database.php';
require_once '../src/LH/Blog.php';

requireAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$postId = $_POST['id'] ?? null;
if (!$postId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Post ID required']);
    exit;
}

$blog = new \LH\Blog();
$deleted = $blog->deletePost($postId);

if ($deleted) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete post']);
}