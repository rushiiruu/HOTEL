<?php
    /**
     * Purpose:
     *   - Displays and manages the logged-in user's account information for La Ginta Real Hotel.
     *   - Allows users to view their profile details (first name, last name, username, birthday).
     *   - Provides an edit mode for users to update their account information and password.
     *   - Handles form validation and updates user data in the 'UserAccount' database table.
 */
    session_start();
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
    
    if (!$username) {
        // Redirect to login if no user is logged in
        header("Location: login.php");
        exit();
    }
    
    $servername = "localhost";
    $dbuser = "root";
    $password = "";
    $dbname = "hotel_db";
    $errorMsg = [];
    $successMsg = "";
    
    // Connect to MySQL server and select the database
    $conn = new mysqli($servername, $dbuser, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Get user information
    $stmt = $conn->prepare("SELECT * FROM UserAccount WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Check if we're in edit mode
    $editMode = isset($_GET['edit']) && $_GET['edit'] == '1';
    
    // Check for success message
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        $successMsg = "Your account has been updated successfully!";
    }
    
    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        $Fname = $_POST['Fname'];
        $Lname = $_POST['Lname'];
        $newUsername = $_POST['username'];
        $password = $_POST['password'];
        $Birthday = $_POST['Birthday'];
        
        // Validate inputs
        if (empty($Fname)) {
            $errorMsg[] = "First name is required";
        }
        
        if (empty($Lname)) {
            $errorMsg[] = "Last name is required";
        }
        
        if (empty($newUsername)) {
            $errorMsg[] = "Username is required";
        }
        
        // Check if the new username already exists (only if username was changed)
        if ($newUsername !== $username) {
            $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM UserAccount WHERE username = ? AND username != ?");
            $checkStmt->bind_param("ss", $newUsername, $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $row = $checkResult->fetch_assoc();
            
            if ($row['count'] > 0) {
                $errorMsg[] = "Username already exists. Please choose another one.";
            }
            $checkStmt->close();
        }
        
        // If no errors, update the database
        if (empty($errorMsg)) {
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
            if ($newUsername !== $username) {
                $fieldsToUpdate[] = "username = ?";
                $params[] = $newUsername;
                $types .= "s";
            }
            if (!empty($password)) { // Only update password if a new one is provided
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
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
                $params[] = $username; 
                $types .= "s";
        
                $sql = "UPDATE UserAccount SET " . implode(", ", $fieldsToUpdate) . " WHERE username = ?";
                $updateStmt = $conn->prepare($sql);
                $updateStmt->bind_param($types, ...$params);
        
                if ($updateStmt->execute()) {
                    // Update session variable if the username was changed
                    if ($newUsername !== $username) {
                        $_SESSION['username'] = $newUsername;
                    }
                    $updateStmt->close();
                    header("Location: UserAccount.php?success=1");
                    exit();
                } else {
                    $errorMsg[] = "Error updating record: " . $updateStmt->error;
                    $updateStmt->close();
                }
            } else {
                $successMsg = "No changes were made.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? 'Edit Account' : 'My Account'; ?> | Hotel Management</title>
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
    <link rel="stylesheet" href="styles/User.css">
</head>
<body>
    <?php include 'NavbarNoBack.php'; ?>
    
    <section class="container">
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

        <?php if (!$editMode): ?>
        <!-- View Account Mode -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user"></i>
                <h2>My Account</h2>
            </div>
            
            <div class="view-account">
                <div class="account-detail">
                    <strong>First Name:</strong>
                    <span><?php echo htmlspecialchars($user['Fname'] ?? ''); ?></span>
                </div>
                <div class="account-detail">
                    <strong>Last Name:</strong>
                    <span><?php echo htmlspecialchars($user['Lname'] ?? ''); ?></span>
                </div>
                <div class="account-detail">
                    <strong>Username:</strong>
                    <span><?php echo htmlspecialchars($user['username'] ?? ''); ?></span>
                </div>
                <div class="account-detail">
                    <strong>Birthday:</strong>
                    <span><?php echo htmlspecialchars($user['Birthday'] ?? ''); ?></span>
                </div>
                
                <div class="account-actions">
                    <a href="?edit=1" class="btn btn-primary">Edit Account</a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Edit Account Mode -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit"></i>
                <h2>Edit Your Account</h2>
            </div>
            
            <form action="" method="post" id="account-form">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="Fname">First Name</label>
                            <input type="text" class="form-control" id="Fname" name="Fname" value="<?php echo htmlspecialchars($user['Fname'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="Lname">Last Name</label>
                            <input type="text" class="form-control" id="Lname" name="Lname" value="<?php echo htmlspecialchars($user['Lname'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
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
                    <a href="UserAccount.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update" class="btn btn-primary">Update Account</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </section>
   
    <?php include 'Footer.php'; ?>
    
    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            
            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    // Toggle eye icon
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>
</html>