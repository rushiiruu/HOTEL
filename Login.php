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
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SIGNUP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  $Fname = $_POST['firstName'];
  $Lname = $_POST['lastName'];
  $username = $_POST['signupUser'];
  $password = $_POST['signupPass'];
  $usertype = $_POST['usertype'];

  // Default birthday if not provided
  $Birthday = $_POST['Birthday'] ?? date('Y-m-d');

  $stmt = $conn->prepare("SELECT username FROM UserAccount WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $usertype = "Guest";

  if ($result->num_rows > 0) {
      $errorMsg['signup'] = "Username already exists.";
  } else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO UserAccount (Fname, Lname, password, Birthday, username, usertype) VALUES (?,?, ?, ?, ?, ?)");
      $stmt->bind_param("ssssss", $Fname, $Lname, $hashedPassword, $Birthday, $username, $usertype);
      if ($stmt->execute()) {
        $successMsg = "Account successfully created.";
        header("Location: Login.php?form=login&success=1");
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

    $stmt = $conn->prepare("SELECT password, usertype FROM UserAccount WHERE username = ?");
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

  <link rel="stylesheet" href="styles/Login.css">
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
          <div class="row">
            <div class="field">
              <label for="signupEmail">Birthday</label>
              <input type="date" required name="Birthday" id="signupEmail" />
            </div>
            <div class="field">
              <label for="signupPass">Password</label>
              <input type="password" required name="signupPass" id="signupPass" />
            </div>
          </div>

          <div class="checkbox-container">
            <input type="checkbox" id="terms" required >
            <label for="terms">I agree to the terms and conditions</label>
          </div>
          <button type="submit" name="create" >Sign up</button>

          <p class="signup-text">
            Already have an account?
            <a href="#" id="login-button" onclick="toggleForm()">Log in here</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div id="popup-notification" class="popup">
  Account successfully created.
</div>
<script>
  setTimeout(() => {
    document.getElementById('popup-notification').style.opacity = '0';
  }, 3000);
</script>
<?php endif; ?>

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