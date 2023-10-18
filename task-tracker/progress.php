<?php
session_start();
$db = new mysqli("localhost", "root", "", "task_tracker");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan pengguna telah login
    if (!isset($_SESSION["email"])) {
        echo "Anda harus login terlebih dahulu.";
        exit;
    }

    $user_id = $_SESSION["user_id"];

    // Loop melalui tugas-tugas yang ditampilkan
    $stmt = $db->prepare("SELECT id, user_id FROM tasks WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    while ($row = $result->fetch_assoc()) {
        $task_id = $row["id"];
        $progress_name = "task_progress_" . $task_id; // Nama unik untuk dropdown progres

        // Periksa apakah ada data POST untuk progres tugas ini
        if (isset($_POST[$progress_name])) {
            $new_progress = $_POST[$progress_name];

            // Perbarui progres dalam database
            $stmt = $db->prepare("UPDATE tasks SET progress = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("sii", $new_progress, $task_id, $user_id);

            if ($stmt->execute()) {
                header("Location: to-do.php");
                exit;
                
            } else {
                echo "Error updating progress for Task ID: " . $task_id . " - " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>
