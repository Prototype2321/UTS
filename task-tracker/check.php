<!DOCTYPE html>
<html>
<head>
<title>Task Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="">
    <script src="script.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-4 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Task Tracker</h2>
       

        <div class="flex items-center justify-between">
    <form method="post" action="add_task.php">
        <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">Add Task</button>
    </form>
        </div>
    
    
        <ul class="mt-4 grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
    <?php
    session_start();
    $db = new mysqli("localhost", "root", "", "task_tracker");
    // Fetch and display tasks for the logged-in user
    $email = $_SESSION["email"]; // Get the user's email from the session
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();
    $_SESSION["user_id"] = $user_id;
    
    $sql = "SELECT COUNT(id) AS taskCount FROM tasks WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalTasksCount = $row['taskCount'];
    } else {
        $totalTasksCount = 0;
    }

    $sql = "SELECT id, title, description, progress,due_date ,is_completed FROM tasks WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="bg-white border rounded-lg p-4 shadow-md">';
            echo "<strong class='text-xl mb-2'>" . htmlspecialchars($row["title"]) . "</strong><br>";
            echo "Description: " . htmlspecialchars($row["description"]) . "<br>";
            echo "Due Date: " .htmlspecialchars($row["due_date"]). "<br>";
            echo '<div id="status_text_' . $row["id"] . '">Status: ' . ($row["is_completed"] ? 'Completed' : 'Not Completed') . '</div>';
            echo '<label class="checkbox-container">Mark as Completed';
            echo '<input type="checkbox" id="status_checkbox_' . $row["id"] . '" onclick="updateStatus(' . $row["id"] . ')" ' . ($row["is_completed"] ? 'checked' : '') . '>';
            echo '<span class="checkmark"></span>';
            echo '</label>';
            echo '<br>';
            // Dropdown Progres untuk Tugas Ini
            echo "Progress: " . ($row["progress"]);
            echo '<form method="post" action="progress.php">';
            echo '<input type="hidden" name="task_id" value="' . $row["id"] . '">';
            echo '<select name="task_progress_' . $row["id"] . '" onchange="updateProgress(' . $row["id"] . ')">';
            echo '<option value="Not yet started" ' . ($row["progress"] == "Not yet started" ? 'selected' : '') . '>Not yet started</option>';
            echo '<option value="In progress" ' . ($row["progress"] == "In progress" ? 'selected' : '') . '>In progress</option>';
            echo '<option value="Waiting on" ' . ($row["progress"] == "Waiting on" ? 'selected' : '') . '>Waiting on</option>';
            // Tambahkan opsi progres lainnya sesuai kebutuhan
            echo '</select>';
            echo '<button type="submit" name="update_progress" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Save</button>';
            echo '</form>';
        
           
            echo '<div class="mt-4">';
            echo '<a href="edit_task.php?id=' . $row["id"] . '" class="text-blue-500 hover:underline ml-2">Edit|</a>';
            echo '<a href="delete_task.php?id=' . $row["id"] . '" class="text-red-500 hover:underline">Delete|</a>';
            echo '</div>';
            echo "</div>";
        }
    } else {
        echo "<p>No tasks found.</p>";
        
    }


    $stmt->close();
    ?>
</ul>

<!-- Task Status Handling Code -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["task_completed"])) {
    // Get the task ID from the form
    $task_id = $_POST["task_completed"];

    // Toggle the status (completed or not completed) in the database
    $sql = "UPDATE tasks SET is_completed = NOT is_completed WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        echo "<p>Status updated successfully!</p>";
    } else {
        echo "<p>Error updating status: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>
    </div>

    <script>
        function updateStatus(taskId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var button = document.getElementById('status_button_' + taskId);
            var statusTextElement = document.getElementById('status_text_' + taskId);
            if (xhr.responseText === '1') {
                button.innerHTML = 'Mark as Completed';
                statusTextElement.innerHTML = 'Status: Completed';
            } else {
                button.innerHTML = 'Mark as Not Completed';
                statusTextElement.innerHTML = 'Status: Not Completed';
            }
        }
    };
    xhr.send('task_id=' + taskId);
}
   </script>
 
 <script>

        function updateProgress(taskId) {
            var newProgress = document.getElementById('task_progress_' + taskId).value;
            
            // Use AJAX to update the progress
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'progress.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // If the update was successful, you can update the UI
                    console.log('Progress updated successfully for Task ID: ' + taskId);
                    
                    // Update the button text based on the new progress
                    var button = document.getElementById('update_button_' + taskId);
                    var statusText = (newProgress === 'Completed') ? 'Not Completed' : 'Completed';
                    button.innerHTML = 'Mark as ' + statusText;
                    
                    // Update the 'Status' text on the page
                    var statusTextElement = document.getElementById('status_text_' + taskId);
                    statusTextElement.innerHTML = 'Status: ' + statusText;
                }
            };
    xhr.send('task_id=' + taskId + '&new_progress=' + newProgress);
}

</script>
<p class="mt-4">Total Tasks Available: <?php echo $totalTasksCount; ?></p>
</body>
</html>