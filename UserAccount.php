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
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
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
            $params[] = $hashedPassword;
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
    <title>Edit Account | Hotel Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Quicksand:wght@300..700&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&family=Roboto:ital,wght@0,100..900;1,100..900&family=Satisfy&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />
    <style>
        :root {
            --primary-color: #4a6fa5;
            --primary-hover:rgb(15, 17, 20);
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --white: #fff;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            margin-top: 150px;
        }
        
        .card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }
        
        .card-header h2 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin: 0;
        }
        
        .card-header i {
            font-size: 1.5rem;
            margin-right: 0.75rem;
            color: var(--primary-color);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.5rem;
        }
        
        .form-col {
            flex: 1;
            padding: 0 0.5rem;
            min-width: 250px;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: var(--border-radius);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
            outline: 0;
        }
        
        .btn {
            display: inline-block;
            font-weight: 500;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            cursor: pointer;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: var(--border-radius);
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            text-decoration: none;
        }
        
        .btn-primary {
            color: var(--white);
            background-color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-secondary {
            color: var(--white);
            background-color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        
        .required-field::after {
            content: "*";
            color: var(--danger-color);
            margin-left: 0.25rem;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .password-toggle i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary-color);
        }


    </style>
</head>
<body>
  <?php include 'NavbarNoBack.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit"></i>
                <h2>Edit Your Account</h2>
            </div>
            
            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1rem;">
                        <?php foreach ($errorMsg as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($successMsg); ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="post" id="account-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="Fname">First Name</label>
                            <input type="text" class="form-control" id="Fname" name="Fname" value="<?php echo htmlspecialchars($user['Fname'] ?? ''); ?>" >
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="Lname">Last Name</label>
                            <input type="text" class="form-control" id="Lname" name="Lname" value="<?php echo htmlspecialchars($user['Lname'] ?? ''); ?>" >
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" >
                </div>
                
                <div class="form-group">
                    <label for="password">New Password <small>(Leave blank to keep current password)</small></label>
                    <div class="password-toggle">
                        <input type="password" class="form-control" id="password" name="password">
                        <i class="far fa-eye" id="togglePassword"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="Birthday">Birthday</label>
                    <input type="date" class="form-control" id="Birthday" name="Birthday" value="<?php echo htmlspecialchars($user['Birthday'] ?? ''); ?>">
                </div>
                
                <div class="form-actions">
                    <a href="Home.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update" class="btn btn-primary">Update Account</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Form validation
        document.getElementById('account-form').addEventListener('submit', function(event) {
            const fname = document.getElementById('Fname').value.trim();
            const lname = document.getElementById('Lname').value.trim();
            const username = document.getElementById('username').value.trim();
            
            if (fname === '' || lname === '' || username === '') {
                event.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    </script>
</body>
</html>