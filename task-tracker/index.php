<!DOCTYPE html>
<html>
<head>
    <title>Login - Task Tracker</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <!-- Include your custom CSS file -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.15/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-4 bg-white rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4">Login</h2>
        <?php
        // Database connection setup (replace with your connection code)
        $db = new mysqli("localhost", "root", "", "task_tracker");

        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }

        // Initialize the $login_berhasil variable
        $login_berhasil = false;

        // Function to check user login
        function check_login($email, $password, $db) {
            // Sanitize user input (optional)
            $email = mysqli_real_escape_string($db, $email);

            // Query to fetch user's hashed password from the database
            $query = "SELECT password FROM users WHERE email = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            
            if ($stmt->fetch() && password_verify($password, $hashed_password))  {
                // Password is correct; login successful
                return true;
            } else {
                // Invalid email or password
                return false;
            }
        }

        // Process the form when submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $password = $_POST["password"];

            // Check login using the function
            $login_berhasil = check_login($email, $password, $db);

            if ($login_berhasil) {
                session_start();
                $_SESSION["email"] = $email;
                header("Location: to-do.php");
                exit;
            } else {
                echo "<p>Login failed. Please try again.</p>";
            }
        }
        ?>
         <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email:</label>
                <input type="email" class="form-input mt-1 block w-full" name="email" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password:</label>
                <input type="password" class="form-input mt-1 block w-full" name="password" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">Login</button>

            <p class="mt-3 mb-0 text-gray-600">Don't have an account? <a href="register.php" class="text-blue-500">Register here</a></p>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>
</body>
</html>