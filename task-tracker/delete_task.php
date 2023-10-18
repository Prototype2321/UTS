<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
  <title>Document</title>
</head>
<body>

<ul class="mt-4">
<?php
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: to-do.php");
    exit;
}

// Pastikan ID tugas yang akan dihapus disediakan melalui parameter GET
if (isset($_GET["id"])) {
    $task_id = $_GET["id"];

    // Database connection setup (gantilah dengan koneksi sesuai dengan Anda)
    $db = new mysqli("localhost", "root", "", "task_tracker");

    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Dapatkan user_id dari sesi
    $email = $_SESSION["email"];
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    // Hapus tugas dari database
    $sql = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        header("Location: to-do.php");
        exit;
    } else {
        echo "Error deleting task: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Task ID is not provided.";
}
?>

</body>
</html>