<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task_id"])) {
    $task_id = $_POST["task_id"];

    $db = new mysqli("localhost", "root", "", "task_tracker");
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Fetch the current status of the task
    $stmt = $db->prepare("SELECT is_completed FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($current_status);
    $stmt->fetch();
    $stmt->close();

    // Toggle the status in the database
    $new_status = $current_status ? 0 : 1;
    $stmt = $db->prepare("UPDATE tasks SET is_completed = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("iii", $new_status, $task_id, $_SESSION["user_id"]);

    if ($stmt->execute()) {
        echo $new_status; // Return the new status (1 for completed, 0 for not completed)
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    $stmt->close();
    $db->close();
} else {
    echo "Invalid request.";
}
?>
