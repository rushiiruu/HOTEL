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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        
  nav {
    position: fixed;
    top: 0;
    margin-top: 0;
    left: 0;
    height: 90px;
    right: 0;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0;
    z-index: 10; 
    transition: top 0.3s ease, background-color 0.3s ease, padding 0.3s ease;
  }
  

nav img {
    width: 40px;
    height: 40px;
}

nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-size: 12px;
    font-weight: bold;
}

nav button {
    padding: 5px 20px;
    background-color: white;
    color: black;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 12px;
    margin-left: 30px;
    margin-right: 15px;
}

.hotel-name {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 24px;
    font-weight: bold;
    color: white;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 400;
    font-style: normal;
    text-align: center;
  }
  
  .hotel-location {
    display: block; 
    font-size: 14px;
    font-weight: normal;
    margin-top: 0px;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 400;
    font-style: normal;
    text-align: center;
  }
  

  .nav-right {
    display: flex;
    align-items: center;
}

.nav-right a {
    margin-right: 20px;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 400;
    font-style: normal;
    font-size: 16px;
    text-decoration: none;
    color: white;
    text-align: center;
}



.hidden-menu {
    display: none;
    flex-direction: column;
    background-color: white;
    position: absolute;
    top: 60px;
    right: 0;
    padding: 10px;
    border: 1px solid #ccc;
  }
  
  .hidden-menu.show {
    display: flex;
  }
  
.menu-icon i {
  font-size: 2rem; 
}

nav.scrolled {
  background-color: black;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  padding-top: 0;
  padding-bottom: 0;
}

  

.hidden-on-load {
    opacity: 0;
    transition: opacity 0.5s ease;
  }
  

  .show-on-scroll {
    opacity: 1;
    transition: opacity 1.2s ease; 
  }

  .side-menu {
    position: fixed;
    top: 0;
    left: -290px; 
    width: 250px;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.95);
    color: white;
    padding-top: 80px;
    display: flex;
    flex-direction: column;
    align-items: start;
    padding-left: 30px;
    transition: left 0.4s ease; 
    z-index: 100;
  }
  
  .side-menu.show {
    left: 0;
  }
  
  .side-menu a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    margin-bottom: 20px;
    font-weight: bold;
  }
  
  .close-menu {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }
  
  
  .close-menu i {
    font-size: 30px; 
  }

  .side-menu-name {
    margin-right: 120px; /* Move the text further to the left */
    font-size: 18px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    margin-top: 20px;
  }
  li {
    text-decoration: none;
    margin-top: 20px;
  }

 

#sideMenu ul {
  list-style-type: none; 
  padding: 0;
  margin: 0;
}

#sideMenu ul li {
  margin-bottom: 10px; 
}

#sideMenu ul li a {
  text-decoration: none;
  display: block;
  padding: 10px 0;
  border-bottom: 1px solid transparent; 
  transition: border-color 0.3s ease;
}

#sideMenu ul li a:hover {
  border-bottom: 1px solid white; 
  padding-left: 20px;
}

#Login-dialog h3{
  text-align: center;
  margin-top: 20px;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 600;
}


.side-menu-bottom {
  margin-top: auto;
  padding-bottom: 30px;
  padding-left: 10px;
}

.side-menu-bottom a {
  display: flex;
  align-items: center;
  color: white;
  text-decoration: none;
  font-size: 20px;
  gap: 10px;
  font-weight: bold;
  margin-bottom: 90px;
}

.menu-icon i {
  font-size: 2rem; 
}


  nav {
    position: fixed;
    top: 0;
    margin-top: 0;
    left: 0;
    height: 90px;
    right: 0;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0;
    z-index: 10; 
    transition: top 0.3s ease, background-color 0.3s ease, padding 0.3s ease;
  }
  

nav img {
    width: 40px;
    height: 40px;
}

nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    font-size: 12px;
    font-weight: bold;
}

nav button {
    padding: 5px 20px;
    background-color: white;
    color: black;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    font-size: 12px;
    margin-left: 30px;
    margin-right: 15px;
}

.hotel-name {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    font-size: 24px;
    font-weight: bold;
    color: white;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 400;
    font-style: normal;
    text-align: center;
  }
  
  .hotel-location {
    display: block; 
    font-size: 14px;
    font-weight: normal;
    margin-top: 0px;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 400;
    font-style: normal;
    text-align: center;
  }
  

  .nav-right {
    display: flex;
    align-items: center;
}

.nav-right a {
    margin-right: 20px;
    font-family: "Cormorant Garamond", serif;
    font-optical-sizing: auto;
    font-weight: 400;
    font-style: normal;
    font-size: 16px;
    text-decoration: none;
    color: white;
    text-align: center;
}



