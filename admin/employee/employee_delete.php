<?php
require '../includes/auth.php';
require '../../config/database.php';

$id = (int)$_GET['id'];
$conn->query("DELETE FROM employees WHERE id=$id");

header('Location: employees.php');