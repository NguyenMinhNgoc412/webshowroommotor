<?php
require 'includes/auth.php';
require '../config/database.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("DELETE FROM customers WHERE id=$id");
}

header("Location: customers.php");
exit;
