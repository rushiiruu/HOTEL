<?php
// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get username from session if it exists
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>

<link rel="stylesheet" href="styles/NavbarNoback.css">
<nav id="navbar" class="scrolled">
  <a href="#" class="menu-icon" onclick="toggleMenu()">
    <i class="bi bi-list" id="menu"></i>
  </a>

  <a href="Home.php" class="hotel-name">
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
    <a href="UserAccount.php">
      <i class="bi bi-person-circle" id="user-icon"></i>
    </a>
    
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
    <li><a href="AboutUs.php">About Us</a></li>
    <li><a href="ManageReservation.php">My Reservation</a></li>
    <li><a href="UserAccount.php">Account</a></li>
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

<script>
  // Menu Toggle Functionality
  function toggleMenu() {
    var sideMenu = document.getElementById('sideMenu');
    sideMenu.classList.toggle('show');
  }
</script>