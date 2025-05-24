<?php
namespace LH;

class Blog {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllPosts($limit = 5, $offset = 0) {
        $sql = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT ? OFFSET ?";
        return $this->db->fetchAll($sql, [$limit, $offset]);
    }

    public function getPost($id) {
        $sql = "SELECT * FROM blogs WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function createPost($title, $description, $image = null) {
        $sql = "INSERT INTO blogs (title, description, image) VALUES (?, ?, ?)";
        $this->db->query($sql, [$title, $description, $image]);
        return $this->db->lastInsertId();
    }

    public function updatePost($id, $title, $description, $image = null) {
        if ($image) {
            $sql = "UPDATE blogs SET title = ?, description = ?, image = ? WHERE id = ?";
            $this->db->query($sql, [$title, $description, $image, $id]);
        } else {
            $sql = "UPDATE blogs SET title = ?, description = ? WHERE id = ?";
            $this->db->query($sql, [$title, $description, $id]);
        }
        return true;
    }

    public function deletePost($id) {
        $sql = "DELETE FROM blogs WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function getPostsPerPage() {
        $sql = "SELECT posts_per_page FROM settings WHERE id = 1";
        $result = $this->db->fetchOne($sql);
        return $result['posts_per_page'] ?? 5;
    }

    public function setPostsPerPage($count) {
        $sql = "UPDATE settings SET posts_per_page = ? WHERE id = 1";
        return $this->db->query($sql, [$count]);
    }

    public function getTotalPosts() {
        $sql = "SELECT COUNT(*) as total FROM blogs";
        $result = $this->db->fetchOne($sql);
        return $result['total'] ?? 0;
    }
}