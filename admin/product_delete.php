<?php
require 'includes/auth.php';
require '../config/database.php';

$id = (int)$_GET['id'];
$conn->query("DELETE FROM products WHERE id=$id");

header('Location: products.php');
