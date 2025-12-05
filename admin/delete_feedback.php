<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$id = $_GET['id'];

// Path to feedback.json
$file = __DIR__ . '/../data/feedback.json';

// Read existing feedbacks
$feedbacks = [];
if (file_exists($file)) {
    $feedbacks = json_decode(file_get_contents($file), true);
}

// Filter out the deleted one
$new_list = [];
foreach ($feedbacks as $f) {
    if ($f['id'] != $id) {
        $new_list[] = $f;
    }
}

// Save updated list back to JSON
file_put_contents($file, json_encode($new_list, JSON_PRETTY_PRINT));

// Redirect back to dashboard
header("Location: dashboard.php?deleted=1");
exit;
