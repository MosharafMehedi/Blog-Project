<?php
require_once 'auth.php';

session_start();
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;