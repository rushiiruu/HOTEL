<?php
    session_start();
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    
    $servername = "localhost";
    $dbuser = "root";
    $password = "";
    $dbname = "hotel_db"; // Database name
    $errorMsg = [];
    $successMsg = "";
    
    // Connect to MySQL server and select the database
    $conn = new mysqli($servername, $dbuser, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $user = $conn->query("SELECT * FROM UserAccount WHERE username = '$username'")->fetch_assoc();

    //update the user account db with only the fields that are not empty
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $Fname = $_POST['Fname'];
        $Lname = $_POST['Lname'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $Birthday = $_POST['Birthday'];
    
        // Build the SQL query dynamically based on changed fields
        $fieldsToUpdate = [];
        $params = [];
        $types = "";
    
        if ($Fname !== $user['Fname']) {
            $fieldsToUpdate[] = "Fname = ?";
            $params[] = $Fname;
            $types .= "s";
        }
        if ($Lname !== $user['Lname']) {
            $fieldsToUpdate[] = "Lname = ?";
            $params[] = $Lname;
            $types .= "s";
        }
        if ($username !== $user['username']) {
            $fieldsToUpdate[] = "username = ?";
            $params[] = $username;
            $types .= "s";
        }
        if (!empty($password)) { // Only update password if a new one is provided
            $fieldsToUpdate[] = "password = ?";
            $params[] = $password;
            $types .= "s";
        }
        if ($Birthday !== $user['Birthday']) {
            $fieldsToUpdate[] = "Birthday = ?";
            $params[] = $Birthday;
            $types .= "s";
        }
    
        // If there are fields to update, execute the query
        if (!empty($fieldsToUpdate)) {
            $params[] = $_SESSION['username']; // Add the WHERE clause parameter
            $types .= "s";
    
            $sql = "UPDATE UserAccount SET " . implode(", ", $fieldsToUpdate) . " WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
    
            if ($stmt->execute()) {
                // Update session variable if the username was changed
                if ($username !== $user['username']) {
                    $_SESSION['username'] = $username;
                }
                header("Location: UserAccount.php?success=1");
                exit();
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "No changes were made.";
        }
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <div>
            <div>
                <div>
                    <label for="">First Name</label>
                    <input type="text" name="Fname" value="<?php echo htmlspecialchars($user['Fname'] ?? ''); ?>">
                </div>
                <div>
                    <label for="">Last Name</label>
                    <input type="text" name="Lname" value="<?php echo htmlspecialchars($user['Lname'] ?? ''); ?>">
                </div>
            <div>
                <label for="">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>">
            </div>
            <div>
                <label for="">Password</label>
                <input type="password" name="password">
            </div>
            <div>
                <label for="">Birthday</label>
                <input type="date" name="Birthday" value="<?php echo htmlspecialchars($user['Birthday'] ?? ''); ?>">
            </div>
        </div>

        <button type="submit" namme=""update>Update</button>
    </form>
</body>
</html>