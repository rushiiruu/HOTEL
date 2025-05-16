<?php
/**
 * Purpose:
 *   - Provides login and signup functionality for La Ginta Real Hotel users.
 *   - Allows users to log in with their credentials or create a new account.
 *   - Handles authentication, session management, and redirects based on user type (Admin or Guest).
 *   - Integrates with the UserAccount database table for user management.
 */
session_start();

// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel_db";
$errorMsg = [];
$successMsg = "";

// Establish connection to MySQL server
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SIGNUP PROCESSING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
  // Retrieve signup form data
  $Fname = $_POST['firstName'];
  $Lname = $_POST['lastName'];
  $username = $_POST['signupUser'];
  $password = $_POST['signupPass'];

  // Use provided birthday or default to today
  $Birthday = $_POST['Birthday'] ?? date('Y-m-d');

  // Check if username already exists
  $stmt = $conn->prepare("SELECT username FROM UserAccount WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $usertype = "Guest";

  if ($result->num_rows > 0) {
        // Username taken

        $_SESSION['Error'] = "Username already exists.";
        include 'ErrorModule.php';
    } else {
        // Hash password and insert new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO UserAccount (Fname, Lname, password, Birthday, username, usertype) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $Fname, $Lname, $hashedPassword, $Birthday, $username, $usertype);
        if ($stmt->execute()) {
            // Signup successful, redirect to login with success message
            $successMsg = "Account successfully created.";
            header("Location: Login.php?form=login&success=1");
            exit();
        } else {
            // Error during account creation
            $_SESSION['Error'] = "Error creating account.";
            include 'ErrorModule.php';
        }
    }
}

// LOGIN PROCESSING
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Retrieve login form data
    $username = $_POST['loginUser'];
    $password = $_POST['loginPass'];

    // Fetch user credentials from database
    $stmt = $conn->prepare("SELECT password, usertype FROM UserAccount WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        $usertype = $row['usertype'];

        // Verify password
        if (password_verify($password, $hashedPassword)) {
            // Set session variables and redirect based on user type
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
            // Incorrect password
            $_SESSION['Error'] = "Incorrect password.";
            include 'ErrorModule.php';
        }
    } else {
        // Username not found
        $_SESSION['Error'] = "Username not found.";
        include 'ErrorModule.php';
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
<script src = "scripts/Login.js"></script>
<main class="main-container">
<section class="container">
  <!-- Left Panel -->
  <aside class="left-panel">
    <img src="https://images.trvl-media.com/lodging/1000000/440000/438500/438418/47e87212.jpg?impolicy=resizecrop&rw=575&rh=575&ra=fill" alt="Login Image" />
  </aside>

  <!-- Right Panel: Contains Login and Signup forms. -->
  <section class="right-panel">
    <!-- Login Form -->
    <article id="login-form" style="<?php echo isset($_POST['create']) ? 'display:none;' : 'display:block;'; ?>">
      <header>
        <h2>Login to your <br>La Ginta Real One <br>Loyalty Account</h2>
      </header>
      <?php if (isset($errorMsg['login'])) echo "<p style='color:red;'>{$errorMsg['login']}</p>"; ?>
      <form action="" method="post" autocomplete="on">
        <label for="loginUser">Username</label>
        <input type="text" required name="loginUser" id="loginUser" autocomplete="username" />
        <label for="loginPass">Password</label>
        <input type="password" required name="loginPass" id="loginPass" autocomplete="current-password" />

        <div class="checkbox-container">
          <input type="checkbox" id="remember" />
          <label for="remember">Remember me</label>
        </div>

        <button type="submit" name="login">Log in</button>

        <p class="signup-text">
          Don't have an account?
          <a href="#" id="create-button" onclick="toggleForm()">Create here</a>
        </p>
      </form>
    </article>

    <!-- Sign Up Form -->
    <article id="signup-form" style="<?php echo isset($_POST['create']) ? 'display:block;' : 'display:none;'; ?>">
      <header>
        <h2>Create Account</h2>
      </header>
      <?php if (isset($errorMsg['signup'])) echo "<p style='color:red;'>{$errorMsg['signup']}</p>"; ?>
      <?php if ($successMsg && isset($_POST['create'])) echo "<p style='color:green;'>$successMsg</p>"; ?>
      <form action="" method="post" autocomplete="on">
        <div class="name-row">
          <div class="name-field">
            <label for="firstName">First Name</label>
            <input type="text" required name="firstName" id="firstName" autocomplete="given-name" />
          </div>
          <div class="name-field">
            <label for="lastName">Last Name</label>
            <input type="text" required name="lastName" id="lastName" autocomplete="family-name" />
          </div>
        </div>

        <label for="signupUser">Username</label>
        <input type="text" required name="signupUser" id="signupUser" autocomplete="username" />
        <div class="row">
          <div class="field">
            <label for="signupEmail">Birthday</label>
            <input type="date" required name="Birthday" id="signupEmail" autocomplete="bday" />
          </div>
          <div class="field">
            <label for="signupPass">Password</label>
            <input type="password" required name="signupPass" id="signupPass" autocomplete="new-password" />
          </div>
        </div>

        <div class="checkbox-container">
          <input type="checkbox" id="terms" required />
          <label for="terms">I agree to the terms and conditions</label>
        </div>
        <button type="submit" name="create">Sign up</button>

        <p class="signup-text">
          Already have an account?
          <a href="#" id="login-button" onclick="toggleForm()">Log in here</a>
        </p>
      </form>
    </article>
  </section>
</section>
</main>

<?php if (isset($_GET['success'])): ?>
<div id="popup-notification" class="popup" role="alert">
  Account successfully created.
</div>
<script>
  setTimeout(() => {
    document.getElementById('popup-notification').style.opacity = '0';
  }, 3000);
</script>
<?php endif; ?>

</body>
</html>