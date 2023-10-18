<!DOCTYPE html>
<html>
<head>
    <title>Add Task - Task Tracker</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-4 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Add Task</h2>
        <?php
        session_start();

        // Check if the user is logged in
        if (!isset($_SESSION["email"])) {
            header("Location: to-do.php");
            exit;
        }
        
        // Database connection setup (replace with your connection code)
        $db = new mysqli("localhost", "root", "", "task_tracker");
        
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }
        
        // Process the form when submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["title"]) && isset($_POST["description"]) && isset($_POST["due_date"])) {
                $title = $_POST["title"];
                $description = $_POST["description"];
                $due_date = $_POST["due_date"];
                
                // Get the user's ID from the session
                $email = $_SESSION["email"];
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->bind_result($user_id);
                $stmt->fetch();
                $stmt->close();
                
                // Insert the task into the tasks table
                $sql = "INSERT INTO tasks (title, description, due_date, user_id) VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($sql);
                
                if (!$stmt) {
                    echo "<p>Error preparing statement: " . $db->error . "</p>";
                } else {
                    $stmt->bind_param("sssi", $title, $description, $due_date, $user_id);
                    
                    if ($stmt->execute()) {
                        echo "<p>Task added successfully!</p>";
                    } else {
                        echo "<p>Error adding task: " . $stmt->error . "</p>";
                    }
                    
                    $stmt->close();
                }
            } else {
                echo "<p>Required form fields are missing.</p>";
            }
        }
        
        ?>
         <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
                <input type="text" name="title" class="form-input mt-1 w-full" required>
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea name="description" class="form-textarea mt-1 w-full"></textarea>
            </div>

            <div class="mb-4">
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date:</label>
                <input type="date" name="due_date" class="form-input mt-1 w-full" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">Add Task</button>
        </form>
        <p class="mt-4"><a href="to-do.php" class="text-blue-500">Back to Task List</a></p>
    </div>
</body>
</html>