.hidden-menu {
    display: none;
    flex-direction: column;
    background-color: white;
    position: absolute;
    top: 60px;
    right: 0;
    padding: 10px;
    border: 1px solid #ccc;
  }
  
  .hidden-menu.show {
    display: flex;
  }
  
.menu-icon i {
  font-size: 2rem; 
}

nav.scrolled {
  background-color: black;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  padding-top: 0;
  padding-bottom: 0;
}

  

.hidden-on-load {
    opacity: 0;
    transition: opacity 0.5s ease;
  }
  

  .show-on-scroll {
    opacity: 1;
    transition: opacity 1.2s ease; 
  }

  .side-menu {
    position: fixed;
    top: 0;
    left: -290px; 
    width: 250px;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.95);
    color: white;
    padding-top: 80px;
    display: flex;
    flex-direction: column;
    align-items: start;
    padding-left: 30px;
    transition: left 0.4s ease; 
    z-index: 100;
  }
  
  .side-menu.show {
    left: 0;
  }
  
  .side-menu a {
    color: white;
    text-decoration: none;
    font-size: 18px;
    margin-bottom: 20px;
    font-weight: bold;
  }
  
  .close-menu {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }
  
  
  .close-menu i {
    font-size: 30px; 
  }

  .side-menu-name {
    margin-right: 120px; /* Move the text further to the left */
    font-size: 18px;
    font-weight: bold;
    color: white;
    text-decoration: none;
    margin-top: 20px;
  }
  li {
    text-decoration: none;
    margin-top: 20px;
  }

 

#sideMenu ul {
  list-style-type: none; 
  padding: 0;
  margin: 0;
}

#sideMenu ul li {
  margin-bottom: 10px; 
}

#sideMenu ul li a {
  text-decoration: none;
  display: block;
  padding: 10px 0;
  border-bottom: 1px solid transparent; 
  transition: border-color 0.3s ease;
}

#sideMenu ul li a:hover {
  border-bottom: 1px solid white; 
  padding-left: 20px;
}

#Login-dialog h3{
  text-align: center;
  margin-top: 20px;
  font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 600;
}



    </style>
</head>
<body>
  <nav id="navbar" class="scrolled">

  <a href="#" class="menu-icon" onclick="toggleMenu()">
        <i class="bi bi-list" id="menu"></i>
      </a>

      <a href="#" class="hotel-name">
        LA GINTA REAL
        <span class="hotel-location">PHILIPPINES</span>
      </a>

      <div class="nav-right">
        <a href="ManageReservation.php">MY RESERVATION</a>
        <a href="Rooms&Suites.php">BOOK</a>
      </div>
    </nav>
    <div id="sideMenu" class="side-menu">

  <!-- User icon and name at the top -->
  <div class="user-info">
    <i class="bi bi-person-circle" id="user-icon"></i>
    <span class="username">
      <?php echo $username ? htmlspecialchars($username) : "Guest"; ?>
    </span>
  </div>

  <!-- Close button -->
  <button class="close-menu" onclick="toggleMenu()">
    <i class="bi bi-x"></i>
  </button>

  <!-- Navigation menu -->
  <ul>
    <li><a href="Home.php">Home</a></li>
    <li><a href="Rooms&Suites.php">Rooms & Suites</a></li>
    <li><a href="#">Exclusive Offers</a></li>
    <li><a href="AboutUs.php">About Us</a></li>
    <li><a href="#">Contact Us</a></li>
    <li><a href="ManageReservation.php">My Reservation</a></li>
  </ul>

  <!-- Login/Logout at the bottom -->
  <div class="side-menu-bottom">
    <?php if ($username): ?>
      <a href="Logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    <?php else: ?>
      <a href="Login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
    <?php endif; ?>
  </div>
</div>

    
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
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update" class="btn btn-primary">Update Account</button>
                </div>
            </form>
        </div>
    </div>
    <script>
// Menu Toggle Functionality
        function toggleMenu() {
            var sideMenu = document.getElementById('sideMenu');
            sideMenu.classList.toggle('show');
        }

        function openModal(roomID) {
          // Show the modal
          const modal = document.getElementById('cancel-modal');
          modal.style.display = 'flex';
      
          // Set the room ID in the hidden input
          const roomIdInput = document.getElementById('room-id-input');
          roomIdInput.value = roomID;
        }
      
        function closeModal() {
          // Hide the modal
          const modal = document.getElementById('cancel-modal');
          modal.style.display = 'none';
        }
      
        // Close the modal if the user clicks outside of it
        window.onclick = function(event) {
          const modal = document.getElementById('cancel-modal');
          if (event.target === modal) {
            closeModal();
          }
        };

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