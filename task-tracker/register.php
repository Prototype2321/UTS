<!DOCTYPE html>
<html>
<head>
    <title>Register - Task Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-4 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Register</h2>
        <?php
        // Database connection setup (replace with your connection code)
        $db = new mysqli("localhost", "root", "", "task_tracker");

        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        // Process the form when submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = password_hash($_POST["password"], PASSWORD_BCRYPT) ;
            

            // Validate the form data
            if (empty($name) || empty($email) || empty($password)) {
                echo "All fields are required.";
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Invalid email format.";
            } else {
                // Check if the email address is already registered
                $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    echo "Email address is already registered.";
                } else {
                    // Insert user data into the database (use prepared statements for security)
                    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $name, $email, $password);

                    if ($stmt->execute()) {
                        // Registration successful, redirect to login page
                        header("Location: index.php");
                        exit;
                    } else {
                        // Handle database insertion error
                        echo "Error: " . $stmt->error;
                    }
                }

                // Close the prepared statement
                $stmt->close();
            }
        }

        // Close the database connection
        $db->close();
        ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                <input type="text" class="form-input mt-1 block w-full" name="name" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email:</label>
                <input type="email" class="form-input mt-1 block w-full" name="email" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" class="form-input mt-1 block w-full" name="password" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">Register</button>
        </form>
        <p class="mt-4">Already have an account? <a href="index.php" class="text-blue-500">Login here</a></p>
    </div>
</body>
</html>