<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_db";
$errorMsg = [];
$successMsg = "";

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create DB if not exists
$db_check = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
if ($db_check->num_rows == 0) {
    $conn->query("CREATE DATABASE $dbname");
}

// Reconnect to the created DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Create UserAccount table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS UserAccount (
    UserID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Fname VARCHAR(50) NOT NULL,
    Lname VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    usertype VARCHAR(10) NOT NULL,
    Birthday DATE
)");

// SIGNUP
// SIGNUP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  $Fname = $_POST['firstName'];
  $Lname = $_POST['lastName'];
  $username = $_POST['signupUser'];
  $password = $_POST['signupPass'];
  $usertype = $_POST['usertype'];

  // Default birthday if not provided
  $Birthday = date('2000-01-01');

  $stmt = $conn->prepare("SELECT username FROM UserAccount WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $errorMsg['signup'] = "Username already exists.";
  } else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO UserAccount (Fname, Lname, password, Birthday, username, usertype) VALUES (?,?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssss", $Fname, $Lname, $hashedPassword, $Birthday, $username, $usertype);
      if ($stmt->execute()) {
        echo "<script>
            alert('Account Successfully Made');
            window.location.href = 'Login.php?form=login';
        </script>";
        exit();
      } else {
          $errorMsg['signup'] = "Error creating account.";
      }
  }
}


// LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['loginUser'];
    $password = $_POST['loginPass'];

    $stmt = $conn->prepare("SELECT password FROM UserAccount WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $usertype = $row['usertype'];

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["username"] = $username;
            if ($usertype == "Admin") {
                $_SESSION["usertype"] = "Admin";
                header("Location: Dashboard.php");
                exit();
            } else {
                $_SESSION["usertype"] = "Guest";
                header("Location: Home.php");
                exit();
            }

        } else {
            $errorMsg['login'] = "Incorrect password.";
        }
    } else {
        $errorMsg['login'] = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet" />
<style>
  body, html {
    margin: 0;
    padding: 0;
    height: 100%;
    width: 100%;
    font-family: "Quicksand", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-size: 10px;
  line-height: 1.6;
    

}

.main-container {
    height: 100vh;
    width: 100%;
    background-image: url("https://drive.google.com/thumbnail?id=1_7EN-fetbc-rp-Kx8_KeB_6E-dSNNETQ&sz=s800");
    background-repeat: repeat;
    background-size: auto;
    display: flex;
    align-items: center;
    
    justify-content: center;
  }
  
  h2 {
    text-align: center;
    font-family: "Cormorant Garamond", serif;
  font-optical-sizing: auto;
  font-weight: 600;
  font-style: italic;
  font-size: 20px;
  }
  
  .container {
    display: flex;
    height: 500px;
    width: 1000px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    overflow: hidden;
    background-color: white;
  }
  
  .left-panel {
    flex: 1;
    padding: 0;
    margin: 0;
  }
  
  .left-panel img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block; /* removes default inline spacing */
  }
  
  
  .right-panel {
    flex: 1;
    background-color: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 20px;
  }
  
  .right-panel h2 {
    margin-bottom: 20px;
  }
  
  .right-panel form {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }
  
  .right-panel input {
    margin-bottom: 10px;
    padding: 5px;
    font-size: 12px;
  }
  
  input[type="text"],
  input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    transition: border-color 0.3s ease;
  }

  .right-panel button {
    padding: 10px;
    font-size: 16px;
    background-color: teal;
    color: white;
    border: none;
    cursor: pointer;
  }
  
  .right-panel button:hover {
    background-color: darkcyan;
  }

  .right-panel input[type="text"],
