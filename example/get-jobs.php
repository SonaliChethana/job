<?php
require_once 'utils/db-conn.php';

header('Content-Type: application/json');

$category = $_GET['category'];

$stmt = $conn->prepare("SELECT * FROM postjob WHERE jobCategory = ?");
$stmt->bind_param("s", $category);
$stmt->execute();

$result = $stmt->get_result();
$jobs = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();

echo json_encode($jobs);
?>