.right-panel input[type="password"] {
  border: none;
  border-bottom: 1px solid #333;
  background: transparent;
  padding: 6px 4px;
  font-size: 14px;
  margin-bottom: 25px;
  outline: none;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.right-panel label {
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 5px;
  display: block;
}

.right-panel .checkbox-container {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.right-panel .checkbox-container input {
  margin-right: 8px;
}

.right-panel button {
  background-color: #1a1a1a;
  color: white;
  padding: 10px;
  font-size: 14px;
  border: none;
  border-radius: 6px;
  text-transform: uppercase;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.right-panel button:hover {
  background-color: #333;
}

.signup-text {
    margin-top: 20px;
    font-size: 14px;
  }
  
  .signup-text a {
    color: teal;
    text-decoration: none;
    margin-left: 5px;
    font-weight: 600;
    cursor: pointer;
  }
  
  .signup-text a:hover {
    text-decoration: underline;
  }
  
  .name-row {
    display: flex;
    gap: 6px; /* was 10px */
    margin-bottom: 10px;
  }
  
  .name-field input {
    padding: 4px;
    font-size: 12px; /* was 13px */
  }
  
  .name-field label {
    font-size: 10px; /* was 11px */
    margin-bottom: 2px;
  }
  
  .name-field {
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  

  .row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
  }
  
  .field {
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  
  .field label {
    font-size: 10px;
    margin-bottom: 3px;
  }
  
  .field input {
    padding: 6px;
    font-size: 12px;
    border-bottom: 1px solid #333;
  }
  
</style>
</head>
<body>
<div class="main-container">
  <div class="container">
    <!-- Left Panel -->
    <div class="left-panel">
      <img src="https://images.trvl-media.com/lodging/1000000/440000/438500/438418/47e87212.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill" alt="Login Image" />
    </div>

    <!-- Right Panel -->
    <div class="right-panel">
      <!-- Login Form -->
      <div id="login-form" style="<?php echo isset($_POST['create']) ? 'display:none;' : 'display:block;'; ?>">
        <h2>Login to your <br>La Ginta Real One <br>Loyalty Account</h2>
        <?php if (isset($errorMsg['login'])) echo "<p style='color:red;'>{$errorMsg['login']}</p>"; ?>
        <form action="" method="post">
          <label for="loginUser">Username</label>
          <input type="text" required name="loginUser" id="loginUser" />

          <label for="loginPass">Password</label>
          <input type="password" required name="loginPass" id="loginPass" />

          <div class="checkbox-container">
            <input type="checkbox" id="remember">
            <label for="remember">Remember me</label>
          </div>

          <button type="submit" name="login">Log in</button>

          <p class="signup-text">
            Don't have an account?
            <a href="#" id="create-button" onclick="toggleForm()">Create here</a>
          </p>
        </form>
      </div>

      <!-- Sign Up Form -->
      <div id="signup-form" style="<?php echo isset($_POST['create']) ? 'display:block;' : 'display:none;'; ?>">
        <h2>Create Account</h2>
        <?php if (isset($errorMsg['signup'])) echo "<p style='color:red;'>{$errorMsg['signup']}</p>"; ?>
        <?php if ($successMsg && isset($_POST['create'])) echo "<p style='color:green;'>$successMsg</p>"; ?>
        <form action="" method="post">
          <div class="name-row">
            <div class="name-field">
              <label for="firstName">First Name</label>
              <input type="text" required name="firstName" id="firstName" />
            </div>
            <div class="name-field">
              <label for="lastName">Last Name</label>
              <input type="text" required name="lastName" id="lastName" />
            </div>
          </div>

          <label for="signupUser">Username</label>
          <input type="text" required name="signupUser" id="signupUser" />
          <label for="">User-type</label>
          <select name="usertype" id="">
            <option value="Guest">Guest</option>
            <option value="Admin">Admin</option>
          </select>

          <div class="row">
            <div class="field">
              <label for="signupEmail">Email</label>
              <input type="text" required name="signupEmail" id="signupEmail" />
            </div>
            <div class="field">
              <label for="signupPass">Password</label>
              <input type="password" required name="signupPass" id="signupPass" />
            </div>
          </div>

          <div class="checkbox-container">
            <input type="checkbox" id="terms">
            <label for="terms">I agree to the terms and conditions</label>
          </div>

          <button type="submit" name="create">Sign up</button>

          <p class="signup-text">
            Already have an account?
            <a href="#" id="login-button" onclick="toggleForm()">Log in here</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleForm() {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    if (loginForm.style.display === 'none') {
      loginForm.style.display = 'block';
      signupForm.style.display = 'none';
    } else {
      loginForm.style.display = 'none';
      signupForm.style.display = 'block';
    }
  }
</script>
</body>
</html>